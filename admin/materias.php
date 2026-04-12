<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_role(ROLE_ADMIN);

$mensaje = '';
$materias = load_data('materias');
$docentes = load_data('docentes');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = post('accion', '');
    if ($accion === 'eliminar') {
        $id = (int) post('id_materia', '0');
        $mat = load_data('matriculas');
        foreach ($mat as $x) {
            if ((int) ($x['id_materia'] ?? 0) === $id) {
                $mensaje = 'No se puede eliminar: existen matrículas en esta asignatura.';
                break;
            }
        }
        if ($mensaje === '') {
            $materias = array_values(array_filter($materias, static fn ($m) => (int) ($m['id_materia'] ?? 0) !== $id));
            save_data('materias', $materias);
            $mensaje = 'Asignatura eliminada.';
        }
    } elseif ($accion === 'guardar') {
        $idDoc = (int) post('id_docente', '0');
        $modalidad = post('modalidad', 'virtual');
        if (!in_array($modalidad, ['presencial', 'virtual'], true)) {
            $modalidad = 'virtual';
        }
        $salon = trim((string) post('salon', ''));
        if ($modalidad === 'virtual') {
            $salon = '';
        }
        $row = [
            'codigo' => post('codigo', ''),
            'nombre' => post('nombre', ''),
            'creditos' => (int) post('creditos', '0'),
            'semestre' => (int) post('semestre', '1'),
            'id_docente' => $idDoc,
            'modalidad' => $modalidad,
            'salon' => $salon,
        ];
        if ($modalidad === 'presencial' && $salon === '') {
            $mensaje = 'En modalidad presencial debe indicar el salón o aula.';
        }
        $editId = (int) post('id_materia', '0');
        if ($mensaje === '') {
            if ($editId > 0) {
                foreach ($materias as &$m) {
                    if ((int) ($m['id_materia'] ?? 0) === $editId) {
                        $m = array_merge($m, $row);
                        break;
                    }
                }
                unset($m);
                $mensaje = 'Asignatura actualizada.';
            } else {
                $row['id_materia'] = next_numeric_id($materias, 'id_materia');
                $materias[] = $row;
                $mensaje = 'Asignatura creada.';
            }
            save_data('materias', $materias);
        }
    }
}

$materias = load_data('materias');
$docentes = load_data('docentes');
$editar = null;
$eid = (int) (get('editar') ?? '0');
if ($eid > 0) {
    $editar = repo_materia_por_id($eid);
}

