<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_role(ROLE_DOCENTE);

$idDoc = auth_id();
if (!$idDoc) {
    redirect('/login.php');
}
$d = repo_docente_por_id($idDoc);
$materias = repo_materias_ordenadas_por_codigo(repo_materias_por_docente($idDoc));

$pageTitle = 'Panel docente';
require PARTIALS_PATH . '/header.php';
?>
<main class="container pb-5">
  <section class="hero-welcome">
    <h1>Bienvenido, <?= h($d['nombre'] ?? 'Docente') ?></h1>
    <p class="mb-0 text-secondary">Asignaturas a su cargo y estudiantes matriculados en cada una.</p>
  </section>

  <?php if (!$materias): ?>
    <div class="alert alert-light border">No tiene asignaturas asignadas. El administrador debe registrar materias con usted como docente.</div>
  <?php endif; ?>

  <?php foreach ($materias as $m): ?>
    <?php
    $idMat = (int) ($m['id_materia'] ?? 0);
    $inscritos = repo_matriculas_de_materia($idMat);
    ?>
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h2 class="h6 form-section-title mb-2">
          <?= h(($m['codigo'] ?? '') . ' — ' . ($m['nombre'] ?? '')) ?>
        </h2>
        <p class="small text-muted mb-3">
          Semestre: <?= (int) ($m['semestre'] ?? 0) ?> · Créditos: <?= (int) ($m['creditos'] ?? 0) ?>
          · <span class="badge bg-secondary"><?= h(materia_modalidad_etiqueta($m)) ?></span>
          <?php if (($m['modalidad'] ?? 'virtual') === 'presencial' && trim((string) ($m['salon'] ?? '')) !== ''): ?>
            · Salón: <strong><?= h($m['salon']) ?></strong>
          <?php elseif (($m['modalidad'] ?? 'virtual') === 'presencial'): ?>
            · <span class="text-warning">Salón no indicado</span>
          <?php endif; ?>
        </p>
        <?php if (!$inscritos): ?>
          <p class="text-muted mb-0 small">Sin estudiantes matriculados en esta asignatura.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead><tr><th>Estudiante</th><th>Documento</th><th>Fecha matrícula</th></tr></thead>
              <tbody>
                <?php foreach ($inscritos as $x): ?>
                  <?php $e = repo_estudiante_por_id((int) ($x['id_estudiante'] ?? 0)); ?>
                  <tr>
                    <td><?= h(estudiante_nombre_completo((int) ($x['id_estudiante'] ?? 0))) ?></td>
                    <td><?= h($e['documento'] ?? '') ?></td>
                    <td><?= h($x['fecha'] ?? '') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</main>
<?php require PARTIALS_PATH . '/footer.php'; ?>
