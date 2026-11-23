<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/bootstrap.php';
http_response_code(404);
security_headers();
$S = site_settings();
$h = base_path() . '/';
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Página não encontrada · MB Consultoria e Assessoria Política</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(url('css/site.css')) ?>">
<link rel="stylesheet" href="<?= e(url('css/cms.css')) ?>">
</head>
<body>
<?php include __DIR__ . '/inc/partials/header.php'; ?>
<main>
  <div class="wrap nf">
    <div class="n">404</div>
    <h1>Essa página não foi encontrada.</h1>
    <p>O endereço pode ter mudado ou o conteúdo não está mais disponível. Volte para o início ou veja os conteúdos publicados.</p>
    <div class="btns" style="justify-content:center;display:flex;gap:12px;flex-wrap:wrap">
      <a class="btn btn-navy" href="<?= e($h) ?>#home">Ir para o início <?= icon('arrow-right') ?></a>
      <a class="btn btn-line" style="color:var(--navy);border-color:var(--navy)" href="<?= e($h) ?>#conteudos">Ver conteúdos</a>
    </div>
  </div>
</main>
<?php include __DIR__ . '/inc/partials/footer.php'; ?>
<script defer src="<?= e(url('js/cms.js')) ?>"></script>
</body>
</html>
