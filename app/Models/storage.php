<?php
declare(strict_types=1);

use App\Models\MysqlStorage;

function storage_usa_mysql(): bool
{
    return defined('DB_ENABLED') && DB_ENABLED === true;
}

function data_file(string $name): string
{
    return DATA_PATH . DIRECTORY_SEPARATOR . $name . '.json';
}

function load_data(string $name): array
{
    if (storage_usa_mysql()) {
        return MysqlStorage::load($name);
    }

    $path = data_file($name);
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

function save_data(string $name, array $data): bool
{
    if (storage_usa_mysql()) {
        return MysqlStorage::save($name, $data);
    }

    if (!is_dir(DATA_PATH)) {
        mkdir(DATA_PATH, 0755, true);
    }
    $path = data_file($name);
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    return file_put_contents($path, $json) !== false;
}

function next_numeric_id(array $items, string $idKey): int
{
    if (storage_usa_mysql()) {
        $map = [
            'id_solicitud' => ['solicitudes', 'id_solicitud'],
            'id_estudiante' => ['estudiantes', 'id_estudiante'],
            'id_admin' => ['administradores', 'id_admin'],
        ];
        if (isset($map[$idKey])) {
            [$table, $column] = $map[$idKey];
            $st = \App\Core\Database::pdo()->query(
                'SELECT COALESCE(MAX(`' . $column . '`), 0) + 1 FROM `' . $table . '`'
            );
            $next = (int) $st->fetchColumn();
            if ($next > 0) {
                return $next;
            }
        }
    }

    $max = 0;
    foreach ($items as $row) {
        if (isset($row[$idKey]) && is_numeric($row[$idKey])) {
            $max = max($max, (int) $row[$idKey]);
        }
    }

    return $max + 1;
}
