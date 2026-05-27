<?php
declare(strict_types=1);

/** true = MySQL (producción). false = respaldo JSON en data/ (solo desarrollo). */
define('DB_ENABLED', true);

define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_NAME', 'solicitudes_academicas');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
