<?php
declare(strict_types=1);

namespace App\Controllers;

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/bootstrap.php';

use App\Services\RegistroEstudianteService;

final class VerificarRegistroController extends Controller
{
    public function run(): void
    {
        if (auth_user()) {
            redirect(dashboard_url_for_user());
        }

        $token = trim((string) (get('token') ?? ''));
        [$mensaje, $tipoMsg] = RegistroEstudianteService::verificarToken($token);

        $this->render('registro_verificado.php', [
            'pageTitle' => 'Verificación de cuenta',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'ok' => $tipoMsg === 'success',
        ]);
    }
}

\App\Controllers\dispatch_if_direct(__FILE__, VerificarRegistroController::class);
