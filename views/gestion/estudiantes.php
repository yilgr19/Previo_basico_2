<?php
$defaults = [
    'tipo_identificacion' => 'CC',
    'documento' => '',
    'nombre' => '',
    'apellido' => '',
    'correo' => '',
    'sexo' => 'M',
    'id_programa' => 0,
    'semestre' => 1,
    'fecha_nacimiento' => '',
    'direccion' => '',
    'barrio' => '',
    'telefono' => '',
    'id_sede' => 1,
    'id_jornada' => 1,
];
$ef = array_merge($defaults, $editar ?? []);
$mWarn = ($mensaje ?? '') !== '' && ($tipoMsg ?? '') === 'warning';
$alertClass = $mWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
if (($tipoMsg ?? '') === 'warning' && !$mWarn) {
    $alertClass = 'border-amber-200 bg-amber-50 text-amber-900';
}
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
?>
<main class="flex-1 w-full mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-semibold text-academic">Registrar estudiante</h1>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('gestion/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <form method="post" class="mb-10 space-y-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <input type="hidden" name="accion" value="guardar">
    <?php if ($editar): ?>
      <input type="hidden" name="id_estudiante" value="<?= (int) $editar['id_estudiante'] ?>">
    <?php endif; ?>
    <h2 class="border-b border-blue-100 pb-2 text-base font-semibold text-academic"><?= $editar ? 'Editar estudiante' : 'Nuevo estudiante' ?></h2>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
      <div>
        <label class="<?= h($lbl) ?>">Tipo identificación</label>
        <select name="tipo_identificacion" class="<?= h($inp) ?>">
          <?php foreach (diccionario_tipos_identificacion() as $t): ?>
            <option value="<?= h($t['codigo']) ?>" <?= (($ef['tipo_identificacion'] ?? '') === $t['codigo']) ? 'selected' : '' ?>><?= h($t['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><label class="<?= h($lbl) ?>">Documento</label><input name="documento" class="<?= h($inp) ?>" value="<?= h((string) ($ef['documento'] ?? '')) ?>" required></div>
      <div><label class="<?= h($lbl) ?>">Nombres</label><input name="nombre" class="<?= h($inp) ?>" value="<?= h((string) ($ef['nombre'] ?? '')) ?>" required></div>
      <div><label class="<?= h($lbl) ?>">Apellidos</label><input name="apellido" class="<?= h($inp) ?>" value="<?= h((string) ($ef['apellido'] ?? '')) ?>" required></div>
      <div><label class="<?= h($lbl) ?>">Correo</label><input name="correo" type="email" class="<?= h($inp) ?>" value="<?= h((string) ($ef['correo'] ?? '')) ?>" required></div>
      <div>
        <label class="<?= h($lbl) ?>">Sexo</label>
        <select name="sexo" class="<?= h($inp) ?>">
          <?php foreach (diccionario_sexo() as $s): ?>
            <option value="<?= h($s['codigo']) ?>" <?= (($ef['sexo'] ?? '') === $s['codigo']) ? 'selected' : '' ?>><?= h($s['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="<?= h($lbl) ?>">Carrera</label>
        <select name="id_programa" class="<?= h($inp) ?>" required>
          <option value="">Seleccione…</option>
          <?php foreach (diccionario_programas() as $p): ?>
            <option value="<?= (int) $p['id'] ?>" <?= (int) ($ef['id_programa'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>><?= h('[' . $p['codigo'] . '] ' . $p['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><label class="<?= h($lbl) ?>">Semestre (1–10)</label><input name="semestre" type="number" min="1" max="10" class="<?= h($inp) ?>" value="<?= h((string) ($ef['semestre'] ?? '1')) ?>"></div>
      <div><label class="<?= h($lbl) ?>">Fecha nacimiento</label><input name="fecha_nacimiento" type="date" class="<?= h($inp) ?>" value="<?= h((string) ($ef['fecha_nacimiento'] ?? '')) ?>"></div>
      <div><label class="<?= h($lbl) ?>">Dirección</label><input name="direccion" class="<?= h($inp) ?>" value="<?= h((string) ($ef['direccion'] ?? '')) ?>"></div>
      <div><label class="<?= h($lbl) ?>">Barrio</label><input name="barrio" class="<?= h($inp) ?>" value="<?= h((string) ($ef['barrio'] ?? '')) ?>"></div>
      <div><label class="<?= h($lbl) ?>">Teléfono</label><input name="telefono" class="<?= h($inp) ?>" value="<?= h((string) ($ef['telefono'] ?? '')) ?>"></div>
      <div>
        <label class="<?= h($lbl) ?>">Sede</label>
        <select name="id_sede" class="<?= h($inp) ?>">
          <?php foreach (diccionario_sedes() as $s): ?>
            <option value="<?= (int) $s['id'] ?>" <?= (int) ($ef['id_sede'] ?? 1) === (int) $s['id'] ? 'selected' : '' ?>><?= h($s['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="<?= h($lbl) ?>">Jornada</label>
        <select name="id_jornada" class="<?= h($inp) ?>">
          <?php foreach (diccionario_jornadas() as $j): ?>
            <option value="<?= (int) $j['id'] ?>" <?= (int) ($ef['id_jornada'] ?? 1) === (int) $j['id'] ? 'selected' : '' ?>><?= h($j['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="<?= h($lbl) ?>">Contraseña<?= $editar ? ' (dejar vacío para no cambiar)' : '' ?></label>
        <input name="clave" type="password" class="<?= h($inp) ?>" <?= $editar ? '' : 'required' ?>>
      </div>
      <div>
        <label class="<?= h($lbl) ?>">Confirmar contraseña</label>
        <input name="clave_confirmar" type="password" class="<?= h($inp) ?>" <?= $editar ? '' : 'required' ?>>
      </div>
    </div>
    <div class="flex flex-wrap gap-3">
      <button type="submit" class="rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white hover:bg-academic-dark"><?= $editar ? 'Actualizar' : 'Guardar' ?></button>
      <?php if ($editar): ?>
        <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50" href="<?= h(url('gestion/estudiantes.php')) ?>">Cancelar edición</a>
      <?php endif; ?>
    </div>
  </form>

  <h2 class="mb-3 text-lg font-semibold text-academic">Listado</h2>
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
          <tr>
            <td class="px-3 py-2"><?= (int) $e['id_estudiante'] ?></td>
            <td class="px-3 py-2"><?= h($e['documento'] ?? '') ?></td>
            <td class="px-3 py-2"><?= h(trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? ''))) ?></td>
            <td class="max-w-xs px-3 py-2 text-xs"><?= h($e['programa'] ?? '') ?></td>
            <td class="px-3 py-2 text-right whitespace-nowrap">
              <a class="mr-1 inline-flex rounded-lg border border-blue-600 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-50" href="<?= h(url('gestion/estudiantes.php?editar=' . (int) $e['id_estudiante'])) ?>">Editar</a>
              <form method="post" class="inline" onsubmit="return confirm('¿Eliminar este estudiante?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_estudiante" value="<?= (int) $e['id_estudiante'] ?>">
                <button type="submit" class="inline-flex rounded-lg border border-red-300 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-50">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$estudiantes): ?>
          <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">Sin registros.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
