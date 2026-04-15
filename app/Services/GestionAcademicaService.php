<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Lógica de alta/edición de registros académicos (estudiantes, docentes, asignaturas).
 */
final class GestionAcademicaService
{
    /** @return array{0: string, 1: string} */
    public static function agregarEstudiante(): array
    {
        $tiposPerm = array_column(diccionario_tipos_identificacion(), 'codigo');
        $tipoId = (string) post('tipo_identificacion', '');
        $sexo = (string) post('sexo', '');
        $sexosPerm = array_column(diccionario_sexo(), 'codigo');
        $idProg = (int) post('id_programa', '0');
        $sem = (int) post('semestre', '1');
        $fn = trim((string) post('fecha_nacimiento', ''));
        $clavePost = (string) post('clave', '');
        $claveConf = (string) post('clave_confirmar', '');
        $editId = (int) post('id_estudiante', '0');

        $err = '';
        if (!in_array($tipoId, $tiposPerm, true)) {
            $err = 'Seleccione el tipo de identificación.';
        } elseif (trim((string) post('documento', '')) === '') {
            $err = 'Ingrese el número de identificación.';
        } elseif (trim((string) post('nombre', '')) === '' || trim((string) post('apellido', '')) === '') {
            $err = 'Ingrese nombres y apellidos.';
        } elseif (trim((string) post('correo', '')) === '') {
            $err = 'Ingrese el correo electrónico.';
        } elseif (!in_array($sexo, $sexosPerm, true)) {
            $err = 'Seleccione el sexo.';
        } elseif ($idProg <= 0) {
            $err = 'Seleccione la carrera.';
        } elseif ($sem < 1 || $sem > 10) {
            $err = 'Seleccione un semestre entre 1 y 10.';
        } elseif ($fn === '') {
            $err = 'Indique la fecha de nacimiento.';
        } elseif (trim((string) post('direccion', '')) === '') {
            $err = 'Ingrese la dirección.';
        } elseif (trim((string) post('barrio', '')) === '') {
            $err = 'Ingrese el barrio.';
        } elseif (trim((string) post('telefono', '')) === '') {
            $err = 'Ingrese el teléfono.';
        } elseif ($editId <= 0) {
            if ($clavePost === '' || $clavePost !== $claveConf) {
                $err = 'Ingrese la contraseña y confírmela correctamente.';
            }
        } elseif ($clavePost !== '' && $clavePost !== $claveConf) {
            $err = 'Las contraseñas no coinciden.';
        }

        if ($err !== '') {
            return [$err, 'warning'];
        }

        $edad = calcular_edad_desde_fecha_ymd($fn);
        $row = [
            'tipo_identificacion' => $tipoId,
            'documento' => trim((string) post('documento', '')),
            'nombre' => trim((string) post('nombre', '')),
            'apellido' => trim((string) post('apellido', '')),
            'correo' => trim((string) post('correo', '')),
            'sexo' => $sexo,
            'id_programa' => $idProg,
            'programa' => programa_label_by_id($idProg),
            'semestre' => $sem,
            'fecha_nacimiento' => $fn,
            'edad' => $edad,
            'direccion' => trim((string) post('direccion', '')),
            'barrio' => trim((string) post('barrio', '')),
            'telefono' => trim((string) post('telefono', '')),
            'id_sede' => (int) post('id_sede', '1'),
            'id_jornada' => (int) post('id_jornada', '1'),
        ];

        $estudiantes = load_data('estudiantes');

        if ($editId > 0) {
            $found = false;
            foreach ($estudiantes as &$e) {
                if ((int) ($e['id_estudiante'] ?? 0) === $editId) {
                    if ($clavePost !== '') {
                        $e['clave'] = $clavePost;
                    }
                    $e = array_merge($e, $row);
                    $found = true;
                    break;
                }
            }
            unset($e);
            if (!$found) {
                return ['Estudiante no encontrado.', 'warning'];
            }
            save_data('estudiantes', $estudiantes);
            return ['Estudiante actualizado.', 'success'];
        }

        foreach ($estudiantes as $e) {
            if ((string) ($e['documento'] ?? '') === $row['documento']) {
                return ['Ya existe un estudiante con ese documento.', 'warning'];
            }
        }
        $row['id_estudiante'] = next_numeric_id($estudiantes, 'id_estudiante');
        $row['clave'] = $clavePost;
        $estudiantes[] = $row;
        save_data('estudiantes', $estudiantes);

        return ['Estudiante registrado correctamente.', 'success'];
    }

