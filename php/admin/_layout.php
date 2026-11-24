<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/inc/bootstrap.php';

const LEAD_STATUS = ['novo' => 'Novo', 'em_contato' => 'Em contato', 'convertido' => 'Convertido', 'descartado' => 'Descartado'];
const ART_STATUS = ['rascunho' => 'Rascunho', 'publicado' => 'Publicado', 'agendado' => 'Agendado'];

/** Status que o visitante realmente vê (agendado com data vencida já está no ar). */
function effective_status(array $a): array
{
    $now = now();
    if ($a['status'] === 'rascunho') { return ['rascunho', 'Rascunho']; }
    if (empty($a['published_at']) || $a['published_at'] > $now) { return ['agendado', 'Agendado para ' . fmt_date($a['published_at'], 'd/m H:i')]; }
    return ['publicado', 'Publicado'];
}

function a_icon(string $n): string
{
    static $i = [
        'home' => '<path d="M3 11 12 3l9 8"/><path d="M5 10v10h14V10"/>',
        'file' => '<path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>',
        'tag' => '<path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'inbox' => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5.1 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.5-6.9A2 2 0 0 0 16.7 4H7.3a2 2 0 0 0-1.8 1.1z"/>',
        'cog' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/>',
        'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'out' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
        'globe' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'eye' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>',
        'trash' => '<path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>',
        'wa' => '<path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.4 8.4 0 1 1 21 11.5z"/>',
        'down' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>',
        'mail' => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
    ];
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($i[$n] ?? '') . '</svg>';
}

function admin_head(string $title, string $active, array $u, string $extraHead = ''): void
{
    $nav = [
        ['index', 'Início', 'home', false], ['artigos', 'Artigos', 'file', false], ['categorias', 'Categorias', 'tag', true],
        ['leads', 'Leads', 'inbox', true], ['configuracoes', 'Configurações', 'cog', true], ['usuarios', 'Usuários', 'users', true],
    ];
    ?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?= e(url('favicon.ico')) ?>" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="<?= e(url('img/favicon-32.png')) ?>">
<link rel="apple-touch-icon" href="<?= e(url('img/apple-touch-icon.png')) ?>">
<meta name="theme-color" content="#101c46">

<meta name="robots" content="noindex,nofollow">
<title><?= e($title) ?> · Painel MB</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css?v=1">
<?= $extraHead ?>
</head>
<body class="adm">
<a class="skip" href="#main">Pular para o conteúdo</a>
<div class="topm">
  <button type="button" class="menu-t" id="menuT" aria-label="Abrir menu" aria-expanded="false"><?= a_icon('menu') ?></button>
  <img src="../img/logo-linha.png" alt="MB Consultoria">
</div>
<div class="shell">
  <aside class="side" id="side" aria-label="Menu do painel">
    <a class="brand" href="index.php"><img src="../img/logo-bloco.png" alt="MB Consultoria e Assessoria Política"></a>
    <nav>
      <?php foreach ($nav as [$file, $label, $ic, $adminOnly]):
          if ($adminOnly && $u['role'] !== 'admin') { continue; } ?>
        <a href="<?= $file ?>.php"<?= $active === $file ? ' aria-current="page"' : '' ?>><?= a_icon($ic) ?><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="side-b">
      <a href="conta.php"<?= $active === 'conta' ? ' aria-current="page"' : '' ?>><?= a_icon('user') ?>Minha conta</a>
      <a href="<?= e(url('')) ?>" target="_blank" rel="noopener"><?= a_icon('globe') ?>Ver o site</a>
      <a href="logout.php"><?= a_icon('out') ?>Sair</a>
      <p class="who"><?= e($u['name']) ?><small><?= $u['role'] === 'admin' ? 'Administrador' : 'Editor' ?></small></p>
    </div>
  </aside>
  <div class="scrim" id="scrim"></div>
  <main id="main" class="main">
    <?php foreach (flash_pull() as $f): ?><div class="alert <?= $f['t'] === 'ok' ? 'ok' : ($f['t'] === 'info' ? 'info' : 'err') ?>" role="status"><?= e($f['m']) ?></div><?php endforeach; ?>
<?php
}

function admin_foot(string $extraScripts = ''): void
{
    ?>
  </main>
</div>
<script src="assets/admin.js?v=1"></script>
<?= $extraScripts ?>
</body>
</html>
<?php
}

/** Cabeçalho simples das telas de entrada (login, esqueci a senha). */
function auth_head(string $title): void
{
    security_headers();
    header('Cache-Control: no-store');
    ?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?= e(url('favicon.ico')) ?>" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="<?= e(url('img/favicon-32.png')) ?>">
<link rel="apple-touch-icon" href="<?= e(url('img/apple-touch-icon.png')) ?>">
<meta name="theme-color" content="#101c46">

<meta name="robots" content="noindex,nofollow">
<title><?= e($title) ?> · Painel MB</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css?v=1">
</head>
<body class="auth">
<main class="auth-box">
  <div class="auth-top"><img src="../img/logo-bloco.png" alt="MB Consultoria e Assessoria Política"></div>
  <div class="auth-card">
    <?php foreach (flash_pull() as $f): ?><div class="alert <?= $f['t'] === 'ok' ? 'ok' : 'err' ?>" role="status"><?= e($f['m']) ?></div><?php endforeach; ?>
<?php
}

function auth_foot(): void
{
    ?>
  </div>
  <p class="auth-foot">© <?= date('Y') ?> MB Consultoria e Assessoria Política</p>
</main>
<script src="assets/admin.js?v=1"></script>
</body>
</html>
<?php
}

/** Paginação do painel preservando os filtros. */
function admin_pager(array $pg, array $query): string
{
    if ($pg['pages'] <= 1) { return ''; }
    $o = '<nav class="pager" aria-label="Paginação">';
    $mk = function (int $n, string $label, string $cls = '') use ($query): string {
        $q = $query; $q['p'] = $n;
        return '<a class="pg ' . $cls . '" href="?' . e(http_build_query($q)) . '">' . $label . '</a>';
    };
    if ($pg['page'] > 1) { $o .= $mk($pg['page'] - 1, '‹'); }
    $from = max(1, $pg['page'] - 2); $to = min($pg['pages'], $pg['page'] + 2);
    if ($from > 1) { $o .= $mk(1, '1') . ($from > 2 ? '<span class="gap">…</span>' : ''); }
    for ($i = $from; $i <= $to; $i++) { $o .= $i === $pg['page'] ? '<span class="pg on" aria-current="page">' . $i . '</span>' : $mk($i, (string)$i); }
    if ($to < $pg['pages']) { $o .= ($to < $pg['pages'] - 1 ? '<span class="gap">…</span>' : '') . $mk($pg['pages'], (string)$pg['pages']); }
    if ($pg['page'] < $pg['pages']) { $o .= $mk($pg['page'] + 1, '›'); }
    return $o . '</nav>';
}

function unique_slug(string $table, string $base, int $ignoreId = 0): string
{
    $slug = $base; $n = 2;
    while ((int)val("SELECT COUNT(*) FROM $table WHERE slug = :s AND id <> :id", [':s' => $slug, ':id' => $ignoreId]) > 0) {
        $slug = mb_substr($base, 0, 110) . '-' . $n++;
    }
    return $slug;
}
