<?php
declare(strict_types=1);

/** Hora oficial Colombia (sin horario de verano). Todas las fechas/horas del sistema usan esta zona. */
define('APP_TIMEZONE', 'America/Bogota');
date_default_timezone_set(APP_TIMEZONE);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('CORE_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'Core');
define('MODELS_PATH', APP_PATH . DIRECTORY_SEPARATOR . 'Models');
define('DATA_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'data');
define('PARTIALS_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'partials');
define('VIEWS_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'views');
define('ASSETS_URL', 'assets');
define('SITE_FOOTER_LINE', 'By Melanny Guate & Camilo Ramirez © 2026 — Sistema Académico');

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = APP_PATH . DIRECTORY_SEPARATOR;
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require_once CORE_PATH . '/helpers.php';
require_once MODELS_PATH . '/data_dictionary.php';
require_once MODELS_PATH . '/storage.php';
require_once CORE_PATH . '/auth.php';
require_once MODELS_PATH . '/repository.php';
