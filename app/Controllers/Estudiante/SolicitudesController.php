<?php
declare(strict_types=1);

namespace App\Controllers\Estudiante;

use App\Controllers\Controller;
use App\Services\SolicitudesService;

final class SolicitudesController extends Controller
{
    public function run(): void
    {
        require_role(\ROLE_ESTUDIANTE);

        $idEst = auth_id();
        if (!$idEst) {
            redirect('/login.php');
        }

        $mensaje = '';
        $tipoMsg = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'nueva_solicitud') {
            [$mensaje, $tipoMsg] = SolicitudesService::registrarDesdeEstudiante($idEst);
        }

        $yo = repo_estudiante_por_id($idEst);
        $todas = array_values(array_filter(load_data('solicitudes'), static fn ($s) => (int) ($s['id_estudiante'] ?? 0) === $idEst));
        $todas = array_map(static fn ($s) => SolicitudesService::normalizarParaVista($s), $todas);

        $solicitudesActivas = [];
        $solicitudesEnRevision = [];
        $solicitudesAprobadas = [];
        $solicitudesRechazadas = [];
        foreach ($todas as $s) {
            $st = (string) ($s['estado'] ?? '');
            if ($st === 'aprobada') {
                $solicitudesAprobadas[] = $s;
            } elseif ($st === 'rechazada') {
                $solicitudesRechazadas[] = $s;
            } elseif ($st === 'en_revision') {
                $solicitudesEnRevision[] = $s;
            } else {
                $solicitudesActivas[] = $s;
            }
        }

        $tab = trim((string) get('tab', 'activas'));
        $tabsValidos = ['activas', 'en_revision', 'aprobadas', 'rechazadas'];
        if (!in_array($tab, $tabsValidos, true)) {
            $tab = 'activas';
        }
        if ($tab === 'en_revision') {
            $listaTab = $solicitudesEnRevision;
        } elseif ($tab === 'aprobadas') {
            $listaTab = $solicitudesAprobadas;
        } elseif ($tab === 'rechazadas') {
            $listaTab = $solicitudesRechazadas;
        } else {
            $listaTab = $solicitudesActivas;
        }

        $this->render('estudiante/solicitudes.php', [
            'pageTitle' => 'Mis solicitudes',
            'yo' => $yo,
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'tab' => $tab,
            'listaTab' => $listaTab,
            'conteosSolicitudes' => [
                'activas' => count($solicitudesActivas),
                'en_revision' => count($solicitudesEnRevision),
                'aprobadas' => count($solicitudesAprobadas),
                'rechazadas' => count($solicitudesRechazadas),
            ],
        ]);
    }
}
