<?php
declare(strict_types=1);
require_once __DIR__ . '/config/config.php';

if (auth_user()) {
    redirect(dashboard_url_for_role(auth_role()));
}
redirect('/login.php');
