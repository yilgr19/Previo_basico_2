<?php
declare(strict_types=1);
/**
 * Verifica que la app use MySQL y muestra conteos + últimos registros.
 * CLI: php database/verificar_registros.php
 */
require_once dirname(__DIR__) . '/config/config.php';

echo "=== Verificación de persistencia ===\n\n";
echo 'DB_ENABLED: ' . (defined('DB_ENABLED') && DB_ENABLED ? 'true (MySQL)' : 'false (JSON)') . "\n";
echo 'Base de datos: ' . DB_NAME . '@' . DB_HOST . "\n\n";

try {
    $pdo = App\Core\Database::pdo();
    $pdo->query('SELECT 1');
    echo "Conexión MySQL: OK\n\n";
} catch (Throwable $e) {
    echo "Conexión MySQL: FALLO — " . $e->getMessage() . "\n";
    exit(1);
}

$tables = [
    'estudiantes' => 'id_estudiante',
    'docentes' => 'id_docente',
    'administradores' => 'id_admin',
    'solicitudes' => 'id_solicitud',
];

foreach ($tables as $table => $pk) {
    $n = (int) $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    echo str_pad($table, 18) . ": $n registros\n";
}

echo "\n--- Últimas 5 solicitudes (MySQL) ---\n";
$st = $pdo->query(
    'SELECT id_solicitud, id_estudiante, id_docente_solicitante, estado, fecha_registro, LEFT(descripcion, 50) AS descripcion
     FROM solicitudes ORDER BY id_solicitud DESC LIMIT 5'
);
foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $quien = (int) $r['id_estudiante'] > 0
        ? 'estudiante #' . $r['id_estudiante']
        : 'docente #' . $r['id_docente_solicitante'];
    echo sprintf(
        "#%d | %s | %s | %s | %s\n",
        $r['id_solicitud'],
        $quien,
        $r['estado'],
        $r['fecha_registro'],
        $r['descripcion'] ?? ''
    );
}

echo "\n--- Prueba load_data() vs MySQL ---\n";
$viaApp = count(load_data('solicitudes'));
$viaSql = (int) $pdo->query('SELECT COUNT(*) FROM solicitudes')->fetchColumn();
echo "load_data('solicitudes'): $viaApp\n";
echo "SELECT COUNT solicitudes: $viaSql\n";
echo ($viaApp === $viaSql ? "Coinciden: la app lee desde MySQL.\n" : "NO coinciden: revisar DB_ENABLED.\n");

echo "\n--- Respuestas del administrador (tabla solicitudes) ---\n";
echo "Columnas: respuesta (texto breve), fecha_respuesta, respondido_en\n\n";
$st = $pdo->query(
    'SELECT id_solicitud, estado, COALESCE(NULLIF(respuesta,\'\'), \'(sin texto breve)\') AS respuesta,
            COALESCE(fecha_respuesta, \'-\') AS fecha_respuesta,
            COALESCE(respondido_en, \'-\') AS respondido_en
     FROM solicitudes ORDER BY id_solicitud'
);
foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo sprintf(
        "#%d | estado=%s | respuesta=%s | respondido=%s\n",
        $r['id_solicitud'],
        $r['estado'],
        $r['respuesta'],
        $r['respondido_en']
    );
}

echo "\n--- Resolución formal (tabla solicitud_respuesta_elaborada) ---\n";
echo "Solo se guarda si marcó la casilla «Guardar también resolución formal».\n\n";
$stRe = $pdo->query(
    'SELECT id_solicitud, numero_respuesta, decision, emitido_en
     FROM solicitud_respuesta_elaborada ORDER BY id_solicitud'
);
$rowsRe = $stRe->fetchAll(PDO::FETCH_ASSOC);
if ($rowsRe === []) {
    echo "(ninguna resolución formal guardada)\n";
} else {
    foreach ($rowsRe as $r) {
        echo sprintf(
            "#%d | %s | decisión=%s | emitido=%s\n",
            $r['id_solicitud'],
            $r['numero_respuesta'],
            $r['decision'],
            $r['emitido_en']
        );
    }
}

echo "\nTip: tras responder en gestión, ejecute de nuevo este script y busque su id_solicitud arriba.\n";