    /** @return array{0: string, 1: string} */
    public static function agregarDocente(): array
    {
        $idSede = (int) post('id_sede', '0');
        $idProg = (int) post('id_programa', '0');
        $idsProgValidos = array_column(diccionario_programas(), 'id');
        if (!in_array($idSede, [1, 2], true)) {
            return ['Seleccione la sede (Cúcuta u Ocaña).', 'warning'];
        }
        if (!in_array($idProg, $idsProgValidos, true)) {
            return ['Seleccione la carrera a la que dicta clase.', 'warning'];
        }
        if (programa_id_sede($idProg) !== $idSede) {
            return ['La carrera seleccionada no corresponde a la sede indicada.', 'warning'];
        }
        $documento = trim((string) post('documento', ''));
        if ($documento === '') {
            return ['Ingrese el documento.', 'warning'];
        }
        $editId = (int) post('id_docente', '0');
        $docentes = load_data('docentes');
        foreach ($docentes as $d) {
            if ((string) ($d['documento'] ?? '') === $documento && (int) ($d['id_docente'] ?? 0) !== $editId) {
                return ['Ya existe otro docente con ese documento.', 'warning'];
            }
        }
        $clavePost = post('clave', '');
        $row = [
            'nombre' => post('nombre', ''),
            'apellido' => post('apellido', ''),
            'documento' => $documento,
            'correo' => post('correo', ''),
            'telefono' => post('telefono', ''),
            'id_sede' => $idSede,
            'id_programa' => $idProg,
            'programa' => programa_label_by_id($idProg),
        ];
        if ($editId > 0) {
            foreach ($docentes as &$d) {
                if ((int) ($d['id_docente'] ?? 0) === $editId) {
                    if ($clavePost !== '') {
                        $d['clave'] = $clavePost;
                    }
                    $d = array_merge($d, $row);
                    save_data('docentes', $docentes);
                    return ['Docente actualizado.', 'success'];
                }
            }
            unset($d);
            return ['Docente no encontrado.', 'warning'];
        }
        $row['id_docente'] = next_numeric_id($docentes, 'id_docente');
        $row['clave'] = $clavePost !== '' ? $clavePost : 'doc123';
        $docentes[] = $row;
        save_data('docentes', $docentes);
        return ['Docente registrado.' . ($clavePost === '' ? ' Contraseña por defecto: doc123.' : ''), 'success'];
    }

    /** @return array{0: string, 1: string} */
    public static function agregarMateria(): array
    {
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
            return ['Seleccione la carrera o programa al que pertenece la asignatura.', 'warning'];
        }
        if ($diaClase === '') {
            return ['Seleccione el día de clase.', 'warning'];
        }
        if ($modalidad === '') {
            return ['Seleccione la modalidad de la clase (virtual o presencial).', 'warning'];
        }
        if ($idDoc <= 0) {
            return ['Busque al docente por número de documento y valide antes de guardar.', 'warning'];
        }
        if ($horaIni === '' || $horaFin === '') {
            return ['Indique hora de inicio y hora de fin de la clase.', 'warning'];
        }
        if ($modalidad === 'presencial' && $salon === '') {
            return ['En modalidad presencial debe indicar el salón o aula.', 'warning'];
        }
        $docAsig = repo_docente_por_id($idDoc);
        if (!$docAsig) {
            return ['Docente no encontrado. Busque de nuevo por documento.', 'warning'];
        }
        if ((int) ($docAsig['id_programa'] ?? 0) !== $idProg) {
            return ['El docente debe estar registrado para la misma carrera (programa) que esta asignatura.', 'warning'];
        }
        if (docente_sede_efectiva($docAsig) !== programa_id_sede($idProg)) {
            return ['La sede del docente no coincide con la carrera de la asignatura.', 'warning'];
        }
        $editId = (int) post('id_materia', '0');
        $materias = load_data('materias');
        $mensaje = '';
        if ($editId > 0) {
            foreach ($materias as &$m) {
                if ((int) ($m['id_materia'] ?? 0) === $editId) {
                    $m = array_merge($m, $row);
                    $mensaje = 'Asignatura actualizada.';
                    break;
                }
            }
            unset($m);
            if ($mensaje === '') {
                return ['Asignatura no encontrada.', 'warning'];
            }
        } else {
            $row['id_materia'] = next_numeric_id($materias, 'id_materia');
            $materias[] = $row;
            $mensaje = 'Asignatura creada.';
        }
        save_data('materias', $materias);
        return [$mensaje, 'success'];
    }

    /**
     * Actualización de datos por el mismo estudiante (perfil).
     *
     * @return array{0: string, 1: string}
     */
    public static function actualizarEstudiantePropio(int $idSesion): array
    {
        if ($idSesion <= 0) {
            return ['Sesión inválida.', 'warning'];
        }
        $_POST['id_estudiante'] = (string) $idSesion;

        return self::agregarEstudiante();
    }
}
