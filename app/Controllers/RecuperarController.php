<?php
declare(strict_types=1);

namespace App\Controllers;

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/bootstrap.php';

final class RecuperarController extends Controller
{
    public function run(): void
    {
        if (auth_user()) {
            redirect(dashboard_url_for_user());
        }

        $error = '';
        $claveTemporal = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $doc = trim((string) post('documento', ''));
            $correo = trim((string) post('correo', ''));

            if ($doc === '' || $correo === '') {
                $error = 'Complete documento y correo registrados.';
            } else {
                $nueva = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(9))), 0, 12);
                $rows = load_data('estudiantes');
                $ok = false;
                foreach ($rows as &$e) {
                    if ((string) ($e['documento'] ?? '') === $doc && strcasecmp((string) ($e['correo'] ?? ''), $correo) === 0) {
                        $e['clave'] = $nueva;
                        $ok = true;
                        break;
                    }
                }
                unset($e);
                if ($ok) {
                    save_data('estudiantes', $rows);
                    $claveTemporal = $nueva;
                } else {
                    $error = 'No coincide documento y correo con un estudiante registrado.';
                }
            }
        }

        $this->render('recuperar.php', [
            'pageTitle' => 'Recuperar contraseña',
            'error' => $error,
            'claveTemporal' => $claveTemporal,
        ]);
    }
}

\App\Controllers\dispatch_if_direct(__FILE__, RecuperarController::class);
