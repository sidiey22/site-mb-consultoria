<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u = require_login();
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { json_out(['ok' => false, 'error' => 'Método não permitido.'], 405); }
csrf_verify();
$r = save_upload($_FILES['imagem'] ?? [], 1600);
if (!$r['ok']) { json_out(['ok' => false, 'error' => $r['error']], 422); }
json_out(['ok' => true, 'url' => $r['url'], 'path' => $r['path']]);
