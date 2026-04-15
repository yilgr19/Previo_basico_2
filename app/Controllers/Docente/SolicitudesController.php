<?php
declare(strict_types=1);

namespace App\Controllers\Docente;

use App\Controllers\Controller;
use App\Services\SolicitudesService;

final class SolicitudesController extends Controller
{
    private const FLASH_KEY = '_flash_doc_sol';

    public function run(): void
    {
        require_role(\ROLE_DOCENTE);

        $idDoc = auth_id();
        if (!$idDoc) {
            redirect('/login.php');
        }
        $doc = repo_docente_por_id($idDoc);
        if (!$doc) {
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'nueva_solicitud_docente') {
            if ($vista !== 'nueva') {
                redirect(url('docente/nueva_solicitud.php'));
            }
            [$mensaje, $tipoMsg] = SolicitudesService::registrarDesdeDocente($idDoc);
            if ($tipoMsg === 'success') {
                $_SESSION[self::FLASH_KEY] = ['mensaje' => $mensaje, 'tipoMsg' => $tipoMsg];
                redirect(url('docente/mis_solicitudes.php'));
            }
        }

        $old = [];
        if ($vista === 'nueva' && ($tipoMsg ?? '') === 'warning' && $_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'nueva_solicitud_docente') {
            $old = solicitud_docente_old_desde_post();
        }

        if ($vista === 'nueva') {
            $this->render('docente/solicitud_nueva.php', [
                'pageTitle' => 'Nueva solicitud',
                'solNavActiva' => 'nueva',
                'doc' => $doc,
                'mensaje' => $mensaje,
                'tipoMsg' => $tipoMsg,
                'old' => $old,
            ]);

            return;
        }

        SolicitudesService::marcarNotificacionesLeidasParaUsuario(auth_user());

        $todas = load_data('solicitudes');
        $propias = [];
        foreach ($todas as $s) {
            if ((int) ($s['id_docente_solicitante'] ?? 0) === $idDoc) {
                $propias[] = SolicitudesService::normalizarParaVista($s);
            }
        }
        usort($propias, static fn ($a, $b) => ((int) ($b['id_solicitud'] ?? 0)) <=> ((int) ($a['id_solicitud'] ?? 0)));

        $solicitudesActivas = [];
        $solicitudesEnRevision = [];
        $solicitudesAprobadas = [];
        $solicitudesRechazadas = [];
        foreach ($propias as $s) {
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

        $menciones = SolicitudesService::listadoMencionesAnonimasParaDocente($idDoc, (string) ($doc['documento'] ?? ''));

        $this->render('docente/mis_solicitudes.php', [
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
            'menciones' => $menciones,
        ]);
    }

    private function vistaDesdeScript(): string
    {
        $b = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));

        return $b === 'nueva_solicitud.php' ? 'nueva' : 'lista';
    }
}
