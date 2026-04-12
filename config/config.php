<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', dirname(__DIR__));
define('DATA_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'data');
define('INCLUDES_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'includes');
define('PARTIALS_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'partials');
define('ASSETS_URL', 'assets');

require_once INCLUDES_PATH . '/helpers.php';
require_once INCLUDES_PATH . '/data_dictionary.php';
require_once INCLUDES_PATH . '/storage.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/repository.php';
