<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\DescargarAnexoController;
use App\Controllers\Estudiante\DashboardController as EstudianteDashboardController;
use App\Controllers\Estudiante\PerfilController as EstudiantePerfilController;
use App\Controllers\Estudiante\SolicitudesController as EstudianteSolicitudesController;
use App\Controllers\Gestion\DashboardController as GestionDashboardController;
use App\Controllers\Gestion\EstudiantesController;
use App\Controllers\Gestion\PlazosController;
use App\Controllers\Gestion\ReportesController;
use App\Controllers\Gestion\SolicitudesController as GestionSolicitudesController;
use App\Controllers\Gestion\SolicitudesRevisionController;
use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\LogoutController;
use App\Controllers\RecuperarController;

final class Router
{
    private static string $currentRoute = '';

    /** @var array<string, array{class-string, mixed...}|string> */
    private const ROUTES = [
        '' => [HomeController::class],
        'login' => [LoginController::class],
        'logout' => [LogoutController::class],
        'recuperar' => [RecuperarController::class],
        'descargar_anexo' => [DescargarAnexoController::class],
        'estudiante/dashboard' => [EstudianteDashboardController::class],
        'estudiante/perfil' => [EstudiantePerfilController::class],
        'estudiante/nueva_solicitud' => [EstudianteSolicitudesController::class],
        'estudiante/mis_solicitudes' => [EstudianteSolicitudesController::class],
        'gestion/dashboard' => [GestionDashboardController::class],
        'gestion/estudiantes' => [EstudiantesController::class],
        'gestion/plazos' => [PlazosController::class],
        'gestion/reportes' => [ReportesController::class],
        'gestion/solicitudes' => [GestionSolicitudesController::class, 1],
        'gestion/solicitudes_sede_ocana' => [GestionSolicitudesController::class, 2],
        'gestion/solicitudes_revision' => [SolicitudesRevisionController::class, 1],
        'gestion/solicitudes_revision_ocana' => [SolicitudesRevisionController::class, 2],
        'admin/dashboard' => 'redirect:gestion/dashboard',
    ];

    public static function currentRoute(): string
    {
        return self::$currentRoute;
    }

    public static function dispatch(): void
    {
        $route = self::resolveRoute();
        self::$currentRoute = $route;

        $handler = self::ROUTES[$route] ?? null;
        if ($handler === null) {
            http_response_code(404);
            exit('Página no encontrada.');
        }

        if (is_string($handler)) {
            if (str_starts_with($handler, 'redirect:')) {
                redirect(url(substr($handler, 9)));
            }
            http_response_code(500);
            exit('Ruta mal configurada.');
        }

        $class = array_shift($handler);
        $controller = new $class(...$handler);
        $controller->run();
    }

    private static function resolveRoute(): string
    {
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $path = parse_url($uri, PHP_URL_PATH);
        $path = is_string($path) ? $path : '/';

        $base = app_base_path();
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }

        $path = trim($path, '/');
        if ($path === 'index.php') {
            $path = '';
        }

        if (str_ends_with($path, '.php')) {
            $path = substr($path, 0, -4);
        }

        return $path;
    }
}
