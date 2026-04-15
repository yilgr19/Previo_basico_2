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

/**
 * Marca de tiempo actual en Colombia (misma zona que APP_TIMEZONE / date_default_timezone_set).
 * Preferir esto para respuestas y auditoría para no depender del TZ por defecto en otros contextos.
 */
function fecha_hora_colombia(): string
{
    $tz = defined('APP_TIMEZONE') ? APP_TIMEZONE : 'America/Bogota';

    return (new \DateTimeImmutable('now', new \DateTimeZone($tz)))->format('Y-m-d H:i:s');
}

/** Texto corto para vistas (origen horario). */
function etiqueta_hora_colombia(): string
{
    return 'Colombia (America/Bogota, UTC−5)';
}
