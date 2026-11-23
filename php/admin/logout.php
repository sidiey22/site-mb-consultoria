<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
do_logout();
redirect('login.php');
