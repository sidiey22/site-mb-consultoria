<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';

if (!is_file(MB_ROOT . '/inc/installed.lock') && is_file(MB_ROOT . '/install.php')) { redirect(url('install.php')); }
mb_session();
if (current_user()) { redirect('index.php'); }

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    $r = attempt_login(post('login'), (string)($_POST['senha'] ?? ''));
    if ($r === true) { redirect('index.php'); }
    $error = $r;
}
auth_head('Entrar');
?>
    <h1>Entrar no painel</h1>
    <p class="sub">Use seu usuário e senha para gerenciar o site.</p>
    <?php if ($error): ?><div class="alert err" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="on">
      <?= csrf_field() ?>
      <div class="fld"><label for="login">Usuário ou e-mail</label><input id="login" name="login" type="text" autocomplete="username" required autofocus value="<?= e(post('login')) ?>"></div>
      <div class="fld"><label for="senha">Senha</label><input id="senha" name="senha" type="password" autocomplete="current-password" required></div>
      <button class="btn" type="submit">Entrar</button>
    </form>
    <p class="auth-links"><a href="esqueci.php">Esqueci minha senha</a></p>
<?php auth_foot();
