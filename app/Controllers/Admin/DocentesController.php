<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;

final class DocentesController extends Controller
{
    public function run(): void
    {
        require_role(\ROLE_ADMIN);

        $mensaje = '';
        $docentes = load_data('docentes');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = post('accion', '');
            if ($accion === 'eliminar') {
                $id = (int) post('id_docente', '0');
                $refs = load_data('materias');
                foreach ($refs as $m) {
                    if ((int) ($m['id_docente'] ?? 0) === $id) {
                        $mensaje = 'No se puede eliminar: hay asignaturas asignadas a este docente.';
                        break;
                    }
                }
                if ($mensaje === '') {
                    $docentes = array_values(array_filter($docentes, static fn ($d) => (int) ($d['id_docente'] ?? 0) !== $id));
                    save_data('docentes', $docentes);
                    $mensaje = 'Docente eliminado.';
                }
            } elseif ($accion === 'guardar') {
                $idSede = (int) post('id_sede', '0');
                $idProg = (int) post('id_programa', '0');
                $idsProgValidos = array_column(diccionario_programas(), 'id');
                if (!in_array($idSede, [1, 2], true)) {
                    $mensaje = 'Seleccione la sede (Cúcuta u Ocaña).';
                } elseif (!in_array($idProg, $idsProgValidos, true)) {
                    $mensaje = 'Seleccione la carrera a la que dicta clase.';
                } elseif (programa_id_sede($idProg) !== $idSede) {
                    $mensaje = 'La carrera seleccionada no corresponde a la sede indicada.';
                } else {
                    $row = [
                        'nombre' => post('nombre', ''),
                        'apellido' => post('apellido', ''),
                        'documento' => post('documento', ''),
                        'correo' => post('correo', ''),
                        'telefono' => post('telefono', ''),
                        'id_sede' => $idSede,
                        'id_programa' => $idProg,
                        'programa' => programa_label_by_id($idProg),
                    ];
                    $clavePost = post('clave', '');
                    $editId = (int) post('id_docente', '0');
                    if ($editId > 0) {
                        foreach ($docentes as &$d) {
                            if ((int) ($d['id_docente'] ?? 0) === $editId) {
                                if ($clavePost !== '') {
                                    $d['clave'] = $clavePost;
                                }
                                $d = array_merge($d, $row);
                                break;
                            }
                        }
                        unset($d);
                        $mensaje = 'Docente actualizado.';
                    } else {
                        $row['id_docente'] = next_numeric_id($docentes, 'id_docente');
                        $row['clave'] = $clavePost !== '' ? $clavePost : 'doc123';
                        $docentes[] = $row;
                        $mensaje = 'Docente registrado.' . ($clavePost === '' ? ' Contraseña por defecto: doc123.' : '');
                    }
                    save_data('docentes', $docentes);
                }
            }
        }

        $docentes = load_data('docentes');
        $editar = null;
        $eid = (int) (get('editar') ?? '0');
        if ($eid > 0) {
            $editar = repo_docente_por_id($eid);
        }

        $this->render('admin/docentes.php', [
            'pageTitle' => 'Registro de docentes',
            'mensaje' => $mensaje,
            'docentes' => $docentes,
            'editar' => $editar,
        ]);
    }
}
