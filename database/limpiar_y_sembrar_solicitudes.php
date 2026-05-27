<?php
declare(strict_types=1);

/**
 * Limpia TODAS las solicitudes (y tablas relacionadas) y crea 8 nuevas:
 * 4 para sede Cúcuta (1) y 4 para sede Ocaña (2).
 * No modifica estudiantes, docentes ni administradores.
 *
 * CLI: php database/limpiar_y_sembrar_solicitudes.php
 */
require_once dirname(__DIR__) . '/config/config.php';

use App\Core\Database;
use App\Services\SolicitudesAnexosUpload;

if (!defined('DB_ENABLED') || DB_ENABLED !== true) {
    fwrite(STDERR, "Error: DB_ENABLED debe ser true.\n");
    exit(1);
}

$pdo = Database::pdo();
$pdo->exec('SET NAMES utf8mb4');

echo "=== Limpieza y siembra de solicitudes ===\n\n";

// --- 1. Limpiar adjuntos en disco ---
$uploadsBase = ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'solicitudes';
if (is_dir($uploadsBase)) {
    $dirs = glob($uploadsBase . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [];
    foreach ($dirs as $dir) {
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') ?: [] as $f) {
            if (is_file($f)) {
                @unlink($f);
            }
        }
        @rmdir($dir);
    }
    echo 'Carpetas de adjuntos eliminadas: ' . count($dirs) . "\n";
}

