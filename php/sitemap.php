<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';
header('Content-Type: application/xml; charset=utf-8');

$urls = [['loc' => abs_url(''), 'lastmod' => date('Y-m-d'), 'prio' => '1.0']];
try {
    foreach (rows('SELECT a.slug, a.updated_at, a.published_at FROM articles a WHERE ' . VISIBLE_SQL . ' ORDER BY a.published_at DESC', [':now' => now()]) as $r) {
        $urls[] = ['loc' => abs_url('conteudos/' . $r['slug']), 'lastmod' => date('Y-m-d', strtotime((string)($r['updated_at'] ?: $r['published_at']))), 'prio' => '0.7'];
    }
} catch (Throwable $e) { /* sem banco: só a página inicial */ }

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo '  <url><loc>' . e($u['loc']) . '</loc><lastmod>' . e($u['lastmod']) . '</lastmod><priority>' . $u['prio'] . "</priority></url>\n";
}
echo '</urlset>' . "\n";
