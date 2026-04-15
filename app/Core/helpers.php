<?php
declare(strict_types=1);

function h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_base_path(): string
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $doc = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $docReal = $doc !== '' ? realpath($doc) : false;
    $rootReal = realpath(ROOT_PATH);
    if ($docReal !== false && $rootReal !== false) {
        $docReal = str_replace('\\', '/', $docReal);
        $rootReal = str_replace('\\', '/', $rootReal);
        if (str_starts_with($rootReal, $docReal)) {
            $rel = trim(substr($rootReal, strlen($docReal)), '/');
            $cached = $rel === '' ? '' : '/' . $rel;
            return $cached;
        }
    }
    $cached = '';
    return $cached;
}

function url(string $path): string
{
    $path = ltrim($path, '/');
    $base = app_base_path();
    if ($base === '') {
        return '/' . $path;
    }
    return $base . '/' . $path;
}

function redirect(string $url): void
{
    if ($url !== '' && ($url[0] ?? '') === '/') {
        $base = app_base_path();
        $alreadyPrefixed = $base !== ''
            && (str_starts_with($url, $base . '/') || $url === $base);
        if ($base === '' || !$alreadyPrefixed) {
            $url = url(ltrim($url, '/'));
        }
    }
    header('Location: ' . $url);
    exit;
}

function calcular_edad_desde_fecha_ymd(string $fechaYmd): ?int
{
    $fechaYmd = trim($fechaYmd);
    if ($fechaYmd === '') {
        return null;
    }
    try {
        $dob = new DateTimeImmutable($fechaYmd);
        $now = new DateTimeImmutable('today');
        return $dob->diff($now)->y;
    } catch (Throwable $e) {
        return null;
    }
}

function post(string $key, ?string $default = null): ?string
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
}

function get(string $key, ?string $default = null): ?string
{
    return isset($_GET[$key]) ? trim((string) $_GET[$key]) : $default;
}

function solicitud_estudiante_old_desde_post(): array
{
    return [
        'id_tipo_solicitud' => post('id_tipo_solicitud', '') ?? '',
        'periodo_academico' => post('periodo_academico', '') ?? '',
        'id_sede_solicitud' => post('id_sede_solicitud', '') ?? '',
        'id_jornada_solicitud' => post('id_jornada_solicitud', '') ?? '',
        'motivo_solicitud' => post('motivo_solicitud', '') ?? '',
        'exposicion' => (string) ($_POST['exposicion'] ?? ''),
        'documento_docente_relacionado' => post('documento_docente_relacionado', '') ?? '',
        'consentimiento_veracidad' => (isset($_POST['consentimiento_veracidad']) && (string) $_POST['consentimiento_veracidad'] === '1'),
    ];
}

function solicitud_docente_old_desde_post(): array
{
    return [
        'id_tipo_solicitud_docente' => post('id_tipo_solicitud_docente', '') ?? '',
        'asunto' => (string) ($_POST['asunto'] ?? ''),
        'prioridad' => post('prioridad', '') ?? '',
        'nrc' => (string) ($_POST['nrc'] ?? ''),
        'nombre_materia' => (string) ($_POST['nombre_materia'] ?? ''),
        'horario_impactado' => (string) ($_POST['horario_impactado'] ?? ''),
        'plan_contingencia' => (string) ($_POST['plan_contingencia'] ?? ''),
        'descripcion_detallada' => (string) ($_POST['descripcion_detallada'] ?? ''),
        'sustento_legal' => (string) ($_POST['sustento_legal'] ?? ''),
        'fecha_inicio' => post('fecha_inicio', '') ?? '',
        'fecha_fin' => post('fecha_fin', '') ?? '',
        'documento_docente_relacionado' => post('documento_docente_relacionado', '') ?? '',
        'consentimiento_responsabilidad' => (isset($_POST['consentimiento_responsabilidad']) && (string) $_POST['consentimiento_responsabilidad'] === '1'),
    ];
}

function gestion_formulario_repoblar_desde_post(): ?array
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || post('accion', '') !== 'cambiar_estado') {
        return null;
    }
    $id = (int) post('id_solicitud', '0');
    if ($id <= 0) {
        return null;
    }

    return [
        'id_solicitud' => $id,
        'estado' => (string) post('estado', ''),
        'respuesta' => (string) ($_POST['respuesta'] ?? ''),
        'incluir_elaborada' => post('incluir_elaborada', '') === '1',
        'elab' => [
            'decision' => trim((string) post('elab_decision', '')),
            'justificacion' => trim((string) ($_POST['elab_justificacion'] ?? '')),
            'normativas' => trim((string) ($_POST['elab_normativas'] ?? '')),
            'subsanacion_items' => trim((string) ($_POST['elab_subsanacion_items'] ?? '')),
            'subsanacion_error_doc' => trim((string) ($_POST['elab_subsanacion_error_doc'] ?? '')),
            'subsanacion_fecha_limite' => trim((string) post('elab_subsanacion_fecha_limite', '')),
            'instrucciones_cierre' => trim((string) ($_POST['elab_instrucciones_cierre'] ?? '')),
            'recursos_apelacion' => trim((string) ($_POST['elab_recursos_apelacion'] ?? '')),
            'funcionario_nombre' => trim((string) ($_POST['elab_funcionario_nombre'] ?? '')),
            'funcionario_cargo' => trim((string) ($_POST['elab_funcionario_cargo'] ?? '')),
            'codigo_verificacion' => trim((string) ($_POST['elab_codigo_verificacion'] ?? '')),
        ],
    ];
}

function asset_url(string $path): string
{
    $p = ltrim($path, '/');
    if (!defined('ROOT_PATH')) {
        return '/assets/' . $p;
    }
    $rootFs = realpath(ROOT_PATH);
    $script = $_SERVER['SCRIPT_FILENAME'] ?? '';
    $scriptDirFs = $script ? realpath(dirname($script)) : false;
    $depth = 0;
    if ($rootFs && $scriptDirFs) {
        $rootFs = str_replace('\\', '/', $rootFs);
        $scriptDirFs = str_replace('\\', '/', $scriptDirFs);
        if (strpos($scriptDirFs, $rootFs) === 0) {
            $rel = trim(substr($scriptDirFs, strlen($rootFs)), '/');
            $depth = $rel === '' ? 0 : substr_count($rel, '/') + 1;
        }
    }
    return str_repeat('../', $depth) . 'assets/' . $p;
}

function fecha_hora_colombia(): string
{
    $tz = defined('APP_TIMEZONE') ? APP_TIMEZONE : 'America/Bogota';

    return (new \DateTimeImmutable('now', new \DateTimeZone($tz)))->format('Y-m-d H:i:s');
}

function etiqueta_hora_colombia(): string
{
    return 'Colombia (America/Bogota, UTC−5)';
}
