<?php
declare(strict_types=1);
require __DIR__ . '/inc/bootstrap.php';

if (get('lista') !== '') { redirect(base_path() . '/#conteudos'); }

$slug = strtolower(get('slug'));
try {
    $a = preg_match('/^[a-z0-9\-]{1,120}$/', $slug) ? public_article_by_slug($slug) : null;
    if (!$a) { require __DIR__ . '/404.php'; exit; }
    ob_start();
    render_article_page($a);
    ob_end_flush();
} catch (Throwable $e) {
    while (ob_get_level() > 0) { ob_end_clean(); }
    $dir = __DIR__ . '/inc/logs';
    if (is_dir($dir) && is_writable($dir)) {
        @file_put_contents($dir . '/error.log', date('c') . ' artigo.php ' . $slug . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine() . "\n", FILE_APPEND);
    }
    http_response_code(500);
    echo '<!DOCTYPE html><meta charset="utf-8"><title>Erro</title><p style="font-family:sans-serif;padding:40px">Não foi possível carregar este artigo agora. <a href="' . htmlspecialchars(base_path() . '/#conteudos') . '">Voltar aos conteúdos</a></p>';
}
