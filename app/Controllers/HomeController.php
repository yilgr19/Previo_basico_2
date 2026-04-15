<?php
declare(strict_types=1);

namespace App\Controllers;

final class HomeController extends Controller
{
    public function run(): void
    {
        if (auth_user()) {
            redirect(dashboard_url_for_user());
        }
        redirect('/login.php');
    }
}
