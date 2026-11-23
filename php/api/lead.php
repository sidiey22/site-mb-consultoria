<?php
declare(strict_types=1);
require dirname(__DIR__) . '/inc/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { json_out(['ok' => false, 'error' => 'Método não permitido.'], 405); }

$ok = ['ok' => true, 'message' => 'Mensagem enviada! Nossa equipe retornará o contato em breve.'];

// 1) Armadilha para robôs: campo escondido que pessoas não preenchem
if (post('website') !== '') { json_out($ok); }

// 2) Tempo mínimo de preenchimento (3 segundos) com carimbo assinado pelo servidor
$t = (int)post('t');
$sig = post('sig');
if ($t <= 0 || !hash_equals(hash_hmac('sha256', (string)$t, app_secret()), $sig)) {
    json_out(['ok' => false, 'error' => 'Não foi possível validar o envio. Recarregue a página e tente de novo.'], 400);
}
$age = time() - $t;
if ($age < 3) { json_out(['ok' => false, 'error' => 'Envio rápido demais. Confira os dados e tente de novo em alguns segundos.'], 429); }
if ($age > 21600) { json_out(['ok' => false, 'error' => 'A página ficou aberta por muito tempo. Recarregue e envie de novo.'], 400); }

try {
    // 3) Limite de envios por IP: 5 por hora
    if (rate_limited('lead', 5, 3600)) {
        json_out(['ok' => false, 'error' => 'Você já enviou algumas mensagens agora há pouco. Tente novamente mais tarde.'], 429);
    }

    // 4) Validação
    $name = mb_substr(preg_replace('/\s+/', ' ', post('nome')) ?? '', 0, 160);
    $email = mb_substr(post('email'), 0, 160);
    $phone = mb_substr(post('telefone'), 0, 40);
    if (mb_strlen($name) < 2) { json_out(['ok' => false, 'error' => 'Informe seu nome completo.'], 422); }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { json_out(['ok' => false, 'error' => 'Informe um e-mail válido.'], 422); }
    if (strlen(preg_replace('/\D+/', '', $phone) ?? '') < 8) { json_out(['ok' => false, 'error' => 'Informe um telefone ou WhatsApp válido, com DDD.'], 422); }
    if (post('aceite') !== '1') { json_out(['ok' => false, 'error' => 'É preciso aceitar a Política de Privacidade para enviar.'], 422); }

    $clip = fn(string $k, int $n) => mb_substr(post($k), 0, $n);
    $page = post('pagina');
    if (!preg_match('~^https?://~i', $page)) { $page = ''; }

    $lead = [
        'name' => $name, 'email' => $email, 'phone' => $phone,
        'city' => $clip('cidade', 120), 'company' => $clip('instituicao', 160), 'subject' => $clip('assunto', 160),
        'message' => mb_substr(post('mensagem'), 0, 5000),
        'form_name' => $clip('form_name', 80) ?: 'Contato',
        'page_url' => mb_substr($page, 0, 250), 'status' => 'novo', 'notes' => '',
        'utm_source' => $clip('utm_source', 120), 'utm_medium' => $clip('utm_medium', 120), 'utm_campaign' => $clip('utm_campaign', 160),
        'utm_term' => $clip('utm_term', 160), 'utm_content' => $clip('utm_content', 160),
        'referrer' => $clip('referrer', 250), 'ip' => client_ip(), 'user_agent' => mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 250),
        'mail_sent' => 0, 'mail_error' => null, 'created_at' => now(), 'updated_at' => now(),
    ];
    $id = insert('leads', $lead);
    rate_hit('lead');
    $lead['id'] = $id;

    // 5) Aviso por e-mail (se falhar, o contato continua salvo no painel)
    $r = notify_new_lead($lead);
    q('UPDATE leads SET mail_sent = :s, mail_error = :e WHERE id = :id', [':s' => $r === true ? 1 : 0, ':e' => $r === true ? null : mb_substr((string)$r, 0, 250), ':id' => $id]);

    json_out($ok);
} catch (Throwable $e) {
    mail_log('lead.php: ' . $e->getMessage());
    json_out(['ok' => false, 'error' => 'Não foi possível registrar o contato agora. Tente novamente em instantes.'], 500);
}
