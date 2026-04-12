<?php
$mWarn = ($mensaje ?? '') !== '' && (
    strpos($mensaje, 'No se puede') !== false || strpos($mensaje, 'debe indicar') !== false || strpos($mensaje, 'Seleccione') !== false
    || strpos($mensaje, 'Busque') !== false || strpos($mensaje, 'Indique') !== false || strpos($mensaje, 'misma carrera') !== false
    || strpos($mensaje, 'Docente no encontrado') !== false || strpos($mensaje, 'sede del docente') !== false
);
$alertClass = $mWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
?>
<main class="flex-1 w-full mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-semibold text-academic">Catálogo de asignaturas</h1>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('admin/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <form method="post" id="form-materia" class="space-y-6">
    <input type="hidden" name="accion" value="guardar">
    <?php if ($editar): ?>
      <input type="hidden" name="id_materia" value="<?= (int) $editar['id_materia'] ?>">
    <?php endif; ?>
    <input type="hidden" name="id_docente" id="hid-id-docente" value="<?= $hidDocente ?>">

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
      <h2 class="mb-1 border-b border-blue-100 pb-2 text-base font-semibold text-academic"><?= $editar ? 'Editar asignatura' : 'Registrar asignatura' ?></h2>
      <p class="mb-4 text-sm text-gray-500">Datos generales del curso y la carrera a la que pertenece.</p>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Código</label>
          <input type="text" name="codigo" class="<?= h($inp) ?>" required value="<?= h($ef['codigo'] ?? '') ?>">
        </div>
        <div class="md:col-span-8">
          <label class="<?= h($lbl) ?>">Nombre de la asignatura</label>
          <input type="text" name="nombre" class="<?= h($inp) ?>" required value="<?= h($ef['nombre'] ?? '') ?>">
        </div>
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Créditos</label>
          <input type="number" name="creditos" class="<?= h($inp) ?>" min="1" max="30" required value="<?= h((string) ($ef['creditos'] ?? '3')) ?>">
        </div>
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Semestre de la asignatura</label>
          <input type="number" name="semestre" class="<?= h($inp) ?>" min="1" max="20" required value="<?= h((string) ($ef['semestre'] ?? '1')) ?>">
        </div>
        <div class="md:col-span-12">
          <label class="<?= h($lbl) ?>">Carrera / programa</label>
          <select name="id_programa" id="fld-id-programa" class="<?= h($inp) ?>" required>
            <option value="">Seleccione la carrera...</option>
            <?php foreach (diccionario_programas() as $p): ?>
              <option value="<?= (int) $p['id'] ?>" data-sede="<?= (int) ($p['id_sede'] ?? 1) ?>" <?= $idProgForm === (int) $p['id'] ? 'selected' : '' ?>>
                <?= h('[' . $p['codigo'] . '] ' . $p['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
      <h2 class="mb-1 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Datos académicos</h2>
      <p class="mb-4 text-sm text-gray-500">Horario de la clase y modalidad.</p>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-12">
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Día</label>
          <select name="dia_clase" class="<?= h($inp) ?>" required>
            <option value="">Seleccione...</option>
            <?php foreach (materia_dias_clase_opciones() as $cod => $etq): ?>
              <option value="<?= h($cod) ?>" <?= $diaValForm === $cod ? 'selected' : '' ?>><?= h($etq) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Hora inicio</label>
          <input type="time" name="hora_inicio" class="<?= h($inp) ?>" required value="<?= h((string) ($ef['hora_inicio'] ?? '')) ?>">
        </div>
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Hora fin</label>
          <input type="time" name="hora_fin" class="<?= h($inp) ?>" required value="<?= h((string) ($ef['hora_fin'] ?? '')) ?>">
        </div>
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Modalidad</label>
          <select name="modalidad" id="fld-modalidad" class="<?= h($inp) ?>" required>
            <option value="">Seleccione...</option>
            <option value="virtual" <?= $modValForm === 'virtual' ? 'selected' : '' ?>>Virtual</option>
            <option value="presencial" <?= $modValForm === 'presencial' ? 'selected' : '' ?>>Presencial</option>
          </select>
        </div>
        <div class="md:col-span-8" id="salon-wrap">
          <label class="<?= h($lbl) ?>">Salón / aula</label>
          <input type="text" name="salon" id="fld-salon" class="<?= h($inp) ?>" maxlength="80"
            placeholder="Ej. A-301, Lab. 2 (solo presencial)"
            value="<?= h((string) ($ef['salon'] ?? '')) ?>">
          <p class="mt-1 text-xs text-gray-500">Obligatorio si la modalidad es presencial.</p>
        </div>
      </div>
    </div>

    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
      <h2 class="mb-1 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Docente</h2>
      <p class="mb-4 text-sm text-gray-500">Busque por número de documento al docente registrado para la <strong class="font-semibold text-gray-700">misma carrera</strong> y sede que eligió arriba.</p>
      <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-12">
        <div class="md:col-span-5">
          <label class="<?= h($lbl) ?>">Identificación del docente</label>
          <input type="text" id="inp-doc-docente" class="<?= h($inp) ?>" autocomplete="off"
            placeholder="Ingrese el número de identificación" value="<?= h($docBuscarDoc) ?>">
        </div>
        <div class="md:col-span-3">
          <button type="button" class="w-full rounded-lg border border-blue-600 bg-white px-4 py-2 text-sm font-medium text-blue-700 shadow-sm hover:bg-blue-50" id="btn-buscar-docente">Buscar</button>
        </div>
        <div class="md:col-span-4">
          <label class="<?= h($lbl) ?>">Nombre del docente</label>
          <input type="text" id="txt-nombre-docente" class="<?= h($inp) ?> cursor-not-allowed bg-gray-100" readonly
            placeholder="Se carga al validar la identificación" value="<?= h($docBuscarNom) ?>">
        </div>
      </div>
    </div>

    <div class="flex flex-wrap gap-2">
      <button type="submit" class="inline-flex rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark"><?= $editar ? 'Actualizar asignatura' : 'Registrar asignatura' ?></button>
      <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50" href="<?= h(url('admin/dashboard.php')) ?>">Volver</a>
      <?php if ($editar): ?>
        <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50" href="<?= h(url('admin/materias.php')) ?>">Cancelar edición</a>
      <?php endif; ?>
    </div>
  </form>

  <h2 class="mb-3 mt-10 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Listado</h2>
  <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="whitespace-nowrap px-2 py-3 text-left text-xs font-semibold text-gray-700">ID</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Código</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Asignatura</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Carrera</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Horario</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Créd.</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Sem.</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Modalidad</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Salón</th>
          <th class="px-2 py-3 text-left text-xs font-semibold text-gray-700">Docente</th>
          <th class="px-2 py-3 text-right text-xs font-semibold text-gray-700"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php foreach ($materias as $m): ?>
          <tr class="hover:bg-gray-50/80">
            <td class="whitespace-nowrap px-2 py-2"><?= (int) $m['id_materia'] ?></td>
            <td class="px-2 py-2"><?= h($m['codigo'] ?? '') ?></td>
            <td class="max-w-[10rem] px-2 py-2"><?= h($m['nombre'] ?? '') ?></td>
            <td class="max-w-xs px-2 py-2 text-xs"><?= h(materia_programa_label($m)) ?></td>
            <td class="whitespace-nowrap px-2 py-2 text-xs"><?= h(materia_horario_resumen($m)) ?></td>
            <td class="px-2 py-2"><?= (int) ($m['creditos'] ?? 0) ?></td>
            <td class="px-2 py-2"><?= (int) ($m['semestre'] ?? 0) ?></td>
            <td class="px-2 py-2"><?= h(materia_modalidad_etiqueta($m)) ?></td>
            <td class="px-2 py-2"><?= h((string) ($m['modalidad'] ?? '') === 'presencial' ? ($m['salon'] ?? '') : '—') ?></td>
            <td class="max-w-[8rem] px-2 py-2 text-xs"><?= h(docente_nombre((int) ($m['id_docente'] ?? 0))) ?></td>
            <td class="whitespace-nowrap px-2 py-2 text-right">
              <a class="mr-1 inline-flex rounded border border-blue-600 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-50" href="<?= h(url('admin/materias.php?editar=' . (int) $m['id_materia'])) ?>">Editar</a>
              <form method="post" class="inline" onsubmit="return confirm('¿Eliminar esta asignatura?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_materia" value="<?= (int) $m['id_materia'] ?>">
                <button type="submit" class="inline-flex rounded border border-red-300 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-50">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$materias): ?>
          <tr><td colspan="11" class="px-3 py-8 text-center text-gray-500">No hay asignaturas. Registre docentes primero.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<script>window.DOCENTES_MATERIAS = <?= $docentesLookupJson ?>;</script>
<script src="<?= h(asset_url('js/admin-materias-form.js')) ?>"></script>
