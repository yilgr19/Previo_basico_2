<?php
/** Vista: CRUD docentes. Variables: $mensaje, $docentes, $editar */
$ef = $editar ?? [];
$idSedeForm = isset($ef['id_sede']) && (int) $ef['id_sede'] > 0 ? (int) $ef['id_sede'] : docente_sede_efectiva($ef);
?>
<main class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4" style="color:#0d47a1;">Registro de docentes</h1>
    <a class="btn btn-outline-secondary btn-sm" href="<?= h(url('admin/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="alert <?= (strpos($mensaje, 'No se puede') !== false || strpos($mensaje, 'Seleccione la carrera') !== false || strpos($mensaje, 'Seleccione la sede') !== false || strpos($mensaje, 'no corresponde a la sede') !== false) ? 'alert-warning' : 'alert-success' ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h2 class="form-section-title h6"><?= $editar ? 'Editar docente' : 'Nuevo docente' ?></h2>
      <form method="post" class="row g-3">
        <input type="hidden" name="accion" value="guardar">
        <?php if ($editar): ?>
          <input type="hidden" name="id_docente" value="<?= (int) $ef['id_docente'] ?>">
        <?php endif; ?>
        <div class="col-md-6">
          <label class="form-label">Nombres</label>
          <input type="text" name="nombre" class="form-control" required value="<?= h($ef['nombre'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Apellidos</label>
          <input type="text" name="apellido" class="form-control" required value="<?= h($ef['apellido'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Documento</label>
          <input type="text" name="documento" class="form-control" required value="<?= h($ef['documento'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Correo</label>
          <input type="email" name="correo" class="form-control" required value="<?= h($ef['correo'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control" value="<?= h($ef['telefono'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Sede</label>
          <select name="id_sede" id="fld-sede-docente" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php foreach (diccionario_sedes() as $s): ?>
              <option value="<?= (int) $s['id'] ?>" <?= $idSedeForm === (int) $s['id'] ? 'selected' : '' ?>><?= h($s['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Indica si el docente pertenece a la sede Cúcuta u Ocaña.</div>
        </div>
        <div class="col-12">
          <label class="form-label">Carrera a la que dicta clase</label>
          <select name="id_programa" id="fld-programa-docente" class="form-select" required>
            <option value="">Seleccione la carrera...</option>
            <?php foreach (diccionario_programas() as $p): ?>
              <option value="<?= (int) $p['id'] ?>" data-sede="<?= (int) ($p['id_sede'] ?? 1) ?>"
                <?= (int) ($ef['id_programa'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
                <?= h('[' . $p['codigo'] . '] ' . $p['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Las carreras se filtran según la sede elegida (cada programa está asociado a Cúcuta u Ocaña en el diccionario de datos).</div>
        </div>
        <div class="col-12">
          <label class="form-label">Contraseña de acceso <?= $editar ? '(vacío = sin cambio)' : '' ?></label>
          <input type="password" name="clave" class="form-control" autocomplete="new-password" placeholder="<?= $editar ? 'Sin cambios' : 'Por defecto doc123' ?>">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary"><?= $editar ? 'Actualizar' : 'Registrar' ?></button>
          <?php if ($editar): ?>
            <a class="btn btn-secondary" href="<?= h(url('admin/docentes.php')) ?>">Cancelar edición</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>
  <script src="<?= h(asset_url('js/admin-docentes-form.js')) ?>"></script>

  <h2 class="h6 form-section-title">Listado</h2>
  <div class="table-responsive card shadow-sm">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Documento</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Sede</th>
          <th>Carrera</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($docentes as $d): ?>
          <tr>
            <td><?= (int) $d['id_docente'] ?></td>
            <td><?= h($d['documento'] ?? '') ?></td>
            <td><?= h(trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? ''))) ?></td>
            <td><?= h($d['correo'] ?? '') ?></td>
            <td><?= h(sede_nombre(docente_sede_efectiva($d))) ?></td>
            <td class="small"><?= h($d['programa'] ?? (isset($d['id_programa']) ? programa_label_by_id((int) $d['id_programa']) : '')) ?></td>
            <td class="table-actions text-nowrap">
              <a class="btn btn-sm btn-outline-primary" href="<?= h(url('admin/docentes.php?editar=' . (int) $d['id_docente'])) ?>">Editar</a>
              <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar este docente?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_docente" value="<?= (int) $d['id_docente'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$docentes): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No hay docentes registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
