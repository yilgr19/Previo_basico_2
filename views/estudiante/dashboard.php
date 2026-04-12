<?php
/**
 * Vista: panel estudiante.
 * Variables: $yo, $mensaje, $matsEst, $solicitudes
 */
?>
<main class="container pb-5">
  <section class="hero-welcome">
    <h1>Hola, <?= h(explode(' ', (string) ($yo['nombre'] ?? 'Estudiante'))[0]) ?></h1>
    <p class="mb-0 text-secondary">Consulta tu matrícula y envía solicitudes académicas según el diccionario de datos.</p>
  </section>

  <?php if ($mensaje): ?>
    <div class="alert alert-info"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h2 class="h6 form-section-title">Mis datos</h2>
          <p class="small mb-1"><strong>Identificación:</strong> <?= h(tipo_identificacion_nombre((string) ($yo['tipo_identificacion'] ?? ''))) ?> <?= h($yo['documento'] ?? '') ?></p>
          <p class="small mb-1"><strong>Correo:</strong> <?= h($yo['correo'] ?? '') ?></p>
          <p class="small mb-1"><strong>Sexo:</strong> <?= h(sexo_nombre((string) ($yo['sexo'] ?? ''))) ?></p>
          <p><strong>Programa:</strong> <?= h($yo['programa'] ?? '') ?></p>
          <p class="mb-1"><strong>Semestre:</strong> <?= h((string) ($yo['semestre'] ?? '')) ?> ·
            <strong>Sede:</strong> <?= h(sede_nombre(isset($yo['id_sede']) ? (int) $yo['id_sede'] : null)) ?> ·
            <strong>Jornada:</strong> <?= h(jornada_nombre(isset($yo['id_jornada']) ? (int) $yo['id_jornada'] : null)) ?>
          </p>
          <?php if (!empty($yo['fecha_nacimiento'])): ?>
            <p class="small mb-1"><strong>Fecha de nacimiento:</strong> <?= h($yo['fecha_nacimiento'] ?? '') ?>
              <?php if (isset($yo['edad'])): ?> · <strong>Edad:</strong> <?= (int) $yo['edad'] ?> años<?php endif; ?>
            </p>
          <?php endif; ?>
          <?php if (trim((string) ($yo['direccion'] ?? '')) !== '' || trim((string) ($yo['barrio'] ?? '')) !== '' || trim((string) ($yo['telefono'] ?? '')) !== ''): ?>
            <p class="small mb-1"><strong>Dirección:</strong> <?= h($yo['direccion'] ?? '') ?></p>
            <p class="small mb-0"><strong>Barrio:</strong> <?= h($yo['barrio'] ?? '') ?> · <strong>Teléfono:</strong> <?= h($yo['telefono'] ?? '') ?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <h2 class="h6 form-section-title">Asignaturas matriculadas</h2>
          <?php if (!$matsEst): ?>
            <p class="text-muted mb-0">Aún no tiene matrículas registradas por administración.</p>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($matsEst as $row): ?>
                <?php
                $x = $row['matricula'];
                $mat = $row['materia'];
                ?>
                <li class="list-group-item px-0 border-0 border-bottom">
                  <?php if ($mat): ?>
                    <div class="fw-semibold"><?= h(materia_nombre((int) ($mat['id_materia'] ?? 0))) ?></div>
                    <div class="small text-muted">
                      <?= h(materia_programa_label($mat)) ?> · <?= h(materia_horario_resumen($mat)) ?>
                      · <span class="badge bg-secondary"><?= h(materia_modalidad_etiqueta($mat)) ?></span>
                      <?php if (($mat['modalidad'] ?? '') === 'presencial' && trim((string) ($mat['salon'] ?? '')) !== ''): ?>
                        · Salón <?= h($mat['salon']) ?>
                      <?php endif; ?>
                      · Matrícula: <?= h($x['fecha'] ?? '') ?>
                    </div>
                  <?php else: ?>
                    <div class="fw-semibold text-warning">Asignatura no encontrada (ID <?= (int) ($x['id_materia'] ?? 0) ?>)</div>
                    <div class="small text-muted">Matrícula: <?= h($x['fecha'] ?? '') ?></div>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mt-4">
    <div class="card-body">
      <h2 class="h6 form-section-title">Nueva solicitud (tipos de solicitud)</h2>
      <form method="post" class="row g-3">
        <input type="hidden" name="accion" value="nueva_solicitud">
        <div class="col-md-6">
          <label class="form-label">Tipo de solicitud</label>
          <select name="id_tipo_solicitud" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php foreach (diccionario_tipos_solicitud() as $t): ?>
              <option value="<?= (int) $t['id'] ?>"><?= h($t['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Descripción</label>
          <textarea name="descripcion" class="form-control" rows="3" required placeholder="Detalle su solicitud"></textarea>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Enviar solicitud</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm mt-4">
    <div class="card-body">
      <h2 class="h6 form-section-title">Mis solicitudes</h2>
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead><tr><th>Fecha</th><th>Tipo</th><th>Estado</th><th>Descripción</th></tr></thead>
          <tbody>
            <?php foreach ($solicitudes as $s): ?>
              <tr>
                <td><?= h($s['fecha'] ?? '') ?></td>
                <td><?= h(tipo_solicitud_nombre((int) ($s['id_tipo_solicitud'] ?? 0))) ?></td>
                <td><?= h($s['estado'] ?? '') ?></td>
                <td><?= h($s['descripcion'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$solicitudes): ?>
              <tr><td colspan="4" class="text-muted">Sin solicitudes aún.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
