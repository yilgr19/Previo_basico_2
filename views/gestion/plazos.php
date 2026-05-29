<?php

$mWarn = ($mensaje ?? '') !== '' && ($tipoMsg ?? '') === 'warning';

$alertClass = $mWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';

if (($tipoMsg ?? '') === 'warning' && !$mWarn) {

    $alertClass = 'border-amber-200 bg-amber-50 text-amber-900';

}

$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';

$lbl = 'mb-1 block text-sm font-medium text-gray-700';

$editarTipo = (int) ($editarTipo ?? 0);

$editarSede = (int) ($editarSede ?? 0);

$editarPlazo = (int) ($editarPlazo ?? 15);

$matriz = $matriz ?? [];

?>

<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">

  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">

    <div>

      <h1 class="text-xl font-semibold text-academic">Plazos por tipo de solicitud</h1>

      <p class="mt-1 text-sm text-gray-600">Configure el plazo <strong class="font-medium text-gray-800">uno a uno</strong> con el formulario, o marque varias filas (o todas) en la tabla para aplicar el mismo número de días.</p>

    </div>

    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('gestion/dashboard')) ?>">Volver al panel</a>

  </div>



  <?php if ($mensaje): ?>

    <div class="mb-6 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>

  <?php endif; ?>



  <form method="post" class="mb-10 space-y-4 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">

    <input type="hidden" name="accion" value="guardar_plazo">

    <h2 class="border-b border-blue-100 pb-2 text-base font-semibold text-academic">

      <?= $editarTipo > 0 && $editarSede > 0 ? 'Actualizar un plazo' : 'Configurar un plazo' ?>

    </h2>

    <p class="text-sm text-gray-500">Un solo tipo de solicitud y una sede por guardado.</p>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

      <div>

        <label class="<?= h($lbl) ?>">Tipo de solicitud</label>

        <select name="id_tipo_solicitud" id="fld_tipo_plazo" class="<?= h($inp) ?>" required>

          <option value="">Seleccione…</option>

          <?php foreach ($tipos as $t): ?>

            <option value="<?= (int) $t['id'] ?>" <?= $editarTipo === (int) $t['id'] ? 'selected' : '' ?>><?= h((string) $t['nombre']) ?></option>

          <?php endforeach; ?>

        </select>

      </div>

      <div>

        <label class="<?= h($lbl) ?>">Sede</label>

        <select name="id_sede" id="fld_sede_plazo" class="<?= h($inp) ?>" required>

          <option value="">Seleccione…</option>

          <?php foreach ($sedes as $s): ?>

            <option value="<?= (int) $s['id'] ?>" <?= $editarSede === (int) $s['id'] ? 'selected' : '' ?>><?= h((string) $s['nombre']) ?></option>

          <?php endforeach; ?>

        </select>

      </div>

      <div>

        <label class="<?= h($lbl) ?>">Plazo (días)</label>

        <input type="number" name="plazo" id="fld_plazo_dias" class="<?= h($inp) ?>" min="1" max="999" step="1" required value="<?= (int) $editarPlazo ?>">

      </div>

    </div>

    <div class="flex flex-wrap gap-2">

      <button type="submit" class="inline-flex rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark">Guardar plazo</button>

      <a href="<?= h(url('gestion/plazos')) ?>" class="inline-flex rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Limpiar formulario</a>

    </div>

  </form>



  <section>

    <h2 class="mb-1 text-base font-semibold text-slate-800">Asignación masiva</h2>

    <p class="mb-4 text-sm text-gray-600">Marque las filas deseadas, indique el plazo y pulse <em>Aplicar a seleccionados</em>. Use <em>Aplicar a todos</em> para el mismo plazo en todos los tipos y sedes sin marcar filas.</p>



    <?php if (!$matriz): ?>

      <p class="rounded-lg border border-dashed border-gray-200 bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">No hay tipos de solicitud en el catálogo.</p>

    <?php else: ?>

      <form method="post" id="form-plazos-masivo" class="space-y-4">

        <input type="hidden" name="accion" value="guardar_plazos_masivo">



        <div class="flex flex-wrap items-end gap-4 rounded-xl border border-blue-100 bg-blue-50/40 p-4">

          <div class="flex items-center gap-2 pt-6">

            <input type="checkbox" id="chk-seleccionar-todos" class="h-4 w-4 rounded border-gray-300 text-academic focus:ring-blue-500" title="Seleccionar todas las filas">

            <label for="chk-seleccionar-todos" class="text-sm font-medium text-gray-800">Seleccionar todos</label>

          </div>

          <div class="min-w-[8rem]">

            <label for="fld_plazo_masivo" class="<?= h($lbl) ?>">Plazo (días)</label>

            <input type="number" name="plazo_masivo" id="fld_plazo_masivo" class="<?= h($inp) ?> mt-0 w-28" min="1" max="999" step="1" required value="<?= (int) $editarPlazo ?>">

          </div>

          <button type="submit" class="inline-flex rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark" onclick="return validarMasivo(false);">

            Aplicar a seleccionados

          </button>

          <button type="submit" name="aplicar_todos" value="1" class="inline-flex rounded-lg border border-academic bg-white px-4 py-2.5 text-sm font-semibold text-academic shadow-sm hover:bg-blue-50" onclick="return validarMasivo(true);">

            Aplicar a todos

          </button>

        </div>

      </form>



        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">

          <table class="min-w-full divide-y divide-gray-200 text-sm">

            <thead class="bg-gray-50">

              <tr>

                <th class="w-10 px-3 py-3" scope="col"><span class="sr-only">Seleccionar</span></th>

                <th class="px-4 py-3 text-left font-semibold text-gray-700">Tipo</th>

                <th class="px-4 py-3 text-left font-semibold text-gray-700">Código</th>

                <th class="px-4 py-3 text-left font-semibold text-gray-700">Sede</th>

                <th class="px-4 py-3 text-left font-semibold text-gray-700">Plazo actual</th>

                <th class="px-4 py-3 text-right font-semibold text-gray-700"></th>

              </tr>

            </thead>

            <tbody class="divide-y divide-gray-100">

              <?php foreach ($matriz as $p):

                $clave = (int) $p['id_tipo_solicitud'] . '-' . (int) $p['id_sede'];

                $plazoVal = $p['plazo'] ?? null;

                $marcar = $editarTipo === (int) $p['id_tipo_solicitud'] && $editarSede === (int) $p['id_sede'];

              ?>

                <tr class="hover:bg-gray-50/80 <?= $marcar ? 'bg-blue-50/60' : '' ?>">

                  <td class="px-3 py-2.5 text-center">

                    <input type="checkbox" form="form-plazos-masivo" name="seleccion[]" value="<?= h($clave) ?>" class="chk-fila-plazo h-4 w-4 rounded border-gray-300 text-academic focus:ring-blue-500" <?= $marcar ? 'checked' : '' ?>>

                  </td>

                  <td class="px-4 py-2.5"><?= h($p['tipo_nombre']) ?></td>

                  <td class="px-4 py-2.5 font-mono text-xs text-gray-600"><?= h($p['tipo_codigo']) ?></td>

                  <td class="px-4 py-2.5"><?= h($p['sede_nombre']) ?></td>

                  <td class="px-4 py-2.5">

                    <?php if ($plazoVal === null): ?>

                      <span class="text-gray-400">Sin configurar</span>

                    <?php else: ?>

                      <span class="font-semibold text-academic"><?= (int) $plazoVal ?> <?= (int) $plazoVal === 1 ? 'día' : 'días' ?></span>

                    <?php endif; ?>

                  </td>

                  <td class="px-4 py-2.5 text-right whitespace-nowrap">

                    <a class="mr-2 inline-flex rounded-lg border border-blue-600 px-2.5 py-1 text-xs font-medium text-blue-700 hover:bg-blue-50" href="<?= h(url('gestion/plazos?tipo=' . (int) $p['id_tipo_solicitud'] . '&sede=' . (int) $p['id_sede'])) ?>">Editar uno</a>

                    <?php if ($plazoVal !== null): ?>

                      <form method="post" class="inline" onsubmit="return confirm('¿Eliminar este plazo?');">

                        <input type="hidden" name="accion" value="eliminar_plazo">

                        <input type="hidden" name="id_tipo_solicitud" value="<?= (int) $p['id_tipo_solicitud'] ?>">

                        <input type="hidden" name="id_sede" value="<?= (int) $p['id_sede'] ?>">

                        <button type="submit" class="inline-flex rounded-lg border border-red-300 px-2.5 py-1 text-xs font-medium text-red-700 hover:bg-red-50">Eliminar</button>

                      </form>

                    <?php endif; ?>

                  </td>

                </tr>

              <?php endforeach; ?>

            </tbody>

          </table>

        </div>

    <?php endif; ?>

  </section>

