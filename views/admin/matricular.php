<?php
/** Vista: matrícula por estudiante. Variables: $mensaje, $tipoMsg, $cargado, $materiasOrdenadas */
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
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('admin/dashboard.php')) ?>">Volver</a>
  </div>
  <p class="mb-6 text-sm text-gray-600">Ingrese el número de identificación del estudiante para cargar sus datos y realizar la matrícula.</p>

  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertMsg) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="mb-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Datos del estudiante</h2>
    <form method="post" class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
      <input type="hidden" name="accion" value="buscar">
      <div class="md:col-span-8">
        <label class="<?= h($lbl) ?>">Número de identificación</label>
        <input type="text" name="documento_buscar" class="<?= h($inp) ?>" placeholder="Ingrese el número de identificación"
          value="<?= h(post('documento_buscar') ?? '') ?>">
      </div>
      <div class="md:col-span-4">
        <button type="submit" class="w-full rounded-lg bg-academic py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark">Buscar</button>
      </div>
    </form>

    <?php if ($show): ?>
      <hr class="my-6 border-gray-200">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="<?= h($lbl) ?>">Nombres</label>
          <input type="text" class="<?= h($inp) ?> cursor-not-allowed bg-gray-100" readonly value="<?= h($show['nombre'] ?? '') ?>" placeholder="Se cargan al validar identificación">
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Apellidos</label>
          <input type="text" class="<?= h($inp) ?> cursor-not-allowed bg-gray-100" readonly value="<?= h($show['apellido'] ?? '') ?>" placeholder="Se cargan al validar identificación">
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Carrera</label>
          <input type="text" class="<?= h($inp) ?> cursor-not-allowed bg-gray-100" readonly value="<?= h($show['programa'] ?? '') ?>" placeholder="Se cargan del registro">
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Semestre</label>
          <input type="text" class="<?= h($inp) ?> cursor-not-allowed bg-gray-100" readonly value="<?= h((string) ($show['semestre'] ?? '')) ?>" placeholder="Se cargan del registro">
        </div>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($show): ?>
  <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-1 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Asignatura a matricular</h2>
    <p class="mb-4 text-sm text-gray-500">(Primero busque o cargue los datos del estudiante)</p>
    <form method="post" class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
      <input type="hidden" name="accion" value="matricular">
      <input type="hidden" name="id_estudiante" value="<?= (int) $show['id_estudiante'] ?>">
      <div class="md:col-span-8">
        <label class="<?= h($lbl) ?>">Asignatura</label>
        <select name="id_materia" id="selMateria" class="<?= h($inp) ?>" required>
          <option value="">Seleccione...</option>
          <?php foreach ($materiasOrdenadas as $m): ?>
            <option value="<?= (int) $m['id_materia'] ?>" data-semestre="<?= (int) ($m['semestre'] ?? 0) ?>">
              <?php
              $lbl = ($m['codigo'] ?? '') . ' — ' . ($m['nombre'] ?? '');
              $lbl .= ' · ' . materia_programa_label($m);
              $lbl .= ' · ' . materia_horario_resumen($m);
              $lbl .= ' · ' . materia_modalidad_etiqueta($m);
              if (($m['modalidad'] ?? '') === 'presencial' && trim((string) ($m['salon'] ?? '')) !== '') {
                  $lbl .= ' · Salón ' . $m['salon'];
              }
              ?>
              <?= h($lbl) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-4">
        <label class="<?= h($lbl) ?>">Semestre de la asignatura</label>
        <input type="text" id="semestreMateria" class="<?= h($inp) ?> cursor-not-allowed bg-gray-100" readonly placeholder="Se carga al seleccionar la asignatura" value="">
      </div>
      <div class="md:col-span-12">
        <button type="submit" class="inline-flex rounded-lg bg-academic px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark">Matricular</button>
      </div>
    </form>
  </div>
  <script src="<?= h(asset_url('js/admin-matricular-form.js')) ?>"></script>
  <?php endif; ?>
</main>
