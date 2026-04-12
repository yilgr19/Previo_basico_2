<?php
declare(strict_types=1);

/** @return array<string,mixed>|null */
function repo_estudiante_por_id(int $id): ?array
{
    foreach (load_data('estudiantes') as $e) {
        if ((int) ($e['id_estudiante'] ?? 0) === $id) {
            return $e;
        }
    }
    return null;
}

/** @return array<string,mixed>|null */
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

/** @return array<string,mixed>|null */
function repo_docente_por_id(int $id): ?array
{
    foreach (load_data('docentes') as $d) {
        if ((int) ($d['id_docente'] ?? 0) === $id) {
            return $d;
        }
    }
    return null;
}

/** @return array<string,mixed>|null */
function repo_materia_por_id(int $id): ?array
{
    foreach (load_data('materias') as $m) {
        if ((int) ($m['id_materia'] ?? 0) === $id) {
            return $m;
        }
    }
    return null;
}

/** @return array<string,mixed>|null */
function repo_matricula_por_id(int $id): ?array
{
    foreach (load_data('matriculas') as $x) {
        if ((int) ($x['id_matricula'] ?? 0) === $id) {
            return $x;
        }
    }
    return null;
}

function repo_existe_matricula(int $idEst, int $idMat): bool
{
    foreach (load_data('matriculas') as $x) {
        if ((int) ($x['id_estudiante'] ?? 0) === $idEst && (int) ($x['id_materia'] ?? 0) === $idMat) {
            return true;
        }
    }
    return false;
}

/** @return list<array<string,mixed>> */
function repo_matriculas_de_estudiante(int $idEst): array
{
    $out = [];
    foreach (load_data('matriculas') as $x) {
        if ((int) ($x['id_estudiante'] ?? 0) === $idEst) {
            $out[] = $x;
        }
    }
    return $out;
}

/** @return list<array<string,mixed>> */
function repo_matriculas_de_materia(int $idMat): array
{
    $out = [];
    foreach (load_data('matriculas') as $x) {
        if ((int) ($x['id_materia'] ?? 0) === $idMat) {
            $out[] = $x;
        }
    }
    return $out;
}

/** @return list<array<string,mixed>> */
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

/** @param array<string,mixed> $m */
function materia_modalidad_etiqueta(array $m): string
{
    $mod = (string) ($m['modalidad'] ?? 'virtual');
    return $mod === 'presencial' ? 'Presencial' : 'Virtual';
}

/**
 * @param list<array<string,mixed>> $materias
 * @return list<array<string,mixed>>
 */
function repo_materias_ordenadas_por_codigo(array $materias): array
{
    usort($materias, static function ($a, $b) {
        return strcmp((string) ($a['codigo'] ?? ''), (string) ($b['codigo'] ?? ''));
    });
    return $materias;
}

/** @return array<string,string> código interno => etiqueta */
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

/** Resumen de horario para tablas y listas. */
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

/** Carrera/programa según diccionario. */
function materia_programa_label(array $m): string
{
    $id = (int) ($m['id_programa'] ?? 0);
    if ($id <= 0) {
        return '—';
    }
    return programa_label_by_id($id);
}

function estudiante_nombre_completo(int $idEst): string
{
    $e = repo_estudiante_por_id($idEst);
    if (!$e) {
        return '—';
    }
    return trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? ''));
}
