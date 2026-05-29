<?php
declare(strict_types=1);

require_once __DIR__ . '/app/Controllers/init.php';
require_once __DIR__ . '/app/Controllers/bootstrap.php';

\App\Controllers\bootstrap(\App\Controllers\HomeController::class);
