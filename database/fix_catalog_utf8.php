<?php
declare(strict_types=1);

/**
 * Repara TODOS los textos corruptos (???) en catálogos y campos denormalizados.
 * CLI: php database/fix_catalog_utf8.php
 */
require_once dirname(__DIR__) . '/config/config.php';

/** @var array<string, list<array<string, mixed>>> $catalog */
$catalog = require __DIR__ . '/catalog_canonical.php';

$pdo = App\Core\Database::pdo();
$pdo->exec('SET NAMES utf8mb4');

$updated = 0;

// --- Catálogos por tabla ---
$stSede = $pdo->prepare('UPDATE sedes SET nombre = ? WHERE id_sede = ?');
foreach ($catalog['sedes'] as $r) {
    $stSede->execute([$r['nombre'], $r['id']]);
    $updated += $stSede->rowCount();
}

$stJorn = $pdo->prepare('UPDATE jornadas SET nombre = ? WHERE id_jornada = ?');
foreach ($catalog['jornadas'] as $r) {
    $stJorn->execute([$r['nombre'], $r['id']]);
    $updated += $stJorn->rowCount();
}

$stTi = $pdo->prepare('UPDATE tipos_identificacion SET nombre = ? WHERE codigo = ?');
foreach ($catalog['tipos_identificacion'] as $r) {
    $stTi->execute([$r['nombre'], $r['codigo']]);
    $updated += $stTi->rowCount();
}

$stSexo = $pdo->prepare('UPDATE sexos SET nombre = ? WHERE codigo = ?');
foreach ($catalog['sexos'] as $r) {
    $stSexo->execute([$r['nombre'], $r['codigo']]);
    $updated += $stSexo->rowCount();
}

$stEst = $pdo->prepare('UPDATE estados_solicitud SET nombre = ? WHERE codigo = ?');
foreach ($catalog['estados_solicitud'] as $r) {
    $stEst->execute([$r['nombre'], $r['codigo']]);
    $updated += $stEst->rowCount();
}

$stMot = $pdo->prepare('UPDATE motivos_solicitud_estudiante SET nombre = ? WHERE codigo = ?');
foreach ($catalog['motivos_solicitud_estudiante'] as $r) {
    $stMot->execute([$r['nombre'], $r['codigo']]);
    $updated += $stMot->rowCount();
}

$stPri = $pdo->prepare('UPDATE prioridades_solicitud_docente SET nombre = ? WHERE codigo = ?');
foreach ($catalog['prioridades_solicitud_docente'] as $r) {
    $stPri->execute([$r['nombre'], $r['codigo']]);
    $updated += $stPri->rowCount();
}

$stCat = $pdo->prepare('UPDATE categorias_docente SET nombre = ? WHERE codigo = ?');
foreach ($catalog['categorias_docente'] as $r) {
    $stCat->execute([$r['nombre'], $r['codigo']]);
    $updated += $stCat->rowCount();
}

$stTc = $pdo->prepare('UPDATE tipos_contrato_docente SET nombre = ? WHERE codigo = ?');
foreach ($catalog['tipos_contrato_docente'] as $r) {
    $stTc->execute([$r['nombre'], $r['codigo']]);
    $updated += $stTc->rowCount();
}

$stDec = $pdo->prepare('UPDATE decisiones_resolucion_formal SET nombre = ? WHERE codigo = ?');
foreach ($catalog['decisiones_resolucion_formal'] as $r) {
    $stDec->execute([$r['nombre'], $r['codigo']]);
    $updated += $stDec->rowCount();
}

$stProg = $pdo->prepare('UPDATE programas SET codigo = ?, nombre = ?, id_sede = ? WHERE id_programa = ?');
$programaLabels = [];
foreach ($catalog['programas'] as $r) {
    $stProg->execute([$r['codigo'], $r['nombre'], $r['id_sede'], $r['id']]);
    $updated += $stProg->rowCount();
    $programaLabels[(int) $r['id']] = '[' . $r['codigo'] . '] ' . $r['nombre'];
}

$stTsEst = $pdo->prepare('UPDATE tipos_solicitud_estudiante SET codigo = ?, nombre = ? WHERE id_tipo_solicitud = ?');
foreach ($catalog['tipos_solicitud_estudiante'] as $r) {
    $stTsEst->execute([$r['codigo'], $r['nombre'], $r['id']]);
    $updated += $stTsEst->rowCount();
}

$stTsDoc = $pdo->prepare('UPDATE tipos_solicitud_docente SET codigo = ?, nombre = ? WHERE id_tipo_solicitud_docente = ?');
foreach ($catalog['tipos_solicitud_docente'] as $r) {
    $stTsDoc->execute([$r['codigo'], $r['nombre'], $r['id']]);
    $updated += $stTsDoc->rowCount();
}

// --- Campos denormalizados en tablas transaccionales ---
$stEstProg = $pdo->prepare('UPDATE estudiantes SET programa = ? WHERE id_estudiante = ? AND id_programa = ?');
foreach ($pdo->query('SELECT id_estudiante, id_programa FROM estudiantes WHERE id_programa IS NOT NULL') as $row) {
    $idProg = (int) $row['id_programa'];
    if (!isset($programaLabels[$idProg])) {
        continue;
    }
    $stEstProg->execute([$programaLabels[$idProg], (int) $row['id_estudiante'], $idProg]);
    $updated += $stEstProg->rowCount();
}