// --- 2. Limpiar tablas de solicitudes (usuarios intactos) ---
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach ([
    'solicitud_anexos',
    'solicitud_detalle_estudiante',
    'solicitud_detalle_docente',
    'solicitud_respuesta_elaborada',
    'solicitudes',
] as $tabla) {
    $pdo->exec("DELETE FROM `$tabla`");
    $pdo->exec("ALTER TABLE `$tabla` AUTO_INCREMENT = 1");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
echo "Tablas de solicitudes vaciadas.\n\n";

// --- 3. Cargar usuarios existentes por sede ---
$estudiantes = load_data('estudiantes');
$docentes = load_data('docentes');

$estPorSede = [1 => [], 2 => []];
$docPorSede = [1 => [], 2 => []];

foreach ($estudiantes as $e) {
    $sede = estudiante_sede_efectiva($e);
    if (isset($estPorSede[$sede])) {
        $estPorSede[$sede][] = $e;
    }
}
foreach ($docentes as $d) {
    $sede = docente_sede_efectiva($d);
    if (isset($docPorSede[$sede])) {
        $docPorSede[$sede][] = $d;
    }
}

foreach ([1 => 'Cúcuta', 2 => 'Ocaña'] as $idSede => $nombre) {
    if (count($estPorSede[$idSede]) < 2 || count($docPorSede[$idSede]) < 2) {
        fwrite(STDERR, "Error: se necesitan al menos 2 estudiantes y 2 docentes en sede $nombre.\n");
        exit(1);
    }
}

/**
 * @param array<string, mixed> $est
 * @return array<string, mixed>
 */
function build_solicitud_estudiante(int $idSol, array $est, array $cfg): array
{
    $idEst = (int) $est['id_estudiante'];
    $idProg = (int) ($est['id_programa'] ?? 0);
    $idSede = (int) $cfg['id_sede_solicitud'];
    $estadoAcad = strtoupper(trim((string) ($est['estado_academico'] ?? 'REGULAR')));
    $tipo = tipo_solicitud_por_id((int) $cfg['id_tipo_solicitud']);
    $motivo = (string) $cfg['motivo'];
    $exposicion = (string) $cfg['exposicion'];

    return [
        'id_solicitud' => $idSol,
        'id_estudiante' => $idEst,
        'id_docente_solicitante' => 0,
        'documento_estudiante' => (string) ($est['documento'] ?? ''),
        'id_tipo_solicitud' => (int) $cfg['id_tipo_solicitud'],
        'id_tipo_solicitud_docente' => 0,
        'codigo_tipo' => (string) ($tipo['codigo'] ?? ''),
        'fecha_registro' => (string) $cfg['fecha_registro'],
        'estado' => (string) $cfg['estado'],
        'descripcion' => $exposicion,
        'documento_docente_relacionado' => '',
        'respuesta' => (string) ($cfg['respuesta'] ?? ''),
        'fecha_respuesta' => (string) ($cfg['fecha_respuesta'] ?? ''),
        'respondido_en' => (string) ($cfg['respondido_en'] ?? ''),
        'respuesta_elaborada' => $cfg['respuesta_elaborada'] ?? null,
        'anexos_archivos' => [],
        'detalle_estudiante' => [
            'perfil_snapshot' => [
                'id_estudiantil' => (string) ($est['documento'] ?? ''),
                'id_programa' => $idProg,
                'programa_nombre' => programa_label_by_id($idProg),
                'estado_academico' => $estadoAcad,
                'estado_academico_label' => estado_academico_estudiante_nombre($estadoAcad),
                'semestre' => (int) ($est['semestre'] ?? 0),
                'id_sede_matricula' => (int) ($est['id_sede'] ?? $idSede),
                'id_jornada_matricula' => (int) ($est['id_jornada'] ?? 1),
            ],
            'clasificacion' => [
                'periodo_academico' => '2026-1',
                'id_sede_solicitud' => $idSede,
                'id_jornada_solicitud' => (int) ($cfg['id_jornada_solicitud'] ?? 1),
            ],
            'cuerpo' => [
                'motivo' => $motivo,
                'motivo_label' => motivo_solicitud_estudiante_nombre($motivo),
                'exposicion' => $exposicion,
            ],
            'consentimientos' => ['veracidad' => true],
        ],
        'detalle_docente' => null,
        'formulario_version' => 2,
        'notif_pendiente_est' => (bool) ($cfg['notif_pendiente_est'] ?? false),
        'notif_pendiente_doc' => false,
        'notif_nueva_gestion' => (bool) ($cfg['notif_nueva_gestion'] ?? true),
    ];
}

/**
 * @param array<string, mixed> $doc
 * @return array<string, mixed>
 */
function build_solicitud_docente(int $idSol, array $doc, array $cfg): array
{
    $idDoc = (int) $doc['id_docente'];
    $tipo = tipo_solicitud_docente_por_id((int) $cfg['id_tipo_solicitud_docente']);
    $prioridad = (string) $cfg['prioridad'];
    $desc = (string) $cfg['descripcion_detallada'];
    $idEmp = trim((string) ($doc['codigo_empleado'] ?? ''));
    if ($idEmp === '') {
        $idEmp = (string) ($doc['documento'] ?? '');
    }

    return [
        'id_solicitud' => $idSol,
        'id_estudiante' => 0,
        'id_docente_solicitante' => $idDoc,
        'documento_estudiante' => '',
        'id_tipo_solicitud' => 0,
        'id_tipo_solicitud_docente' => (int) $cfg['id_tipo_solicitud_docente'],
        'codigo_tipo' => (string) ($tipo['codigo'] ?? ''),
        'fecha_registro' => (string) $cfg['fecha_registro'],
        'estado' => (string) $cfg['estado'],
        'descripcion' => $desc,
        'documento_docente_relacionado' => '',
        'respuesta' => (string) ($cfg['respuesta'] ?? ''),
        'fecha_respuesta' => (string) ($cfg['fecha_respuesta'] ?? ''),
        'respondido_en' => (string) ($cfg['respondido_en'] ?? ''),
        'respuesta_elaborada' => $cfg['respuesta_elaborada'] ?? null,
        'anexos_archivos' => [],
        'detalle_estudiante' => null,
        'detalle_docente' => [
            'perfil_snapshot' => [
                'id_empleado' => $idEmp,
                'unidad_academica' => trim((string) ($doc['unidad_academica'] ?? sede_nombre(docente_sede_efectiva($doc)))),
                'categoria_docente' => strtolower(trim((string) ($doc['categoria_docente'] ?? 'asociado'))),
                'categoria_docente_label' => categoria_docente_nombre((string) ($doc['categoria_docente'] ?? 'asociado')),
                'tipo_contrato' => strtolower(trim((string) ($doc['tipo_contrato'] ?? 'tiempo_completo'))),
                'tipo_contrato_label' => tipo_contrato_docente_nombre((string) ($doc['tipo_contrato'] ?? 'tiempo_completo')),
                'documento' => (string) ($doc['documento'] ?? ''),
                'nombre_completo' => trim(($doc['nombre'] ?? '') . ' ' . ($doc['apellido'] ?? '')),
            ],
            'clasificacion' => [
                'asunto' => (string) $cfg['asunto'],
                'prioridad' => $prioridad,
                'prioridad_label' => prioridad_solicitud_docente_nombre($prioridad),
            ],
            'carga_afectada' => [
                'nrc' => (string) ($cfg['nrc'] ?? ''),
                'nombre_materia' => (string) ($cfg['nombre_materia'] ?? ''),
                'horario_impactado' => (string) ($cfg['horario_impactado'] ?? ''),
                'plan_contingencia' => (string) ($cfg['plan_contingencia'] ?? ''),
            ],
            'cuerpo' => [
                'descripcion_detallada' => $desc,
                'sustento_legal' => (string) ($cfg['sustento_legal'] ?? ''),
                'fecha_inicio' => (string) $cfg['fecha_inicio'],
                'fecha_fin' => (string) $cfg['fecha_fin'],
            ],
            'consentimientos' => ['responsabilidad' => true],
        ],
        'formulario_version' => 2,
        'notif_pendiente_est' => false,
        'notif_pendiente_doc' => (bool) ($cfg['notif_pendiente_doc'] ?? false),
        'notif_nueva_gestion' => (bool) ($cfg['notif_nueva_gestion'] ?? true),
    ];
}

$solicitudes = [];
$id = 1;

// --- Cúcuta (4): 2 estudiantiles + 2 docentes ---
$e1 = $estPorSede[1][0];
$e2 = $estPorSede[1][1];
$d1 = $docPorSede[1][0];
$d2 = $docPorSede[1][1];

$solicitudes[] = build_solicitud_estudiante($id++, $e1, [
    'id_tipo_solicitud' => 11,
    'id_sede_solicitud' => 1,
    'motivo' => 'economicos',
    'exposicion' => 'Solicito constancia de estudio para trámite de beca municipal en Cúcuta.',
    'fecha_registro' => '2026-05-20 09:15:00',
    'estado' => 'pendiente',
    'notif_nueva_gestion' => true,
]);

$solicitudes[] = build_solicitud_estudiante($id++, $e2, [
    'id_tipo_solicitud' => 2,
    'id_sede_solicitud' => 1,
    'id_jornada_solicitud' => 1,
    'motivo' => 'cruce_horarios',
    'exposicion' => 'Petición de curso dirigido por cruce de horarios con práctica empresarial.',
    'fecha_registro' => '2026-05-21 10:30:00',
    'estado' => 'en_revision',
    'respuesta' => 'Su solicitud está en revisión por el comité de currículo.',
    'fecha_respuesta' => '2026-05-22',
    'respondido_en' => '2026-05-22 14:00:00',
    'notif_pendiente_est' => true,
    'notif_nueva_gestion' => false,
]);

$solicitudes[] = build_solicitud_docente($id++, $d1, [
    'id_tipo_solicitud_docente' => 3,
    'asunto' => 'Comisión de estudios — actualización curricular',
    'prioridad' => 'media',
    'nrc' => '20456',
    'nombre_materia' => 'Bases de datos aplicadas',
    'horario_impactado' => 'Martes y jueves 8:00–10:00',
    'plan_contingencia' => 'Sesión asíncrona en plataforma.',
    'descripcion_detallada' => 'Permiso para comisión de estudios de dos semanas en sede Cúcuta.',
    'sustento_legal' => 'Acuerdo interno de facultad 012/2025.',
    'fecha_inicio' => '2026-06-01',
    'fecha_fin' => '2026-06-15',
    'fecha_registro' => '2026-05-18 08:00:00',
    'estado' => 'rechazada',
    'fecha_respuesta' => '2026-05-19',
    'respondido_en' => '2026-05-19 11:20:00',
    'respuesta' => 'No procede por falta de plan de contingencia detallado.',
    'notif_pendiente_doc' => true,
    'notif_nueva_gestion' => false,
    'respuesta_elaborada' => [
        'numero_respuesta' => 'RES-2026-00003',
        'id_solicitud' => 3,
        'emitido_en' => '2026-05-19 11:20:00',
        'decision' => 'rechazado',
        'justificacion' => 'El plan de contingencia no cumple los requisitos mínimos del reglamento docente.',
        'normativas' => 'Art. 45 reglamento docente',
        'subsanacion_items' => '',
        'subsanacion_error_doc' => '',
        'subsanacion_fecha_limite' => '',
        'instrucciones_cierre' => '',
        'recursos_apelacion' => '',
        'funcionario_nombre' => 'Coordinación académica',
        'funcionario_cargo' => 'Registro y control',
        'codigo_verificacion' => '',
    ],
]);

$solicitudes[] = build_solicitud_docente($id++, $d2, [
    'id_tipo_solicitud_docente' => 14,
    'asunto' => 'Salida pedagógica al observatorio',
    'prioridad' => 'baja',
    'nrc' => '30120',
    'nombre_materia' => 'Física aplicada',
    'horario_impactado' => 'Viernes 14:00–17:00',
    'plan_contingencia' => 'Informe escrito sustituto para quienes no asistan.',
    'descripcion_detallada' => 'Autorización de salida pedagógica con grupo de Tecnología en Desarrollo de Software.',
    'sustento_legal' => '',
    'fecha_inicio' => '2026-06-10',
    'fecha_fin' => '2026-06-10',
    'fecha_registro' => '2026-05-23 16:45:00',
    'estado' => 'pendiente',
    'notif_nueva_gestion' => true,
]);

// --- Ocaña (4): 2 estudiantiles + 2 docentes ---
$e3 = $estPorSede[2][0];
$e4 = $estPorSede[2][1];
$d3 = $docPorSede[2][0];
$d4 = $docPorSede[2][1];

$solicitudes[] = build_solicitud_estudiante($id++, $e3, [
    'id_tipo_solicitud' => 11,
    'id_sede_solicitud' => 2,
    'motivo' => 'economicos',
    'exposicion' => 'Constancia de estudio para trámite de apoyo económico en extensión Ocaña.',
    'fecha_registro' => '2026-05-19 11:00:00',
    'estado' => 'aprobada',
    'respuesta' => 'Constancia disponible para retiro en registro.',
    'fecha_respuesta' => '2026-05-20',
    'respondido_en' => '2026-05-20 09:30:00',
    'notif_pendiente_est' => true,
    'notif_nueva_gestion' => false,
    'respuesta_elaborada' => [
        'numero_respuesta' => 'RES-2026-00005',
        'id_solicitud' => 5,
        'emitido_en' => '2026-05-20 09:30:00',
        'decision' => 'aprobado',
        'justificacion' => 'Cumple requisitos académicos y documentación completa.',
        'normativas' => 'Reglamento estudiantil art. 28',
        'subsanacion_items' => '',
        'subsanacion_error_doc' => '',
        'subsanacion_fecha_limite' => '',
        'instrucciones_cierre' => 'Retirar en ventanilla de registro Ocaña.',
        'recursos_apelacion' => '',
        'funcionario_nombre' => 'Secretaría académica Ocaña',
        'funcionario_cargo' => 'Registro y control',
        'codigo_verificacion' => '',
    ],
]);

$solicitudes[] = build_solicitud_estudiante($id++, $e4, [
    'id_tipo_solicitud' => 4,
    'id_sede_solicitud' => 2,
    'id_jornada_solicitud' => 2,
    'motivo' => 'salud',
    'exposicion' => 'Cambio de jornada por recomendación médica; adjunto soporte en ventanilla.',
    'fecha_registro' => '2026-05-22 08:20:00',
    'estado' => 'pendiente',
    'notif_nueva_gestion' => true,
]);

$solicitudes[] = build_solicitud_docente($id++, $d3, [
    'id_tipo_solicitud_docente' => 3,
    'asunto' => 'Comisión corta — diseño gráfico Ocaña',
    'prioridad' => 'media',
    'nrc' => '10550',
    'nombre_materia' => 'Tipografía digital',
    'horario_impactado' => 'Lunes 10:00–12:00',
    'plan_contingencia' => 'Tutoría remota el miércoles.',
    'descripcion_detallada' => 'Comisión de estudios para actualización de syllabus en extensión Ocaña.',
    'sustento_legal' => 'Acuerdo extensión Ocaña 003/2026.',
    'fecha_inicio' => '2026-06-05',
    'fecha_fin' => '2026-06-12',
    'fecha_registro' => '2026-05-17 07:30:00',
    'estado' => 'en_revision',
    'notif_nueva_gestion' => false,
]);

$solicitudes[] = build_solicitud_docente($id++, $d4, [
    'id_tipo_solicitud_docente' => 2,
    'asunto' => 'Licencia corta por capacitación',
    'prioridad' => 'alta',
    'nrc' => '10601',
    'nombre_materia' => 'Producción multimedia',
    'horario_impactado' => 'Miércoles 14:00–18:00',
    'plan_contingencia' => 'Docente de apoyo asignado por coordinación.',
    'descripcion_detallada' => 'Permiso remunerado de tres días para diplomado en gestión de contenidos.',
    'sustento_legal' => 'Art. 32 estatuto docente.',
    'fecha_inicio' => '2026-06-20',
    'fecha_fin' => '2026-06-22',
    'fecha_registro' => '2026-05-24 13:10:00',
    'estado' => 'pendiente',
    'notif_nueva_gestion' => true,
]);

// Corregir id_solicitud en respuesta_elaborada embebida
foreach ($solicitudes as &$s) {
    $sid = (int) $s['id_solicitud'];
    if (is_array($s['respuesta_elaborada'] ?? null)) {
        $s['respuesta_elaborada']['id_solicitud'] = $sid;
    }
}
unset($s);

if (!save_data('solicitudes', $solicitudes)) {
    fwrite(STDERR, "Error al guardar solicitudes en MySQL.\n");
    exit(1);
}

echo "Solicitudes insertadas: " . count($solicitudes) . "\n\n";

echo "--- Resumen por sede ---\n";
foreach ($solicitudes as $s) {
    $idSol = (int) $s['id_solicitud'];
    $sede = 1;
    if (is_array($s['detalle_estudiante'] ?? null)) {
        $sede = (int) (($s['detalle_estudiante']['clasificacion']['id_sede_solicitud'] ?? 1));
    } elseif ((int) ($s['id_docente_solicitante'] ?? 0) > 0) {
        $doc = repo_docente_por_id((int) $s['id_docente_solicitante']);
        $sede = $doc ? docente_sede_efectiva($doc) : 1;
    }
    $sedeNom = $sede === 2 ? 'Ocaña' : 'Cúcuta';
    $tipo = solicitud_es_radicada_docente($s) ? 'Docente' : 'Estudiante';
    echo sprintf(
        "#%d | %s | %s | %s | %s\n",
        $idSol,
        $sedeNom,
        $tipo,
        $s['estado'],
        solicitud_tipo_etiqueta($s)
    );
}

$n = (int) $pdo->query('SELECT COUNT(*) FROM solicitudes')->fetchColumn();
echo "\nTotal en BD: $n solicitudes (esperado: 8).\n";
echo "Usuarios conservados: " . count($estudiantes) . " estudiantes, " . count($docentes) . " docentes.\n";
echo "\nListo.\n";
