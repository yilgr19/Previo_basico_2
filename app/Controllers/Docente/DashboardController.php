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

        $todas = load_data('solicitudes');
        $nActivas = 0;
        $nEnRevision = 0;
        $nAprobadas = 0;
        $nRechazadas = 0;
        foreach ($todas as $s) {
            if ((int) ($s['id_docente_solicitante'] ?? 0) !== $idDoc) {
                continue;
            }
            $st = (string) ($s['estado'] ?? '');
            if ($st === 'aprobada') {
                $nAprobadas++;
            } elseif ($st === 'rechazada') {
                $nRechazadas++;
            } elseif ($st === 'en_revision') {
                $nEnRevision++;
            } else {
                $nActivas++;
            }
        }

        $this->render('docente/dashboard.php', [
            'pageTitle' => 'Panel docente',
            'd' => $d,
            'nActivas' => $nActivas,
            'nEnRevision' => $nEnRevision,
            'nAprobadas' => $nAprobadas,
            'nRechazadas' => $nRechazadas,
        ]);
    }
}
