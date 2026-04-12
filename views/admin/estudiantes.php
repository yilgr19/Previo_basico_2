<?php
/**
 * Vista: registro de estudiantes (formulario ampliado).
 * Variables: $mensaje, $tipoMsg, $estudiantes, $editar
 */
$defaults = [
    'tipo_identificacion' => '',
    'documento' => '',
    'nombre' => '',
    'apellido' => '',
    'correo' => '',
    'sexo' => '',
    'id_programa' => 0,
    'semestre' => 1,
    'fecha_nacimiento' => '',
    'direccion' => '',
    'barrio' => '',
    'telefono' => '',
    'id_sede' => 1,
    'id_jornada' => 1,
];
$ef = array_merge($defaults, $editar ?? []);
$edadMostrar = '';
if (!empty($ef['fecha_nacimiento'])) {
    $ea = calcular_edad_desde_fecha_ymd((string) $ef['fecha_nacimiento']);
    if ($ea !== null) {
        $edadMostrar = $ea . ' años';
    }
}
?>
<main class="container pb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0" style="color:#0d47a1;">Registrar estudiante</h1>
    <a class="btn btn-outline-secondary btn-sm" href="<?= h(url('admin/dashboard.php')) ?>">Volver al panel</a>
  </div>

  <?php if ($mensaje): ?>
    <div class="alert alert-<?= h($tipoMsg) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm mb-4 border-0">
    <div class="bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-top px-3 py-3">
      <h2 class="h5 mb-1" style="color:#0d47a1;">Registro de estudiante</h2>
      <p class="small text-secondary mb-0">Complete los datos del estudiante para realizar el registro.</p>
    </div>
    <div class="card-body pt-4">
      <form method="post" class="row g-3" id="form-estudiante" autocomplete="off">
        <input type="hidden" name="accion" value="guardar">
        <?php if ($editar): ?>
          <input type="hidden" name="id_estudiante" value="<?= (int) $ef['id_estudiante'] ?>">
        <?php endif; ?>

        <div class="col-md-6">
          <label class="form-label">Tipo de identificación</label>
          <select name="tipo_identificacion" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php foreach (diccionario_tipos_identificacion() as $t): ?>
              <option value="<?= h($t['codigo']) ?>" <?= ($ef['tipo_identificacion'] ?? '') === $t['codigo'] ? 'selected' : '' ?>><?= h($t['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Número de identificación</label>
          <input type="text" name="documento" class="form-control" required placeholder="Ingrese el número"
            value="<?= h((string) ($ef['documento'] ?? '')) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Nombres</label>
          <input type="text" name="nombre" class="form-control" required placeholder="Ingrese los nombres"
            value="<?= h((string) ($ef['nombre'] ?? '')) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Apellidos</label>
          <input type="text" name="apellido" class="form-control" required placeholder="Ingrese los apellidos"
            value="<?= h((string) ($ef['apellido'] ?? '')) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Correo</label>
          <input type="email" name="correo" class="form-control" required placeholder="ejemplo@correo.com"
            value="<?= h((string) ($ef['correo'] ?? '')) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Sexo</label>
          <select name="sexo" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php foreach (diccionario_sexo() as $s): ?>
              <option value="<?= h($s['codigo']) ?>" <?= ($ef['sexo'] ?? '') === $s['codigo'] ? 'selected' : '' ?>><?= h($s['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Carrera que cursa</label>
          <select name="id_programa" class="form-select" required>
            <option value="">Seleccione la carrera...</option>
            <?php foreach (diccionario_programas() as $p): ?>
              <option value="<?= (int) $p['id'] ?>" <?= (int) ($ef['id_programa'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
                <?= h('[' . $p['codigo'] . '] ' . $p['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Semestre</label>
          <select name="semestre" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php for ($s = 1; $s <= 10; $s++): ?>
              <option value="<?= $s ?>" <?= (int) ($ef['semestre'] ?? 1) === $s ? 'selected' : '' ?>><?= $s ?>°</option>
            <?php endfor; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Fecha de nacimiento</label>
          <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="form-control" required
            value="<?= h((string) ($ef['fecha_nacimiento'] ?? '')) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Edad</label>
          <input type="text" id="campo_edad" class="form-control bg-light" readonly placeholder="Se calcula automáticamente"
            value="<?= h($edadMostrar) ?>">
        </div>

        <div class="col-12">
          <label class="form-label">Dirección</label>
          <input type="text" name="direccion" class="form-control" required placeholder="Ingrese la dirección"
            value="<?= h((string) ($ef['direccion'] ?? '')) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Barrio</label>
          <input type="text" name="barrio" class="form-control" required placeholder="Ingrese el barrio"
            value="<?= h((string) ($ef['barrio'] ?? '')) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <input type="text" name="telefono" class="form-control" required placeholder="Ingrese el teléfono"
            value="<?= h((string) ($ef['telefono'] ?? '')) ?>">
        </div>

        <div class="col-md-6">
          <label class="form-label">Sede</label>
          <select name="id_sede" class="form-select" required>
            <?php foreach (diccionario_sedes() as $s): ?>
              <option value="<?= (int) $s['id'] ?>" <?= (int) ($ef['id_sede'] ?? 1) === (int) $s['id'] ? 'selected' : '' ?>><?= h($s['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Jornada</label>
          <select name="id_jornada" class="form-select" required>
            <?php foreach (diccionario_jornadas() as $j): ?>
              <option value="<?= (int) $j['id'] ?>" <?= (int) ($ef['id_jornada'] ?? 1) === (int) $j['id'] ? 'selected' : '' ?>><?= h($j['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Contraseña <?= $editar ? '(vacío = sin cambio)' : '' ?></label>
          <input type="password" name="clave" class="form-control" autocomplete="new-password" placeholder="Ingrese la contraseña">
        </div>
        <div class="col-md-6">
          <label class="form-label">Confirmar contraseña</label>
          <input type="password" name="clave_confirmar" class="form-control" autocomplete="new-password" placeholder="Repita la contraseña">
        </div>

        <div class="col-12 pt-2">
          <button type="submit" class="btn btn-primary px-4"><?= $editar ? 'Actualizar estudiante' : 'Registrar estudiante' ?></button>
          <?php if ($editar): ?>
            <a class="btn btn-outline-secondary" href="<?= h(url('admin/estudiantes.php')) ?>">Cancelar edición</a>
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
          <th>Identificación</th>
          <th>Nombre</th>
          <th>Programa</th>
          <th>Sem.</th>
          <th>Sede</th>
          <th>Jornada</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($estudiantes as $e): ?>
          <tr>
            <td><?= (int) $e['id_estudiante'] ?></td>
            <td class="small"><?= h(tipo_identificacion_nombre((string) ($e['tipo_identificacion'] ?? ''))) ?><br><span class="text-muted"><?= h($e['documento'] ?? '') ?></span></td>
            <td><?= h(trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? ''))) ?></td>
            <td class="small"><?= h($e['programa'] ?? '') ?></td>
            <td><?= (int) ($e['semestre'] ?? 0) ?></td>
            <td><?= h(sede_nombre(isset($e['id_sede']) ? (int) $e['id_sede'] : null)) ?></td>
            <td><?= h(jornada_nombre(isset($e['id_jornada']) ? (int) $e['id_jornada'] : null)) ?></td>
            <td class="table-actions text-nowrap">
              <a class="btn btn-sm btn-outline-primary" href="<?= h(url('admin/estudiantes.php?editar=' . (int) $e['id_estudiante'])) ?>">Editar</a>
              <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar este estudiante y sus matrículas?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_estudiante" value="<?= (int) $e['id_estudiante'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$estudiantes): ?>
          <tr><td colspan="8" class="text-center text-muted py-4">No hay estudiantes registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<script src="<?= h(asset_url('js/admin-estudiantes-form.js')) ?>"></script>
