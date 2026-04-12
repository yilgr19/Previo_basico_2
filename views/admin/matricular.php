<?php
/** Vista: matrícula por estudiante. Variables: $mensaje, $tipoMsg, $cargado, $materiasOrdenadas */
$show = $cargado ?? null;
?>
<main class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h1 class="h4 mb-0" style="color:#0d47a1;">Matrícula de asignaturas</h1>
    <a class="btn btn-outline-secondary btn-sm" href="<?= h(url('admin/dashboard.php')) ?>">Volver</a>
  </div>
  <p class="text-secondary small mb-4">Ingrese el número de identificación del estudiante para cargar sus datos y realizar la matrícula.</p>

  <?php if ($mensaje): ?>
    <div class="alert alert-<?= h($tipoMsg) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h2 class="form-section-title h6">Datos del estudiante</h2>
      <form method="post" class="row g-3 align-items-end">
        <input type="hidden" name="accion" value="buscar">
        <div class="col-md-8">
          <label class="form-label">Número de identificación</label>
          <input type="text" name="documento_buscar" class="form-control" placeholder="Ingrese el número de identificación"
            value="<?= h(post('documento_buscar') ?? '') ?>">
        </div>
        <div class="col-md-4">
          <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
      </form>

      <?php if ($show): ?>
      <hr>
      <div class="row g-3 mt-1">
        <div class="col-md-6">
          <label class="form-label">Nombres</label>
          <input type="text" class="form-control" readonly value="<?= h($show['nombre'] ?? '') ?>" placeholder="Se cargan al validar identificación">
        </div>
        <div class="col-md-6">
          <label class="form-label">Apellidos</label>
          <input type="text" class="form-control" readonly value="<?= h($show['apellido'] ?? '') ?>" placeholder="Se cargan al validar identificación">
        </div>
        <div class="col-md-6">
          <label class="form-label">Carrera</label>
          <input type="text" class="form-control" readonly value="<?= h($show['programa'] ?? '') ?>" placeholder="Se cargan del registro">
        </div>
        <div class="col-md-6">
          <label class="form-label">Semestre</label>
          <input type="text" class="form-control" readonly value="<?= h((string) ($show['semestre'] ?? '')) ?>" placeholder="Se cargan del registro">
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($show): ?>
  <div class="card shadow-sm">
    <div class="card-body">
      <h2 class="form-section-title h6">Asignatura a matricular</h2>
      <p class="small text-muted mb-3">(Primero busque o cargue los datos del estudiante)</p>
      <form method="post" class="row g-3 align-items-end">
        <input type="hidden" name="accion" value="matricular">
        <input type="hidden" name="id_estudiante" value="<?= (int) $show['id_estudiante'] ?>">
        <div class="col-md-8">
          <label class="form-label">Asignatura</label>
          <select name="id_materia" id="selMateria" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php foreach ($materiasOrdenadas as $m): ?>
              <option value="<?= (int) $m['id_materia'] ?>" data-semestre="<?= (int) ($m['semestre'] ?? 0) ?>">
                <?php
                $lbl = ($m['codigo'] ?? '') . ' — ' . ($m['nombre'] ?? '');
                $lbl .= ' · ' . materia_programa_label($m);
                $lbl .= ' · ' . materia_horario_resumen($m);
                $lbl .= ' · ' . materia_modalidad_etiqueta($m);
                if (($m['modalidad'] ?? '') === 'presencial' && trim((string) ($m['salon'] ?? '')) !== '') {
                    $lbl .= ' · Salón ' . $m['salon'];
                }
                ?>
                <?= h($lbl) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Semestre de la asignatura</label>
          <input type="text" id="semestreMateria" class="form-control" readonly placeholder="Se carga al seleccionar la asignatura" value="">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Matricular</button>
        </div>
      </form>
    </div>
  </div>
  <script src="<?= h(asset_url('js/admin-matricular-form.js')) ?>"></script>
  <?php endif; ?>
</main>
