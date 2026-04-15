<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;
use App\Services\SolicitudesService;

final class DashboardController extends Controller
{
    public function run(): void
    {
        require_gestion_admin();
        $u = auth_user();
        $nombreAdmin = trim((string) ($u['nombre'] ?? 'Administrador'));

        $nSolicitudes = 0;
        $nSolicitudesPendienteORevision = 0;
        foreach (load_data('solicitudes') as $row) {
            $nSolicitudes++;
            $s = SolicitudesService::normalizarParaVista($row);
            $st = (string) ($s['estado'] ?? '');
            if ($st === 'pendiente' || $st === 'en_revision') {
                $nSolicitudesPendienteORevision++;
            }
        }

        $this->render('gestion/dashboard.php', [
            'pageTitle' => 'Panel de gestión académica',
            'adminNombre' => $nombreAdmin,
            'nSolicitudes' => $nSolicitudes,
            'nSolicitudesPendienteORevision' => $nSolicitudesPendienteORevision,
        ]);
    }
}
