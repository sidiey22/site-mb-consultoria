<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u = require_login();
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { redirect('artigos.php'); }
csrf_verify();
header('Cache-Control: no-store');

$cat = (int)post('category_id');
$catRow = $cat ? row('SELECT name FROM categories WHERE id = :id', [':id' => $cat]) : null;
$pub = post('published_at');
$pubTs = $pub !== '' ? strtotime(str_replace('T', ' ', $pub)) : false;
$a = [
    'id' => 0,
    'title' => post('title') ?: 'Título do artigo',
    'slug' => slugify(post('slug') ?: post('title')),
    'summary' => post('summary'),
    'cover' => post('cover_current'),
    'content' => sanitize_html((string)($_POST['content'] ?? '')),
    'category_id' => $cat ?: null,
    'cat_name' => $catRow['name'] ?? '',
    'author' => post('author'),
    'published_at' => $pubTs ? date('Y-m-d H:i:s', $pubTs) : now(),
    'seo_title' => post('seo_title'),
    'seo_description' => post('seo_description'),
    'updated_at' => now(),
];
render_article_page($a, true);
