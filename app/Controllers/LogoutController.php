<?php
declare(strict_types=1);

namespace App\Controllers;

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/bootstrap.php';

final class LogoutController extends Controller
{
    public function run(): void
    {
        logout_user();
        redirect('/login');
    }
}

\App\Controllers\dispatch_if_direct(__FILE__, LogoutController::class);
