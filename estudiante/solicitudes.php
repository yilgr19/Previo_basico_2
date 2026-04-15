<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';

$tab = trim((string) get('tab', ''));
$valid = ['activas', 'en_revision', 'aprobadas', 'rechazadas'];
$target = url('estudiante/mis_solicitudes.php');
if ($tab !== '' && in_array($tab, $valid, true)) {
    $target .= '?tab=' . rawurlencode($tab);
}
redirect($target);
