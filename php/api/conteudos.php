<?php
declare(strict_types=1);
require dirname(__DIR__) . '/inc/bootstrap.php';

$cat = preg_replace('/[^a-z0-9\-]/', '', strtolower(get('cat'))) ?? '';
$page = max(1, (int)get('pagina', '1'));
json_out(render_posts_inner($cat, $page));
