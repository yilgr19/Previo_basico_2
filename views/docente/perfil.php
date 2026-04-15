<?php
$ef = $editar ?? [];
$mWarn = ($mensaje ?? '') !== '' && ($tipoMsg ?? '') === 'warning';
$alertClass = $mWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
if (($tipoMsg ?? '') === 'warning' && !$mWarn) {
    $alertClass = 'border-amber-200 bg-amber-50 text-amber-900';
}
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
$ro = $inp . ' bg-gray-50 text-gray-800';
?>
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-xl font-semibold text-academic">Mi perfil</h1>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('docente/dashboard.php')) ?>">Volver al panel</a>
  </div>
  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <p class="mb-4 text-sm text-gray-600">Actualice sus datos de contacto, sede, carrera a la que dicta y contraseña. Los datos institucionales (empleo) solo son informativos.</p>

  <?php if (!empty($ef['codigo_empleado']) || !empty($ef['unidad_academica']) || !empty($ef['categoria_docente']) || !empty($ef['tipo_contrato'])): ?>
  <div class="mb-6 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
    <h2 class="mb-3 text-sm font-semibold text-academic">Datos institucionales</h2>
    <div class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
      <?php if (!empty($ef['codigo_empleado'])): ?>
        <div><span class="text-gray-500">Código empleado</span><div class="<?= h($ro) ?>"><?= h((string) $ef['codigo_empleado']) ?></div></div>
      <?php endif; ?>
      <?php if (!empty($ef['unidad_academica'])): ?>
        <div class="md:col-span-2"><span class="text-gray-500">Unidad académica</span><div class="<?= h($ro) ?>"><?= h((string) $ef['unidad_academica']) ?></div></div>
      <?php endif; ?>
      <?php if (!empty($ef['categoria_docente'])): ?>
        <div><span class="text-gray-500">Categoría</span><div class="<?= h($ro) ?>"><?= h(categoria_docente_nombre((string) $ef['categoria_docente'])) ?></div></div>
      <?php endif; ?>
      <?php if (!empty($ef['tipo_contrato'])): ?>
        <div><span class="text-gray-500">Tipo de contrato</span><div class="<?= h($ro) ?>"><?= h(tipo_contrato_docente_nombre((string) $ef['tipo_contrato'])) ?></div></div>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <form method="post" class="space-y-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <input type="hidden" name="accion" value="guardar_perfil_docente">
    <input type="hidden" name="id_docente" value="<?= (int) ($ef['id_docente'] ?? 0) ?>">
    <h2 class="border-b border-blue-100 pb-2 text-base font-semibold text-academic">Datos editables</h2>
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
        <label class="<?= h($lbl) ?>">Carrera a la que dicta clase</label>
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
<script src="<?= h(asset_url('js/docentes-form.js')) ?>"></script>
