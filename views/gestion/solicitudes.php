<?php
$mWarn = ($mensaje ?? '') !== '' && ($tipoMsg ?? '') === 'warning';
$alertClass = $mWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
if (($tipoMsg ?? '') === 'warning' && !$mWarn) {
    $alertClass = 'border-amber-200 bg-amber-50 text-amber-900';
}
?>
<?php
$bandejaScript = $bandejaScript ?? 'gestion/solicitudes.php';
$idSedeBandeja = (int) ($idSedeBandeja ?? 0);
$uBandeja = url($bandejaScript);
$uBandejaCucuta = url('gestion/solicitudes.php');
$uBandejaOcana = url('gestion/solicitudes_sede_ocana.php');
?>
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold text-academic"><?= h($pageTitle ?? 'Solicitudes institucionales') ?></h1>
      <p class="mt-1 text-sm text-gray-600">Revise solicitudes de estudiantes y de docentes, filtre y responda según el estado del trámite. La tabla se ordena por <strong class="font-medium text-gray-800">fecha de registro</strong> (más recientes primero). Solo se listan trámites de la sede indicada en el título.</p>
      <?php if ($idSedeBandeja === 1 || $idSedeBandeja === 2): ?>
        <p class="mt-2 flex flex-wrap gap-2 text-xs">
          <span class="font-medium text-gray-600">Otra sede:</span>
          <?php if ($idSedeBandeja !== 1): ?>
            <a class="text-academic underline hover:text-academic-dark" href="<?= h($uBandejaCucuta) ?>">Bandeja sede Cúcuta</a>
          <?php endif; ?>
          <?php if ($idSedeBandeja !== 2): ?>
            <a class="text-academic underline hover:text-academic-dark" href="<?= h($uBandejaOcana) ?>">Bandeja sede Ocaña</a>
          <?php endif; ?>
        </p>
      <?php endif; ?>
    </div>
    <div class="flex flex-wrap gap-2">
      <a class="inline-flex items-center rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-sm font-medium text-amber-900 shadow-sm hover:bg-amber-100" href="<?= h(url('gestion/solicitudes_revision.php')) ?>">Solo en revisión Cúcuta</a>
      <a class="inline-flex items-center rounded-lg border border-amber-200/90 bg-amber-50/80 px-3 py-1.5 text-sm font-medium text-amber-950 shadow-sm hover:bg-amber-100" href="<?= h(url('gestion/solicitudes_revision_ocana.php')) ?>">Solo en revisión Ocaña</a>
      <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('gestion/dashboard.php')) ?>">Volver al panel</a>
    </div>
  </div>

  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>">
      <?= h($mensaje) ?>
      <?php if (($tipoMsg ?? '') === 'warning'): ?>
        <span class="mt-2 block text-[13px] font-normal">Los textos del formulario de la fila afectada se <strong class="font-medium text-gray-800">conservaron</strong>; corrija lo indicado y pulse Guardar de nuevo.</span>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <form method="get" action="<?= h($uBandeja) ?>" class="mb-6 space-y-4 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Radicante</label>
        <select name="radicante" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
          <option value="" <?= (($filtroRadicante ?? '') === '') ? 'selected' : '' ?>>Todos</option>
          <option value="estudiantes" <?= (($filtroRadicante ?? '') === 'estudiantes') ? 'selected' : '' ?>>Solo estudiantes</option>
          <option value="docentes" <?= (($filtroRadicante ?? '') === 'docentes') ? 'selected' : '' ?>>Solo docentes</option>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Desde (fecha registro)</label>
        <input type="date" name="fecha_desde" value="<?= h($filtroFechaDesde ?? '') ?>" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Hasta (fecha registro)</label>
        <input type="date" name="fecha_hasta" value="<?= h($filtroFechaHasta ?? '') ?>" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Estado</label>
        <select name="estado" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
          <option value="">Todos</option>
          <?php foreach (diccionario_estados_solicitud() as $e): ?>
            <option value="<?= h($e['codigo']) ?>" <?= (($filtroEstado ?? '') === $e['codigo']) ? 'selected' : '' ?>><?= h($e['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Aprobación</label>
        <select name="aprobacion" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
          <option value="">Todas</option>
          <option value="aprobadas" <?= (($filtroAprob ?? '') === 'aprobadas') ? 'selected' : '' ?>>Solo aprobadas</option>
          <option value="no_aprobadas" <?= (($filtroAprob ?? '') === 'no_aprobadas') ? 'selected' : '' ?>>No aprobadas (incl. pendiente, revisión, rechazada)</option>
        </select>
      </div>
    </div>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
      <div class="min-w-0 flex-1">
        <label class="mb-1 block text-sm font-medium text-gray-700">Buscar por documento o nombre del radicante (y documento de docente mencionado en solicitudes de estudiante)</label>
        <div class="flex gap-2">
          <input type="search" name="buscar" value="<?= h($buscarDoc ?? '') ?>" placeholder="Ej. 1098… o parte del nombre" class="mt-1 block min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
          <button type="submit" class="mt-1 inline-flex shrink-0 items-center justify-center rounded-lg bg-academic px-4 py-2 text-white shadow-sm hover:bg-academic-dark" title="Buscar" aria-label="Buscar">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </button>
        </div>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Aplicar filtros</button>
        <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" href="<?= h($uBandeja) ?>">Limpiar</a>
      </div>
    </div>
  </form>

  <?php require __DIR__ . '/partials/tabla_solicitudes_gestion.php'; ?>
</main>
