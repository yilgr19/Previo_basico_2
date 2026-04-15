<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;
use App\Services\SolicitudesService;

final class SolicitudesController extends Controller
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
        $filtroEstado = get('estado', '');
        $filtroAprob = get('aprobacion', '');
        $filtroRadicante = get('radicante', '');
        if ($filtroRadicante !== 'estudiantes' && $filtroRadicante !== 'docentes') {
            $filtroRadicante = '';
        }
        $buscarDoc = get('buscar', '');

        $items = SolicitudesService::listadoParaAdmin([
            'fecha_desde' => $filtroFechaDesde,
            'fecha_hasta' => $filtroFechaHasta,
            'estado' => $filtroEstado,
            'aprobacion' => $filtroAprob,
            'radicante' => $filtroRadicante,
            'buscar' => $buscarDoc,
        ]);

        $this->render('gestion/solicitudes.php', [
            'pageTitle' => 'Solicitudes estudiantiles',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'items' => $items,
            'filtroFechaDesde' => $filtroFechaDesde,
            'filtroFechaHasta' => $filtroFechaHasta,
            'filtroEstado' => $filtroEstado,
            'filtroAprob' => $filtroAprob,
            'filtroRadicante' => $filtroRadicante,
            'buscarDoc' => $buscarDoc,
        ]);
    }
}
