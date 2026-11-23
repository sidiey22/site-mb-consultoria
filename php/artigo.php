<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';

if (get('lista') !== '') { redirect(base_path() . '/#conteudos'); }

$slug = strtolower(get('slug'));
$a = preg_match('/^[a-z0-9\-]{1,120}$/', $slug) ? public_article_by_slug($slug) : null;
if (!$a) { require __DIR__ . '/404.php'; exit; }

render_article_page($a);
