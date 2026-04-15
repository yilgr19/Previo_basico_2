<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;
use App\Services\SolicitudesService;

final class SolicitudesRevisionController extends Controller
{
    public function run(): void
    {
        require_gestion_admin();

        $mensaje = '';
        $tipoMsg = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'cambiar_estado') {
            $id = (int) post('id_solicitud', '0');
            $est = (string) post('estado', '');
            $resp = (string) post('respuesta', '');
            $guardarElab = post('incluir_elaborada', '') === '1';
            [$mensaje, $tipoMsg] = SolicitudesService::actualizarEstadoAdmin($id, $est, $resp, $guardarElab);
        }

        $filtroFechaDesde = get('fecha_desde', '');
        $filtroFechaHasta = get('fecha_hasta', '');
        $filtroRadicante = get('radicante', '');
        if ($filtroRadicante !== 'estudiantes' && $filtroRadicante !== 'docentes') {
            $filtroRadicante = '';
        }
        $buscarDoc = get('buscar', '');

        $items = SolicitudesService::listadoParaAdmin([
            'fecha_desde' => $filtroFechaDesde,
            'fecha_hasta' => $filtroFechaHasta,
            'estado' => 'en_revision',
            'aprobacion' => '',
            'radicante' => $filtroRadicante,
            'buscar' => $buscarDoc,
        ]);

        $this->render('gestion/solicitudes_revision.php', [
            'pageTitle' => 'Solicitudes en revisión',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'items' => $items,
            'filtroFechaDesde' => $filtroFechaDesde,
            'filtroFechaHasta' => $filtroFechaHasta,
            'filtroRadicante' => $filtroRadicante,
            'buscarDoc' => $buscarDoc,
        ]);
    }
}
