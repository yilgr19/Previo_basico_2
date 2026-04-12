<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;

final class DashboardController extends Controller
{
    public function run(): void
    {
        require_role(\ROLE_ADMIN);
        $this->render('admin/dashboard.php', [
            'pageTitle' => 'Panel administrador',
        ]);
    }
}
