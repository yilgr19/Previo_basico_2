<?php
declare(strict_types=1);

namespace App\Controllers\Docente;

use App\Controllers\Controller;
use App\Services\SolicitudesService;

final class SolicitudesController extends Controller
{
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

        $mensaje = '';
        $tipoMsg = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'nueva_solicitud_docente') {
            [$mensaje, $tipoMsg] = SolicitudesService::registrarDesdeDocente($idDoc);
        }

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

        $this->render('docente/solicitudes.php', [
            'pageTitle' => 'Solicitudes',
            'doc' => $doc,
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
}
