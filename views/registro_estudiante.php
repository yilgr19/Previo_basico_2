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
$ef = array_merge($defaults, $repoblar ?? []);
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
?>
<main class="flex w-full flex-1 flex-col px-4 py-10">
  <div class="mx-auto w-full max-w-3xl">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
      <h1 class="text-xl font-semibold text-academic">Registro de estudiante</h1>
      <a class="text-sm font-medium text-academic hover:underline" href="<?= h(url('login')) ?>">¿Ya tiene cuenta? Iniciar sesión</a>
    </div>

    <?php if (!empty($mostrarPendiente)): ?>
      <div class="rounded-xl border border-green-200 bg-green-50 p-6 text-sm text-green-900 shadow-sm">
        <h2 class="mb-2 text-base font-semibold">Revise su correo institucional</h2>
        <p class="mb-3"><?= h($mensaje ?? '') ?></p>
        <p class="text-green-800">El enlace de activación vence en <?= (int) (defined('REGISTRO_TOKEN_HORAS') ? REGISTRO_TOKEN_HORAS : 24) ?> horas. Hasta verificar el correo no podrá iniciar sesión.</p>
        <?php if (!empty($enlaceDev)): ?>
          <div class="mt-4 rounded-lg border border-amber-300 bg-amber-50 p-4 text-amber-950">
            <p class="mb-2 font-medium">Modo desarrollo (XAMPP)</p>
            <p class="mb-2 text-xs">En producción este enlace solo llega por correo. Aquí puede abrirlo directamente:</p>
            <a class="break-all font-mono text-xs text-blue-700 underline" href="<?= h($enlaceDev) ?>"><?= h($enlaceDev) ?></a>
          </div>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <?php if ($mensaje): ?>
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900"><?= h($mensaje) ?></div>
      <?php endif; ?>

      <div class="mb-4 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-900">
        Debe registrarse con su <strong>correo universitario</strong> (ejemplo: nombre.apellido<?= h($dominioEjemplo ?? '@fesc.edu.co') ?>).
        Enviaremos un enlace de verificación a ese correo para activar la cuenta.
      </div>

      <form method="post" class="space-y-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Correo institucional</label>
            <input name="correo" type="email" class="<?= h($inp) ?>" value="<?= h((string) ($ef['correo'] ?? '')) ?>" placeholder="usuario<?= h($dominioEjemplo ?? '@fesc.edu.co') ?>" required>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Sexo</label>
            <select name="sexo" class="<?= h($inp) ?>">
              <?php foreach (diccionario_sexo() as $s): ?>
                <option value="<?= h($s['codigo']) ?>" <?= (($ef['sexo'] ?? '') === $s['codigo']) ? 'selected' : '' ?>><?= h($s['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Semestre (1–10)</label>
            <input name="semestre" type="number" min="1" max="10" class="<?= h($inp) ?>" value="<?= h((string) ($ef['semestre'] ?? '1')) ?>">
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
          <div><label class="<?= h($lbl) ?>">Fecha nacimiento</label><input name="fecha_nacimiento" type="date" class="<?= h($inp) ?>" value="<?= h((string) ($ef['fecha_nacimiento'] ?? '')) ?>" required></div>
          <div><label class="<?= h($lbl) ?>">Teléfono</label><input name="telefono" class="<?= h($inp) ?>" value="<?= h((string) ($ef['telefono'] ?? '')) ?>" required></div>
          <div><label class="<?= h($lbl) ?>">Dirección</label><input name="direccion" class="<?= h($inp) ?>" value="<?= h((string) ($ef['direccion'] ?? '')) ?>" required></div>
          <div><label class="<?= h($lbl) ?>">Barrio</label><input name="barrio" class="<?= h($inp) ?>" value="<?= h((string) ($ef['barrio'] ?? '')) ?>" required></div>
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
            <label class="<?= h($lbl) ?>">Contraseña (mín. <?= (int) ($claveMin ?? 8) ?> caracteres)</label>
            <input name="clave" type="password" class="<?= h($inp) ?>" minlength="<?= (int) ($claveMin ?? 8) ?>" required>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Confirmar contraseña</label>
            <input name="clave_confirmar" type="password" class="<?= h($inp) ?>" minlength="<?= (int) ($claveMin ?? 8) ?>" required>
          </div>
        </div>
        <button type="submit" class="rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white hover:bg-academic-dark">Registrarme y verificar correo</button>
      </form>
    <?php endif; ?>
  </div>
</main>
