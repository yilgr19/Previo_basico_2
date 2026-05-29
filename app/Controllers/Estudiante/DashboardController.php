<?php
declare(strict_types=1);

namespace App\Controllers\Estudiante;

require_once dirname(__DIR__) . '/init.php';
require_once dirname(__DIR__) . '/bootstrap.php';

use App\Controllers\Controller;

final class DashboardController extends Controller
{
    public function run(): void
    {
        require_role(\ROLE_ESTUDIANTE);

        $idEst = auth_id();
        if (!$idEst) {
            redirect('/login');
        }
        $yo = repo_estudiante_por_id($idEst);
        $todas = array_values(array_filter(load_data('solicitudes'), static fn ($s) => (int) ($s['id_estudiante'] ?? 0) === $idEst));
        $nActivas = 0;
        $nEnRevision = 0;
        $nAprobadas = 0;
        $nRechazadas = 0;
        foreach ($todas as $s) {
            $st = (string) ($s['estado'] ?? '');
            if ($st === 'aprobada') {
                $nAprobadas++;
            } elseif ($st === 'rechazada') {
                $nRechazadas++;
            } elseif ($st === 'en_revision') {
                $nEnRevision++;
            } else {
                $nActivas++;
            }
        }

        $this->render('estudiante/dashboard.php', [
            'pageTitle' => 'Inicio — Estudiante',
            'yo' => $yo,
            'nActivas' => $nActivas,
            'nEnRevision' => $nEnRevision,
            'nAprobadas' => $nAprobadas,
            'nRechazadas' => $nRechazadas,
        ]);
    }
}

\App\Controllers\dispatch_if_direct(__FILE__, DashboardController::class);