$pageTitle = 'Asignaturas';
require PARTIALS_PATH . '/header.php';
?>
<main class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4" style="color:#0d47a1;">Catálogo de asignaturas</h1>
    <a class="btn btn-outline-secondary btn-sm" href="<?= h(url('admin/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="alert <?= (strpos($mensaje, 'No se puede') !== false || strpos($mensaje, 'debe indicar') !== false) ? 'alert-warning' : 'alert-success' ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h2 class="form-section-title h6"><?= $editar ? 'Editar asignatura' : 'Nueva asignatura' ?></h2>
      <p class="small text-muted">En el mismo registro se asigna el docente que dicta la materia.</p>
      <form method="post" class="row g-3">
        <input type="hidden" name="accion" value="guardar">
        <?php if ($editar): ?>
          <input type="hidden" name="id_materia" value="<?= (int) $editar['id_materia'] ?>">
        <?php endif; ?>
        <div class="col-md-4">
          <label class="form-label">Código</label>
          <input type="text" name="codigo" class="form-control" required value="<?= h($editar['codigo'] ?? '') ?>">
        </div>
        <div class="col-md-8">
          <label class="form-label">Nombre de la asignatura</label>
          <input type="text" name="nombre" class="form-control" required value="<?= h($editar['nombre'] ?? '') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Créditos</label>
          <input type="number" name="creditos" class="form-control" min="1" max="30" required value="<?= h((string) ($editar['creditos'] ?? '3')) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Semestre de la asignatura</label>
          <input type="number" name="semestre" class="form-control" min="1" max="20" required value="<?= h((string) ($editar['semestre'] ?? '1')) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Docente asignado</label>
          <select name="id_docente" class="form-select" required>
            <option value="">Seleccione docente...</option>
            <?php foreach ($docentes as $d): ?>
              <option value="<?= (int) $d['id_docente'] ?>" <?= isset($editar['id_docente']) && (int) $editar['id_docente'] === (int) $d['id_docente'] ? 'selected' : '' ?>>
                <?= h(trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? '')) . ' — ' . ($d['documento'] ?? '')) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php
        $modVal = (string) ($editar['modalidad'] ?? 'virtual');
        if (!in_array($modVal, ['presencial', 'virtual'], true)) {
            $modVal = 'virtual';
        }
        ?>
        <div class="col-md-4">
          <label class="form-label">Modalidad</label>
          <select name="modalidad" id="fld-modalidad" class="form-select" required>
            <option value="virtual" <?= $modVal === 'virtual' ? 'selected' : '' ?>>Virtual</option>
            <option value="presencial" <?= $modVal === 'presencial' ? 'selected' : '' ?>>Presencial</option>
          </select>
        </div>
        <div class="col-md-4" id="salon-wrap">
          <label class="form-label">Salón / aula</label>
          <input type="text" name="salon" id="fld-salon" class="form-control" maxlength="80"
            placeholder="Ej. A-301, Lab. 2"
            value="<?= h((string) ($editar['salon'] ?? '')) ?>">
          <div class="form-text">Obligatorio solo si la modalidad es presencial.</div>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary"><?= $editar ? 'Actualizar' : 'Guardar' ?></button>
          <?php if ($editar): ?>
            <a class="btn btn-secondary" href="<?= h(url('admin/materias.php')) ?>">Cancelar edición</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <h2 class="h6 form-section-title">Listado</h2>
  <div class="table-responsive card shadow-sm">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Código</th>
          <th>Asignatura</th>
          <th>Créd.</th>
          <th>Sem.</th>
          <th>Modalidad</th>
          <th>Salón</th>
          <th>Docente</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($materias as $m): ?>
          <tr>
            <td><?= (int) $m['id_materia'] ?></td>
            <td><?= h($m['codigo'] ?? '') ?></td>
            <td><?= h($m['nombre'] ?? '') ?></td>
            <td><?= (int) ($m['creditos'] ?? 0) ?></td>
            <td><?= (int) ($m['semestre'] ?? 0) ?></td>
            <td><?= h(materia_modalidad_etiqueta($m)) ?></td>
            <td><?= h((string) ($m['modalidad'] ?? '') === 'presencial' ? ($m['salon'] ?? '') : '—') ?></td>
            <td><?= h(docente_nombre((int) ($m['id_docente'] ?? 0))) ?></td>
            <td class="table-actions text-nowrap">
              <a class="btn btn-sm btn-outline-primary" href="<?= h(url('admin/materias.php?editar=' . (int) $m['id_materia'])) ?>">Editar</a>
              <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar esta asignatura?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_materia" value="<?= (int) $m['id_materia'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$materias): ?>
          <tr><td colspan="9" class="text-center text-muted py-4">No hay asignaturas. Registre docentes primero.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<script>
(function () {
  function syncSalon() {
    var m = document.getElementById('fld-modalidad');
    var s = document.getElementById('fld-salon');
    var wrap = document.getElementById('salon-wrap');
    if (!m || !s || !wrap) return;
    var pres = m.value === 'presencial';
    s.required = pres;
    if (!pres) {
      s.value = '';
      s.setAttribute('disabled', 'disabled');
    } else {
      s.removeAttribute('disabled');
    }
  }
  var sel = document.getElementById('fld-modalidad');
  if (sel) {
    sel.addEventListener('change', syncSalon);
    syncSalon();
  }
})();
</script>
<?php require PARTIALS_PATH . '/footer.php'; ?>
