<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';
header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\nDisallow: " . base_path() . "/admin/\nDisallow: " . base_path() . "/inc/\nDisallow: " . base_path() . "/api/\n\nSitemap: " . abs_url('sitemap.xml') . "\n";
