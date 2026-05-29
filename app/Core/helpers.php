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
        'consentimiento_veracidad' => (isset($_POST['consentimiento_veracidad']) && (string) $_POST['consentimiento_veracidad'] === '1'),
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

/** Datos del formulario de registro/edición de estudiante (sin contraseñas). */
function gestion_estudiante_old_desde_post(): array
{
    $editId = (int) post('id_estudiante', '0');
    $out = [
        'tipo_identificacion' => (string) post('tipo_identificacion', 'CC'),
        'documento' => trim((string) post('documento', '')),
        'nombre' => trim((string) post('nombre', '')),
        'apellido' => trim((string) post('apellido', '')),
        'correo' => trim((string) post('correo', '')),
        'sexo' => (string) post('sexo', 'M'),
        'id_programa' => (int) post('id_programa', '0'),
        'semestre' => (int) post('semestre', '1'),
        'fecha_nacimiento' => trim((string) post('fecha_nacimiento', '')),
        'direccion' => trim((string) post('direccion', '')),
        'barrio' => trim((string) post('barrio', '')),
        'telefono' => trim((string) post('telefono', '')),
        'id_sede' => (int) post('id_sede', '1'),
        'id_jornada' => (int) post('id_jornada', '1'),
    ];
    if ($editId > 0) {
        $out['id_estudiante'] = $editId;
    }

    return $out;
}

/** Alias del formulario de autoregistro (mismos campos que gestión). */
function registro_estudiante_old_desde_post(): array
{
    return gestion_estudiante_old_desde_post();
}

function correo_es_institucional_estudiante(string $correo): bool
{
    $correo = strtolower(trim($correo));
    if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $dominio = strtolower((string) substr(strrchr($correo, '@') ?: '', 1));
    if ($dominio === '') {
        return false;
    }
    $permitidos = defined('REGISTRO_DOMINIOS_CORREO') ? REGISTRO_DOMINIOS_CORREO : ['fesc.edu.co'];
    foreach ($permitidos as $permitido) {
        if ($dominio === strtolower((string) $permitido)) {
            return true;
        }
    }

    return false;
}

function asset_url(string $path): string
{
    return url(ASSETS_URL . '/' . ltrim($path, '/'));
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
