<main class="flex-1 w-full mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
  <section class="mb-8 rounded-xl border-l-4 border-academic bg-gradient-to-r from-blue-50 to-white p-6 shadow-sm">
    <h1 class="text-xl font-semibold text-academic">Bienvenido, <?= h($d['nombre'] ?? 'Docente') ?></h1>
    <p class="mt-1 text-gray-600">Asignaturas a su cargo y estudiantes matriculados en cada una.</p>
    <p class="mt-2 text-sm text-gray-600">
      <strong class="text-gray-800">Sede:</strong> <?= h(sede_nombre(docente_sede_efectiva($d))) ?>
    </p>
    <?php if (!empty($d['programa']) || !empty($d['id_programa'])): ?>
      <p class="mt-1 text-sm text-gray-600">
        <strong class="text-gray-800">Carrera a la que dicta clase:</strong>
        <?= h($d['programa'] ?? programa_label_by_id((int) ($d['id_programa'] ?? 0))) ?>
      </p>
    <?php endif; ?>
  </section>

  <?php if (!$materias): ?>
    <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">No tiene asignaturas asignadas. El administrador debe registrar materias con usted como docente.</div>
  <?php endif; ?>

  <?php foreach ($materias as $m): ?>
    <?php
    $idMat = (int) ($m['id_materia'] ?? 0);
    $inscritos = repo_matriculas_de_materia($idMat);
    ?>
    <div class="mb-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
      <h2 class="mb-2 border-b border-blue-100 pb-2 text-base font-semibold text-academic">
        <?= h(($m['codigo'] ?? '') . ' — ' . ($m['nombre'] ?? '')) ?>
      </h2>
      <p class="mb-2 text-sm text-gray-600">
        <strong class="text-gray-800">Carrera:</strong> <?= h(materia_programa_label($m)) ?>
      </p>
      <p class="mb-4 text-sm text-gray-600">
        <strong class="text-gray-800">Horario:</strong> <?= h(materia_dia_etiqueta((string) ($m['dia_clase'] ?? ''))) ?>
        <?= h(trim((string) ($m['hora_inicio'] ?? '')) !== '' ? ', ' . ($m['hora_inicio'] ?? '') . ' a ' . ($m['hora_fin'] ?? '') : '') ?>
        · Semestre materia: <?= (int) ($m['semestre'] ?? 0) ?> · Créditos: <?= (int) ($m['creditos'] ?? 0) ?>
        · <span class="inline-flex rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-800"><?= h(materia_modalidad_etiqueta($m)) ?></span>
        <?php if (($m['modalidad'] ?? 'virtual') === 'presencial' && trim((string) ($m['salon'] ?? '')) !== ''): ?>
          · Salón: <strong class="text-gray-900"><?= h($m['salon']) ?></strong>
        <?php elseif (($m['modalidad'] ?? 'virtual') === 'presencial'): ?>
          · <span class="text-amber-700">Salón no indicado</span>
        <?php endif; ?>
      </p>
      <?php if (!$inscritos): ?>
        <p class="text-sm text-gray-500">Sin estudiantes matriculados en esta asignatura.</p>
      <?php else: ?>
        <div class="overflow-x-auto rounded-lg border border-gray-100">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50"><tr>
              <th class="px-3 py-2 text-left font-semibold text-gray-700">Estudiante</th>
              <th class="px-3 py-2 text-left font-semibold text-gray-700">Documento</th>
              <th class="px-3 py-2 text-left font-semibold text-gray-700">Fecha matrícula</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
              <?php foreach ($inscritos as $x): ?>
                <?php $e = repo_estudiante_por_id((int) ($x['id_estudiante'] ?? 0)); ?>
                <tr>
                  <td class="px-3 py-2"><?= h(estudiante_nombre_completo((int) ($x['id_estudiante'] ?? 0))) ?></td>
                  <td class="px-3 py-2"><?= h($e['documento'] ?? '') ?></td>
                  <td class="px-3 py-2"><?= h($x['fecha'] ?? '') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</main>
