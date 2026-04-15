<?php
declare(strict_types=1);

namespace App\Controllers;

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
            $tipo = (string) post('tipo', '');
            $doc = trim((string) post('documento', ''));
            $correo = trim((string) post('correo', ''));

            if (!in_array($tipo, ['estudiante', 'docente'], true) || $doc === '' || $correo === '') {
                $error = 'Complete tipo, documento y correo registrados.';
            } else {
                $nueva = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(9))), 0, 12);
                if ($tipo === 'estudiante') {
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
                } else {
                    $rows = load_data('docentes');
                    $ok = false;
                    foreach ($rows as &$d) {
                        if ((string) ($d['documento'] ?? '') === $doc && strcasecmp((string) ($d['correo'] ?? ''), $correo) === 0) {
                            $d['clave'] = $nueva;
                            $ok = true;
                            break;
                        }
                    }
                    unset($d);
                    if ($ok) {
                        save_data('docentes', $rows);
                        $claveTemporal = $nueva;
                    } else {
                        $error = 'No coincide documento y correo con un docente registrado.';
                    }
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
