<?php
/** Vista: panel docente. Variables: $d (docente), $materias (lista de materias del docente) */
?>
<main class="container pb-5">
  <section class="hero-welcome">
    <h1>Bienvenido, <?= h($d['nombre'] ?? 'Docente') ?></h1>
    <p class="mb-0 text-secondary">Asignaturas a su cargo y estudiantes matriculados en cada una.</p>
    <p class="small text-muted mt-2 mb-0">
      <strong>Sede:</strong> <?= h(sede_nombre(docente_sede_efectiva($d))) ?>
    </p>
    <?php if (!empty($d['programa']) || !empty($d['id_programa'])): ?>
      <p class="small text-muted mt-1 mb-0">
        <strong>Carrera a la que dicta clase:</strong>
        <?= h($d['programa'] ?? programa_label_by_id((int) ($d['id_programa'] ?? 0))) ?>
      </p>
    <?php endif; ?>
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
        <p class="small text-muted mb-2">
          <strong>Carrera:</strong> <?= h(materia_programa_label($m)) ?>
        </p>
        <p class="small text-muted mb-3">
          <strong>Horario:</strong> <?= h(materia_dia_etiqueta((string) ($m['dia_clase'] ?? ''))) ?>
          <?= h(trim((string) ($m['hora_inicio'] ?? '')) !== '' ? ', ' . ($m['hora_inicio'] ?? '') . ' a ' . ($m['hora_fin'] ?? '') : '') ?>
          · Semestre materia: <?= (int) ($m['semestre'] ?? 0) ?> · Créditos: <?= (int) ($m['creditos'] ?? 0) ?>
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
