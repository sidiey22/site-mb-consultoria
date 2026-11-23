<?php
declare(strict_types=1);

if (defined('MB_BOOT')) { return; }
define('MB_BOOT', 1);
define('MB_ROOT', dirname(__DIR__));

$__cfg = __DIR__ . '/config.php';
if (is_file($__cfg)) { require $__cfg; }

$__defaults = [
    'DB_DRIVER' => 'mysql', 'DB_HOST' => 'localhost', 'DB_NAME' => '', 'DB_USER' => '', 'DB_PASS' => '',
    'DB_PATH' => '', // somente para testes locais com sqlite
    'SITE_URL' => '', 'TIMEZONE' => 'America/Cuiaba',
    'SMTP_HOST' => '', 'SMTP_PORT' => 465, 'SMTP_SECURE' => 'ssl', 'SMTP_USER' => '', 'SMTP_PASS' => '', 'SMTP_FROM' => '',
    'UPLOAD_MAX_BYTES' => 5 * 1024 * 1024,
];
foreach ($__defaults as $k => $v) { if (!defined($k)) { define($k, $v); } }

date_default_timezone_set(TIMEZONE);
mb_internal_encoding('UTF-8');

require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
require __DIR__ . '/settings.php';
require __DIR__ . '/sanitize.php';
require __DIR__ . '/images.php';
require __DIR__ . '/smtp.php';
require __DIR__ . '/mail.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/site.php';
