<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
mb_session();

$sent = false;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    $email = post('email');
    if (!rate_limited('reset', 3, 3600)) {
        rate_hit('reset');
        $u = filter_var($email, FILTER_VALIDATE_EMAIL) ? row('SELECT id, name, email FROM users WHERE email = :e AND active = 1', [':e' => $email]) : null;
        if ($u) {
            $token = create_password_reset((int)$u['id']);
            $link = abs_url('admin/redefinir.php?token=' . $token);
            $html = mail_layout('Redefinir sua senha',
                '<p>Olá, ' . e($u['name']) . '.</p><p>Recebemos um pedido para criar uma nova senha no painel do site. O link vale por 1 hora.</p>'
                . '<p style="margin:22px 0"><a href="' . e($link) . '" style="display:inline-block;background:#192347;color:#f2c47e;text-decoration:none;padding:13px 24px;border-radius:999px;font-weight:bold">Criar nova senha</a></p>'
                . '<p style="font-size:13px;color:#6b7188">Se você não pediu, ignore este e-mail: sua senha continua a mesma.</p>');
            send_mail($u['email'], 'Redefinir sua senha do painel', $html);
        }
    }
    $sent = true; // sempre a mesma resposta, exista o e-mail ou não
}
auth_head('Esqueci minha senha');
?>
    <h1>Esqueci minha senha</h1>
<?php if ($sent): ?>
    <p class="sub">Se esse e-mail estiver cadastrado, enviamos um link para criar uma nova senha. Confira também a caixa de spam.</p>
    <p class="auth-links"><a href="login.php">Voltar para o login</a></p>
<?php else: ?>
    <p class="sub">Digite o e-mail da sua conta e enviaremos um link para criar uma nova senha.</p>
    <form method="post">
      <?= csrf_field() ?>
      <div class="fld"><label for="email">E-mail</label><input id="email" name="email" type="email" autocomplete="email" required autofocus></div>
      <button class="btn" type="submit">Enviar link</button>
    </form>
    <p class="auth-links"><a href="login.php">Voltar para o login</a></p>
<?php endif; auth_foot();
