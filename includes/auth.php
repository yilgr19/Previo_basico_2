<?php
declare(strict_types=1);

const ROLE_ADMIN = 'administrador';
const ROLE_DOCENTE = 'docente';
const ROLE_ESTUDIANTE = 'estudiante';

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_role(): ?string
{
    $u = auth_user();
    return $u['rol'] ?? null;
}

function auth_id(): ?int
{
    $u = auth_user();
    return isset($u['id']) ? (int) $u['id'] : null;
}

function require_login(): void
{
    if (!auth_user()) {
        redirect('/login.php');
    }
}

function require_role(string $role): void
{
    require_login();
    if (auth_role() !== $role) {
        redirect('/login.php');
    }
}

function login_user(string $rol, int $id, string $nombre, string $identificador): void
{
    $_SESSION['user'] = [
        'rol' => $rol,
        'id' => $id,
        'nombre' => $nombre,
        'identificador' => $identificador,
    ];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/** Acepta documento o correo (si existe en el registro). */
function usuario_coincide_docente_o_estudiante(array $registro, string $usuario): bool
{
    $u = trim($usuario);
    if ($u === '') {
        return false;
    }
    if ((string) ($registro['documento'] ?? '') === $u) {
        return true;
    }
    $correo = $registro['correo'] ?? '';
    if ($correo !== '' && strcasecmp((string) $correo, $u) === 0) {
        return true;
    }
    return false;
}

function attempt_login(string $rol, string $usuario, string $clave): bool
{
    $usuario = trim($usuario);
    $clave = trim($clave);
    if ($usuario === '' || $clave === '') {
        return false;
    }

    if ($rol === ROLE_ADMIN) {
        foreach (load_data('administradores') as $a) {
            if (strcasecmp((string) ($a['correo'] ?? ''), $usuario) === 0 && ($a['clave'] ?? '') === $clave) {
                login_user(ROLE_ADMIN, (int) $a['id_admin'], (string) $a['nombre'], (string) $a['correo']);
                return true;
            }
        }
        return false;
    }

    if ($rol === ROLE_DOCENTE) {
        foreach (load_data('docentes') as $d) {
            if (($d['clave'] ?? '') !== $clave) {
                continue;
            }
            if (!usuario_coincide_docente_o_estudiante($d, $usuario)) {
                continue;
            }
            login_user(ROLE_DOCENTE, (int) $d['id_docente'], trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? '')), (string) $d['documento']);
            return true;
        }
        return false;
    }

    if ($rol === ROLE_ESTUDIANTE) {
        foreach (load_data('estudiantes') as $e) {
            if (($e['clave'] ?? '') !== $clave) {
                continue;
            }
            if (!usuario_coincide_docente_o_estudiante($e, $usuario)) {
                continue;
            }
            login_user(ROLE_ESTUDIANTE, (int) $e['id_estudiante'], trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? '')), (string) $e['documento']);
            return true;
        }
        return false;
    }

    return false;
}

function dashboard_url_for_role(?string $rol): string
{
    switch ($rol) {
        case ROLE_ADMIN:
            return url('admin/dashboard.php');
        case ROLE_DOCENTE:
            return url('docente/dashboard.php');
        case ROLE_ESTUDIANTE:
            return url('estudiante/dashboard.php');
        default:
            return url('login.php');
    }
}
