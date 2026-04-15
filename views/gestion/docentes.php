<?php
$alertClass = ($tipoMsg ?? 'success') === 'warning' ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
$ef = $editar ?? [];
?>
<main class="flex-1 w-full mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-semibold text-academic">Registro de docentes</h1>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('gestion/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <form method="post" class="mb-10 space-y-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <input type="hidden" name="accion" value="guardar">
    <?php if ($editar): ?>
      <input type="hidden" name="id_docente" value="<?= (int) $editar['id_docente'] ?>">
    <?php endif; ?>
    <h2 class="border-b border-blue-100 pb-2 text-base font-semibold text-academic"><?= $editar ? 'Editar docente' : 'Nuevo docente' ?></h2>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <div>
        <label class="<?= h($lbl) ?>">Sede</label>
        <select name="id_sede" id="fld-sede-docente" class="<?= h($inp) ?>" required>
          <option value="">Seleccione…</option>
          <?php foreach (diccionario_sedes() as $s): ?>
            <option value="<?= (int) $s['id'] ?>" <?= (int) ($ef['id_sede'] ?? 0) === (int) $s['id'] ? 'selected' : '' ?>><?= h($s['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="<?= h($lbl) ?>">Carrera</label>
        <select name="id_programa" id="fld-programa-docente" class="<?= h($inp) ?>" required>
          <option value="">Seleccione…</option>
          <?php foreach (diccionario_programas() as $p): ?>
            <option value="<?= (int) $p['id'] ?>" data-sede="<?= (int) $p['id_sede'] ?>" <?= (int) ($ef['id_programa'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>><?= h('[' . $p['codigo'] . '] ' . $p['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><label class="<?= h($lbl) ?>">Nombres</label><input name="nombre" class="<?= h($inp) ?>" value="<?= h((string) ($ef['nombre'] ?? '')) ?>" required></div>
      <div><label class="<?= h($lbl) ?>">Apellidos</label><input name="apellido" class="<?= h($inp) ?>" value="<?= h((string) ($ef['apellido'] ?? '')) ?>" required></div>
      <div><label class="<?= h($lbl) ?>">Documento</label><input name="documento" class="<?= h($inp) ?>" value="<?= h((string) ($ef['documento'] ?? '')) ?>" required></div>
      <div><label class="<?= h($lbl) ?>">Correo</label><input name="correo" type="email" class="<?= h($inp) ?>" value="<?= h((string) ($ef['correo'] ?? '')) ?>" required></div>
      <div><label class="<?= h($lbl) ?>">Teléfono</label><input name="telefono" class="<?= h($inp) ?>" value="<?= h((string) ($ef['telefono'] ?? '')) ?>"></div>
      <div><label class="<?= h($lbl) ?>">Contraseña<?= $editar ? ' (vacío para no cambiar)' : '' ?></label><input name="clave" type="password" class="<?= h($inp) ?>"></div>
    </div>
    <div class="flex flex-wrap gap-3">
      <button type="submit" class="rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white hover:bg-academic-dark">Guardar</button>
      <?php if ($editar): ?>
        <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50" href="<?= h(url('gestion/docentes.php')) ?>">Cancelar edición</a>
      <?php endif; ?>
    </div>
  </form>

  <h2 class="mb-3 text-lg font-semibold text-academic">Listado por sede</h2>
  <?php
  $docentesPorSede = [];
  foreach ($docentes as $d) {
      $sid = docente_sede_efectiva($d);
      $docentesPorSede[$sid][] = $d;
  }
  ksort($docentesPorSede);
  $titulosSede = [1 => 'Sede Cúcuta', 2 => 'Sede Ocaña'];
  ?>
  <?php foreach ($docentesPorSede as $sid => $lista): ?>
    <div class="mb-6">
      <h3 class="mb-2 text-base font-semibold text-sky-800"><?= h($titulosSede[$sid] ?? 'Sede') ?></h3>
      <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-50"><tr>
            <th class="px-3 py-2 text-left font-semibold text-gray-700">ID</th>
            <th class="px-3 py-2 text-left font-semibold text-gray-700">Documento</th>
            <th class="px-3 py-2 text-left font-semibold text-gray-700">Nombre</th>
            <th class="px-3 py-2 text-left font-semibold text-gray-700">Carrera</th>
            <th class="px-3 py-2 text-right font-semibold text-gray-700"></th>
          </tr></thead>
          <tbody class="divide-y divide-gray-100">
            <?php foreach ($lista as $d): ?>
              <tr>
                <td class="px-3 py-2"><?= (int) $d['id_docente'] ?></td>
                <td class="px-3 py-2"><?= h($d['documento'] ?? '') ?></td>
                <td class="px-3 py-2"><?= h(trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? ''))) ?></td>
                <td class="max-w-xs px-3 py-2 text-xs"><?= h($d['programa'] ?? '') ?></td>
                <td class="px-3 py-2 text-right whitespace-nowrap">
                  <a class="mr-1 inline-flex rounded-lg border border-blue-600 px-2 py-1 text-xs text-blue-700 hover:bg-blue-50" href="<?= h(url('gestion/docentes.php?editar=' . (int) $d['id_docente'])) ?>">Editar</a>
                  <form method="post" class="inline" onsubmit="return confirm('¿Eliminar?');">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id_docente" value="<?= (int) $d['id_docente'] ?>">
                    <button type="submit" class="inline-flex rounded-lg border border-red-300 px-2 py-1 text-xs text-red-700 hover:bg-red-50">Eliminar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (!$docentes): ?>
    <p class="text-gray-500">Sin docentes registrados.</p>
  <?php endif; ?>
</main>
