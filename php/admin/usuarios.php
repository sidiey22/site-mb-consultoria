<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u = require_login(true);

function admins_count(): int { return (int)val("SELECT COUNT(*) FROM users WHERE role = 'admin' AND active = 1"); }

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    $acao = post('acao'); $id = (int)post('id');
    $name = mb_substr(post('nome'), 0, 120); $email = mb_substr(post('email'), 0, 160);
    $username = strtolower(mb_substr(post('usuario'), 0, 60));
    $role = post('nivel') === 'admin' ? 'admin' : 'editor';
    $pass = (string)($_POST['senha'] ?? '');

    $check = function (int $ignore) use ($name, $email, $username): ?string {
        if (mb_strlen($name) < 2) { return 'Digite o nome.'; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { return 'Digite um e-mail válido.'; }
        if (!preg_match('/^[a-z0-9._-]{3,60}$/', $username)) { return 'O usuário deve ter de 3 a 60 caracteres: letras minúsculas, números, ponto, hífen ou sublinhado.'; }
        if ((int)val('SELECT COUNT(*) FROM users WHERE LOWER(email) = :e AND id <> :id', [':e' => mb_strtolower($email), ':id' => $ignore]) > 0) { return 'Já existe um usuário com esse e-mail.'; }
        if ((int)val('SELECT COUNT(*) FROM users WHERE username = :u AND id <> :id', [':u' => $username, ':id' => $ignore]) > 0) { return 'Esse nome de usuário já está em uso.'; }
        return null;
    };

    if ($acao === 'criar') {
        $err = $check(0) ?? password_problem($pass);
        if ($err) { flash('err', $err); }
        else { insert('users', ['name' => $name, 'email' => $email, 'username' => $username, 'password_hash' => password_hash($pass, PASSWORD_DEFAULT), 'role' => $role, 'active' => 1, 'created_at' => now()]); flash('ok', 'Usuário criado. Passe o usuário e a senha para a pessoa.'); }
    } elseif ($acao === 'salvar' && $id) {
        $target = row('SELECT * FROM users WHERE id = :id', [':id' => $id]);
        $active = post('ativo') === '1' ? 1 : 0;
        $err = $target ? $check($id) : 'Usuário não encontrado.';
        if (!$err && $pass !== '') { $err = password_problem($pass); }
        if (!$err && $target['role'] === 'admin' && ($role !== 'admin' || !$active) && admins_count() <= 1) { $err = 'Precisa existir pelo menos um Administrador ativo.'; }
        if (!$err && $id === (int)$u['id'] && !$active) { $err = 'Você não pode desativar a sua própria conta.'; }
        if ($err) { flash('err', $err); }
        else {
            $data = ['name' => $name, 'email' => $email, 'username' => $username, 'role' => $role, 'active' => $active];
            if ($pass !== '') { $data['password_hash'] = password_hash($pass, PASSWORD_DEFAULT); }
            update('users', $id, $data);
            flash('ok', 'Usuário atualizado.');
        }
    } elseif ($acao === 'excluir' && $id) {
        $target = row('SELECT * FROM users WHERE id = :id', [':id' => $id]);
        if (!$target) { flash('err', 'Usuário não encontrado.'); }
        elseif ($id === (int)$u['id']) { flash('err', 'Você não pode excluir a sua própria conta.'); }
        elseif ($target['role'] === 'admin' && admins_count() <= 1) { flash('err', 'Precisa existir pelo menos um Administrador.'); }
        else { q('DELETE FROM users WHERE id = :id', [':id' => $id]); flash('ok', 'Usuário excluído.'); }
    }
    redirect('usuarios.php');
}

$users = rows('SELECT * FROM users ORDER BY name');
$editId = (int)get('editar');
admin_head('Usuários', 'usuarios', $u);
?>
<div class="ph"><div><h1>Usuários</h1><p><b>Administrador</b> tem acesso a tudo. <b>Editor</b> só cuida dos artigos.</p></div></div>

