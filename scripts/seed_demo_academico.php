<?php
/**
 * Genera datos de demostración:
 * - 5 estudiantes y 5 docentes en data/
 * - 3 solicitudes radicadas por estudiantes (mencionan documento de un profesor distinto en cada caso)
 * - 1 solicitud radicada por un docente
 *
 * Uso (desde la raíz del proyecto):
 *   php scripts/seed_demo_academico.php
 *
 * ADVERTENCIA: sobrescribe data/estudiantes.json, data/docentes.json y data/solicitudes.json.
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root . '/config/config.php';

$progTds = programa_label_by_id(124);
$progDg = programa_label_by_id(121);

$docentes = [
    [
        'id_docente' => 1,
        'nombre' => 'Juliana Daniela',
        'apellido' => 'Orozco',
        'documento' => '111111',
        'correo' => 'j.orozco@universidad.edu.co',
        'telefono' => '3001112233',
        'id_sede' => 1,
        'id_programa' => 121,
        'programa' => $progDg,
        'codigo_empleado' => 'EMP-5001',
        'unidad_academica' => 'Facultad de Ingeniería — Departamento de Sistemas',
        'categoria_docente' => 'asociado',
        'tipo_contrato' => 'tiempo_completo',
        'clave' => 'demo123',
    ],
    [
        'id_docente' => 2,
        'nombre' => 'Carlos Andrés',
        'apellido' => 'Méndez',
        'documento' => '201002',
        'correo' => 'c.mendez@universidad.edu.co',
        'telefono' => '3002223344',
        'id_sede' => 1,
        'id_programa' => 124,
        'programa' => $progTds,
        'codigo_empleado' => 'EMP-5002',
        'unidad_academica' => 'Facultad de Ingeniería — Departamento de Sistemas',
        'categoria_docente' => 'titular',
        'tipo_contrato' => 'tiempo_completo',
        'clave' => 'demo123',
    ],
    [
        'id_docente' => 3,
        'nombre' => 'Laura Patricia',
        'apellido' => 'Ruiz',
        'documento' => '201003',
        'correo' => 'l.ruiz@universidad.edu.co',
        'telefono' => '3003334455',
        'id_sede' => 1,
        'id_programa' => 121,
        'programa' => $progDg,
        'codigo_empleado' => 'EMP-5003',
        'unidad_academica' => 'Facultad de Diseño — Área Gráfica',
        'categoria_docente' => 'asistente',
        'tipo_contrato' => 'medio_tiempo',
        'clave' => 'demo123',
    ],
    [
        'id_docente' => 4,
        'nombre' => 'Sergio Iván',
        'apellido' => 'Delgado',
        'documento' => '201004',
        'correo' => 's.delgado@universidad.edu.co',
        'telefono' => '3004445566',
        'id_sede' => 1,
        'id_programa' => 124,
        'programa' => $progTds,
        'codigo_empleado' => 'EMP-5004',
        'unidad_academica' => 'Facultad de Ingeniería — Laboratorios',
        'categoria_docente' => 'asociado',
        'tipo_contrato' => 'catedra',
        'clave' => 'demo123',
    ],
    [
        'id_docente' => 5,
        'nombre' => 'Ana María',
        'apellido' => 'Vásquez',
        'documento' => '201005',
        'correo' => 'a.vasquez@universidad.edu.co',
        'telefono' => '3005556677',
        'id_sede' => 1,
        'id_programa' => 121,
        'programa' => $progDg,
        'codigo_empleado' => 'EMP-5005',
        'unidad_academica' => 'Facultad de Diseño — Taller',
        'categoria_docente' => 'asociado',
        'tipo_contrato' => 'tiempo_completo',
        'clave' => 'demo123',
    ],
];

$estudiantes = [
    [
        'id_estudiante' => 1,
        'tipo_identificacion' => 'CC',
        'documento' => '301001',
        'nombre' => 'Andrea',
        'apellido' => 'López',
        'correo' => 'a.lopez@estudiante.edu.co',
        'sexo' => 'F',
        'id_programa' => 124,
        'programa' => $progTds,
        'estado_academico' => 'REGULAR',
        'semestre' => 4,
        'fecha_nacimiento' => '2003-04-12',
        'edad' => 22,
        'direccion' => 'Calle 15 #8-40',
        'barrio' => 'La Playa',
        'telefono' => '3101002001',
        'id_sede' => 1,
        'id_jornada' => 1,
        'clave' => 'demo123',
    ],
    [
        'id_estudiante' => 2,
        'tipo_identificacion' => 'CC',
        'documento' => '301002',
        'nombre' => 'Brayan',
        'apellido' => 'Castro',
        'correo' => 'b.castro@estudiante.edu.co',
        'sexo' => 'M',
        'id_programa' => 124,
        'programa' => $progTds,
        'estado_academico' => 'REGULAR',
        'semestre' => 3,
        'fecha_nacimiento' => '2004-11-20',
        'edad' => 21,
        'direccion' => 'Av. Libertadores 102',
        'barrio' => 'Centro',
        'telefono' => '3101002002',
        'id_sede' => 1,
        'id_jornada' => 1,
        'clave' => 'demo123',
    ],
    [
        'id_estudiante' => 3,
        'tipo_identificacion' => 'CC',
        'documento' => '301003',
        'nombre' => 'Daniela',
        'apellido' => 'Torres',
        'correo' => 'd.torres@estudiante.edu.co',
        'sexo' => 'F',
        'id_programa' => 121,
        'programa' => $progDg,
        'estado_academico' => 'REGULAR',
        'semestre' => 2,
        'fecha_nacimiento' => '2005-02-01',
        'edad' => 20,
        'direccion' => 'Carrera 18 #12-55',
        'barrio' => 'La Esperanza',
        'telefono' => '3101002003',
        'id_sede' => 1,
        'id_jornada' => 2,
        'clave' => 'demo123',
    ],
    [
        'id_estudiante' => 4,
        'tipo_identificacion' => 'CC',
        'documento' => '301004',
        'nombre' => 'Felipe',
        'apellido' => 'Núñez',
        'correo' => 'f.nunez@estudiante.edu.co',
        'sexo' => 'M',
        'id_programa' => 124,
        'programa' => $progTds,
        'estado_academico' => 'REGULAR',
        'semestre' => 1,
        'fecha_nacimiento' => '2006-08-30',
        'edad' => 19,
        'direccion' => 'Mz 4 Casa 12',
        'barrio' => 'Los Pinos',
        'telefono' => '3101002004',
        'id_sede' => 1,
        'id_jornada' => 1,
        'clave' => 'demo123',
    ],
    [
        'id_estudiante' => 5,
        'tipo_identificacion' => 'CC',
        'documento' => '301005',
        'nombre' => 'Gabriela',
        'apellido' => 'Soto',
        'correo' => 'g.soto@estudiante.edu.co',
        'sexo' => 'F',
        'id_programa' => 121,
        'programa' => $progDg,
        'estado_academico' => 'REGULAR',
        'semestre' => 5,
        'fecha_nacimiento' => '2002-12-05',
        'edad' => 23,
        'direccion' => 'Vereda El Carmen',
        'barrio' => 'Rural',
        'telefono' => '3101002005',
        'id_sede' => 1,
        'id_jornada' => 1,
        'clave' => 'demo123',
    ],
];

function detalle_estudiante_demo(array $est, string $periodo, int $idSedeSol, int $idJornadaSol, string $motivo, string $exposicion): array
{
    $idProg = (int) ($est['id_programa'] ?? 0);
    $estadoAcad = strtoupper(trim((string) ($est['estado_academico'] ?? 'REGULAR')));

    return [
        'perfil_snapshot' => [
            'id_estudiantil' => (string) ($est['documento'] ?? ''),
            'id_programa' => $idProg,
            'programa_nombre' => programa_label_by_id($idProg),
            'estado_academico' => $estadoAcad,
            'estado_academico_label' => estado_academico_estudiante_nombre($estadoAcad),
            'semestre' => (int) ($est['semestre'] ?? 0),
            'id_sede_matricula' => (int) ($est['id_sede'] ?? 0),
            'id_jornada_matricula' => (int) ($est['id_jornada'] ?? 0),
        ],
        'clasificacion' => [
            'periodo_academico' => $periodo,
            'id_sede_solicitud' => $idSedeSol,
            'id_jornada_solicitud' => $idJornadaSol,
        ],
        'cuerpo' => [
            'motivo' => $motivo,
            'motivo_label' => motivo_solicitud_estudiante_nombre($motivo),
            'exposicion' => $exposicion,
        ],
        'consentimientos' => [
            'veracidad' => true,
        ],
    ];
}

function detalle_docente_demo(array $doc): array
{
    $idEmp = trim((string) ($doc['codigo_empleado'] ?? ''));
    if ($idEmp === '') {
        $idEmp = (string) ($doc['documento'] ?? '');
    }

    return [
        'perfil_snapshot' => [
            'id_empleado' => $idEmp,
            'unidad_academica' => trim((string) ($doc['unidad_academica'] ?? '')),
            'categoria_docente' => strtolower(trim((string) ($doc['categoria_docente'] ?? ''))),
            'categoria_docente_label' => categoria_docente_nombre((string) ($doc['categoria_docente'] ?? '')),
            'tipo_contrato' => strtolower(trim((string) ($doc['tipo_contrato'] ?? ''))),
            'tipo_contrato_label' => tipo_contrato_docente_nombre((string) ($doc['tipo_contrato'] ?? '')),
            'documento' => (string) ($doc['documento'] ?? ''),
            'nombre_completo' => trim(($doc['nombre'] ?? '') . ' ' . ($doc['apellido'] ?? '')),
        ],
        'clasificacion' => [
            'asunto' => 'Comisión corta para actualización de syllabus',
            'prioridad' => 'media',
            'prioridad_label' => prioridad_solicitud_docente_nombre('media'),
        ],
        'carga_afectada' => [
            'nrc' => '20456',
            'nombre_materia' => 'Bases de datos aplicadas',
            'horario_impactado' => 'Martes y jueves 8:00–10:00',
            'plan_contingencia' => 'Sesión asíncrona en plataforma y tutoría el viernes.',
        ],
        'cuerpo' => [
            'descripcion_detallada' => 'Solicito permiso para participar en comisión de estudios durante dos semanas. Coordinación con el docente Sergio Iván Delgado para cubrir laboratorio.',
            'sustento_legal' => 'Acuerdo interno de facultad 012/2025.',
            'fecha_inicio' => '2026-05-05',
            'fecha_fin' => '2026-05-19',
        ],
        'consentimientos' => [
            'responsabilidad' => true,
        ],
    ];
}

// Índices por id para armar solicitudes
$e = [];
foreach ($estudiantes as $row) {
    $e[(int) $row['id_estudiante']] = $row;
}
$d = [];
foreach ($docentes as $row) {
    $d[(int) $row['id_docente']] = $row;
}

$solicitudes = [
    [
        'id_solicitud' => 1,
        'id_estudiante' => 1,
        'id_docente_solicitante' => 0,
        'documento_estudiante' => $e[1]['documento'],
        'id_tipo_solicitud' => 11,
        'id_tipo_solicitud_docente' => 0,
        'codigo_tipo' => 'REQ_CONST_EST',
        'fecha_registro' => '2026-04-10',
        'estado' => 'pendiente',
        'descripcion' => 'Constancia para beca; el profesor Carlos Andrés Méndez puede confirmar asistencia al módulo práctico.',
        'documento_docente_relacionado' => '201002',
        'respuesta' => '',
        'fecha_respuesta' => '',
        'respondido_en' => '',
        'anexos_archivos' => [],
        'detalle_estudiante' => detalle_estudiante_demo(
            $e[1],
            '2026-1',
            1,
            1,
            'economicos',
            'Requiero constancia de estudio para trámite de beca municipal. Indico como referencia académica al docente Carlos Andrés Méndez (documento 201002), quien orientó el proyecto integrador del periodo.'
        ),
        'detalle_docente' => null,
        'formulario_version' => 2,
        'notif_pendiente_est' => false,
        'notif_pendiente_doc' => false,
    ],
    [
        'id_solicitud' => 2,
        'id_estudiante' => 2,
        'id_docente_solicitante' => 0,
        'documento_estudiante' => $e[2]['documento'],
        'id_tipo_solicitud' => 12,
        'id_tipo_solicitud_docente' => 0,
        'codigo_tipo' => 'REQ_CERT_NOTAS',
        'fecha_registro' => '2026-04-08',
        'estado' => 'pendiente',
        'descripcion' => 'Certificado de notas; la profesora Laura Patricia Ruiz dictó la asignatura referida.',
        'documento_docente_relacionado' => '201003',
        'respuesta' => '',
        'fecha_respuesta' => '',
        'respondido_en' => '',
        'anexos_archivos' => [],
        'detalle_estudiante' => detalle_estudiante_demo(
            $e[2],
            '2026-1',
            1,
            1,
            'cruce_horarios',
            'Solicito certificado de notas del periodo anterior. La docente Laura Patricia Ruiz (documento 201003) puede validar la información de la asignatura de interfaz gráfica.'
        ),
        'detalle_docente' => null,
        'formulario_version' => 2,
        'notif_pendiente_est' => false,
        'notif_pendiente_doc' => false,
    ],
    [
        'id_solicitud' => 3,
        'id_estudiante' => 3,
        'id_docente_solicitante' => 0,
        'documento_estudiante' => $e[3]['documento'],
        'id_tipo_solicitud' => 13,
        'id_tipo_solicitud_docente' => 0,
        'codigo_tipo' => 'REQ_OTRA',
        'fecha_registro' => '2026-04-05',
        'estado' => 'pendiente',
        'descripcion' => 'Gestión varios; mención a Ana María Vásquez como tutora de taller.',
        'documento_docente_relacionado' => '201005',
        'respuesta' => '',
        'fecha_respuesta' => '',
        'respondido_en' => '',
        'anexos_archivos' => [],
        'detalle_estudiante' => detalle_estudiante_demo(
            $e[3],
            '2026-1',
            1,
            2,
            'otro',
            'Requiero gestión administrativa relacionada con taller de diseño. Como referencia indico a la profesora Ana María Vásquez (documento 201005), tutora del espacio práctico.'
        ),
        'detalle_docente' => null,
        'formulario_version' => 2,
        'notif_pendiente_est' => false,
        'notif_pendiente_doc' => false,
    ],
    [
        'id_solicitud' => 4,
        'id_estudiante' => 0,
        'id_docente_solicitante' => 1,
        'documento_estudiante' => '',
        'id_tipo_solicitud' => 0,
        'id_tipo_solicitud_docente' => 3,
        'codigo_tipo' => 'DOC_COM_EST',
        'fecha_registro' => '2026-04-14',
        'estado' => 'pendiente',
        'descripcion' => 'Comisión de estudios; coordinación mencionada con docente Sergio Iván Delgado.',
        'documento_docente_relacionado' => '201004',
        'respuesta' => '',
        'fecha_respuesta' => '',
        'respondido_en' => '',
        'anexos_archivos' => [],
        'detalle_estudiante' => null,
        'detalle_docente' => detalle_docente_demo($d[1]),
        'formulario_version' => 2,
        'notif_pendiente_est' => false,
        'notif_pendiente_doc' => false,
    ],
];

$okE = save_data('estudiantes', $estudiantes);
$okD = save_data('docentes', $docentes);
$okS = save_data('solicitudes', $solicitudes);

if (!$okE || !$okD || !$okS) {
    fwrite(STDERR, "Error al guardar uno o más archivos JSON.\n");
    exit(1);
}

echo "Listo.\n";
echo "- Estudiantes: " . count($estudiantes) . " (IDs 1–5). Clave de acceso demo: demo123\n";
echo "- Docentes: " . count($docentes) . " (IDs 1–5). Clave de acceso demo: demo123\n";
echo "- Solicitudes: " . count($solicitudes) . " (#1–3 estudiantes, mencionan doc. 201002, 201003 y 201005; #4 docente Juliana Orozco).\n";
echo "  Inicio de sesión: documento numérico + clave demo123\n";
