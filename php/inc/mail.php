<?php
declare(strict_types=1);

/** Envia um e-mail usando o SMTP das Configurações (ou o mail() do servidor se o SMTP estiver vazio). */
function send_mail(array|string $to, string $subject, string $html, ?string $replyTo = null, ?string $replyName = null): true|string
{
    $to = is_array($to) ? $to : [$to];
    $s = settings_all();
    $fromEmail = trim($s['smtp_from']) !== '' ? trim($s['smtp_from']) : trim($s['smtp_user']);
    $fromName = $s['smtp_from_name'] !== '' ? $s['smtp_from_name'] : 'Site MB Consultoria';

    if (trim($s['smtp_host']) !== '') {
        $smtp = new MbSmtp(trim($s['smtp_host']), (int)$s['smtp_port'] ?: 465, in_array($s['smtp_secure'], ['ssl', 'tls', 'none'], true) ? $s['smtp_secure'] : 'ssl',
            trim($s['smtp_user']), $s['smtp_pass'], $s['smtp_verify'] !== '0');
        $r = $smtp->send($fromEmail, $fromName, $to, $subject, $html, '', $replyTo, $replyName);
        if ($r !== true) { mail_log('SMTP: ' . $r); }
        return $r;
    }

    // Sem SMTP configurado: tenta o envio padrão do servidor.
    if ($fromEmail === '') { $fromEmail = 'no-reply@' . preg_replace('/^www\./', '', (string)($_SERVER['SERVER_NAME'] ?? 'localhost')); }
    $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\nFrom: =?UTF-8?B?" . base64_encode($fromName) . "?= <$fromEmail>\r\n";
    if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) { $headers .= "Reply-To: $replyTo\r\n"; }
    $ok = @mail(implode(',', $to), '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, $headers);
    if (!$ok) { mail_log('mail(): falha no envio padrão do servidor'); return 'O servidor não conseguiu enviar o e-mail. Configure o SMTP em Configurações > E-mail.'; }
    return true;
}

function mail_log(string $msg): void
{
    $dir = MB_ROOT . '/inc/logs';
    if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
    @file_put_contents($dir . '/mail.log', '[' . now() . '] ' . $msg . "\n", FILE_APPEND | LOCK_EX);
}

/** Moldura de e-mail na identidade do site. */
function mail_layout(string $title, string $inner): string
{
    return '<!DOCTYPE html><html lang="pt-BR"><body style="margin:0;background:#f2f3f6;font-family:Arial,Helvetica,sans-serif;color:#192347">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f2f3f6;padding:24px 12px"><tr><td align="center">'
        . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:18px;overflow:hidden">'
        . '<tr><td style="background:#192347;padding:22px 28px;border-bottom:4px solid #c9a063"><span style="color:#f2c47e;font-size:13px;letter-spacing:.5px">MB Consultoria e Assessoria Política</span>'
        . '<div style="color:#ffffff;font-size:22px;margin-top:6px;font-weight:bold">' . e($title) . '</div></td></tr>'
        . '<tr><td style="padding:26px 28px;font-size:15px;line-height:1.6">' . $inner . '</td></tr>'
        . '<tr><td style="padding:16px 28px;background:#f7f8fb;font-size:12px;color:#6b7188">Mensagem automática do site da MB Consultoria.</td></tr>'
        . '</table></td></tr></table></body></html>';
}

function mail_row(string $label, string $value, bool $isHtml = false): string
{
    if (trim(strip_tags($value)) === '') { return ''; }
    return '<tr><td style="padding:8px 12px 8px 0;color:#6b7188;font-size:13px;vertical-align:top;white-space:nowrap">' . e($label)
        . '</td><td style="padding:8px 0;border-bottom:1px solid #eceef4">' . ($isHtml ? $value : nl2br(e($value))) . '</td></tr>';
}

/** Avisa a equipe sobre um lead novo. Retorna true ou a mensagem de erro. */
function notify_new_lead(array $lead): true|string
{
    $recipients = array_filter(array_map('trim', preg_split('/[,;\s]+/', setting('notify_emails')) ?: []));
    if (!$recipients) { return 'Nenhum e-mail de aviso configurado.'; }
    $wa = wa_link((string)$lead['phone'], 'Olá, ' . $lead['name'] . '! Aqui é da MB Consultoria e Assessoria Política. Recebemos seu contato.');
    $t = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">'
        . mail_row('Nome', (string)$lead['name'])
        . mail_row('E-mail', '<a href="mailto:' . e($lead['email']) . '" style="color:#192347">' . e($lead['email']) . '</a>', true)
        . mail_row('Telefone / WhatsApp', (string)$lead['phone'])
        . mail_row('Instituição ou projeto', (string)$lead['company'])
        . mail_row('Cidade / Estado', (string)$lead['city'])
        . mail_row('Assunto', (string)$lead['subject'])
        . mail_row('Mensagem', (string)$lead['message'])
        . mail_row('Formulário', (string)$lead['form_name'])
        . mail_row('Origem', trim(($lead['utm_source'] ?? '') . ' ' . ($lead['utm_medium'] ?? '') . ' ' . ($lead['utm_campaign'] ?? '')))
        . mail_row('Recebido em', fmt_date($lead['created_at'], 'd/m/Y \à\s H:i'))
        . '</table>';
    $btns = '<p style="margin:22px 0 0">'
        . ($wa !== '' ? '<a href="' . e($wa) . '" style="display:inline-block;background:#192347;color:#f2c47e;text-decoration:none;padding:12px 20px;border-radius:999px;font-weight:bold;font-size:14px;margin-right:8px">Responder pelo WhatsApp</a>' : '')
        . '<a href="' . e(abs_url('admin/lead.php?id=' . (int)$lead['id'])) . '" style="display:inline-block;border:1px solid #192347;color:#192347;text-decoration:none;padding:11px 20px;border-radius:999px;font-weight:bold;font-size:14px">Abrir no painel</a></p>';
    return send_mail($recipients, 'Novo contato pelo site: ' . $lead['name'], mail_layout('Novo contato pelo site', $t . $btns), (string)$lead['email'], (string)$lead['name']);
}
