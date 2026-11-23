<?php
declare(strict_types=1);
/**
 * Instalador do painel MB.
 * Monta as tabelas, cria o primeiro administrador e se bloqueia depois de usado.
 * Depois de instalar, APAGUE este arquivo pelo Gerenciador de Arquivos.
 */
require __DIR__ . '/inc/bootstrap.php';
security_headers();
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

$lock = MB_ROOT . '/inc/installed.lock';
$locked = is_file($lock);

function inst_page(string $title, string $body): void
{
    ?><!DOCTYPE html>
<html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title><?= e($title) ?> · Instalação do painel MB</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin/assets/admin.css?v=1">
<style>.req{list-style:none;margin:0 0 18px;padding:0}.req li{display:flex;gap:10px;align-items:flex-start;padding:7px 0;border-bottom:1px solid #eef0f6;font-size:14px}.req b{flex:0 0 22px}.req .ok b{color:#1a8a4a}.req .no b{color:#c0392b}.req small{display:block;color:#6b7390}</style>
</head><body class="auth"><main class="auth-box" style="max-width:560px">
<div class="auth-top"><img src="img/logo-bloco.png" alt="MB Consultoria e Assessoria Política"></div>
<div class="auth-card"><?= $body ?></div>
<p class="auth-foot">© <?= date('Y') ?> MB Consultoria e Assessoria Política</p></main></body></html>
<?php
    exit;
}

if ($locked) {
    inst_page('Já instalado', '<h1>Já instalado</h1><p class="sub">O painel já foi instalado e este instalador está bloqueado.</p>'
        . '<p><a class="btn" href="admin/login.php" style="display:block;text-align:center;text-decoration:none">Ir para o painel</a></p>'
        . '<p class="hint" style="margin-top:14px">Por segurança, apague o arquivo <b>install.php</b> pelo Gerenciador de Arquivos.</p>');
}

/* ---------- verificações ---------- */
$checks = [];
$add = function (string $label, bool $ok, string $help = '') use (&$checks) { $checks[] = [$label, $ok, $help]; };
$add('PHP 8.0 ou superior (versão atual: ' . PHP_VERSION . ')', PHP_VERSION_ID >= 80000, 'No cPanel: "Selecionar versão do PHP" e escolha 8.1 ou mais nova.');
$add('Extensão PDO MySQL', extension_loaded('pdo_mysql') || (DB_DRIVER === 'sqlite' && extension_loaded('pdo_sqlite')), 'No cPanel > Selecionar versão do PHP > Extensões, marque pdo_mysql.');
$add('Extensão GD (imagens)', extension_loaded('gd'), 'Marque "gd" em Extensões do PHP.');
$add('Extensão mbstring', extension_loaded('mbstring'), 'Marque "mbstring" em Extensões do PHP.');
$add('Extensão fileinfo', extension_loaded('fileinfo'), 'Marque "fileinfo" em Extensões do PHP.');
$add('Pasta uploads/ com permissão de escrita', is_dir(MB_ROOT . '/uploads') && is_writable(MB_ROOT . '/uploads'), 'No Gerenciador de Arquivos, clique com o botão direito em "uploads" > Permissões > 755.');
$add('Pasta inc/ com permissão de escrita', is_writable(MB_ROOT . '/inc'), 'Clique com o botão direito em "inc" > Permissões > 755.');
$cfgOk = DB_DRIVER === 'sqlite' || (DB_NAME !== '' && strpos(DB_NAME, 'USUARIOCPANEL') === false && strpos(DB_PASS, 'COLOQUE') === false);
$add('Arquivo inc/config.php preenchido', $cfgOk, 'Abra inc/config.php no Gerenciador de Arquivos > Editar e coloque o nome do banco, usuário e senha.');
$dbErr = '';
$dbOk = false;
if ($cfgOk) {
    try { db(); $dbOk = true; } catch (Throwable $e) { $dbErr = $e->getMessage(); }
}
$add('Conexão com o banco de dados', $dbOk, $cfgOk ? ('O servidor respondeu: ' . $dbErr . ' — confira nome, usuário e senha, e se o usuário foi adicionado ao banco com "Todos os privilégios".') : 'Preencha o config.php primeiro.');
$allOk = !in_array(false, array_column($checks, 1), true);

$errors = [];
$old = ['nome' => '', 'email' => '', 'usuario' => '', 'avisos' => ''];

if ($allOk && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    mb_session();
    csrf_verify();
    $old = ['nome' => post('nome'), 'email' => post('email'), 'usuario' => strtolower(post('usuario')), 'avisos' => post('avisos')];
    $pass = (string)($_POST['senha'] ?? ''); $pass2 = (string)($_POST['senha2'] ?? '');
    if (mb_strlen($old['nome']) < 2) { $errors[] = 'Digite o seu nome.'; }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) { $errors[] = 'Digite um e-mail válido.'; }
    if (!preg_match('/^[a-z0-9._-]{3,60}$/', $old['usuario'])) { $errors[] = 'O usuário deve ter de 3 a 60 caracteres: letras minúsculas, números, ponto, hífen ou sublinhado.'; }
    if ($pass !== $pass2) { $errors[] = 'As duas senhas não são iguais.'; }
    elseif (($pp = password_problem($pass)) !== null) { $errors[] = $pp; }
    $avisos = array_filter(array_map('trim', preg_split('/[,;\s]+/', $old['avisos']) ?: []));
    foreach ($avisos as $a) { if (!filter_var($a, FILTER_VALIDATE_EMAIL)) { $errors[] = 'E-mail inválido nos avisos de leads: ' . $a; } }

    if (!$errors) {
        try {
            foreach (schema_statements() as $sql) {
                try { db()->exec($sql); }
                catch (Throwable $e) { if (stripos($sql, 'CREATE INDEX') !== 0) { throw $e; } } // índice já existe: ok
            }
            if ((int)val('SELECT COUNT(*) FROM users') > 0) { throw new RuntimeException('O banco já tem usuários. Use um banco vazio ou apague as tabelas antigas pelo phpMyAdmin.'); }
            insert('users', ['name' => $old['nome'], 'email' => $old['email'], 'username' => $old['usuario'], 'password_hash' => password_hash($pass, PASSWORD_DEFAULT), 'role' => 'admin', 'active' => 1, 'created_at' => now()]);
            foreach (['Artigos', 'Cases', 'Redes sociais', 'Comunicação política', 'Marketing eleitoral', 'Reputação', 'Gestão pública'] as $cat) {
                if (!val('SELECT COUNT(*) FROM categories WHERE slug = :s', [':s' => slugify($cat)])) {
                    insert('categories', ['name' => $cat, 'slug' => slugify($cat), 'created_at' => now()]);
                }
            }
            set_setting('app_secret', bin2hex(random_bytes(32)));
            foreach (setting_defaults() as $k => $v) {
                if ($k === 'app_secret') { continue; }
                if (!val('SELECT COUNT(*) FROM settings WHERE skey = :k', [':k' => $k])) { set_setting($k, (string)$v); }
            }
            set_setting('notify_emails', implode(', ', $avisos ?: [$old['email']]));
            if (@file_put_contents($lock, 'instalado em ' . now() . "\n") === false) { throw new RuntimeException('Não consegui gravar inc/installed.lock. Dê permissão de escrita à pasta inc/.'); }
            @unlink(__FILE__);
            $gone = !is_file(__FILE__);
            inst_page('Instalação concluída', '<h1>Tudo pronto!</h1><p class="sub">O painel foi instalado e o primeiro administrador criado.</p>'
                . '<div class="alert ok">Usuário: <b>' . e($old['usuario']) . '</b></div>'
                . ($gone ? '<div class="alert ok">O arquivo install.php foi apagado automaticamente.</div>'
                         : '<div class="alert err"><b>Importante:</b> apague agora o arquivo <b>install.php</b> pelo Gerenciador de Arquivos. Ele já está bloqueado, mas não deve ficar no servidor.</div>')
                . '<p><a class="btn" href="admin/login.php" style="display:block;text-align:center;text-decoration:none">Entrar no painel</a></p>'
                . '<p class="hint" style="margin-top:14px">Dentro do painel, em Configurações, preencha os dados de e-mail (SMTP) e faça o teste de envio.</p>');
        } catch (Throwable $e) {
            $errors[] = 'Não foi possível concluir a instalação: ' . $e->getMessage();
        }
    }
}

