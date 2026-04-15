<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;

final class ReportesController extends Controller
{
    public function run(): void
    {
        require_gestion_admin();

        $mensaje = '';
        $tipoMsg = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'eliminar_matricula') {
            $id = (int) post('id_matricula', '0');
            $mat = load_data('matriculas');
            $mat = array_values(array_filter($mat, static fn ($m) => (int) ($m['id_matricula'] ?? 0) !== $id));
            save_data('matriculas', $mat);
            $mensaje = 'Matrícula eliminada.';
        }

        $estudiantes = load_data('estudiantes');
        $docentes = load_data('docentes');
        $materias = load_data('materias');
        $materiasOrdenadas = repo_materias_ordenadas_por_codigo($materias);
        $matriculas = load_data('matriculas');

        $this->render('gestion/reportes.php', [
            'pageTitle' => 'Reportes',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'estudiantes' => $estudiantes,
            'docentes' => $docentes,
            'materiasOrdenadas' => $materiasOrdenadas,
            'matriculas' => $matriculas,
        ]);
    }
}
