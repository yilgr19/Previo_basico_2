<?php
declare(strict_types=1);

function repo_estudiante_por_id(int $id): ?array
{
    if (function_exists('storage_usa_mysql') && storage_usa_mysql()) {
        $st = \App\Core\Database::pdo()->prepare(
            'SELECT * FROM estudiantes WHERE id_estudiante = ? LIMIT 1'
        );
        $st->execute([$id]);
        $e = $st->fetch();
        if (!$e) {
            return null;
        }
        $e['id_estudiante'] = (int) $e['id_estudiante'];
        $e['id_programa'] = isset($e['id_programa']) ? (int) $e['id_programa'] : 0;
        $e['semestre'] = isset($e['semestre']) ? (int) $e['semestre'] : 0;
        $e['edad'] = isset($e['edad']) ? (int) $e['edad'] : 0;
        $e['id_sede'] = isset($e['id_sede']) ? (int) $e['id_sede'] : 0;
        $e['id_jornada'] = isset($e['id_jornada']) ? (int) $e['id_jornada'] : 0;

        return $e;
    }

    foreach (load_data('estudiantes') as $e) {
        if ((int) ($e['id_estudiante'] ?? 0) === $id) {
            return $e;
        }
    }

    return null;
}

function repo_docente_por_id(int $id): ?array
{
    if (function_exists('storage_usa_mysql') && storage_usa_mysql()) {
        $st = \App\Core\Database::pdo()->prepare(
            'SELECT * FROM docentes WHERE id_docente = ? LIMIT 1'
        );
        $st->execute([$id]);
        $d = $st->fetch();
        if (!$d) {
            return null;
        }
        $d['id_docente'] = (int) $d['id_docente'];
        $d['id_programa'] = isset($d['id_programa']) ? (int) $d['id_programa'] : 0;
        $d['id_sede'] = isset($d['id_sede']) ? (int) $d['id_sede'] : 0;

        return $d;
    }

    foreach (load_data('docentes') as $d) {
        if ((int) ($d['id_docente'] ?? 0) === $id) {
            return $d;
        }
    }

    return null;
}
