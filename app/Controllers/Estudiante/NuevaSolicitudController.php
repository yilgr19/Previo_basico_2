<?php
declare(strict_types=1);

namespace App\Controllers\Estudiante;

require_once dirname(__DIR__) . '/init.php';
require_once dirname(__DIR__) . '/bootstrap.php';

use App\Controllers\Controller;
use App\Services\PlazosSolicitudService;
use App\Services\SolicitudesService;

final class NuevaSolicitudController extends Controller
{
    private const FLASH_KEY = '_flash_est_sol';

    public function run(): void
    {
        require_role(\ROLE_ESTUDIANTE);

        $idEst = auth_id();
        if (!$idEst) {
            redirect('/login');
        }

        $mensaje = '';
        $tipoMsg = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'nueva_solicitud') {
            [$mensaje, $tipoMsg] = SolicitudesService::registrarDesdeEstudiante($idEst);
            if ($tipoMsg === 'success') {
                $_SESSION[self::FLASH_KEY] = ['mensaje' => $mensaje, 'tipoMsg' => $tipoMsg];
                redirect(url('estudiante/mis_solicitudes'));
            }
        }

        $yo = repo_estudiante_por_id($idEst);

        $old = [];
        if (($tipoMsg ?? '') === 'warning' && $_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'nueva_solicitud') {
            $old = solicitud_estudiante_old_desde_post();
        }

        $this->render('estudiante/solicitud_nueva.php', [
            'pageTitle' => 'Nueva solicitud',
            'solNavActiva' => 'nueva',
            'yo' => $yo,
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'old' => $old,
            'matrizPlazos' => PlazosSolicitudService::matrizParaFrontend(),
        ]);
    }
}

\App\Controllers\dispatch_if_direct(__FILE__, NuevaSolicitudController::class);
