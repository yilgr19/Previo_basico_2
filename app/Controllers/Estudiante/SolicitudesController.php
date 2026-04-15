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

        $activas = [];
        $aprobadas = [];
        $rechazadasOtras = [];
        foreach ($todas as $s) {
            $st = (string) ($s['estado'] ?? '');
            if ($st === 'aprobada') {
                $aprobadas[] = $s;
            } elseif ($st === 'rechazada') {
                $rechazadasOtras[] = $s;
            } else {
                $activas[] = $s;
            }
        }

        $this->render('estudiante/solicitudes.php', [
            'pageTitle' => 'Mis solicitudes',
            'yo' => $yo,
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'activas' => $activas,
            'aprobadas' => $aprobadas,
            'rechazadasOtras' => $rechazadasOtras,
        ]);
    }
}
