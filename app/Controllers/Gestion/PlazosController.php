<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;
use App\Services\PlazosSolicitudService;

final class PlazosController extends Controller
{
    public function run(): void
    {
        require_gestion_admin();

        $mensaje = '';
        $tipoMsg = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = post('accion', '');
            if ($accion === 'guardar_plazo') {
                [$mensaje, $tipoMsg] = PlazosSolicitudService::guardarDesdePost();
            } elseif ($accion === 'guardar_plazos_masivo') {
                [$mensaje, $tipoMsg] = PlazosSolicitudService::guardarMasivoDesdePost();
            } elseif ($accion === 'eliminar_plazo') {
                [$mensaje, $tipoMsg] = PlazosSolicitudService::eliminarDesdePost();
            }
        }

        $editarTipo = (int) (get('tipo') ?? '0');
        $editarSede = (int) (get('sede') ?? '0');
        $editarPlazo = 15;
        if ($editarTipo > 0 && $editarSede > 0) {
            $d = PlazosSolicitudService::plazoDias($editarTipo, $editarSede);
            if ($d !== null && $d > 0) {
                $editarPlazo = $d;
            }
        }

        $this->render('gestion/plazos.php', [
            'pageTitle' => 'Plazos por tipo y sede',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'matriz' => PlazosSolicitudService::listarMatrizCompleta(),
            'tipos' => diccionario_tipos_solicitud(),
            'sedes' => diccionario_sedes(),
            'editarTipo' => $editarTipo,
            'editarSede' => $editarSede,
            'editarPlazo' => $editarPlazo,
        ]);
    }
}
