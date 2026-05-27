<?php
declare(strict_types=1);

/**
 * Migra data/*.json a MySQL (ejecutar una vez tras crear el esquema).
 * CLI: php database/seed_from_json.php
 */

require_once dirname(__DIR__) . '/config/config.php';

function seed_leer_json(string $name): array
{
    $path = DATA_PATH . DIRECTORY_SEPARATOR . $name . '.json';
    if (!is_file($path)) {
        return [];
    }
    $raw = file_get_contents($path);
    if ($raw === false || $raw === '') {
        return [];
    }
    $decoded = json_decode($raw, true);

    return is_array($decoded) ? $decoded : [];
}

$archivos = ['estudiantes', 'docentes', 'administradores', 'solicitudes'];

echo 'Migrando JSON → MySQL (' . DB_NAME . ")...\n";

try {
    \App\Core\Database::ping();
} catch (Throwable $e) {
    fwrite(STDERR, 'Error de conexión: ' . $e->getMessage() . "\n");
    exit(1);
}

foreach ($archivos as $nombre) {
    $rows = seed_leer_json($nombre);
    if ($rows === []) {
        echo "  - {$nombre}: sin datos en JSON, omitido.\n";
        continue;
    }
    \App\Models\MysqlStorage::save($nombre, $rows);
    echo '  - ' . $nombre . ': ' . count($rows) . " registros.\n";
}

echo "Listo.\n";
