<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;
use App\Services\SolicitudesService;

final class SolicitudesRevisionController extends Controller
{
    public function __construct(
        private readonly int $idSedeBandeja = 0
    ) {
    }

    public function run(): void
    {
        require_gestion_admin();
        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'GET') {
            SolicitudesService::marcarNotificacionesGestionLeidas();
        }

        $mensaje = '';
        $tipoMsg = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'cambiar_estado') {
            $id = (int) post('id_solicitud', '0');
            $est = (string) post('estado', '');
            $resp = (string) post('respuesta', '');
            $guardarElab = post('incluir_elaborada', '') === '1';
            if ($this->idSedeBandeja > 0 && !SolicitudesService::solicitudPerteneceASedeBandeja($id, $this->idSedeBandeja)) {
                $mensaje = 'Esta solicitud no corresponde a la sede de esta bandeja.';
                $tipoMsg = 'warning';
            } else {
                [$mensaje, $tipoMsg] = SolicitudesService::actualizarEstadoAdmin($id, $est, $resp, $guardarElab);
            }
        }

        $gestionRepoblar = null;
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && post('accion', '') === 'cambiar_estado' && ($tipoMsg ?? '') === 'warning') {
            $gestionRepoblar = gestion_formulario_repoblar_desde_post();
        }

        $filtroFechaDesde = get('fecha_desde', '');
        $filtroFechaHasta = get('fecha_hasta', '');
        $filtroRadicante = get('radicante', '');
        if ($filtroRadicante !== 'estudiantes' && $filtroRadicante !== 'docentes') {
            $filtroRadicante = '';
        }
        $buscarDoc = get('buscar', '');

        $opts = [
            'fecha_desde' => $filtroFechaDesde,
            'fecha_hasta' => $filtroFechaHasta,
            'estado' => 'en_revision',
            'aprobacion' => '',
            'radicante' => $filtroRadicante,
            'buscar' => $buscarDoc,
        ];
        if ($this->idSedeBandeja > 0) {
            $opts['id_sede'] = $this->idSedeBandeja;
        }
        $items = SolicitudesService::listadoParaAdmin($opts);
        $items = array_values(array_filter(
            $items,
            static function (array $row): bool {
                $s = $row['solicitud'] ?? [];

                return solicitud_estado_a_codigo((string) ($s['estado'] ?? '')) === 'en_revision';
            }
        ));

        $titulosSede = [1 => 'Cúcuta', 2 => 'Ocaña'];
        $nombreSede = $titulosSede[$this->idSedeBandeja] ?? '';
        $pageTitle = $this->idSedeBandeja > 0
            ? 'Solicitudes en revisión — Sede ' . $nombreSede
            : 'Solicitudes en revisión';
        $revisionScript = $this->idSedeBandeja === 2
            ? 'gestion/solicitudes_revision_ocana.php'
            : 'gestion/solicitudes_revision.php';

        $this->render('gestion/solicitudes_revision.php', [
            'pageTitle' => $pageTitle,
            'revisionScript' => $revisionScript,
            'idSedeBandeja' => $this->idSedeBandeja,
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'items' => $items,
            'filtroFechaDesde' => $filtroFechaDesde,
            'filtroFechaHasta' => $filtroFechaHasta,
            'filtroRadicante' => $filtroRadicante,
            'buscarDoc' => $buscarDoc,
            'gestionRepoblar' => $gestionRepoblar,
        ]);
    }
}
