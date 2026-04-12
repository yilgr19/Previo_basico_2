<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_role(ROLE_ESTUDIANTE);

$idEst = auth_id();
if (!$idEst) {
    redirect('/login.php');
}
$yo = repo_estudiante_por_id($idEst);
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'nueva_solicitud') {
    $tipo = (int) post('id_tipo_solicitud', '0');
    $desc = post('descripcion', '');
    if ($tipo > 0 && $desc !== '') {
        $sol = load_data('solicitudes');
        $sol[] = [
            'id_solicitud' => next_numeric_id($sol, 'id_solicitud'),
            'fecha' => date('Y-m-d'),
            'estado' => 'En revisión',
            'descripcion' => $desc,
            'id_estudiante' => $idEst,
            'id_tipo_solicitud' => $tipo,
        ];
        save_data('solicitudes', $sol);
        $mensaje = 'Solicitud registrada.';
    } else {
        $mensaje = 'Complete tipo y descripción.';
    }
}

$solicitudes = array_values(array_filter(load_data('solicitudes'), static fn ($s) => (int) ($s['id_estudiante'] ?? 0) === $idEst));
$matriculas = repo_matriculas_de_estudiante($idEst);
$matsEst = [];
foreach ($matriculas as $x) {
    $mid = (int) ($x['id_materia'] ?? 0);
    $m = repo_materia_por_id($mid);
    $matsEst[] = ['matricula' => $x, 'materia' => $m];
}
usort($matsEst, static function ($a, $b) {
    $ca = $a['materia'] ? (string) ($a['materia']['codigo'] ?? '') : '';
    $cb = $b['materia'] ? (string) ($b['materia']['codigo'] ?? '') : '';
    return strcmp($ca, $cb);
});

$pageTitle = 'Panel estudiante';
require PARTIALS_PATH . '/header.php';
require VIEWS_PATH . '/estudiante/dashboard.php';
require PARTIALS_PATH . '/footer.php';
