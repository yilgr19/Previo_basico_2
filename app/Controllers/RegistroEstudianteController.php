<?php
declare(strict_types=1);

namespace App\Controllers;

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/bootstrap.php';

use App\Services\RegistroEstudianteService;

final class RegistroEstudianteController extends Controller
{
    public function run(): void
    {
        if (auth_user()) {
            redirect(dashboard_url_for_user());
        }

        if (!defined('REGISTRO_ESTUDIANTE_HABILITADO') || !REGISTRO_ESTUDIANTE_HABILITADO) {
            redirect('/login');
        }

        $mensaje = '';
        $tipoMsg = '';
        $repoblar = null;
        $enlaceDev = null;
        $mostrarPendiente = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$mensaje, $tipoMsg, $enlaceDev] = RegistroEstudianteService::solicitarRegistro();
            if ($tipoMsg === 'success') {
                $mostrarPendiente = true;
            } elseif ($tipoMsg === 'warning') {
                $repoblar = registro_estudiante_old_desde_post();
            }
        }

        $dominios = defined('REGISTRO_DOMINIOS_CORREO') ? REGISTRO_DOMINIOS_CORREO : ['fesc.edu.co'];
        $dominioEjemplo = '@' . ($dominios[0] ?? 'fesc.edu.co');

        $this->render('registro_estudiante.php', [
            'pageTitle' => 'Registro de estudiante',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'repoblar' => $repoblar,
            'mostrarPendiente' => $mostrarPendiente,
            'enlaceDev' => $enlaceDev,
            'dominioEjemplo' => $dominioEjemplo,
            'claveMin' => defined('REGISTRO_CLAVE_MIN') ? (int) REGISTRO_CLAVE_MIN : 8,
        ]);
    }
}

\App\Controllers\dispatch_if_direct(__FILE__, RegistroEstudianteController::class);
