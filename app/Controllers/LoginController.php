<?php
declare(strict_types=1);

namespace App\Controllers;

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
