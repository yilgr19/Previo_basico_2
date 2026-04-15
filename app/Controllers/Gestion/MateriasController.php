<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;
use App\Services\GestionAcademicaService;

final class MateriasController extends Controller
{
    public function run(): void
    {
        require_gestion_admin();

        $mensaje = '';
        $tipoMsg = 'success';
        $materias = repo_materias_ordenadas_por_codigo(load_data('materias'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = post('accion', '');
            if ($accion === 'eliminar') {
                $id = (int) post('id_materia', '0');
                $mat = load_data('matriculas');
                foreach ($mat as $x) {
                    if ((int) ($x['id_materia'] ?? 0) === $id) {
                        $mensaje = 'No se puede eliminar: existen matrículas en esta asignatura.';
                        $tipoMsg = 'warning';
                        break;
                    }
                }
                if ($mensaje === '') {
                    $raw = load_data('materias');
                    $raw = array_values(array_filter($raw, static fn ($m) => (int) ($m['id_materia'] ?? 0) !== $id));
                    save_data('materias', $raw);
                    $mensaje = 'Asignatura eliminada.';
                }
            } elseif ($accion === 'guardar') {
                [$mensaje, $tipoMsg] = GestionAcademicaService::agregarMateria();
            }
        }

        $materias = repo_materias_ordenadas_por_codigo(load_data('materias'));
        $docentes = load_data('docentes');
        $editar = null;
        $eid = (int) (get('editar') ?? '0');
        if ($eid > 0) {
            $editar = repo_materia_por_id($eid);
        }

        $docBuscarDoc = '';
        $docBuscarNom = '';
        $hidDocente = 0;
        if ($editar) {
            $hidDocente = (int) ($editar['id_docente'] ?? 0);
            if ($hidDocente > 0) {
                $ddEd = repo_docente_por_id($hidDocente);
                if ($ddEd) {
                    $docBuscarDoc = (string) ($ddEd['documento'] ?? '');
                    $docBuscarNom = trim(($ddEd['nombre'] ?? '') . ' ' . ($ddEd['apellido'] ?? ''));
                }
            }
        }
        $ef = $editar ?? [];
        $modValForm = (string) ($ef['modalidad'] ?? 'virtual');
        if (!in_array($modValForm, ['presencial', 'virtual'], true)) {
            $modValForm = 'virtual';
        }
        $diaValForm = (string) ($ef['dia_clase'] ?? 'lunes');
        if (!array_key_exists($diaValForm, materia_dias_clase_opciones())) {
            $diaValForm = 'lunes';
        }
        $idProgForm = (int) ($ef['id_programa'] ?? 0);
        $docentesLookupJson = json_encode(
            array_values(array_map(static function ($d) {
                return [
                    'id' => (int) ($d['id_docente'] ?? 0),
                    'documento' => trim((string) ($d['documento'] ?? '')),
                    'nombre' => trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? '')),
                    'id_programa' => (int) ($d['id_programa'] ?? 0),
                    'id_sede' => docente_sede_efectiva($d),
                ];
            }, $docentes)),
            JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP
        );

        $this->render('gestion/materias.php', [
            'pageTitle' => 'Asignaturas',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'materias' => $materias,
            'docentes' => $docentes,
            'editar' => $editar,
            'docBuscarDoc' => $docBuscarDoc,
            'docBuscarNom' => $docBuscarNom,
            'hidDocente' => $hidDocente,
            'ef' => $ef,
            'modValForm' => $modValForm,
            'diaValForm' => $diaValForm,
            'idProgForm' => $idProgForm,
            'docentesLookupJson' => $docentesLookupJson,
        ]);
    }
}
