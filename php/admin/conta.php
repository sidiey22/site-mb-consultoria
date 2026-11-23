<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u = require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    if (post('acao') === 'dados') {
        $name = mb_substr(post('nome'), 0, 120); $email = mb_substr(post('email'), 0, 160);
        if (mb_strlen($name) < 2) { flash('err', 'Digite o seu nome.'); }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { flash('err', 'Digite um e-mail válido.'); }
        elseif ((int)val('SELECT COUNT(*) FROM users WHERE LOWER(email) = :e AND id <> :id', [':e' => mb_strtolower($email), ':id' => $u['id']]) > 0) { flash('err', 'Esse e-mail já é de outro usuário.'); }
        else { update('users', (int)$u['id'], ['name' => $name, 'email' => $email]); flash('ok', 'Dados atualizados.'); }
    } elseif (post('acao') === 'senha') {
        $cur = (string)($_POST['atual'] ?? ''); $n1 = (string)($_POST['nova'] ?? ''); $n2 = (string)($_POST['nova2'] ?? '');
        $hash = (string)val('SELECT password_hash FROM users WHERE id = :id', [':id' => $u['id']]);
        if (!password_verify($cur, $hash)) { flash('err', 'A senha atual não confere.'); }
        elseif ($n1 !== $n2) { flash('err', 'As duas senhas novas não são iguais.'); }
        elseif (($pp = password_problem($n1)) !== null) { flash('err', $pp); }
        else {
            q('UPDATE users SET password_hash = :h WHERE id = :id', [':h' => password_hash($n1, PASSWORD_DEFAULT), ':id' => $u['id']]);
            session_regenerate_id(true);
            flash('ok', 'Senha alterada com sucesso.');
        }
    }
    redirect('conta.php');
}
$me = row('SELECT name, email, username, role FROM users WHERE id = :id', [':id' => $u['id']]);
admin_head('Minha conta', 'conta', $u);
?>
<div class="ph"><div><h1>Minha conta</h1><p>Usuário: <b><?= e($me['username']) ?></b> · <?= $me['role'] === 'admin' ? 'Administrador' : 'Editor' ?></p></div></div>
<div class="grid2">
  <form method="post" class="card">
    <?= csrf_field() ?><input type="hidden" name="acao" value="dados">
    <h2>Meus dados</h2>
    <div class="fld"><label for="nome">Nome</label><input id="nome" name="nome" type="text" value="<?= e($me['name']) ?>" required></div>
    <div class="fld"><label for="email">E-mail</label><input id="email" name="email" type="email" value="<?= e($me['email']) ?>" required><small>É para este e-mail que vai o link de "esqueci minha senha".</small></div>
    <button class="btn gold" type="submit">Salvar dados</button>
  </form>
  <form method="post" class="card" autocomplete="off">
    <?= csrf_field() ?><input type="hidden" name="acao" value="senha">
    <h2>Trocar senha</h2>
    <div class="fld"><label for="atual">Senha atual</label><input id="atual" name="atual" type="password" autocomplete="current-password" required></div>
    <div class="fld"><label for="nova">Nova senha</label><input id="nova" name="nova" type="password" autocomplete="new-password" required><small>Mínimo 8 caracteres, com letras e números.</small></div>
    <div class="fld"><label for="nova2">Repita a nova senha</label><input id="nova2" name="nova2" type="password" autocomplete="new-password" required></div>
    <button class="btn gold" type="submit">Trocar senha</button>
  </form>
</div>
<?php admin_foot();
