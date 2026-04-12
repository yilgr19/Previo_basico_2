<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_role(ROLE_ADMIN);

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'eliminar_matricula') {
    $id = (int) post('id_matricula', '0');
    $mat = load_data('matriculas');
    $mat = array_values(array_filter($mat, static fn ($m) => (int) ($m['id_matricula'] ?? 0) !== $id));
    save_data('matriculas', $mat);
    $mensaje = 'Matrícula eliminada.';
}

$estudiantes = load_data('estudiantes');
$docentes = load_data('docentes');
$materias = load_data('materias');
$materiasOrdenadas = repo_materias_ordenadas_por_codigo($materias);
$matriculas = load_data('matriculas');

$pageTitle = 'Reportes';
require PARTIALS_PATH . '/header.php';
?>
<main class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4" style="color:#0d47a1;">Reportes y gestión de registros</h1>
    <a class="btn btn-outline-secondary btn-sm" href="<?= h(url('admin/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <p class="text-secondary">Consulte lo registrado y use los accesos para editar o eliminar. Las matrículas se eliminan aquí sin borrar estudiantes ni asignaturas.</p>
  <?php if ($mensaje): ?>
    <div class="alert alert-success"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <section class="mb-5">
    <h2 class="h5 form-section-title">Estudiantes</h2>
    <div class="table-responsive card shadow-sm">
      <table class="table table-sm mb-0">
        <thead class="table-light"><tr><th>ID</th><th>Documento</th><th>Nombre</th><th>Programa</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($estudiantes as $e): ?>
            <tr>
              <td><?= (int) $e['id_estudiante'] ?></td>
              <td><?= h($e['documento'] ?? '') ?></td>
              <td><?= h(trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? ''))) ?></td>
              <td class="small"><?= h($e['programa'] ?? '') ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="<?= h(url('admin/estudiantes.php?editar=' . (int) $e['id_estudiante'])) ?>">Editar</a></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$estudiantes): ?><tr><td colspan="5" class="text-muted">Sin registros.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="mb-5">
    <h2 class="h5 form-section-title">Docentes</h2>
    <div class="table-responsive card shadow-sm">
      <table class="table table-sm mb-0">
        <thead class="table-light"><tr><th>ID</th><th>Documento</th><th>Nombre</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($docentes as $d): ?>
            <tr>
              <td><?= (int) $d['id_docente'] ?></td>
              <td><?= h($d['documento'] ?? '') ?></td>
              <td><?= h(trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? ''))) ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="<?= h(url('admin/docentes.php?editar=' . (int) $d['id_docente'])) ?>">Editar</a></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$docentes): ?><tr><td colspan="4" class="text-muted">Sin registros.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="mb-5">
    <h2 class="h5 form-section-title">Asignaturas</h2>
    <div class="table-responsive card shadow-sm">
      <table class="table table-sm mb-0">
        <thead class="table-light"><tr><th>ID</th><th>Código</th><th>Nombre</th><th>Modalidad</th><th>Salón</th><th>Docente</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($materiasOrdenadas as $m): ?>
            <tr>
              <td><?= (int) $m['id_materia'] ?></td>
              <td><?= h($m['codigo'] ?? '') ?></td>
              <td><?= h($m['nombre'] ?? '') ?></td>
              <td><?= h(materia_modalidad_etiqueta($m)) ?></td>
              <td><?= h((string) ($m['modalidad'] ?? '') === 'presencial' ? ($m['salon'] ?? '') : '—') ?></td>
              <td><?= h(docente_nombre((int) ($m['id_docente'] ?? 0))) ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="<?= h(url('admin/materias.php?editar=' . (int) $m['id_materia'])) ?>">Editar</a></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$materiasOrdenadas): ?><tr><td colspan="7" class="text-muted">Sin registros.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="mb-3">
    <h2 class="h5 form-section-title">Matrículas por asignatura</h2>
    <p class="small text-secondary">Cada bloque corresponde a una asignatura; dentro se listan los estudiantes matriculados.</p>
    <?php foreach ($materiasOrdenadas as $mat): ?>
      <?php
      $idMat = (int) ($mat['id_materia'] ?? 0);
      $enEsta = array_values(array_filter($matriculas, static fn ($x) => (int) ($x['id_materia'] ?? 0) === $idMat));
      ?>
      <div class="card shadow-sm mb-4">
        <div class="card-header py-2 bg-light">
          <strong><?= h(($mat['codigo'] ?? '') . ' — ' . ($mat['nombre'] ?? '')) ?></strong>
          <span class="text-muted small ms-2">
            <?= h(materia_modalidad_etiqueta($mat)) ?>
            <?php if (($mat['modalidad'] ?? '') === 'presencial' && trim((string) ($mat['salon'] ?? '')) !== ''): ?>
              · Salón <?= h($mat['salon']) ?>
            <?php endif; ?>
            · Docente: <?= h(docente_nombre((int) ($mat['id_docente'] ?? 0))) ?>
          </span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead class="table-light">
                <tr><th>ID matrícula</th><th>Fecha</th><th>Estudiante</th><th></th></tr>
              </thead>
              <tbody>
                <?php foreach ($enEsta as $x): ?>
                  <tr>
                    <td><?= (int) $x['id_matricula'] ?></td>
                    <td><?= h($x['fecha'] ?? '') ?></td>
                    <td><?= h(estudiante_nombre_completo((int) ($x['id_estudiante'] ?? 0))) ?> <span class="text-muted">(#<?= (int) ($x['id_estudiante'] ?? 0) ?>)</span></td>
                    <td class="text-nowrap">
                      <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta matrícula?');">
                        <input type="hidden" name="accion" value="eliminar_matricula">
                        <input type="hidden" name="id_matricula" value="<?= (int) $x['id_matricula'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if (!$enEsta): ?>
                  <tr><td colspan="4" class="text-muted py-3">Sin matrículas en esta asignatura.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php
    $idsMat = array_map(static fn ($m) => (int) ($m['id_materia'] ?? 0), $materias);
    $huerfanas = array_values(array_filter($matriculas, static function ($x) use ($idsMat) {
        return !in_array((int) ($x['id_materia'] ?? 0), $idsMat, true);
    }));
    ?>
    <?php if ($huerfanas): ?>
      <div class="alert alert-warning">
        <strong>Matrículas sin asignatura asociada</strong> (ID de materia inexistente):
        <ul class="mb-0 mt-2">
          <?php foreach ($huerfanas as $x): ?>
            <li>
              Matrícula #<?= (int) $x['id_matricula'] ?> — materia ID <?= (int) ($x['id_materia'] ?? 0) ?>
              <form method="post" class="d-inline ms-2" onsubmit="return confirm('¿Eliminar?');">
                <input type="hidden" name="accion" value="eliminar_matricula">
                <input type="hidden" name="id_matricula" value="<?= (int) $x['id_matricula'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <?php if (!$materiasOrdenadas && !$matriculas): ?>
      <p class="text-muted">No hay asignaturas ni matrículas registradas.</p>
    <?php endif; ?>
  </section>
</main>
<?php require PARTIALS_PATH . '/footer.php'; ?>