ob_start();
?>
<h1>Instalar o painel</h1>
<p class="sub">Confira os requisitos, depois crie o primeiro administrador.</p>
<ul class="req">
<?php foreach ($checks as [$label, $ok, $help]): ?>
  <li class="<?= $ok ? 'ok' : 'no' ?>"><b><?= $ok ? '✓' : '✕' ?></b><span><?= e($label) ?><?php if (!$ok && $help): ?><small><?= e($help) ?></small><?php endif; ?></span></li>
<?php endforeach; ?>
</ul>
<?php if (!$allOk): ?>
  <div class="alert err">Resolva os itens marcados com ✕ e atualize esta página (F5).</div>
<?php else:
  mb_session(); ?>
  <?php foreach ($errors as $er): ?><div class="alert err" role="alert"><?= e($er) ?></div><?php endforeach; ?>
  <form method="post" autocomplete="off">
    <?= csrf_field() ?>
    <div class="fld"><label for="nome">Seu nome</label><input id="nome" name="nome" type="text" required value="<?= e($old['nome']) ?>"></div>
    <div class="fld"><label for="email">Seu e-mail</label><input id="email" name="email" type="email" required value="<?= e($old['email']) ?>"><small>Usado para recuperar a senha.</small></div>
    <div class="fld"><label for="usuario">Usuário para entrar</label><input id="usuario" name="usuario" type="text" required pattern="[a-z0-9._-]{3,60}" placeholder="ex.: mariana" value="<?= e($old['usuario']) ?>"></div>
    <div class="fld"><label for="senha">Senha</label><input id="senha" name="senha" type="password" required autocomplete="new-password"><small>Mínimo 8 caracteres, com letras e números.</small></div>
    <div class="fld"><label for="senha2">Repita a senha</label><input id="senha2" name="senha2" type="password" required autocomplete="new-password"></div>
    <div class="fld"><label for="avisos">E-mails que vão receber os contatos do site (opcional)</label><input id="avisos" name="avisos" type="text" placeholder="Separe por vírgula. Vazio = o seu e-mail" value="<?= e($old['avisos']) ?>"></div>
    <button class="btn" type="submit">Instalar</button>
  </form>
<?php endif; ?>
<?php
inst_page('Instalar', (string)ob_get_clean());
