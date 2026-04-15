<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;
use App\Services\GestionAcademicaService;

final class DocentesController extends Controller
{
    public function run(): void
    {
        require_gestion_admin();

        $mensaje = '';
        $tipoMsg = 'success';
        $docentes = load_data('docentes');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = post('accion', '');
            if ($accion === 'eliminar') {
                $id = (int) post('id_docente', '0');
                $refs = load_data('materias');
                $bloqueo = false;
                foreach ($refs as $m) {
                    if ((int) ($m['id_docente'] ?? 0) === $id) {
                        $bloqueo = true;
                        break;
                    }
                }
                if ($bloqueo) {
                    $mensaje = 'No se puede eliminar: hay asignaturas asignadas a este docente.';
                    $tipoMsg = 'warning';
                } else {
                    $docentes = array_values(array_filter($docentes, static fn ($d) => (int) ($d['id_docente'] ?? 0) !== $id));
                    save_data('docentes', $docentes);
                    $mensaje = 'Docente eliminado.';
                }
            } elseif ($accion === 'guardar') {
                [$mensaje, $tipoMsg] = GestionAcademicaService::agregarDocente();
            }
        }

        $docentes = load_data('docentes');
        $editar = null;
        $eid = (int) (get('editar') ?? '0');
        if ($eid > 0) {
            $editar = repo_docente_por_id($eid);
        }

        $this->render('gestion/docentes.php', [
            'pageTitle' => 'Registro de docentes',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'docentes' => $docentes,
            'editar' => $editar,
        ]);
    }
}
