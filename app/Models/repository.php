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

function repo_materia_por_id(int $id): ?array
{
    foreach (load_data('materias') as $m) {
        if ((int) ($m['id_materia'] ?? 0) === $id) {
            return $m;
        }
    }
    return null;
}

function repo_materias_por_docente(int $idDoc): array
{
    $out = [];
    foreach (load_data('materias') as $m) {
        if ((int) ($m['id_docente'] ?? 0) === $idDoc) {
            $out[] = $m;
        }
    }
    return $out;
}

function docente_nombre(int $idDoc): string
{
    $d = repo_docente_por_id($idDoc);
    if (!$d) {
        return '—';
    }
    return trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? ''));
}

function materia_nombre(int $idMat): string
{
    $m = repo_materia_por_id($idMat);
    if (!$m) {
        return '—';
    }
    return (string) (($m['codigo'] ?? '') . ' — ' . ($m['nombre'] ?? ''));
}

function materia_modalidad_etiqueta(array $m): string
{
    $mod = (string) ($m['modalidad'] ?? 'virtual');
    return $mod === 'presencial' ? 'Presencial' : 'Virtual';
}

function repo_materias_ordenadas_por_codigo(array $materias): array
{
    usort($materias, static function ($a, $b) {
        return strcmp((string) ($a['codigo'] ?? ''), (string) ($b['codigo'] ?? ''));
    });
    return $materias;
}

function materia_dias_clase_opciones(): array
{
    return [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sabado' => 'Sábado',
        'domingo' => 'Domingo',
    ];
}

function materia_dia_corto(?string $cod): string
{
    $map = [
        'lunes' => 'Lun',
        'martes' => 'Mar',
        'miercoles' => 'Mié',
        'jueves' => 'Jue',
        'viernes' => 'Vie',
        'sabado' => 'Sáb',
        'domingo' => 'Dom',
    ];
    return $map[$cod ?? ''] ?? '—';
}

function materia_dia_etiqueta(?string $cod): string
{
    $op = materia_dias_clase_opciones();
    return $op[$cod ?? ''] ?? ($cod ?: '—');
}

function materia_horario_resumen(array $m): string
{
    $d = materia_dia_corto((string) ($m['dia_clase'] ?? ''));
    $a = trim((string) ($m['hora_inicio'] ?? ''));
    $b = trim((string) ($m['hora_fin'] ?? ''));
    if ($a === '' && $b === '') {
        return '—';
    }
    return $d . ' ' . $a . '–' . $b;
}

function materia_programa_label(array $m): string
{
    $id = (int) ($m['id_programa'] ?? 0);
    if ($id <= 0) {
        return '—';
    }
    return programa_label_by_id($id);
}

/** Materias del programa (malla) para selección en solicitudes estudiantiles. */
function repo_materias_por_programa(int $idPrograma): array
{
    if ($idPrograma <= 0) {
        return [];
    }
    $out = [];
    foreach (load_data('materias') as $m) {
        if ((int) ($m['id_programa'] ?? 0) === $idPrograma) {
            $out[] = $m;
        }
    }
    return repo_materias_ordenadas_por_codigo($out);
}

function estudiante_nombre_completo(int $idEst): string
{
    $e = repo_estudiante_por_id($idEst);
    if (!$e) {
        return '—';
    }
    return trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? ''));
}
