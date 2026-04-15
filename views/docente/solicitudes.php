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
$idEmp = trim((string) ($doc['codigo_empleado'] ?? ''));
if ($idEmp === '') {
    $idEmp = (string) ($doc['documento'] ?? '');
}
$hoy = date('Y-m-d');
$finMes = date('Y-m-d', strtotime('+30 days'));
$tab = $tab ?? 'activas';
$listaTab = $listaTab ?? [];
$conteosSolicitudes = $conteosSolicitudes ?? ['activas' => 0, 'en_revision' => 0, 'aprobadas' => 0, 'rechazadas' => 0];
$uSolic = h(url('docente/solicitudes.php'));
?>
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold text-academic">Solicitudes</h1>
      <p class="mt-1 text-sm text-gray-600">Radique solicitudes institucionales (catálogo docente) y consulte trámites donde figura su documento (vista confidencial).</p>
    </div>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('docente/dashboard.php')) ?>">Volver al panel</a>
  </div>

  <nav class="mb-6 flex flex-wrap gap-2 rounded-xl border border-gray-200 bg-white p-2 shadow-sm" aria-label="Secciones">
    <a href="#nueva-solicitud" class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Nueva solicitud</a>
    <a href="#mis-solicitudes" class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-academic hover:bg-blue-50">Mis solicitudes</a>
  </nav>

  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div id="nueva-solicitud" class="mb-10 scroll-mt-24 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Nueva solicitud (docente)</h2>
    <form method="post" enctype="multipart/form-data" class="space-y-8">
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
                <option value="<?= (int) $t['id'] ?>"><?= h((string) $t['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Asunto</label>
            <input type="text" name="asunto" class="<?= h($inp) ?>" required minlength="3" placeholder="Título breve de la petición">
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Nivel de prioridad</label>
            <select name="prioridad" class="<?= h($inp) ?>" required>
              <option value="">Seleccione…</option>
              <?php foreach (diccionario_prioridad_solicitud_docente() as $p): ?>
                <option value="<?= h((string) $p['codigo']) ?>"><?= h((string) $p['nombre']) ?></option>
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
            <input type="text" name="nrc" class="<?= h($inp) ?>" placeholder="Ej. NRC o código de grupo" autocomplete="off">
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Nombre de la asignatura</label>
            <input type="text" name="nombre_materia" class="<?= h($inp) ?>" placeholder="Para trazabilidad">
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Horario impactado</label>
            <textarea name="horario_impactado" class="<?= h($inp) ?>" rows="2" placeholder="Días y horas exactas afectados"></textarea>
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Plan de contingencia (reposición / cobertura)</label>
            <textarea name="plan_contingencia" class="<?= h($inp) ?>" rows="3" placeholder="Cómo recuperará horas o quién cubrirá"></textarea>
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">4. Cuerpo y justificación</legend>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Descripción detallada <span class="text-gray-500">(mín. 10 caracteres)</span></label>
            <textarea name="descripcion_detallada" class="<?= h($inp) ?>" rows="5" required placeholder="Explique la necesidad"></textarea>
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Sustento legal / normativo <span class="text-gray-500">(opcional)</span></label>
            <textarea name="sustento_legal" class="<?= h($inp) ?>" rows="2" placeholder="Estatuto docente, reglamento, etc."></textarea>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Fecha de inicio</label>
            <input type="date" name="fecha_inicio" class="<?= h($inp) ?>" value="<?= h($hoy) ?>" required>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Fecha de fin</label>
            <input type="date" name="fecha_fin" class="<?= h($inp) ?>" value="<?= h($finMes) ?>" required>
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Otro documento de docente relacionado <span class="text-gray-500">(opcional)</span></label>
            <input type="text" name="documento_docente_relacionado" class="<?= h($inp) ?>" placeholder="Solo números" inputmode="numeric">
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
          <input type="checkbox" name="consentimiento_responsabilidad" value="1" required class="mt-1">
          <span>Confirmo que mi ausencia o cambio no impedirá el cumplimiento del microcurrículo o que he dejado actividades programadas para los estudiantes.</span>
        </label>
      </fieldset>

      <div>
        <button type="submit" class="inline-flex rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark">Enviar solicitud</button>
      </div>
    </form>
  </div>

  <section id="mis-solicitudes" class="mb-10 scroll-mt-24 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <h2 class="mb-2 text-base font-semibold text-academic">Mis solicitudes</h2>
    <p class="mb-4 text-xs text-gray-500">Consulte todo lo radicado filtrando por estado. <strong>Activas</strong> son solicitudes pendientes de gestión.</p>
    <div class="mb-4 flex flex-wrap gap-2">
      <?php
      $filtrosDoc = [
          'activas' => ['label' => 'Activas', 'count' => (int) ($conteosSolicitudes['activas'] ?? 0), 'hint' => 'Pendiente'],
          'en_revision' => ['label' => 'En revisión', 'count' => (int) ($conteosSolicitudes['en_revision'] ?? 0), 'hint' => ''],
          'aprobadas' => ['label' => 'Aprobadas', 'count' => (int) ($conteosSolicitudes['aprobadas'] ?? 0), 'hint' => ''],
          'rechazadas' => ['label' => 'Rechazadas', 'count' => (int) ($conteosSolicitudes['rechazadas'] ?? 0), 'hint' => ''],
      ];
      foreach ($filtrosDoc as $k => $info):
          $active = $tab === $k;
          $cls = $active
              ? 'border-academic bg-academic text-white shadow-sm'
              : 'border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100';
          ?>
        <a href="<?= $uSolic ?>?tab=<?= h($k) ?>#mis-solicitudes" title="<?= h($info['hint']) ?>" class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium <?= h($cls) ?>">
          <?= h($info['label']) ?>
          <span class="<?= $active ? 'bg-white/20' : 'bg-gray-200/80' ?> rounded-full px-1.5 py-0.5 font-mono text-[10px]"><?= (int) $info['count'] ?></span>
        </a>
      <?php endforeach; ?>
    </div>
    <div class="overflow-x-auto rounded-lg border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50"><tr>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">ID</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Fecha</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Tipo</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Estado</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Descripción</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Anexos</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($listaTab as $s):
              $idSol = (int) ($s['id_solicitud'] ?? 0);
              $anexos = $s['anexos_archivos'] ?? [];
              ?>
            <tr>
              <td class="px-3 py-2 font-mono"><?= $idSol ?></td>
              <td class="whitespace-nowrap px-3 py-2"><?= h((string) ($s['fecha_registro'] ?? '')) ?></td>
              <td class="px-3 py-2"><?= h(solicitud_tipo_etiqueta($s)) ?></td>
              <td class="px-3 py-2"><?= h(solicitud_estado_nombre((string) ($s['estado'] ?? ''))) ?></td>
              <td class="max-w-md px-3 py-2 text-xs"><?= nl2br(h(solicitud_resumen_texto($s))) ?></td>
              <td class="px-3 py-2 text-xs">
                <?php if (is_array($anexos) && $anexos !== []): ?>
                  <?php foreach ($anexos as $i => $m): ?>
                    <?php $cat = (string) ($m['categoria'] ?? 'general'); ?>
                    <a class="text-academic hover:underline" href="<?= h(url('descargar_anexo.php?s=' . $idSol . '&f=' . $i)) ?>"><?= h((string) ($m['original'] ?? 'archivo')) ?></a><?php if ($cat !== '' && $cat !== 'general'): ?> <span class="text-gray-500">(<?= h(solicitud_etiqueta_categoria_anexo($cat)) ?>)</span><?php endif; ?><br>
                  <?php endforeach; ?>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if ($listaTab === []): ?>
            <tr><td colspan="6" class="px-3 py-8 text-center text-gray-500">No hay solicitudes en esta categoría.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <div class="mb-10 rounded-xl border border-indigo-100 bg-indigo-50/30 p-6 shadow-sm">
    <h2 class="mb-2 text-base font-semibold text-indigo-950">Donde usted es mencionado (estudiantes)</h2>
    <p class="mb-4 text-xs text-indigo-900/90">Solo se muestra el tipo de trámite y el estado. No puede ver texto, anexos ni datos del solicitante para proteger su confidencialidad.</p>
    <div class="overflow-x-auto rounded-lg border border-indigo-200/80 bg-white">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-indigo-100/80"><tr>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Referencia</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Fecha</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Tipo</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Estado</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach (($menciones ?? []) as $row):
              $s = $row['solicitud'];
              $idS = (int) ($s['id_solicitud'] ?? 0);
              ?>
            <tr>
              <td class="px-3 py-2 font-mono text-xs"><?= h(solicitud_referencia_anonima($idS)) ?></td>
              <td class="whitespace-nowrap px-3 py-2"><?= h((string) ($s['fecha_registro'] ?? '')) ?></td>
              <td class="px-3 py-2"><?= h(tipo_solicitud_nombre((int) ($s['id_tipo_solicitud'] ?? 0))) ?></td>
              <td class="px-3 py-2"><?= h(solicitud_estado_nombre((string) ($s['estado'] ?? ''))) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($menciones ?? [])): ?>
            <tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">No hay solicitudes de estudiantes que lo mencionen con su documento.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
