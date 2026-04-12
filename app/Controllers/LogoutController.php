<?php
declare(strict_types=1);

namespace App\Controllers;

final class LogoutController extends Controller
{
    public function run(): void
    {
        logout_user();
        redirect('/login.php');
    }
}
