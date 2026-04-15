<?php
$mWarn = ($mensaje ?? '') !== '' && (
    strpos((string) $mensaje, 'No se puede') !== false || strpos((string) $mensaje, 'debe indicar') !== false || strpos((string) $mensaje, 'Seleccione') !== false
    || strpos((string) $mensaje, 'Busque') !== false || strpos((string) $mensaje, 'Indique') !== false || strpos((string) $mensaje, 'misma carrera') !== false
    || strpos((string) $mensaje, 'Docente no encontrado') !== false || strpos((string) $mensaje, 'sede del docente') !== false
);
$alertClass = $mWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
?>
<main class="flex-1 w-full mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-semibold text-academic">Catálogo de asignaturas</h1>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('gestion/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <form method="post" id="form-materia" class="mb-10 space-y-6">
    <input type="hidden" name="accion" value="guardar">
    <?php if ($editar): ?>
      <input type="hidden" name="id_materia" value="<?= (int) $editar['id_materia'] ?>">
    <?php endif; ?>
    <input type="hidden" name="id_docente" id="hid-id-docente" value="<?= (int) $hidDocente ?>">

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
      <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic"><?= $editar ? 'Editar asignatura' : 'Registrar asignatura' ?></h2>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Código</label>
          <input type="text" name="codigo" class="<?= h($inp) ?>" required value="<?= h($ef['codigo'] ?? '') ?>">
        </div>
        <div class="md:col-span-8">
          <label class="<?= h($lbl) ?>">Nombre</label>
          <input type="text" name="nombre" class="<?= h($inp) ?>" required value="<?= h($ef['nombre'] ?? '') ?>">
        </div>
        <div class="md:col-span-3">
          <label class="<?= h($lbl) ?>">Créditos</label>
          <input type="number" name="creditos" min="1" max="30" class="<?= h($inp) ?>" required value="<?= h((string) ($ef['creditos'] ?? '3')) ?>">
        </div>
        <div class="md:col-span-3">
          <label class="<?= h($lbl) ?>">Semestre</label>
          <input type="number" name="semestre" min="1" max="20" class="<?= h($inp) ?>" required value="<?= h((string) ($ef['semestre'] ?? '1')) ?>">
        </div>
        <div class="md:col-span-6">
          <label class="<?= h($lbl) ?>">Carrera</label>
          <select name="id_programa" id="fld-id-programa" class="<?= h($inp) ?>" required>
            <option value="">Seleccione…</option>
            <?php foreach (diccionario_programas() as $p): ?>
              <option value="<?= (int) $p['id'] ?>" data-sede="<?= (int) $p['id_sede'] ?>" <?= (int) $idProgForm === (int) $p['id'] ? 'selected' : '' ?>><?= h('[' . $p['codigo'] . '] ' . $p['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Modalidad</label>
          <select name="modalidad" id="fld-modalidad" class="<?= h($inp) ?>" required>
            <option value="virtual" <?= $modValForm === 'virtual' ? 'selected' : '' ?>>Virtual</option>
            <option value="presencial" <?= $modValForm === 'presencial' ? 'selected' : '' ?>>Presencial</option>
          </select>
        </div>
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Salón (presencial)</label>
          <input type="text" name="salon" id="fld-salon" class="<?= h($inp) ?>" value="<?= h((string) ($ef['salon'] ?? '')) ?>">
        </div>
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Día de clase</label>
          <select name="dia_clase" class="<?= h($inp) ?>" required>
            <?php foreach (materia_dias_clase_opciones() as $k => $lab): ?>
              <option value="<?= h($k) ?>" <?= $diaValForm === $k ? 'selected' : '' ?>><?= h($lab) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="md:col-span-3">
          <label class="<?= h($lbl) ?>">Hora inicio</label>
          <input type="time" name="hora_inicio" class="<?= h($inp) ?>" required value="<?= h((string) ($ef['hora_inicio'] ?? '')) ?>">
        </div>
        <div class="md:col-span-3">
          <label class="<?= h($lbl) ?>">Hora fin</label>
          <input type="time" name="hora_fin" class="<?= h($inp) ?>" required value="<?= h((string) ($ef['hora_fin'] ?? '')) ?>">
        </div>
        <div class="md:col-span-12 rounded-lg border border-dashed border-gray-200 bg-gray-50/80 p-4">
          <p class="mb-2 text-sm font-medium text-gray-800">Docente asignado</p>
          <div class="flex flex-wrap items-end gap-3">
            <div class="min-w-[12rem] flex-1">
              <label class="<?= h($lbl) ?>">Documento docente</label>
              <input type="text" id="inp-doc-docente" class="<?= h($inp) ?>" placeholder="Número de documento" value="<?= h($docBuscarDoc) ?>">
            </div>
            <button type="button" id="btn-buscar-docente" class="rounded-lg border border-academic bg-white px-4 py-2 text-sm font-medium text-academic hover:bg-blue-50">Buscar</button>
            <div class="min-w-[12rem] flex-1">
              <label class="<?= h($lbl) ?>">Nombre (validado)</label>
              <input type="text" id="txt-nombre-docente" class="<?= h($inp) ?>" readonly value="<?= h($docBuscarNom) ?>" placeholder="Se carga al buscar">
            </div>
          </div>
        </div>
      </div>
      <div class="mt-6 flex flex-wrap gap-3">
        <button type="submit" class="rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white hover:bg-academic-dark"><?= $editar ? 'Actualizar' : 'Guardar' ?></button>
        <?php if ($editar): ?>
          <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50" href="<?= h(url('gestion/materias.php')) ?>">Cancelar edición</a>
        <?php endif; ?>
      </div>
    </div>
  </form>

  <h2 class="mb-3 text-lg font-semibold text-academic">Listado</h2>
  <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50"><tr>
        <th class="px-2 py-2 text-left text-xs font-semibold">Cód.</th>
        <th class="px-2 py-2 text-left text-xs font-semibold">Nombre</th>
        <th class="px-2 py-2 text-left text-xs font-semibold">Horario</th>
        <th class="px-2 py-2 text-right text-xs font-semibold"></th>
      </tr></thead>
      <tbody>
        <?php foreach ($materias as $m): ?>
          <tr class="border-t border-gray-100">
            <td class="px-2 py-2"><?= h($m['codigo'] ?? '') ?></td>
            <td class="px-2 py-2"><?= h($m['nombre'] ?? '') ?></td>
            <td class="px-2 py-2 text-xs"><?= h(materia_horario_resumen($m)) ?></td>
            <td class="px-2 py-2 text-right whitespace-nowrap">
              <a class="mr-1 inline-flex rounded border border-blue-600 px-2 py-1 text-xs text-blue-700 hover:bg-blue-50" href="<?= h(url('gestion/materias.php?editar=' . (int) $m['id_materia'])) ?>">Editar</a>
              <form method="post" class="inline" onsubmit="return confirm('¿Eliminar asignatura?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_materia" value="<?= (int) $m['id_materia'] ?>">
                <button type="submit" class="inline-flex rounded border border-red-300 px-2 py-1 text-xs text-red-700 hover:bg-red-50">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$materias): ?>
          <tr><td colspan="4" class="px-3 py-6 text-center text-gray-500">Sin registros.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<script>window.DOCENTES_MATERIAS = <?= $docentesLookupJson ?>;</script>
<script src="<?= h(asset_url('js/materias-form.js')) ?>"></script>
