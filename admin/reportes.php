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
        <thead class="table-light"><tr><th>ID</th><th>Código</th><th>Nombre</th><th>Docente</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($materias as $m): ?>
            <tr>
              <td><?= (int) $m['id_materia'] ?></td>
              <td><?= h($m['codigo'] ?? '') ?></td>
              <td><?= h($m['nombre'] ?? '') ?></td>
              <td><?= h(docente_nombre((int) ($m['id_docente'] ?? 0))) ?></td>
              <td><a class="btn btn-sm btn-outline-primary" href="<?= h(url('admin/materias.php?editar=' . (int) $m['id_materia'])) ?>">Editar</a></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$materias): ?><tr><td colspan="5" class="text-muted">Sin registros.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="mb-3">
    <h2 class="h5 form-section-title">Matrículas</h2>
    <div class="table-responsive card shadow-sm">
      <table class="table table-sm mb-0">
        <thead class="table-light">
          <tr><th>ID</th><th>Fecha</th><th>Estudiante</th><th>Asignatura</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($matriculas as $x): ?>
            <tr>
              <td><?= (int) $x['id_matricula'] ?></td>
              <td><?= h($x['fecha'] ?? '') ?></td>
              <td><?= h(estudiante_nombre_completo((int) ($x['id_estudiante'] ?? 0))) ?> <span class="text-muted">(#<?= (int) ($x['id_estudiante'] ?? 0) ?>)</span></td>
              <td><?= h(materia_nombre((int) ($x['id_materia'] ?? 0))) ?></td>
              <td>
                <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta matrícula?');">
                  <input type="hidden" name="accion" value="eliminar_matricula">
                  <input type="hidden" name="id_matricula" value="<?= (int) $x['id_matricula'] ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar matrícula</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$matriculas): ?><tr><td colspan="5" class="text-muted">Sin matrículas.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
<?php require PARTIALS_PATH . '/footer.php'; ?>
