<?php
declare(strict_types=1);

function data_file(string $name): string
{
    return DATA_PATH . DIRECTORY_SEPARATOR . $name . '.json';
}

function load_data(string $name): array
{
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
    if (!is_dir(DATA_PATH)) {
        mkdir(DATA_PATH, 0755, true);
    }
    $path = data_file($name);
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($path, $json) !== false;
}

function next_numeric_id(array $items, string $idKey): int
{
    $max = 0;
    foreach ($items as $row) {
        if (isset($row[$idKey]) && is_numeric($row[$idKey])) {
            $max = max($max, (int) $row[$idKey]);
        }
    }
    return $max + 1;
}
