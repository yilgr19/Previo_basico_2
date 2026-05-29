<?php
declare(strict_types=1);

namespace App\Controllers;

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/bootstrap.php';

final class LoginController extends Controller
{
    public function run(): void
    {
        if (auth_user()) {
            redirect(dashboard_url_for_user());
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = post('usuario', '');
            $clave = post('clave', '');
            if (attempt_login((string) $usuario, (string) $clave)) {
                redirect(dashboard_url_for_user());
            }
            $error = 'Usuario o contraseña incorrectos.';
        }

        $this->render('login.php', [
            'pageTitle' => 'Iniciar sesión',
            'error' => $error,
        ]);
    }
}

\App\Controllers\dispatch_if_direct(__FILE__, LoginController::class);