<section class="card">
  <h2>Novo usuário</h2>
  <form method="post" autocomplete="off">
    <?= csrf_field() ?><input type="hidden" name="acao" value="criar">
    <div class="row3">
      <div class="fld"><label for="n-nome">Nome</label><input id="n-nome" name="nome" type="text" required></div>
      <div class="fld"><label for="n-email">E-mail</label><input id="n-email" name="email" type="email" required></div>
      <div class="fld"><label for="n-usuario">Usuário para entrar</label><input id="n-usuario" name="usuario" type="text" required pattern="[a-z0-9._-]{3,60}" placeholder="ex.: maria.silva"></div>
    </div>
    <div class="row3">
      <div class="fld"><label for="n-nivel">Nível</label><select id="n-nivel" name="nivel"><option value="editor">Editor (só artigos)</option><option value="admin">Administrador (tudo)</option></select></div>
      <div class="fld"><label for="n-senha">Senha</label><input id="n-senha" name="senha" type="password" required autocomplete="new-password"><small>Mínimo 8 caracteres, com letras e números.</small></div>
      <div class="fld" style="justify-content:flex-end"><button class="btn gold" type="submit"><?= a_icon('plus') ?>Criar usuário</button></div>
    </div>
  </form>
</section>

<section class="card">
  <h2>Usuários cadastrados</h2>
  <?php foreach ($users as $x): $isMe = (int)$x['id'] === (int)$u['id']; ?>
    <details class="card" style="margin:0 0 12px;padding:14px 18px"<?= $editId === (int)$x['id'] ? ' open' : '' ?>>
      <summary style="cursor:pointer;display:flex;gap:12px;align-items:center;flex-wrap:wrap;list-style:none">
        <b style="color:var(--navy)"><?= e($x['name']) ?></b>
        <span class="badge <?= $x['role'] === 'admin' ? 'b-novo' : 'b-rascunho' ?>"><?= $x['role'] === 'admin' ? 'Administrador' : 'Editor' ?></span>
        <?php if (!$x['active']): ?><span class="badge b-descartado">Desativado</span><?php endif; ?>
        <?= $isMe ? '<span class="badge b-star">Você</span>' : '' ?>
        <small style="color:var(--mute)"><?= e($x['username']) ?> · <?= e($x['email']) ?><?= $x['last_login'] ? ' · último acesso ' . e(fmt_date($x['last_login'], 'd/m/Y H:i')) : '' ?></small>
      </summary>
      <form method="post" style="margin-top:16px" autocomplete="off">
        <?= csrf_field() ?><input type="hidden" name="acao" value="salvar"><input type="hidden" name="id" value="<?= (int)$x['id'] ?>">
        <div class="row3">
          <div class="fld"><label for="e<?= (int)$x['id'] ?>-nome">Nome</label><input id="e<?= (int)$x['id'] ?>-nome" name="nome" type="text" value="<?= e($x['name']) ?>" required></div>
          <div class="fld"><label for="e<?= (int)$x['id'] ?>-email">E-mail</label><input id="e<?= (int)$x['id'] ?>-email" name="email" type="email" value="<?= e($x['email']) ?>" required></div>
          <div class="fld"><label for="e<?= (int)$x['id'] ?>-usuario">Usuário</label><input id="e<?= (int)$x['id'] ?>-usuario" name="usuario" type="text" value="<?= e($x['username']) ?>" required pattern="[a-z0-9._-]{3,60}"></div>
        </div>
        <div class="row3">
          <div class="fld"><label for="e<?= (int)$x['id'] ?>-nivel">Nível</label><select id="e<?= (int)$x['id'] ?>-nivel" name="nivel"><option value="editor"<?= $x['role'] === 'editor' ? ' selected' : '' ?>>Editor</option><option value="admin"<?= $x['role'] === 'admin' ? ' selected' : '' ?>>Administrador</option></select></div>
          <div class="fld"><label for="e<?= (int)$x['id'] ?>-senha">Nova senha (opcional)</label><input id="e<?= (int)$x['id'] ?>-senha" name="senha" type="password" autocomplete="new-password" placeholder="Deixe vazio para manter"></div>
          <div class="fld" style="justify-content:flex-end"><label class="chk"><input type="checkbox" name="ativo" value="1"<?= $x['active'] ? ' checked' : '' ?>> Conta ativa</label></div>
        </div>
        <div class="form-actions"><button class="btn gold" type="submit">Salvar alterações</button></div>
      </form>
      <?php if (!$isMe): ?>
      <form method="post" style="margin-top:12px" data-confirm="Excluir o usuário <?= e($x['name']) ?>?">
        <?= csrf_field() ?><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int)$x['id'] ?>">
        <button class="btn danger sm" type="submit"><?= a_icon('trash') ?>Excluir usuário</button>
      </form>
      <?php endif; ?>
    </details>
  <?php endforeach; ?>
</section>
<?php admin_foot();
