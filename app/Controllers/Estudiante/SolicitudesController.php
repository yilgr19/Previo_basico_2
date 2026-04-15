<?php
declare(strict_types=1);

namespace App\Controllers\Estudiante;

use App\Controllers\Controller;
use App\Services\SolicitudesService;

final class SolicitudesController extends Controller
{
    private const FLASH_KEY = '_flash_est_sol';

    public function run(): void
    {
        require_role(\ROLE_ESTUDIANTE);

        $idEst = auth_id();
        if (!$idEst) {
            redirect('/login.php');
        }

        $vista = $this->vistaDesdeScript();

        $mensaje = '';
        $tipoMsg = 'success';

        if ($vista === 'lista') {
            $flash = $_SESSION[self::FLASH_KEY] ?? null;
            if (is_array($flash)) {
                unset($_SESSION[self::FLASH_KEY]);
                $mensaje = (string) ($flash['mensaje'] ?? '');
                $tipoMsg = (string) ($flash['tipoMsg'] ?? 'success');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'nueva_solicitud') {
            if ($vista !== 'nueva') {
                redirect(url('estudiante/nueva_solicitud.php'));
            }
            [$mensaje, $tipoMsg] = SolicitudesService::registrarDesdeEstudiante($idEst);
            if ($tipoMsg === 'success') {
                $_SESSION[self::FLASH_KEY] = ['mensaje' => $mensaje, 'tipoMsg' => $tipoMsg];
                redirect(url('estudiante/mis_solicitudes.php'));
            }
        }

        $yo = repo_estudiante_por_id($idEst);

        $old = [];
        if ($vista === 'nueva' && ($tipoMsg ?? '') === 'warning' && $_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'nueva_solicitud') {
            $old = solicitud_estudiante_old_desde_post();
        }

        if ($vista === 'nueva') {
            $this->render('estudiante/solicitud_nueva.php', [
                'pageTitle' => 'Nueva solicitud',
                'solNavActiva' => 'nueva',
                'yo' => $yo,
                'mensaje' => $mensaje,
                'tipoMsg' => $tipoMsg,
                'old' => $old,
            ]);

            return;
        }

        SolicitudesService::marcarNotificacionesLeidasParaUsuario(auth_user());

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

        $this->render('estudiante/mis_solicitudes.php', [
            'pageTitle' => 'Mis solicitudes',
            'solNavActiva' => 'lista',
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

    private function vistaDesdeScript(): string
    {
        $b = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));

        return $b === 'nueva_solicitud.php' ? 'nueva' : 'lista';
    }
}
