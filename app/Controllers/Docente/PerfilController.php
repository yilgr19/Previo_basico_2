<?php
declare(strict_types=1);

namespace App\Controllers\Docente;

use App\Controllers\Controller;
use App\Services\GestionAcademicaService;

final class PerfilController extends Controller
{
    public function run(): void
    {
        require_role(\ROLE_DOCENTE);

        $idDoc = auth_id();
        if (!$idDoc) {
            redirect('/login.php');
        }

        $mensaje = '';
        $tipoMsg = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'guardar_perfil_docente') {
            [$mensaje, $tipoMsg] = GestionAcademicaService::actualizarDocentePropio($idDoc);
            if ($tipoMsg === 'success') {
                $d = repo_docente_por_id($idDoc);
                if ($d && isset($_SESSION['user'])) {
                    $_SESSION['user']['nombre'] = trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? ''));
                    $_SESSION['user']['identificador'] = (string) ($d['documento'] ?? '');
                }
            }
        }

        $editar = repo_docente_por_id($idDoc);

        $this->render('docente/perfil.php', [
            'pageTitle' => 'Mi perfil',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'editar' => $editar,
        ]);
    }
}
