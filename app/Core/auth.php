<?php
declare(strict_types=1);

const ROLE_ADMIN = 'administrador';
const ROLE_DOCENTE = 'docente';
const ROLE_ESTUDIANTE = 'estudiante';

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
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

function login_user(int $id, string $nombre, string $identificador, string $dashboardPath, string $rol = ''): void
{
    $_SESSION['user'] = [
        'id' => $id,
        'nombre' => $nombre,
        'identificador' => $identificador,
        'dashboard' => $dashboardPath,
        'rol' => $rol,
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

function require_role(string $rolEsperado): void
{
    $u = auth_user();
    if (!$u) {
        redirect('/login.php');
    }
    $r = (string) ($u['rol'] ?? '');
    if ($r !== $rolEsperado) {
        redirect(dashboard_url_for_user());
    }
}

function require_gestion_admin(): void
{
    require_login();
    $u = auth_user();
    if (!$u || (string) ($u['rol'] ?? '') !== ROLE_ADMIN) {
        redirect(dashboard_url_for_user());
    }
}

function attempt_login(string $usuario, string $clave): bool
{
    $usuario = trim($usuario);
    $clave = trim($clave);
    if ($usuario === '' || $clave === '') {
        return false;
    }

    foreach (load_data('administradores') as $a) {
        if (strcasecmp((string) ($a['correo'] ?? ''), $usuario) === 0 && ($a['clave'] ?? '') === $clave) {
            login_user((int) $a['id_admin'], (string) $a['nombre'], (string) $a['correo'], 'gestion/dashboard.php', ROLE_ADMIN);
            return true;
        }
    }

    foreach (load_data('docentes') as $d) {
        if (($d['clave'] ?? '') !== $clave) {
            continue;
        }
        if (!usuario_coincide_docente_o_estudiante($d, $usuario)) {
            continue;
        }
        login_user(
            (int) $d['id_docente'],
            trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? '')),
            (string) $d['documento'],
            'docente/dashboard.php',
            ROLE_DOCENTE
        );
        return true;
    }

    foreach (load_data('estudiantes') as $e) {
        if (($e['clave'] ?? '') !== $clave) {
            continue;
        }
        if (!usuario_coincide_docente_o_estudiante($e, $usuario)) {
            continue;
        }
        login_user(
            (int) $e['id_estudiante'],
            trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? '')),
            (string) $e['documento'],
            'estudiante/dashboard.php',
            ROLE_ESTUDIANTE
        );
        return true;
    }

    return false;
}

function dashboard_url_normalizada(array $u): string
{
    $path = ltrim(str_replace('\\', '/', (string) ($u['dashboard'] ?? '')), '/');
    if ($path === 'admin/dashboard.php') {
        return url('gestion/dashboard.php');
    }

    return url($path);
}

function dashboard_url_for_user(): string
{
    $u = auth_user();
    if (!$u) {
        return url('login.php');
    }
    if (!empty($u['dashboard'])) {
        return dashboard_url_normalizada($u);
    }
    if (!empty($u['rol'])) {
        return match ((string) $u['rol']) {
            ROLE_ADMIN => url('gestion/dashboard.php'),
            ROLE_DOCENTE => url('docente/dashboard.php'),
            ROLE_ESTUDIANTE => url('estudiante/dashboard.php'),
            default => url('login.php'),
        };
    }

    return url('index.php');
}
