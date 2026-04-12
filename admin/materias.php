<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_role(ROLE_ADMIN);

$mensaje = '';
$materias = load_data('materias');
$docentes = load_data('docentes');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = post('accion', '');
    if ($accion === 'eliminar') {
        $id = (int) post('id_materia', '0');
        $mat = load_data('matriculas');
        foreach ($mat as $x) {
            if ((int) ($x['id_materia'] ?? 0) === $id) {
                $mensaje = 'No se puede eliminar: existen matrículas en esta asignatura.';
                break;
            }
        }
        if ($mensaje === '') {
            $materias = array_values(array_filter($materias, static fn ($m) => (int) ($m['id_materia'] ?? 0) !== $id));
            save_data('materias', $materias);
            $mensaje = 'Asignatura eliminada.';
        }
    } elseif ($accion === 'guardar') {
        $diasOk = array_keys(materia_dias_clase_opciones());
        $diaClase = post('dia_clase', '');
        if (!in_array($diaClase, $diasOk, true)) {
            $diaClase = '';
        }
        $idProg = (int) post('id_programa', '0');
        $idDoc = (int) post('id_docente', '0');
        $modalidad = post('modalidad', '');
        if (!in_array($modalidad, ['presencial', 'virtual'], true)) {
            $modalidad = '';
        }
        $salon = trim((string) post('salon', ''));
        if ($modalidad === 'virtual') {
            $salon = '';
        }
        $horaIni = trim((string) post('hora_inicio', ''));
        $horaFin = trim((string) post('hora_fin', ''));
        $row = [
            'codigo' => post('codigo', ''),
            'nombre' => post('nombre', ''),
            'creditos' => (int) post('creditos', '0'),
            'semestre' => (int) post('semestre', '1'),
            'id_programa' => $idProg,
            'dia_clase' => $diaClase,
            'hora_inicio' => $horaIni,
            'hora_fin' => $horaFin,
            'id_docente' => $idDoc,
            'modalidad' => $modalidad,
            'salon' => $salon,
        ];
        if ($idProg <= 0) {
            $mensaje = 'Seleccione la carrera o programa al que pertenece la asignatura.';
        } elseif ($diaClase === '') {
            $mensaje = 'Seleccione el día de clase.';
        } elseif ($modalidad === '') {
            $mensaje = 'Seleccione la modalidad de la clase (virtual o presencial).';
        } elseif ($idDoc <= 0) {
            $mensaje = 'Busque al docente por número de documento y valide antes de guardar.';
        } elseif ($horaIni === '' || $horaFin === '') {
            $mensaje = 'Indique hora de inicio y hora de fin de la clase.';
        } elseif ($modalidad === 'presencial' && $salon === '') {
            $mensaje = 'En modalidad presencial debe indicar el salón o aula.';
        } elseif ($idDoc > 0) {
            $docAsig = repo_docente_por_id($idDoc);
            if (!$docAsig) {
                $mensaje = 'Docente no encontrado. Busque de nuevo por documento.';
            } elseif ((int) ($docAsig['id_programa'] ?? 0) !== $idProg) {
                $mensaje = 'El docente debe estar registrado para la misma carrera (programa) que esta asignatura.';
            } elseif (docente_sede_efectiva($docAsig) !== programa_id_sede($idProg)) {
                $mensaje = 'La sede del docente no coincide con la carrera de la asignatura.';
            }
        }
        $editId = (int) post('id_materia', '0');
        if ($mensaje === '') {
            if ($editId > 0) {
                foreach ($materias as &$m) {
                    if ((int) ($m['id_materia'] ?? 0) === $editId) {
                        $m = array_merge($m, $row);
                        break;
                    }
                }
                unset($m);
                $mensaje = 'Asignatura actualizada.';
            } else {
                $row['id_materia'] = next_numeric_id($materias, 'id_materia');
                $materias[] = $row;
                $mensaje = 'Asignatura creada.';
            }
            save_data('materias', $materias);
        }
    }
}

$materias = load_data('materias');
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

$pageTitle = 'Asignaturas';
require PARTIALS_PATH . '/header.php';
require VIEWS_PATH . '/admin/materias.php';
require PARTIALS_PATH . '/footer.php';
