<?php
declare(strict_types=1);

/**
 * Elimina TODAS las solicitudes, detalles, respuestas y adjuntos.
 * Conserva estudiantes, docentes y administradores.
 *
 * CLI: php database/limpiar_solicitudes.php
 */
require_once dirname(__DIR__) . '/config/config.php';

use App\Core\Database;

if (!defined('DB_ENABLED') || DB_ENABLED !== true) {
    fwrite(STDERR, "Error: DB_ENABLED debe ser true.\n");
    exit(1);
}

$pdo = Database::pdo();

echo "=== Limpieza total de solicitudes ===\n\n";

$uploadsBase = ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'solicitudes';
$dirsRemoved = 0;
if (is_dir($uploadsBase)) {
    $dirs = glob($uploadsBase . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR) ?: [];
    foreach ($dirs as $dir) {
        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') ?: [] as $f) {
            if (is_file($f)) {
                @unlink($f);
            }
        }
        @rmdir($dir);
        $dirsRemoved++;
    }
    echo "Carpetas de adjuntos eliminadas: $dirsRemoved\n";
}

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$counts = [];
foreach ([
    'solicitud_anexos',
    'solicitud_detalle_estudiante',
    'solicitud_detalle_docente',
    'solicitud_respuesta_elaborada',
    'solicitudes',
] as $tabla) {
    $counts[$tabla] = (int) $pdo->query("SELECT COUNT(*) FROM `$tabla`")->fetchColumn();
    $pdo->exec("DELETE FROM `$tabla`");
    $pdo->exec("ALTER TABLE `$tabla` AUTO_INCREMENT = 1");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

foreach ($counts as $tabla => $n) {
    echo "  $tabla: $n filas eliminadas\n";
}

$nEst = (int) $pdo->query('SELECT COUNT(*) FROM estudiantes')->fetchColumn();
$nDoc = (int) $pdo->query('SELECT COUNT(*) FROM docentes')->fetchColumn();
$nAdm = (int) $pdo->query('SELECT COUNT(*) FROM administradores')->fetchColumn();
echo "\nUsuarios conservados: $nEst estudiantes, $nDoc docentes, $nAdm administradores.\n";
echo "Solicitudes en BD: 0 — puede crearlas manualmente desde la aplicación.\n\nListo.\n";
