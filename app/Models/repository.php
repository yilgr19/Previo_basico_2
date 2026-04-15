<?php
declare(strict_types=1);

function repo_estudiante_por_id(int $id): ?array
{
    foreach (load_data('estudiantes') as $e) {
        if ((int) ($e['id_estudiante'] ?? 0) === $id) {
            return $e;
        }
    }
    return null;
}

function repo_estudiante_por_documento(string $doc): ?array
{
    $doc = trim($doc);
    foreach (load_data('estudiantes') as $e) {
        if ((string) ($e['documento'] ?? '') === $doc) {
            return $e;
        }
    }
    return null;
}

function repo_docente_por_id(int $id): ?array
{
    foreach (load_data('docentes') as $d) {
        if ((int) ($d['id_docente'] ?? 0) === $id) {
            return $d;
        }
    }
    return null;
}
