<?php
$mWarn = ($mensaje ?? '') !== '' && ($tipoMsg ?? '') === 'warning';
$alertClass = $mWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
if (($tipoMsg ?? '') === 'warning' && !$mWarn) {
    $alertClass = 'border-amber-200 bg-amber-50 text-amber-900';
}
?>
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold text-academic">Solicitudes institucionales</h1>
      <p class="mt-1 text-sm text-gray-600">Revise solicitudes de estudiantes y de docentes, filtre y responda según el estado del trámite.</p>
    </div>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('gestion/dashboard.php')) ?>">Volver al panel</a>
  </div>

  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <form method="get" action="<?= h(url('gestion/solicitudes.php')) ?>" class="mb-6 space-y-4 rounded-xl border border-gray-100 bg-white p-4 shadow-sm">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Desde (fecha registro)</label>
        <input type="date" name="fecha_desde" value="<?= h($filtroFechaDesde ?? '') ?>" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Hasta (fecha registro)</label>
        <input type="date" name="fecha_hasta" value="<?= h($filtroFechaHasta ?? '') ?>" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Estado</label>
        <select name="estado" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
          <option value="">Todos</option>
          <?php foreach (diccionario_estados_solicitud() as $e): ?>
            <option value="<?= h($e['codigo']) ?>" <?= (($filtroEstado ?? '') === $e['codigo']) ? 'selected' : '' ?>><?= h($e['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">Aprobación</label>
        <select name="aprobacion" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
          <option value="">Todas</option>
          <option value="aprobadas" <?= (($filtroAprob ?? '') === 'aprobadas') ? 'selected' : '' ?>>Solo aprobadas</option>
          <option value="no_aprobadas" <?= (($filtroAprob ?? '') === 'no_aprobadas') ? 'selected' : '' ?>>No aprobadas (incl. pendiente, revisión, rechazada)</option>
        </select>
      </div>
    </div>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
      <div class="min-w-0 flex-1">
        <label class="mb-1 block text-sm font-medium text-gray-700">Buscar por documento (estudiante o docente indicado) o nombre</label>
        <div class="flex gap-2">
          <input type="search" name="buscar" value="<?= h($buscarDoc ?? '') ?>" placeholder="Ej. 1098… o parte del nombre" class="mt-1 block min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
          <button type="submit" class="mt-1 inline-flex shrink-0 items-center justify-center rounded-lg bg-academic px-4 py-2 text-white shadow-sm hover:bg-academic-dark" title="Buscar" aria-label="Buscar">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </button>
        </div>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Aplicar filtros</button>
        <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" href="<?= h(url('gestion/solicitudes.php')) ?>">Limpiar</a>
      </div>
    </div>
  </form>

  <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">ID</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Fecha</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Radicante</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Tipo</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Doc. mencionado</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Anexos</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Estado</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Detalle / gestión</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php foreach ($items as $row):
            $s = $row['solicitud'];
            $e = $row['estudiante'];
            $ds = $row['docente_solicitante'] ?? null;
            $idSol = (int) ($s['id_solicitud'] ?? 0);
            $nomEst = $e ? trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? '')) : '';
            $anexos = $s['anexos_archivos'] ?? [];
            ?>
          <tr class="align-top">
            <td class="px-3 py-2 font-mono"><?= $idSol ?></td>
            <td class="whitespace-nowrap px-3 py-2"><?= h((string) ($s['fecha_registro'] ?? '')) ?></td>
            <td class="max-w-xs px-3 py-2">
              <?php if ($e): ?>
                <span class="text-[10px] font-semibold uppercase text-gray-500">Estudiante</span>
                <div class="font-medium text-gray-900"><?= h($nomEst) ?></div>
                <div class="text-xs text-gray-500">Doc. <?= h((string) ($s['documento_estudiante'] ?? ($e['documento'] ?? ''))) ?></div>
              <?php elseif ($ds): ?>
                <span class="text-[10px] font-semibold uppercase text-gray-500">Docente</span>
                <div class="font-medium text-gray-900"><?= h(trim(($ds['nombre'] ?? '') . ' ' . ($ds['apellido'] ?? ''))) ?></div>
                <div class="text-xs text-gray-500">Doc. <?= h((string) ($ds['documento'] ?? '')) ?></div>
              <?php else: ?>
                <span class="text-gray-400">—</span>
              <?php endif; ?>
            </td>
            <td class="max-w-[10rem] px-3 py-2 text-xs"><?= h(solicitud_tipo_etiqueta($s)) ?></td>
            <td class="px-3 py-2 font-mono text-xs"><?= h((string) ($s['documento_docente_relacionado'] ?? '') ?: '—') ?></td>
            <td class="max-w-[8rem] px-3 py-2 text-xs">
              <?php if (is_array($anexos) && $anexos !== []): ?>
                <?php foreach ($anexos as $i => $m): ?>
                  <?php $cat = (string) ($m['categoria'] ?? 'general'); ?>
                  <a class="block text-academic hover:underline" href="<?= h(url('descargar_anexo.php?s=' . $idSol . '&f=' . $i)) ?>"><?= h((string) ($m['original'] ?? 'archivo')) ?><?php if ($cat !== '' && $cat !== 'general'): ?> <span class="text-gray-500">(<?= h(solicitud_etiqueta_categoria_anexo($cat)) ?>)</span><?php endif; ?></a>
                <?php endforeach; ?>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
            <td class="px-3 py-2"><?= h(solicitud_estado_nombre((string) ($s['estado'] ?? ''))) ?></td>
            <td class="min-w-[14rem] px-3 py-2">
              <details class="mb-2">
                <summary class="cursor-pointer text-xs font-medium text-academic hover:underline">Ver detalle</summary>
                <div class="mt-1 space-y-2 rounded border border-gray-100 bg-gray-50 p-2 text-xs text-gray-800">
                  <div><?= nl2br(h(solicitud_resumen_texto($s))) ?></div>
                  <?php
                  $de = $s['detalle_estudiante'] ?? null;
                  if (is_array($de)):
                      $cl = $de['clasificacion'] ?? [];
                      $cu = $de['cuerpo'] ?? [];
                  ?>
                    <dl class="grid grid-cols-1 gap-1 border-t border-gray-200 pt-2 sm:grid-cols-2">
                      <dt class="font-semibold text-gray-600">Periodo</dt><dd><?= h((string) ($cl['periodo_academico'] ?? '—')) ?></dd>
                      <dt class="font-semibold text-gray-600">Sede / jornada (petición)</dt><dd><?= h(sede_nombre((int) ($cl['id_sede_solicitud'] ?? 0))) ?> / <?= h(jornada_nombre((int) ($cl['id_jornada_solicitud'] ?? 0))) ?></dd>
                      <dt class="font-semibold text-gray-600">Motivo</dt><dd><?= h((string) ($cu['motivo_label'] ?? '—')) ?></dd>
                      <?php if (!empty($cu['materias_etiquetas']) && is_array($cu['materias_etiquetas'])): ?>
                        <dt class="font-semibold text-gray-600 sm:col-span-2">Asignaturas</dt>
                        <dd class="sm:col-span-2"><?= h(implode('; ', array_map('strval', $cu['materias_etiquetas']))) ?></dd>
                      <?php endif; ?>
                    </dl>
                  <?php endif; ?>
                  <?php
                  $dd = $s['detalle_docente'] ?? null;
                  if (is_array($dd)):
                      $cla = $dd['clasificacion'] ?? [];
                      $carga = $dd['carga_afectada'] ?? [];
                      $cuer = $dd['cuerpo'] ?? [];
                  ?>
                    <dl class="grid grid-cols-1 gap-1 border-t border-gray-200 pt-2 sm:grid-cols-2">
                      <dt class="font-semibold text-gray-600">Asunto</dt><dd><?= h((string) ($cla['asunto'] ?? '—')) ?></dd>
                      <dt class="font-semibold text-gray-600">Prioridad</dt><dd><?= h((string) ($cla['prioridad_label'] ?? '—')) ?></dd>
                      <dt class="font-semibold text-gray-600">NRC / materia</dt><dd><?= h(trim((string) ($carga['nrc'] ?? '') . ' — ' . (string) ($carga['nombre_materia'] ?? ''))) ?></dd>
                      <dt class="font-semibold text-gray-600">Periodo (fechas)</dt><dd><?= h((string) ($cuer['fecha_inicio'] ?? '—') . ' → ' . (string) ($cuer['fecha_fin'] ?? '—')) ?></dd>
                    </dl>
                  <?php endif; ?>
                </div>
              </details>
              <form method="post" class="space-y-2 rounded border border-gray-100 bg-gray-50 p-2">
                <input type="hidden" name="accion" value="cambiar_estado">
                <input type="hidden" name="id_solicitud" value="<?= $idSol ?>">
                <select name="estado" class="block w-full rounded border border-gray-300 px-2 py-1 text-xs">
                  <?php foreach (diccionario_estados_solicitud() as $opt): ?>
                    <option value="<?= h($opt['codigo']) ?>" <?= ((string) ($s['estado'] ?? '') === $opt['codigo']) ? 'selected' : '' ?>><?= h($opt['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
                <textarea name="respuesta" rows="2" class="block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Respuesta breve al estudiante"><?= h((string) ($s['respuesta'] ?? '')) ?></textarea>
                <button type="submit" class="w-full rounded bg-academic py-1 text-xs font-semibold text-white hover:bg-academic-dark">Guardar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$items): ?>
          <tr><td colspan="8" class="px-3 py-8 text-center text-gray-500">No hay solicitudes con los filtros indicados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
