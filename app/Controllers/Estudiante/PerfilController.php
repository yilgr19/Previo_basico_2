<?php
declare(strict_types=1);

namespace App\Controllers\Estudiante;

use App\Controllers\Controller;
use App\Services\GestionAcademicaService;

final class PerfilController extends Controller
{
    public function run(): void
    {
        require_role(\ROLE_ESTUDIANTE);

        $idEst = auth_id();
        if (!$idEst) {
            redirect('/login.php');
        }

        $mensaje = '';
        $tipoMsg = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'guardar_perfil') {
            [$mensaje, $tipoMsg] = GestionAcademicaService::actualizarEstudiantePropio($idEst);
        }

        $editar = repo_estudiante_por_id($idEst);

        $this->render('estudiante/perfil.php', [
            'pageTitle' => 'Mi perfil',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'editar' => $editar,
        ]);
    }
}
