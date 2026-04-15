<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;
use App\Services\GestionAcademicaService;

final class EstudiantesController extends Controller
{
    public function run(): void
    {
        require_gestion_admin();

        $mensaje = '';
        $tipoMsg = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'guardar') {
            [$mensaje, $tipoMsg] = GestionAcademicaService::agregarEstudiante();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = post('accion', '');
            $estudiantes = load_data('estudiantes');
            if ($accion === 'eliminar') {
                $id = (int) post('id_estudiante', '0');
                $estudiantes = array_values(array_filter($estudiantes, static fn ($e) => (int) ($e['id_estudiante'] ?? 0) !== $id));
                save_data('estudiantes', $estudiantes);
                $mensaje = 'Estudiante eliminado.';
            }
        }

        $estudiantes = load_data('estudiantes');
        $editar = null;
        $eid = (int) (get('editar') ?? '0');
        if ($eid > 0) {
            $editar = repo_estudiante_por_id($eid);
        }

        $this->render('gestion/estudiantes.php', [
            'pageTitle' => 'Registrar estudiante',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'estudiantes' => $estudiantes,
            'editar' => $editar,
        ]);
    }
}
