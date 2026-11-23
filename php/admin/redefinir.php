<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
mb_session();

$token = get('token', post('token'));
$reset = find_valid_reset($token);
$error = '';
$done = false;

if ($reset && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    $p1 = (string)($_POST['senha'] ?? ''); $p2 = (string)($_POST['senha2'] ?? '');
    if ($p1 !== $p2) { $error = 'As duas senhas não são iguais.'; }
    elseif (($pp = password_problem($p1)) !== null) { $error = $pp; }
    else {
        q('UPDATE users SET password_hash = :h WHERE id = :id', [':h' => password_hash($p1, PASSWORD_DEFAULT), ':id' => $reset['user_id']]);
        q('UPDATE password_resets SET used = 1 WHERE user_id = :u', [':u' => $reset['user_id']]);
        $done = true;
    }
}
auth_head('Nova senha');
if ($done): ?>
    <h1>Senha alterada</h1>
    <p class="sub">Pronto! Sua nova senha já vale. Entre no painel com ela.</p>
    <a class="btn" href="login.php">Ir para o login</a>
<?php elseif (!$reset): ?>
    <h1>Link inválido</h1>
    <p class="sub">Esse link expirou ou já foi usado. Peça um novo para continuar.</p>
    <a class="btn" href="esqueci.php">Pedir novo link</a>
<?php else: ?>
    <h1>Criar nova senha</h1>
    <p class="sub">Escolha uma senha com pelo menos 8 caracteres, com letras e números.</p>
    <?php if ($error): ?><div class="alert err" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <div class="fld"><label for="senha">Nova senha</label><input id="senha" name="senha" type="password" autocomplete="new-password" required autofocus></div>
      <div class="fld"><label for="senha2">Repita a nova senha</label><input id="senha2" name="senha2" type="password" autocomplete="new-password" required></div>
      <button class="btn" type="submit">Salvar nova senha</button>
    </form>
<?php endif; auth_foot();
