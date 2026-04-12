<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;

final class EstudiantesController extends Controller
{
    public function run(): void
    {
        require_role(\ROLE_ADMIN);

        $mensaje = '';
        $tipoMsg = 'success';
        $estudiantes = load_data('estudiantes');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = post('accion', '');
            if ($accion === 'eliminar') {
                $id = (int) post('id_estudiante', '0');
                $estudiantes = array_values(array_filter($estudiantes, static fn ($e) => (int) ($e['id_estudiante'] ?? 0) !== $id));
                save_data('estudiantes', $estudiantes);
                $mat = load_data('matriculas');
                $mat = array_values(array_filter($mat, static fn ($m) => (int) ($m['id_estudiante'] ?? 0) !== $id));
                save_data('matriculas', $mat);
                $mensaje = 'Estudiante eliminado.';
            } elseif ($accion === 'guardar') {
                $clavePost = (string) post('clave', '');
                $claveConf = (string) post('clave_confirmar', '');
                $editId = (int) post('id_estudiante', '0');

                $tiposPerm = array_column(diccionario_tipos_identificacion(), 'codigo');
                $tipoId = (string) post('tipo_identificacion', '');
                $sexosPerm = array_column(diccionario_sexo(), 'codigo');
                $sexo = (string) post('sexo', '');
                $idProg = (int) post('id_programa', '0');
                $sem = (int) post('semestre', '1');
                $fn = trim((string) post('fecha_nacimiento', ''));

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
                    $mensaje = $err;
                    $tipoMsg = 'warning';
                } else {
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
                    if ($editId > 0) {
                        foreach ($estudiantes as &$e) {
                            if ((int) ($e['id_estudiante'] ?? 0) === $editId) {
                                if ($clavePost !== '') {
                                    $e['clave'] = $clavePost;
                                }
                                $e = array_merge($e, $row);
                                break;
                            }
                        }
                        unset($e);
                        $mensaje = 'Estudiante actualizado.';
                    } else {
                        $row['id_estudiante'] = next_numeric_id($estudiantes, 'id_estudiante');
                        $row['clave'] = $clavePost;
                        $estudiantes[] = $row;
                        $mensaje = 'Estudiante registrado correctamente.';
                    }
                    save_data('estudiantes', $estudiantes);
                    $tipoMsg = 'success';
                }
            }
        }

        $estudiantes = load_data('estudiantes');
        $editar = null;
        $eid = (int) (get('editar') ?? '0');
        if ($eid > 0) {
            $editar = repo_estudiante_por_id($eid);
        }

        $this->render('admin/estudiantes.php', [
            'pageTitle' => 'Registrar estudiante',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'estudiantes' => $estudiantes,
            'editar' => $editar,
        ]);
    }
}
