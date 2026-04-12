<?php
$ef = $editar ?? [];
$idSedeForm = isset($ef['id_sede']) && (int) $ef['id_sede'] > 0 ? (int) $ef['id_sede'] : docente_sede_efectiva($ef);
$msgWarn = (strpos($mensaje ?? '', 'No se puede') !== false || strpos($mensaje ?? '', 'Seleccione la carrera') !== false || strpos($mensaje ?? '', 'Seleccione la sede') !== false || strpos($mensaje ?? '', 'no corresponde a la sede') !== false);
$alertClass = $msgWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
?>
<main class="flex-1 w-full mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-semibold text-academic">Registro de docentes</h1>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('admin/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="mb-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic"><?= $editar ? 'Editar docente' : 'Nuevo docente' ?></h2>
    <form method="post" class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <input type="hidden" name="accion" value="guardar">
      <?php if ($editar): ?>
        <input type="hidden" name="id_docente" value="<?= (int) $ef['id_docente'] ?>">
      <?php endif; ?>
      <div>
        <label class="<?= h($lbl) ?>">Nombres</label>
        <input type="text" name="nombre" class="<?= h($inp) ?>" required value="<?= h($ef['nombre'] ?? '') ?>">
      </div>
      <div>
        <label class="<?= h($lbl) ?>">Apellidos</label>
        <input type="text" name="apellido" class="<?= h($inp) ?>" required value="<?= h($ef['apellido'] ?? '') ?>">
      </div>
      <div>
        <label class="<?= h($lbl) ?>">Documento</label>
        <input type="text" name="documento" class="<?= h($inp) ?>" required value="<?= h($ef['documento'] ?? '') ?>">
      </div>
      <div>
        <label class="<?= h($lbl) ?>">Correo</label>
        <input type="email" name="correo" class="<?= h($inp) ?>" required value="<?= h($ef['correo'] ?? '') ?>">
      </div>
      <div>
        <label class="<?= h($lbl) ?>">Teléfono</label>
        <input type="text" name="telefono" class="<?= h($inp) ?>" value="<?= h($ef['telefono'] ?? '') ?>">
      </div>
      <div>
        <label class="<?= h($lbl) ?>">Sede</label>
        <select name="id_sede" id="fld-sede-docente" class="<?= h($inp) ?>" required>
          <option value="">Seleccione...</option>
          <?php foreach (diccionario_sedes() as $s): ?>
            <option value="<?= (int) $s['id'] ?>" <?= $idSedeForm === (int) $s['id'] ? 'selected' : '' ?>><?= h($s['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
        <p class="mt-1 text-xs text-gray-500">Indica si el docente pertenece a la sede Cúcuta u Ocaña.</p>
      </div>
      <div class="md:col-span-2">
        <label class="<?= h($lbl) ?>">Carrera a la que dicta clase</label>
        <select name="id_programa" id="fld-programa-docente" class="<?= h($inp) ?>" required>
          <option value="">Seleccione la carrera...</option>
          <?php foreach (diccionario_programas() as $p): ?>
            <option value="<?= (int) $p['id'] ?>" data-sede="<?= (int) ($p['id_sede'] ?? 1) ?>"
              <?= (int) ($ef['id_programa'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
              <?= h('[' . $p['codigo'] . '] ' . $p['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <p class="mt-1 text-xs text-gray-500">Las carreras se filtran según la sede elegida.</p>
      </div>
      <div class="md:col-span-2">
        <label class="<?= h($lbl) ?>">Contraseña de acceso <?= $editar ? '(vacío = sin cambio)' : '' ?></label>
        <input type="password" name="clave" class="<?= h($inp) ?>" autocomplete="new-password" placeholder="<?= $editar ? 'Sin cambios' : 'Por defecto doc123' ?>">
      </div>
      <div class="flex flex-wrap gap-2 md:col-span-2">
        <button type="submit" class="inline-flex rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark"><?= $editar ? 'Actualizar' : 'Registrar' ?></button>
        <?php if ($editar): ?>
          <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50" href="<?= h(url('admin/docentes.php')) ?>">Cancelar edición</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
  <script src="<?= h(asset_url('js/admin-docentes-form.js')) ?>"></script>

  <h2 class="mb-3 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Listado</h2>
  <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">ID</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Documento</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Nombre</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Correo</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Sede</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Carrera</th>
          <th class="px-3 py-3 text-right font-semibold text-gray-700"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php foreach ($docentes as $d): ?>
          <tr class="hover:bg-gray-50/80">
            <td class="px-3 py-2"><?= (int) $d['id_docente'] ?></td>
            <td class="px-3 py-2"><?= h($d['documento'] ?? '') ?></td>
            <td class="px-3 py-2"><?= h(trim(($d['nombre'] ?? '') . ' ' . ($d['apellido'] ?? ''))) ?></td>
            <td class="px-3 py-2"><?= h($d['correo'] ?? '') ?></td>
            <td class="px-3 py-2"><?= h(sede_nombre(docente_sede_efectiva($d))) ?></td>
            <td class="max-w-xs px-3 py-2 text-xs"><?= h($d['programa'] ?? (isset($d['id_programa']) ? programa_label_by_id((int) $d['id_programa']) : '')) ?></td>
            <td class="whitespace-nowrap px-3 py-2 text-right">
              <a class="mr-1 inline-flex rounded-lg border border-blue-600 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-50" href="<?= h(url('admin/docentes.php?editar=' . (int) $d['id_docente'])) ?>">Editar</a>
              <form method="post" class="inline" onsubmit="return confirm('¿Eliminar este docente?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_docente" value="<?= (int) $d['id_docente'] ?>">
                <button type="submit" class="inline-flex rounded-lg border border-red-300 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-50">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$docentes): ?>
          <tr><td colspan="7" class="px-3 py-8 text-center text-gray-500">No hay docentes registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
