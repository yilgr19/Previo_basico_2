<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;

final class DashboardController extends Controller
{
    public function run(): void
    {
        require_gestion_admin();
        $this->render('gestion/dashboard.php', [
            'pageTitle' => 'Panel de gestión académica',
        ]);
    }
}
