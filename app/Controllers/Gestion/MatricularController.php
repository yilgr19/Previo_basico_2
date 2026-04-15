<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;
use App\Services\GestionAcademicaService;

final class MatricularController extends Controller
{
    public function run(): void
    {
        require_gestion_admin();

        $mensaje = '';
        $tipoMsg = 'success';
        $cargado = null;
        $materias = load_data('materias');
        $materiasOrdenadas = repo_materias_ordenadas_por_codigo($materias);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = post('accion', '');
            if ($accion === 'buscar') {
                $doc = post('documento_buscar', '');
                $cargado = repo_estudiante_por_documento($doc);
                if (!$cargado) {
                    $mensaje = 'No se encontró un estudiante con ese documento.';
                    $tipoMsg = 'warning';
                }
            } elseif ($accion === 'matricular') {
                [$mensaje, $tipoMsg] = GestionAcademicaService::agregarMatricula();
                $idEst = (int) post('id_estudiante', '0');
                $cargado = repo_estudiante_por_id($idEst);
            }
        }

        $this->render('gestion/matricular.php', [
            'pageTitle' => 'Matrícula de asignaturas',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'cargado' => $cargado,
            'materiasOrdenadas' => $materiasOrdenadas,
        ]);
    }
}
