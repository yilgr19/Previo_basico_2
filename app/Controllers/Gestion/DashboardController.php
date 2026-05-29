<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

require_once dirname(__DIR__) . '/init.php';
require_once dirname(__DIR__) . '/bootstrap.php';

use App\Controllers\Controller;
use App\Services\SolicitudesService;

final class DashboardController extends Controller
{
    public function run(): void
    {
        require_gestion_admin();
        $u = auth_user();
        $nombreAdmin = trim((string) ($u['nombre'] ?? 'Administrador'));

        $conteosSolicitudesPorSede = SolicitudesService::conteosPanelPorSedeBandeja();

        $this->render('gestion/dashboard.php', [
            'pageTitle' => 'Panel de gestión académica',
            'adminNombre' => $nombreAdmin,
            'conteosSolicitudesPorSede' => $conteosSolicitudesPorSede,
        ]);
    }
}

\App\Controllers\dispatch_if_direct(__FILE__, DashboardController::class);
