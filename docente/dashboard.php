<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_role(ROLE_DOCENTE);

$idDoc = auth_id();
if (!$idDoc) {
    redirect('/login.php');
}
$d = repo_docente_por_id($idDoc);
$materias = repo_materias_ordenadas_por_codigo(repo_materias_por_docente($idDoc));

$pageTitle = 'Panel docente';
require PARTIALS_PATH . '/header.php';
require VIEWS_PATH . '/docente/dashboard.php';
require PARTIALS_PATH . '/footer.php';
