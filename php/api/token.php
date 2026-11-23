<?php
declare(strict_types=1);
require dirname(__DIR__) . '/inc/bootstrap.php';

// Entrega um "carimbo de tempo" assinado: o formulário só é aceito se passaram alguns segundos desde que a página abriu.
$t = (string)time();
json_out(['t' => $t, 'sig' => hash_hmac('sha256', $t, app_secret())]);
