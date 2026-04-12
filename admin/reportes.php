<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_role(ROLE_ADMIN);

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'eliminar_matricula') {
    $id = (int) post('id_matricula', '0');
    $mat = load_data('matriculas');
    $mat = array_values(array_filter($mat, static fn ($m) => (int) ($m['id_matricula'] ?? 0) !== $id));
    save_data('matriculas', $mat);
    $mensaje = 'Matrícula eliminada.';
}

$estudiantes = load_data('estudiantes');
$docentes = load_data('docentes');
$materias = load_data('materias');
$materiasOrdenadas = repo_materias_ordenadas_por_codigo($materias);
$matriculas = load_data('matriculas');

$pageTitle = 'Reportes';
require PARTIALS_PATH . '/header.php';
require VIEWS_PATH . '/admin/reportes.php';
require PARTIALS_PATH . '/footer.php';
