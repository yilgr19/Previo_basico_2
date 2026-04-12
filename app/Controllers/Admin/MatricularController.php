<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;

final class MatricularController extends Controller
{
    public function run(): void
    {
        require_role(\ROLE_ADMIN);

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
                $idEst = (int) post('id_estudiante', '0');
                $idMat = (int) post('id_materia', '0');
                $cargado = repo_estudiante_por_id($idEst);
                if (!$cargado || $idMat <= 0) {
                    $mensaje = 'Datos incompletos para matricular.';
                    $tipoMsg = 'danger';
                } elseif (repo_existe_matricula($idEst, $idMat)) {
                    $mensaje = 'El estudiante ya está matriculado en esa asignatura.';
                    $tipoMsg = 'warning';
                } else {
                    $mat = load_data('matriculas');
                    $mat[] = [
                        'id_matricula' => next_numeric_id($mat, 'id_matricula'),
                        'id_estudiante' => $idEst,
                        'id_materia' => $idMat,
                        'fecha' => date('Y-m-d'),
                    ];
                    save_data('matriculas', $mat);
                    $mensaje = 'Matrícula registrada correctamente.';
                    $tipoMsg = 'success';
                }
            }
        }

        $this->render('admin/matricular.php', [
            'pageTitle' => 'Matrícula de asignaturas',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'cargado' => $cargado,
            'materiasOrdenadas' => $materiasOrdenadas,
        ]);
    }
}