</main>

<script>

(function () {

  var master = document.getElementById('chk-seleccionar-todos');

  if (!master) return;



  function filas() {

    return document.querySelectorAll('.chk-fila-plazo');

  }



  master.addEventListener('change', function () {

    filas().forEach(function (cb) { cb.checked = master.checked; });

  });



  filas().forEach(function (cb) {

    cb.addEventListener('change', function () {

      var todos = filas();

      var marcados = 0;

      todos.forEach(function (c) { if (c.checked) marcados++; });

      master.checked = marcados > 0 && marcados === todos.length;

      master.indeterminate = marcados > 0 && marcados < todos.length;

    });

  });

})();



function validarMasivo(aplicarTodos) {

  var plazo = document.getElementById('fld_plazo_masivo');

  if (!plazo || plazo.value === '' || parseInt(plazo.value, 10) < 1) {

    alert('Indique un plazo entre 1 y 999 días.');

    return false;

  }

  if (aplicarTodos) {

    return confirm('¿Aplicar ' + plazo.value + ' día(s) a TODOS los tipos de solicitud y sedes?');

  }

  var alguno = false;

  document.querySelectorAll('.chk-fila-plazo').forEach(function (cb) {

    if (cb.checked) alguno = true;

  });

  if (!alguno) {

    alert('Seleccione al menos una fila o use el botón «Aplicar a todos».');

    return false;

  }

  return confirm('¿Aplicar ' + plazo.value + ' día(s) a las filas seleccionadas?');

}

</script>


