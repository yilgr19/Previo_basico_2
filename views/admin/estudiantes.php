<?php
/**
 * Vista: registro de estudiantes (formulario ampliado).
 * Variables: $mensaje, $tipoMsg, $estudiantes, $editar
 */
$defaults = [
    'tipo_identificacion' => '',
    'documento' => '',
    'nombre' => '',
    'apellido' => '',
    'correo' => '',
    'sexo' => '',
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
$edadMostrar = '';
if (!empty($ef['fecha_nacimiento'])) {
    $ea = calcular_edad_desde_fecha_ymd((string) $ef['fecha_nacimiento']);
    if ($ea !== null) {
        $edadMostrar = $ea . ' años';
    }
}
$alertMsg = match ($tipoMsg ?? '') {
    'success' => 'border-green-200 bg-green-50 text-green-900',
    'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
    'danger' => 'border-red-200 bg-red-50 text-red-900',
    default => 'border-sky-200 bg-sky-50 text-sky-900',
};
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
?>
<main class="flex-1 w-full mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-semibold text-academic">Registrar estudiante</h1>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('admin/dashboard.php')) ?>">Volver al panel</a>
  </div>

  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertMsg) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="mb-8 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
    <div class="border-b border-blue-100 bg-blue-50/80 px-4 py-4 sm:px-6">
      <h2 class="text-lg font-semibold text-academic">Registro de estudiante</h2>
      <p class="mt-1 text-sm text-gray-600">Complete los datos del estudiante para realizar el registro.</p>
    </div>
    <div class="p-4 sm:p-6">
      <form method="post" class="grid grid-cols-1 gap-4 md:grid-cols-2" id="form-estudiante" autocomplete="off">
        <input type="hidden" name="accion" value="guardar">
        <?php if ($editar): ?>
          <input type="hidden" name="id_estudiante" value="<?= (int) $ef['id_estudiante'] ?>">
        <?php endif; ?>

        <div>
          <label class="<?= h($lbl) ?>">Tipo de identificación</label>
          <select name="tipo_identificacion" class="<?= h($inp) ?>" required>
            <option value="">Seleccione...</option>
            <?php foreach (diccionario_tipos_identificacion() as $t): ?>
              <option value="<?= h($t['codigo']) ?>" <?= ($ef['tipo_identificacion'] ?? '') === $t['codigo'] ? 'selected' : '' ?>><?= h($t['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Número de identificación</label>
          <input type="text" name="documento" class="<?= h($inp) ?>" required placeholder="Ingrese el número"
            value="<?= h((string) ($ef['documento'] ?? '')) ?>">
        </div>

        <div>
          <label class="<?= h($lbl) ?>">Nombres</label>
          <input type="text" name="nombre" class="<?= h($inp) ?>" required placeholder="Ingrese los nombres"
            value="<?= h((string) ($ef['nombre'] ?? '')) ?>">
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Apellidos</label>
          <input type="text" name="apellido" class="<?= h($inp) ?>" required placeholder="Ingrese los apellidos"
            value="<?= h((string) ($ef['apellido'] ?? '')) ?>">
        </div>

        <div>
          <label class="<?= h($lbl) ?>">Correo</label>
          <input type="email" name="correo" class="<?= h($inp) ?>" required placeholder="ejemplo@correo.com"
            value="<?= h((string) ($ef['correo'] ?? '')) ?>">
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Sexo</label>
          <select name="sexo" class="<?= h($inp) ?>" required>
            <option value="">Seleccione...</option>
            <?php foreach (diccionario_sexo() as $s): ?>
              <option value="<?= h($s['codigo']) ?>" <?= ($ef['sexo'] ?? '') === $s['codigo'] ? 'selected' : '' ?>><?= h($s['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="<?= h($lbl) ?>">Carrera que cursa</label>
          <select name="id_programa" class="<?= h($inp) ?>" required>
            <option value="">Seleccione la carrera...</option>
            <?php foreach (diccionario_programas() as $p): ?>
              <option value="<?= (int) $p['id'] ?>" <?= (int) ($ef['id_programa'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
                <?= h('[' . $p['codigo'] . '] ' . $p['nombre']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Semestre</label>
          <select name="semestre" class="<?= h($inp) ?>" required>
            <option value="">Seleccione...</option>
            <?php for ($s = 1; $s <= 10; $s++): ?>
              <option value="<?= $s ?>" <?= (int) ($ef['semestre'] ?? 1) === $s ? 'selected' : '' ?>><?= $s ?>°</option>
            <?php endfor; ?>
          </select>
        </div>

        <div>
          <label class="<?= h($lbl) ?>">Fecha de nacimiento</label>
          <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="<?= h($inp) ?>" required
            value="<?= h((string) ($ef['fecha_nacimiento'] ?? '')) ?>">
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Edad</label>
          <input type="text" id="campo_edad" class="<?= h($inp) ?> cursor-not-allowed bg-gray-100" readonly placeholder="Se calcula automáticamente"
            value="<?= h($edadMostrar) ?>">
        </div>

        <div class="md:col-span-2">
          <label class="<?= h($lbl) ?>">Dirección</label>
          <input type="text" name="direccion" class="<?= h($inp) ?>" required placeholder="Ingrese la dirección"
            value="<?= h((string) ($ef['direccion'] ?? '')) ?>">
        </div>

        <div>
          <label class="<?= h($lbl) ?>">Barrio</label>
          <input type="text" name="barrio" class="<?= h($inp) ?>" required placeholder="Ingrese el barrio"
            value="<?= h((string) ($ef['barrio'] ?? '')) ?>">
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Teléfono</label>
          <input type="text" name="telefono" class="<?= h($inp) ?>" required placeholder="Ingrese el teléfono"
            value="<?= h((string) ($ef['telefono'] ?? '')) ?>">
        </div>

        <div>
          <label class="<?= h($lbl) ?>">Sede</label>
          <select name="id_sede" class="<?= h($inp) ?>" required>
            <?php foreach (diccionario_sedes() as $s): ?>
              <option value="<?= (int) $s['id'] ?>" <?= (int) ($ef['id_sede'] ?? 1) === (int) $s['id'] ? 'selected' : '' ?>><?= h($s['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Jornada</label>
          <select name="id_jornada" class="<?= h($inp) ?>" required>
            <?php foreach (diccionario_jornadas() as $j): ?>
              <option value="<?= (int) $j['id'] ?>" <?= (int) ($ef['id_jornada'] ?? 1) === (int) $j['id'] ? 'selected' : '' ?>><?= h($j['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="<?= h($lbl) ?>">Contraseña <?= $editar ? '(vacío = sin cambio)' : '' ?></label>
          <input type="password" name="clave" class="<?= h($inp) ?>" autocomplete="new-password" placeholder="Ingrese la contraseña">
        </div>
        <div>
          <label class="<?= h($lbl) ?>">Confirmar contraseña</label>
          <input type="password" name="clave_confirmar" class="<?= h($inp) ?>" autocomplete="new-password" placeholder="Repita la contraseña">
        </div>

        <div class="flex flex-wrap gap-2 pt-2 md:col-span-2">
          <button type="submit" class="inline-flex rounded-lg bg-academic px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark focus:outline-none focus:ring-2 focus:ring-blue-500"><?= $editar ? 'Actualizar estudiante' : 'Registrar estudiante' ?></button>
          <?php if ($editar): ?>
            <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50" href="<?= h(url('admin/estudiantes.php')) ?>">Cancelar edición</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <h2 class="mb-3 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Listado</h2>
  <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">ID</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Identificación</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Nombre</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Programa</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Sem.</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Sede</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Jornada</th>
          <th class="px-3 py-3 text-right font-semibold text-gray-700"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 bg-white">
        <?php foreach ($estudiantes as $e): ?>
          <tr class="hover:bg-gray-50/80">
            <td class="whitespace-nowrap px-3 py-2"><?= (int) $e['id_estudiante'] ?></td>
            <td class="max-w-xs px-3 py-2 text-xs"><?= h(tipo_identificacion_nombre((string) ($e['tipo_identificacion'] ?? ''))) ?><br><span class="text-gray-500"><?= h($e['documento'] ?? '') ?></span></td>
            <td class="px-3 py-2"><?= h(trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? ''))) ?></td>
            <td class="max-w-xs px-3 py-2 text-xs"><?= h($e['programa'] ?? '') ?></td>
            <td class="px-3 py-2"><?= (int) ($e['semestre'] ?? 0) ?></td>
            <td class="px-3 py-2"><?= h(sede_nombre(isset($e['id_sede']) ? (int) $e['id_sede'] : null)) ?></td>
            <td class="px-3 py-2"><?= h(jornada_nombre(isset($e['id_jornada']) ? (int) $e['id_jornada'] : null)) ?></td>
            <td class="whitespace-nowrap px-3 py-2 text-right">
              <a class="mr-1 inline-flex rounded-lg border border-blue-600 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-50" href="<?= h(url('admin/estudiantes.php?editar=' . (int) $e['id_estudiante'])) ?>">Editar</a>
              <form method="post" class="inline" onsubmit="return confirm('¿Eliminar este estudiante y sus matrículas?');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id_estudiante" value="<?= (int) $e['id_estudiante'] ?>">
                <button type="submit" class="inline-flex rounded-lg border border-red-300 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-50">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$estudiantes): ?>
          <tr><td colspan="8" class="px-3 py-8 text-center text-gray-500">No hay estudiantes registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<script src="<?= h(asset_url('js/admin-estudiantes-form.js')) ?>"></script>
