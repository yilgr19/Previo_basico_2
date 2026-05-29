<?php
declare(strict_types=1);

namespace App\Services;

final class GestionAcademicaService
{
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

    public static function actualizarEstudiantePropio(int $idSesion): array
    {
        if ($idSesion <= 0) {
            return ['Sesión inválida.', 'warning'];
        }
        $_POST['id_estudiante'] = (string) $idSesion;

        return self::agregarEstudiante();
    }
}
