<?php
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
$ro = $inp . ' bg-gray-50 text-gray-800';
$mWarn = ($mensaje ?? '') !== '' && ($tipoMsg ?? '') === 'warning';
$alertClass = $mWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
if (($tipoMsg ?? '') === 'warning' && !$mWarn) {
    $alertClass = 'border-amber-200 bg-amber-50 text-amber-900';
}
$doc = $doc ?? [];
$old = $old ?? [];
$idEmp = trim((string) ($doc['codigo_empleado'] ?? ''));
if ($idEmp === '') {
    $idEmp = (string) ($doc['documento'] ?? '');
}
$hoy = date('Y-m-d');
$finMes = date('Y-m-d', strtotime('+30 days'));
$valFechaIni = ($old['fecha_inicio'] ?? '') !== '' ? (string) $old['fecha_inicio'] : $hoy;
$valFechaFin = ($old['fecha_fin'] ?? '') !== '' ? (string) $old['fecha_fin'] : $finMes;
?>
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold text-academic">Nueva solicitud</h1>
      <p class="mt-1 text-sm text-gray-600">Radique una solicitud institucional con el catálogo docente.</p>
    </div>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('docente/dashboard.php')) ?>">Volver al panel</a>
  </div>

  <?php require dirname(__DIR__) . '/partials/sol_nav_docente.php'; ?>

  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>">
      <?= h($mensaje) ?>
      <?php if (($tipoMsg ?? '') === 'warning'): ?>
        <span class="mt-2 block text-[13px] font-normal">Puede corregir y enviar de nuevo: <strong class="font-medium text-gray-800">conservamos lo que escribió</strong> en el formulario (excepto archivos adjuntos; vuelva a seleccionarlos si eran obligatorios).</span>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Formulario de radicación (docente)</h2>
    <form method="post" enctype="multipart/form-data" action="<?= h(url('docente/nueva_solicitud.php')) ?>" class="space-y-8">
      <input type="hidden" name="accion" value="nueva_solicitud_docente">

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">1. Perfil y vinculación</legend>
        <p class="mb-3 text-xs text-gray-500">Datos de origen según su registro (enrutamiento y aprobaciones).</p>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <span class="<?= h($lbl) ?>">ID de empleado / código</span>
            <div class="<?= h($ro) ?>"><?= h($idEmp) ?></div>
          </div>
          <div>
            <span class="<?= h($lbl) ?>">Documento</span>
            <div class="<?= h($ro) ?>"><?= h((string) ($doc['documento'] ?? '—')) ?></div>
          </div>
          <div class="md:col-span-2">
            <span class="<?= h($lbl) ?>">Unidad académica</span>
            <div class="<?= h($ro) ?>"><?= h(trim((string) ($doc['unidad_academica'] ?? '')) !== '' ? (string) $doc['unidad_academica'] : '—') ?></div>
          </div>
          <div>
            <span class="<?= h($lbl) ?>">Categoría docente</span>
            <div class="<?= h($ro) ?>"><?= h(categoria_docente_nombre((string) ($doc['categoria_docente'] ?? ''))) ?></div>
          </div>
          <div>
            <span class="<?= h($lbl) ?>">Tipo de contrato</span>
            <div class="<?= h($ro) ?>"><?= h(tipo_contrato_docente_nombre((string) ($doc['tipo_contrato'] ?? ''))) ?></div>
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">2. Clasificación de la solicitud</legend>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Tipo de solicitud (catálogo docente)</label>
            <select name="id_tipo_solicitud_docente" class="<?= h($inp) ?>" required>
              <option value="">Seleccione…</option>
              <?php foreach (diccionario_tipos_solicitud_docente() as $t): ?>
                <option value="<?= (int) $t['id'] ?>" <?= ((string) ($old['id_tipo_solicitud_docente'] ?? '')) === (string) (int) $t['id'] ? 'selected' : '' ?>><?= h((string) $t['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Asunto</label>
            <input type="text" name="asunto" class="<?= h($inp) ?>" required minlength="3" placeholder="Título breve de la petición" value="<?= h((string) ($old['asunto'] ?? '')) ?>">
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Nivel de prioridad</label>
            <select name="prioridad" class="<?= h($inp) ?>" required>
              <option value="">Seleccione…</option>
              <?php foreach (diccionario_prioridad_solicitud_docente() as $p): ?>
                <option value="<?= h((string) $p['codigo']) ?>" <?= ((string) ($old['prioridad'] ?? '')) === (string) $p['codigo'] ? 'selected' : '' ?>><?= h((string) $p['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">3. Carga académica afectada</legend>
        <p class="mb-3 text-xs text-gray-600">Si impacta sus clases, detalle el grupo y el plan de contingencia.</p>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="<?= h($lbl) ?>">ID de grupo (NRC / código)</label>
            <input type="text" name="nrc" class="<?= h($inp) ?>" placeholder="Ej. NRC o código de grupo" autocomplete="off" value="<?= h((string) ($old['nrc'] ?? '')) ?>">
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Nombre de la asignatura</label>
            <input type="text" name="nombre_materia" class="<?= h($inp) ?>" placeholder="Para trazabilidad" value="<?= h((string) ($old['nombre_materia'] ?? '')) ?>">
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Horario impactado</label>
            <textarea name="horario_impactado" class="<?= h($inp) ?>" rows="2" placeholder="Días y horas exactas afectados"><?= h((string) ($old['horario_impactado'] ?? '')) ?></textarea>
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Plan de contingencia (reposición / cobertura)</label>
            <textarea name="plan_contingencia" class="<?= h($inp) ?>" rows="3" placeholder="Cómo recuperará horas o quién cubrirá"><?= h((string) ($old['plan_contingencia'] ?? '')) ?></textarea>
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">4. Cuerpo y justificación</legend>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Descripción detallada <span class="text-gray-500">(mín. 10 caracteres)</span></label>
            <textarea name="descripcion_detallada" class="<?= h($inp) ?>" rows="5" required placeholder="Explique la necesidad"><?= h((string) ($old['descripcion_detallada'] ?? '')) ?></textarea>
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Sustento legal / normativo <span class="text-gray-500">(opcional)</span></label>
            <textarea name="sustento_legal" class="<?= h($inp) ?>" rows="2" placeholder="Estatuto docente, reglamento, etc."><?= h((string) ($old['sustento_legal'] ?? '')) ?></textarea>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Fecha de inicio</label>
            <input type="date" name="fecha_inicio" class="<?= h($inp) ?>" value="<?= h($valFechaIni) ?>" required>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Fecha de fin</label>
            <input type="date" name="fecha_fin" class="<?= h($inp) ?>" value="<?= h($valFechaFin) ?>" required>
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Otro documento de docente relacionado <span class="text-gray-500">(opcional)</span></label>
            <input type="text" name="documento_docente_relacionado" class="<?= h($inp) ?>" placeholder="Solo números" inputmode="numeric" value="<?= h((string) ($old['documento_docente_relacionado'] ?? '')) ?>">
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">5. Soportes y anexos</legend>
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="<?= h($lbl) ?>">Anexos generales</label>
            <input type="file" name="anexos[]" class="<?= h($inp) ?>" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,application/pdf,image/*" multiple>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Documentación de terceros <span class="text-gray-500">(invitaciones, certificados médicos, actas, etc.)</span></label>
            <input type="file" name="anexos_terceros[]" class="<?= h($inp) ?>" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,application/pdf,image/*" multiple>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Formatos institucionales digitalizados</label>
            <input type="file" name="anexos_formatos[]" class="<?= h($inp) ?>" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,application/pdf,image/*" multiple>
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">6. Declaración de responsabilidad</legend>
        <label class="flex cursor-pointer items-start gap-3 text-sm text-gray-800">
          <input type="checkbox" name="consentimiento_responsabilidad" value="1" required class="mt-1" <?= !empty($old['consentimiento_responsabilidad']) ? 'checked' : '' ?>>
          <span>Confirmo que mi ausencia o cambio no impedirá el cumplimiento del microcurrículo o que he dejado actividades programadas para los estudiantes.</span>
        </label>
      </fieldset>

      <div>
        <button type="submit" class="inline-flex rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark">Enviar solicitud</button>
      </div>
    </form>
  </div>
</main>
