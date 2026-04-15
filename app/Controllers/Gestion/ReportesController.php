<?php
declare(strict_types=1);

namespace App\Controllers\Gestion;

use App\Controllers\Controller;

final class ReportesController extends Controller
{
    public function run(): void
    {
        require_gestion_admin();

        $mensaje = '';
        $tipoMsg = 'success';

        $estudiantes = load_data('estudiantes');
        $docentes = load_data('docentes');

        $this->render('gestion/reportes.php', [
            'pageTitle' => 'Reportes',
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'estudiantes' => $estudiantes,
            'docentes' => $docentes,
        ]);
    }
}
