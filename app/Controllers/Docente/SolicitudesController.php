<?php
declare(strict_types=1);

namespace App\Controllers\Docente;

use App\Controllers\Controller;
use App\Services\SolicitudesService;

final class SolicitudesController extends Controller
{
    public function run(): void
    {
        require_role(\ROLE_DOCENTE);

        $idDoc = auth_id();
        if (!$idDoc) {
            redirect('/login.php');
        }
        $doc = repo_docente_por_id($idDoc);
        if (!$doc) {
            redirect('/login.php');
        }

        $mensaje = '';
        $tipoMsg = 'success';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'nueva_solicitud_docente') {
            [$mensaje, $tipoMsg] = SolicitudesService::registrarDesdeDocente($idDoc);
        }

        $todas = load_data('solicitudes');
        $propias = [];
        foreach ($todas as $s) {
            if ((int) ($s['id_docente_solicitante'] ?? 0) === $idDoc) {
                $propias[] = SolicitudesService::normalizarParaVista($s);
            }
        }
        usort($propias, static fn ($a, $b) => ((int) ($b['id_solicitud'] ?? 0)) <=> ((int) ($a['id_solicitud'] ?? 0)));

        $materiasDocente = repo_materias_ordenadas_por_codigo(repo_materias_por_docente($idDoc));

        $menciones = SolicitudesService::listadoMencionesAnonimasParaDocente($idDoc, (string) ($doc['documento'] ?? ''));

        $this->render('docente/solicitudes.php', [
            'pageTitle' => 'Solicitudes',
            'doc' => $doc,
            'materiasDocente' => $materiasDocente,
            'mensaje' => $mensaje,
            'tipoMsg' => $tipoMsg,
            'propias' => $propias,
            'menciones' => $menciones,
        ]);
    }
}
