<?php
declare(strict_types=1);

namespace App\Controllers;

final class LoginController extends Controller
{
    public function run(): void
    {
        if (auth_user()) {
            redirect(dashboard_url_for_role(auth_role()));
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rol = post('rol', '');
            $usuario = post('usuario', '');
            $clave = post('clave', '');
            $map = [
                'administrador' => ROLE_ADMIN,
                'docente' => ROLE_DOCENTE,
                'estudiante' => ROLE_ESTUDIANTE,
            ];
            $r = $map[$rol] ?? '';
            if ($r && attempt_login($r, $usuario, (string) $clave)) {
                redirect(dashboard_url_for_role($r));
            }
            $error = 'Credenciales incorrectas o rol no coincide.';
        }

        $this->render('login.php', [
            'pageTitle' => 'Iniciar sesión',
            'error' => $error,
        ]);
    }
}