$stDocProg = $pdo->prepare('UPDATE docentes SET programa = ? WHERE id_docente = ? AND id_programa = ?');
foreach ($pdo->query('SELECT id_docente, id_programa FROM docentes WHERE id_programa IS NOT NULL') as $row) {
    $idProg = (int) $row['id_programa'];
    if (!isset($programaLabels[$idProg])) {
        continue;
    }
    $stDocProg->execute([$programaLabels[$idProg], (int) $row['id_docente'], $idProg]);
    $updated += $stDocProg->rowCount();
}

$motivoLabels = [];
foreach ($catalog['motivos_solicitud_estudiante'] as $m) {
    $motivoLabels[$m['codigo']] = $m['nombre'];
}

$stDetProg = $pdo->prepare(
    'UPDATE solicitud_detalle_estudiante SET programa_nombre = ? WHERE id_solicitud = ? AND id_programa = ?'
);
$stDetMot = $pdo->prepare(
    'UPDATE solicitud_detalle_estudiante SET motivo_label = ? WHERE id_solicitud = ? AND motivo = ?'
);
foreach ($pdo->query('SELECT id_solicitud, id_programa, motivo FROM solicitud_detalle_estudiante') as $row) {
    $idSol = (int) $row['id_solicitud'];
    $idProg = (int) ($row['id_programa'] ?? 0);
    if ($idProg > 0 && isset($programaLabels[$idProg])) {
        $stDetProg->execute([$programaLabels[$idProg], $idSol, $idProg]);
        $updated += $stDetProg->rowCount();
    }
    $mot = (string) ($row['motivo'] ?? '');
    if ($mot !== '' && isset($motivoLabels[$mot])) {
        $stDetMot->execute([$motivoLabels[$mot], $idSol, $mot]);
        $updated += $stDetMot->rowCount();
    }
}

$categoriaLabels = [];
foreach ($catalog['categorias_docente'] as $c) {
    $categoriaLabels[$c['codigo']] = $c['nombre'];
}
$contratoLabels = [];
foreach ($catalog['tipos_contrato_docente'] as $c) {
    $contratoLabels[$c['codigo']] = $c['nombre'];
}
$prioridadLabels = [];
foreach ($catalog['prioridades_solicitud_docente'] as $p) {
    $prioridadLabels[$p['codigo']] = $p['nombre'];
}

$stDetCat = $pdo->prepare(
    'UPDATE solicitud_detalle_docente SET categoria_docente_label = ? WHERE id_solicitud = ? AND categoria_docente = ?'
);
$stDetCon = $pdo->prepare(
    'UPDATE solicitud_detalle_docente SET tipo_contrato_label = ? WHERE id_solicitud = ? AND tipo_contrato = ?'
);
$stDetPri = $pdo->prepare(
    'UPDATE solicitud_detalle_docente SET prioridad_label = ? WHERE id_solicitud = ? AND prioridad = ?'
);
foreach ($pdo->query(
    'SELECT id_solicitud, categoria_docente, tipo_contrato, prioridad FROM solicitud_detalle_docente'
) as $row) {
    $idSol = (int) $row['id_solicitud'];
    $cat = (string) ($row['categoria_docente'] ?? '');
    if ($cat !== '' && isset($categoriaLabels[$cat])) {
        $stDetCat->execute([$categoriaLabels[$cat], $idSol, $cat]);
        $updated += $stDetCat->rowCount();
    }
    $con = (string) ($row['tipo_contrato'] ?? '');
    if ($con !== '' && isset($contratoLabels[$con])) {
        $stDetCon->execute([$contratoLabels[$con], $idSol, $con]);
        $updated += $stDetCon->rowCount();
    }
    $pri = (string) ($row['prioridad'] ?? '');
    if ($pri !== '' && isset($prioridadLabels[$pri])) {
        $stDetPri->execute([$prioridadLabels[$pri], $idSol, $pri]);
        $updated += $stDetPri->rowCount();
    }
}

// --- Verificación final: ningún campo de texto con ?? ---
$remaining = [];
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $table) {
    $cols = $pdo->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        $type = strtolower((string) $c['Type']);
        if (!preg_match('/^(varchar|char|text|tinytext|mediumtext|longtext)/', $type)) {
            continue;
        }
        $col = $c['Field'];
        $cnt = (int) $pdo->query("SELECT COUNT(*) FROM `$table` WHERE `$col` LIKE '%??%'")->fetchColumn();
        if ($cnt > 0) {
            $remaining[] = "$table.$col ($cnt filas)";
        }
    }
}

echo "Filas actualizadas (aprox.): $updated\n";
if ($remaining === []) {
    echo "OK: no quedan textos con ?? en ninguna tabla.\n";
} else {
    echo "AVISO: aún hay campos con ??:\n";
    foreach ($remaining as $r) {
        echo "  - $r\n";
    }
    exit(1);
}
