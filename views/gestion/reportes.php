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
  <p class="mb-6 text-sm text-gray-600">Consulte lo registrado. Para <strong>crear o editar</strong> estudiantes o docentes use las tarjetas del panel principal.</p>
  <?php if (!empty($mensaje)): ?>
    <div class="mb-6 rounded-lg border px-4 py-3 text-sm <?= h($alertMsg) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <section class="mb-10">
    <h2 class="mb-3 border-b border-blue-100 pb-2 text-lg font-semibold text-academic">Estudiantes</h2>
    <?php
    $estudiantesPorSedeYCarrera = [];
    foreach ($estudiantes as $e) {
        $sid = estudiante_sede_efectiva($e);
        $etiq = trim((string) ($e['programa'] ?? ''));
        if ($etiq === '' && !empty($e['id_programa'])) {
            $etiq = programa_label_by_id((int) $e['id_programa']);
        }
        if ($etiq === '') {
            $etiq = 'Sin carrera asignada';
        }
        if (!isset($estudiantesPorSedeYCarrera[$sid])) {
            $estudiantesPorSedeYCarrera[$sid] = [];
        }
        $estudiantesPorSedeYCarrera[$sid][$etiq][] = $e;
    }
    ksort($estudiantesPorSedeYCarrera);
    foreach ($estudiantesPorSedeYCarrera as $sid => &$porCarreraEst) {
        ksort($porCarreraEst, SORT_NATURAL | SORT_FLAG_CASE);
    }
    unset($porCarreraEst);
    $titulosSedeEst = [1 => 'Sede Cúcuta', 2 => 'Sede Ocaña'];
    $idsSedeOrdenEst = array_keys($estudiantesPorSedeYCarrera);
    sort($idsSedeOrdenEst, SORT_NUMERIC);
    ?>
    <?php if (!$estudiantes): ?>
      <div class="rounded-xl border border-gray-200 bg-white shadow-sm"><div class="overflow-x-auto"><table class="min-w-full text-sm"><tbody><tr><td class="px-4 py-6 text-gray-500">Sin registros.</td></tr></tbody></table></div></div>
    <?php else: ?>
      <?php foreach ($idsSedeOrdenEst as $idSedeEst): ?>
        <?php
        $porCarreraEst = $estudiantesPorSedeYCarrera[$idSedeEst] ?? [];
        if ($porCarreraEst === []) {
            continue;
        }
        $tituloSedeEst = $titulosSedeEst[$idSedeEst] ?? ('Sede ' . sede_nombre($idSedeEst));
        ?>
        <div class="mb-6">
          <h3 class="mb-3 text-lg font-semibold text-sky-800"><?= h($tituloSedeEst) ?></h3>
          <?php foreach ($porCarreraEst as $carreraTituloEst => $listaEst): ?>
            <div class="mb-4 ms-0 md:ms-3">
              <h4 class="mb-2 text-sm font-semibold text-blue-800"><?= h($carreraTituloEst) ?></h4>
              <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                  <thead class="bg-gray-50"><tr>
                    <th class="px-3 py-3 text-left font-semibold text-gray-700">ID</th>
                    <th class="px-3 py-3 text-left font-semibold text-gray-700">Documento</th>
                    <th class="px-3 py-3 text-left font-semibold text-gray-700">Nombre</th>
                    <th class="px-3 py-3 text-right font-semibold text-gray-700"></th>
                  </tr></thead>
                  <tbody class="divide-y divide-gray-100">
                    <?php foreach ($listaEst as $e): ?>
                      <tr class="hover:bg-gray-50/80">
                        <td class="px-3 py-2"><?= (int) $e['id_estudiante'] ?></td>
                        <td class="px-3 py-2"><?= h($e['documento'] ?? '') ?></td>
                        <td class="px-3 py-2"><?= h(trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? ''))) ?></td>
                        <td class="px-3 py-2 text-right">
                          <a class="inline-flex rounded-lg border border-blue-600 px-2.5 py-1 text-xs font-medium text-blue-700 hover:bg-blue-50" href="<?= h(url('gestion/estudiantes.php?editar=' . (int) $e['id_estudiante'])) ?>">Editar</a>
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

</main>
