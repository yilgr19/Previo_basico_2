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
            [$mensaje, $tipoMsg] = SolicitudesService::actualizarEstadoAdmin($id, $est, $resp);
        }

        $filtroFechaDesde = get('fecha_desde', '');
        $filtroFechaHasta = get('fecha_hasta', '');
        $filtroEstado = get('estado', '');
        $filtroAprob = get('aprobacion', '');
        $buscarDoc = get('buscar', '');

        $items = SolicitudesService::listadoParaAdmin([
            'fecha_desde' => $filtroFechaDesde,
            'fecha_hasta' => $filtroFechaHasta,
            'estado' => $filtroEstado,
            'aprobacion' => $filtroAprob,
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
            'buscarDoc' => $buscarDoc,
        ]);
    }
}
