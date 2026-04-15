<?php
$show = $cargado ?? null;
$alertMsg = match ($tipoMsg ?? '') {
    'success' => 'border-green-200 bg-green-50 text-green-900',
    'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
    default => 'border-sky-200 bg-sky-50 text-sky-900',
};
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
?>
<main class="flex-1 w-full mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-semibold text-academic">Matrícula de asignaturas</h1>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('gestion/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <p class="mb-6 text-sm text-gray-600">Ingrese el documento del estudiante para cargar sus datos y seleccione la asignatura.</p>
  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertMsg) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="mb-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Buscar estudiante</h2>
    <form method="post" class="flex flex-wrap items-end gap-4">
      <input type="hidden" name="accion" value="buscar">
      <div>
        <label class="<?= h($lbl) ?>">Documento</label>
        <input name="documento_buscar" class="<?= h($inp) ?> w-56" placeholder="Documento" value="<?= h(post('documento_buscar') ?? '') ?>">
      </div>
      <button type="submit" class="rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white hover:bg-academic-dark">Buscar</button>
    </form>
  </div>

  <?php if ($show): ?>
    <div class="mb-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
      <h2 class="mb-2 text-base font-semibold text-academic">Datos cargados</h2>
      <p class="text-sm text-gray-700"><?= h(trim(($show['nombre'] ?? '') . ' ' . ($show['apellido'] ?? ''))) ?> — <?= h($show['documento'] ?? '') ?> — <?= h($show['programa'] ?? '') ?></p>
      <form method="post" class="mt-4 flex flex-wrap items-end gap-4">
        <input type="hidden" name="accion" value="matricular">
        <input type="hidden" name="id_estudiante" value="<?= (int) $show['id_estudiante'] ?>">
        <div class="min-w-[16rem] flex-1">
          <label class="<?= h($lbl) ?>">Asignatura</label>
          <select name="id_materia" id="selMateria" class="<?= h($inp) ?>" required>
            <option value="">Seleccione curso…</option>
            <?php foreach ($materiasOrdenadas as $mat): ?>
              <option value="<?= (int) $mat['id_materia'] ?>" data-semestre="<?= h((string) ($mat['semestre'] ?? '')) ?>">
                <?= h(($mat['codigo'] ?? '') . ' — ' . ($mat['nombre'] ?? '')) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Semestre (referencia)</label>
          <input type="text" id="semestreMateria" class="<?= h($inp) ?> w-28" readonly placeholder="—">
        </div>
        <button type="submit" class="rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white hover:bg-academic-dark">Matricular</button>
      </form>
    </div>
  <?php endif; ?>
</main>
<script src="<?= h(asset_url('js/matricular-form.js')) ?>"></script>
