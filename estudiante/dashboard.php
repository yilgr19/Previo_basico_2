<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_role(ROLE_ESTUDIANTE);

$idEst = auth_id();
if (!$idEst) {
    redirect('/login.php');
}
$yo = repo_estudiante_por_id($idEst);
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && post('accion', '') === 'nueva_solicitud') {
    $tipo = (int) post('id_tipo_solicitud', '0');
    $desc = post('descripcion', '');
    if ($tipo > 0 && $desc !== '') {
        $sol = load_data('solicitudes');
        $sol[] = [
            'id_solicitud' => next_numeric_id($sol, 'id_solicitud'),
            'fecha' => date('Y-m-d'),
            'estado' => 'En revisión',
            'descripcion' => $desc,
            'id_estudiante' => $idEst,
            'id_tipo_solicitud' => $tipo,
        ];
        save_data('solicitudes', $sol);
        $mensaje = 'Solicitud registrada.';
    } else {
        $mensaje = 'Complete tipo y descripción.';
    }
}

$solicitudes = array_values(array_filter(load_data('solicitudes'), static fn ($s) => (int) ($s['id_estudiante'] ?? 0) === $idEst));
$matriculas = repo_matriculas_de_estudiante($idEst);
$matsEst = [];
foreach ($matriculas as $x) {
    $mid = (int) ($x['id_materia'] ?? 0);
    $m = repo_materia_por_id($mid);
    $matsEst[] = ['matricula' => $x, 'materia' => $m];
}
usort($matsEst, static function ($a, $b) {
    $ca = $a['materia'] ? (string) ($a['materia']['codigo'] ?? '') : '';
    $cb = $b['materia'] ? (string) ($b['materia']['codigo'] ?? '') : '';
    return strcmp($ca, $cb);
});

$pageTitle = 'Panel estudiante';
require PARTIALS_PATH . '/header.php';
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
          <p><strong>Documento:</strong> <?= h($yo['documento'] ?? '') ?></p>
          <p><strong>Programa:</strong> <?= h($yo['programa'] ?? '') ?></p>
          <p class="mb-0"><strong>Semestre:</strong> <?= h((string) ($yo['semestre'] ?? '')) ?> ·
            <strong>Sede:</strong> <?= h(sede_nombre(isset($yo['id_sede']) ? (int) $yo['id_sede'] : null)) ?> ·
            <strong>Jornada:</strong> <?= h(jornada_nombre(isset($yo['id_jornada']) ? (int) $yo['id_jornada'] : null)) ?>
          </p>
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
                      <span class="badge bg-secondary"><?= h(materia_modalidad_etiqueta($mat)) ?></span>
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
<?php require PARTIALS_PATH . '/footer.php'; ?>
