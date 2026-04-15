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
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-semibold text-academic">Mi perfil</h1>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('estudiante/dashboard.php')) ?>">Volver al inicio</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <form method="post" class="space-y-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <input type="hidden" name="accion" value="guardar_perfil">
    <input type="hidden" name="id_estudiante" value="<?= (int) ($ef['id_estudiante'] ?? 0) ?>">
    <p class="text-sm text-gray-600">Actualice sus datos de contacto y ubicación. El documento debe coincidir con el registrado.</p>
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
        <label class="<?= h($lbl) ?>">Nueva contraseña (opcional)</label>
        <input name="clave" type="password" class="<?= h($inp) ?>" autocomplete="new-password">
      </div>
      <div>
        <label class="<?= h($lbl) ?>">Confirmar contraseña</label>
        <input name="clave_confirmar" type="password" class="<?= h($inp) ?>" autocomplete="new-password">
      </div>
    </div>
    <button type="submit" class="rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white hover:bg-academic-dark">Guardar cambios</button>
  </form>
</main>
