<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;
use App\Services\SolicitudesService;

final class SolicitudesController extends Controller
{
    public function __construct(
        private readonly int $idSedeBandeja = 0
    ) {
    }

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
            if ($this->idSedeBandeja > 0 && !SolicitudesService::solicitudPerteneceASedeBandeja($id, $this->idSedeBandeja)) {
                $mensaje = 'Esta solicitud no corresponde a la sede de esta bandeja.';
                $tipoMsg = 'warning';
            } else {
                [$mensaje, $tipoMsg] = SolicitudesService::actualizarEstadoAdmin($id, $est, $resp, $guardarElab);
            }
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

        $opts = [
            'fecha_desde' => $filtroFechaDesde,
            'fecha_hasta' => $filtroFechaHasta,
            'estado' => $filtroEstado,
            'aprobacion' => $filtroAprob,
            'radicante' => $filtroRadicante,
            'buscar' => $buscarDoc,
        ];
        if ($this->idSedeBandeja > 0) {
            $opts['id_sede'] = $this->idSedeBandeja;
        }
        $items = SolicitudesService::listadoParaAdmin($opts);

        $titulosSede = [1 => 'Cúcuta', 2 => 'Ocaña'];
        $nombreSede = $titulosSede[$this->idSedeBandeja] ?? '';
        $pageTitle = $this->idSedeBandeja > 0
            ? 'Bandeja de solicitudes — Sede ' . $nombreSede
            : 'Solicitudes institucionales';
        $bandejaScript = $this->idSedeBandeja === 2
            ? 'gestion/solicitudes_sede_ocana.php'
            : 'gestion/solicitudes.php';

        $this->render('gestion/solicitudes.php', [
            'pageTitle' => $pageTitle,
            'bandejaScript' => $bandejaScript,
            'idSedeBandeja' => $this->idSedeBandeja,
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
