<?php
declare(strict_types=1);

namespace App\Controllers\Docente;

use App\Controllers\Controller;

final class DashboardController extends Controller
{
    public function run(): void
    {
        require_role(\ROLE_DOCENTE);

        $idDoc = auth_id();
        if (!$idDoc) {
            redirect('/login.php');
        }
        $d = repo_docente_por_id($idDoc);

        $this->render('docente/dashboard.php', [
            'pageTitle' => 'Panel docente',
            'd' => $d,
        ]);
    }
}
