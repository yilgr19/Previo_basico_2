<?php
$alertMsg = match ($tipoMsg ?? 'success') {
    'success' => 'border-green-200 bg-green-50 text-green-900',
    'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
    default => 'border-sky-200 bg-sky-50 text-sky-900',
};
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
?>
<main class="flex-1 w-full mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-semibold text-academic">Reportes y gestión de registros</h1>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('gestion/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <p class="mb-6 text-sm text-gray-600">Consulte lo registrado. Para <strong>crear o editar</strong> estudiantes, docentes, asignaturas o matrículas use las tarjetas del panel principal.</p>
  <?php if (!empty($mensaje)): ?>
    <div class="mb-6 rounded-lg border px-4 py-3 text-sm <?= h($alertMsg) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <section class="mb-10">
    <h2 class="mb-3 border-b border-blue-100 pb-2 text-lg font-semibold text-academic">Estudiantes</h2>
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50"><tr>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">ID</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Documento</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Nombre</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Programa</th>
          <th class="px-3 py-3 text-right font-semibold text-gray-700"></th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($estudiantes as $e): ?>
            <tr class="hover:bg-gray-50/80">
              <td class="px-3 py-2"><?= (int) $e['id_estudiante'] ?></td>
              <td class="px-3 py-2"><?= h($e['documento'] ?? '') ?></td>
              <td class="px-3 py-2"><?= h(trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? ''))) ?></td>
              <td class="max-w-xs px-3 py-2 text-xs"><?= h($e['programa'] ?? '') ?></td>
              <td class="px-3 py-2 text-right">
                <a class="inline-flex rounded-lg border border-blue-600 px-2.5 py-1 text-xs font-medium text-blue-700 hover:bg-blue-50" href="<?= h(url('gestion/estudiantes.php?editar=' . (int) $e['id_estudiante'])) ?>">Editar</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$estudiantes): ?><tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">Sin registros.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="mb-10">
    <h2 class="mb-3 border-b border-blue-100 pb-2 text-lg font-semibold text-academic">Docentes</h2>
    <?php
    $docentesPorSedeYCarrera = [];
    foreach ($docentes as $d) {
        $sid = docente_sede_efectiva($d);
        $etiq = trim((string) ($d['programa'] ?? ''));
        if ($etiq === '' && !empty($d['id_programa'])) {
            $etiq = programa_label_by_id((int) $d['id_programa']);
        }
        if ($etiq === '') {
            $etiq = 'Sin carrera asignada';
        }
        if (!isset($docentesPorSedeYCarrera[$sid])) {
            $docentesPorSedeYCarrera[$sid] = [];
        }
        $docentesPorSedeYCarrera[$sid][$etiq][] = $d;
    }
    ksort($docentesPorSedeYCarrera);
    foreach ($docentesPorSedeYCarrera as $sid => &$porCarrera) {
        ksort($porCarrera, SORT_NATURAL | SORT_FLAG_CASE);
    }
    unset($porCarrera);
    $titulosSede = [1 => 'Sede Cúcuta', 2 => 'Sede Ocaña'];
    $idsSedeOrden = array_keys($docentesPorSedeYCarrera);
    sort($idsSedeOrden, SORT_NUMERIC);
    ?>
    <?php if (!$docentes): ?>
      <div class="rounded-xl border border-gray-200 bg-white shadow-sm"><div class="overflow-x-auto"><table class="min-w-full text-sm"><tbody><tr><td class="px-4 py-6 text-gray-500">Sin registros.</td></tr></tbody></table></div></div>
    <?php else: ?>
      <?php foreach ($idsSedeOrden as $idSede): ?>
        <?php
        $porCarrera = $docentesPorSedeYCarrera[$idSede] ?? [];
        if ($porCarrera === []) {
            continue;
        }
        $tituloSede = $titulosSede[$idSede] ?? ('Sede ' . sede_nombre($idSede));
        ?>
        <div class="mb-6">
          <h3 class="mb-3 text-lg font-semibold text-sky-800"><?= h($tituloSede) ?></h3>
          <?php foreach ($porCarrera as $carreraTitulo => $listaDoc): ?>
            <div class="mb-4 ms-0 md:ms-3">
              <h4 class="mb-2 text-sm font-semibold text-blue-800"><?= h($carreraTitulo) ?></h4>
              <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                  <thead class="bg-gray-50"><tr>
                    <th class="px-3 py-3 text-left font-semibold text-gray-700">ID</th>
                    <th class="px-3 py-3 text-left font-semibold text-gray-700">Documento</th>
                    <th class="px-3 py-3 text-left font-semibold text-gray-700">Nombre</th>
                    <th class="px-3 py-3 text-right font-semibold text-gray-700"></th>
                  </tr></thead>
                  <tbody class="divide-y divide-gray-100">
                    <?php foreach ($listaDoc as $d): ?>
                      <tr class="hover:bg-gray-50/80">
                        <td class="px-3 py-2"><?= (int) $d['id_docente'] ?></td>
                        <td class="px-3 py-2"><?= h($d['documento'] ?? '') ?></td>
                        <td class="px-3 py-2"><?= h(trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? ''))) ?></td>
                        <td class="px-3 py-2 text-right">
                          <a class="inline-flex rounded-lg border border-blue-600 px-2.5 py-1 text-xs font-medium text-blue-700 hover:bg-blue-50" href="<?= h(url('gestion/docentes.php?editar=' . (int) $d['id_docente'])) ?>">Editar</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>

  <section class="mb-10">
    <h2 class="mb-3 border-b border-blue-100 pb-2 text-lg font-semibold text-academic">Asignaturas</h2>
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50"><tr>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">ID</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Código</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Nombre</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Carrera</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Horario</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Modalidad</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Salón</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Docente</th>
          <th class="px-2 py-3 text-right text-xs font-semibold text-gray-700"></th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($materiasOrdenadas as $m): ?>
            <tr class="hover:bg-gray-50/80">
              <td class="px-2 py-2"><?= (int) $m['id_materia'] ?></td>
              <td class="px-2 py-2"><?= h($m['codigo'] ?? '') ?></td>
              <td class="max-w-[10rem] px-2 py-2"><?= h($m['nombre'] ?? '') ?></td>
              <td class="max-w-xs px-2 py-2 text-xs"><?= h(materia_programa_label($m)) ?></td>
              <td class="whitespace-nowrap px-2 py-2 text-xs"><?= h(materia_horario_resumen($m)) ?></td>
              <td class="px-2 py-2"><?= h(materia_modalidad_etiqueta($m)) ?></td>
              <td class="px-2 py-2"><?= h((string) ($m['modalidad'] ?? '') === 'presencial' ? ($m['salon'] ?? '') : '—') ?></td>
              <td class="max-w-[8rem] px-2 py-2 text-xs"><?= h(docente_nombre((int) ($m['id_docente'] ?? 0))) ?></td>
              <td class="px-2 py-2 text-right">
                <a class="inline-flex rounded-lg border border-blue-600 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-50" href="<?= h(url('gestion/materias.php?editar=' . (int) $m['id_materia'])) ?>">Editar</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$materiasOrdenadas): ?><tr><td colspan="9" class="px-3 py-6 text-center text-gray-500">Sin registros.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="mb-6">
    <h2 class="mb-2 border-b border-blue-100 pb-2 text-lg font-semibold text-academic">Matrículas por asignatura</h2>
    <p class="mb-4 text-sm text-gray-600">Cada bloque corresponde a una asignatura; puede eliminar matrículas desde el botón.</p>
    <?php foreach ($materiasOrdenadas as $mat): ?>
      <?php
      $idMat = (int) ($mat['id_materia'] ?? 0);
      $enEsta = array_values(array_filter($matriculas, static fn ($x) => (int) ($x['id_materia'] ?? 0) === $idMat));
      ?>
      <div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 bg-gray-50 px-4 py-3">
          <strong class="text-gray-900"><?= h(($mat['codigo'] ?? '') . ' — ' . ($mat['nombre'] ?? '')) ?></strong>
          <span class="mt-1 block text-sm text-gray-600 md:mt-0 md:inline md:ms-2">
            <?= h(materia_programa_label($mat)) ?> · <?= h(materia_horario_resumen($mat)) ?>
            · <?= h(materia_modalidad_etiqueta($mat)) ?>
            <?php if (($mat['modalidad'] ?? '') === 'presencial' && trim((string) ($mat['salon'] ?? '')) !== ''): ?>
              · Salón <?= h($mat['salon']) ?>
            <?php endif; ?>
            · Docente: <?= h(docente_nombre((int) ($mat['id_docente'] ?? 0))) ?>
          </span>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50"><tr>
              <th class="px-3 py-2 text-left font-semibold text-gray-700">ID matrícula</th>
              <th class="px-3 py-2 text-left font-semibold text-gray-700">Fecha</th>
              <th class="px-3 py-2 text-left font-semibold text-gray-700">Estudiante</th>
              <th class="px-3 py-2 text-right font-semibold text-gray-700"></th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
              <?php foreach ($enEsta as $x): ?>
                <tr>
                  <td class="px-3 py-2"><?= (int) $x['id_matricula'] ?></td>
                  <td class="px-3 py-2"><?= h($x['fecha'] ?? '') ?></td>
                  <td class="px-3 py-2"><?= h(estudiante_nombre_completo((int) ($x['id_estudiante'] ?? 0))) ?> <span class="text-gray-500">(#<?= (int) ($x['id_estudiante'] ?? 0) ?>)</span></td>
                  <td class="whitespace-nowrap px-3 py-2 text-right">
                    <form method="post" class="inline" onsubmit="return confirm('¿Eliminar esta matrícula?');">
                      <input type="hidden" name="accion" value="eliminar_matricula">
                      <input type="hidden" name="id_matricula" value="<?= (int) $x['id_matricula'] ?>">
                      <button type="submit" class="inline-flex rounded-lg border border-red-300 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-50">Eliminar</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$enEsta): ?>
                <tr><td colspan="4" class="px-3 py-4 text-gray-500">Sin matrículas en esta asignatura.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endforeach; ?>
    <?php
    $idsMat = array_map(static fn ($m) => (int) ($m['id_materia'] ?? 0), $materiasOrdenadas);
    $huerfanas = array_values(array_filter($matriculas, static function ($x) use ($idsMat) {
        return !in_array((int) ($x['id_materia'] ?? 0), $idsMat, true);
    }));
    ?>
    <?php if ($huerfanas): ?>
      <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        <strong>Matrículas sin asignatura asociada</strong> (ID de materia inexistente):
        <ul class="mt-2 list-inside list-disc space-y-1">
          <?php foreach ($huerfanas as $x): ?>
            <li>
              Matrícula #<?= (int) $x['id_matricula'] ?> — materia ID <?= (int) ($x['id_materia'] ?? 0) ?>
              <form method="post" class="ms-2 inline" onsubmit="return confirm('¿Eliminar?');">
                <input type="hidden" name="accion" value="eliminar_matricula">
                <input type="hidden" name="id_matricula" value="<?= (int) $x['id_matricula'] ?>">
                <button type="submit" class="inline-flex rounded border border-red-300 px-2 py-0.5 text-xs text-red-700 hover:bg-red-100">Eliminar</button>
              </form>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <?php if (!$materiasOrdenadas && !$matriculas): ?>
      <p class="text-gray-500">No hay asignaturas ni matrículas registradas.</p>
    <?php endif; ?>
  </section>
</main>
