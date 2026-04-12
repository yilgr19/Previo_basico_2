<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_role(ROLE_ADMIN);

$pageTitle = 'Panel administrador';
require PARTIALS_PATH . '/header.php';
require VIEWS_PATH . '/admin/dashboard.php';
require PARTIALS_PATH . '/footer.php';
