<?php
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
$mWarn = ($mensaje ?? '') !== '' && ($tipoMsg ?? '') === 'warning';
$alertClass = $mWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
if (($tipoMsg ?? '') === 'warning' && !$mWarn) {
    $alertClass = 'border-amber-200 bg-amber-50 text-amber-900';
}
?>
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold text-academic">Mis solicitudes</h1>
      <p class="mt-1 text-sm text-gray-600">Radique trámites según el catálogo institucional y consulte respuestas.</p>
    </div>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('estudiante/dashboard.php')) ?>">Volver al inicio</a>
  </div>

  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="mb-10 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Nueva solicitud</h2>
    <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <input type="hidden" name="accion" value="nueva_solicitud">
      <div class="md:col-span-2">
        <label class="<?= h($lbl) ?>">Tipo de solicitud</label>
        <select name="id_tipo_solicitud" class="<?= h($inp) ?>" required>
          <option value="">Seleccione…</option>
          <?php foreach (diccionario_tipos_solicitud() as $t): ?>
            <option value="<?= (int) $t['id'] ?>"><?= h((string) $t['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="<?= h($lbl) ?>">Descripción del caso <span class="text-gray-500">(mín. 10 caracteres)</span></label>
        <textarea name="descripcion" class="<?= h($inp) ?>" rows="4" required placeholder="Explique su solicitud con el detalle necesario"></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="<?= h($lbl) ?>">Documento del docente relacionado <span class="text-gray-500">(opcional, si aplica)</span></label>
        <input type="text" name="documento_docente_relacionado" class="<?= h($inp) ?>" placeholder="Solo números, sin puntos" inputmode="numeric">
      </div>
      <div class="md:col-span-2">
        <label class="<?= h($lbl) ?>">Evidencias (PDF o imágenes, máx. 8 archivos, 5 MB c/u)</label>
        <input type="file" name="anexos[]" class="<?= h($inp) ?>" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,application/pdf,image/*" multiple>
      </div>
      <div class="md:col-span-2">
        <button type="submit" class="inline-flex rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark">Enviar solicitud</button>
      </div>
    </form>
  </div>

  <div class="mb-10 rounded-xl border border-amber-100 bg-amber-50/40 p-6 shadow-sm">
    <h2 class="mb-4 text-base font-semibold text-amber-950">En trámite (pendiente o en revisión)</h2>
    <div class="overflow-x-auto rounded-lg border border-amber-200/80 bg-white">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-amber-100/80"><tr>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Fecha</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Tipo</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Estado</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Descripción</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Anexos</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Respuesta</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($activas ?? [] as $s): ?>
            <?php $idSol = (int) ($s['id_solicitud'] ?? 0); $ax = $s['anexos_archivos'] ?? []; ?>
            <tr>
              <td class="whitespace-nowrap px-3 py-2"><?= h((string) ($s['fecha_registro'] ?? '')) ?></td>
              <td class="px-3 py-2"><?= h(tipo_solicitud_nombre((int) ($s['id_tipo_solicitud'] ?? 0))) ?></td>
              <td class="px-3 py-2"><?= h(solicitud_estado_nombre((string) ($s['estado'] ?? ''))) ?></td>
              <td class="max-w-md px-3 py-2 text-xs text-gray-800"><?= nl2br(h((string) ($s['descripcion'] ?? ''))) ?></td>
              <td class="max-w-[7rem] px-3 py-2 text-xs">
                <?php if (is_array($ax) && $ax !== []): ?>
                  <?php foreach ($ax as $i => $m): ?>
                    <a class="text-academic hover:underline" href="<?= h(url('descargar_anexo.php?s=' . $idSol . '&f=' . $i)) ?>"><?= h((string) ($m['original'] ?? 'archivo')) ?></a><br>
                  <?php endforeach; ?>
                <?php else: ?>—<?php endif; ?>
              </td>
              <td class="max-w-xs px-3 py-2 text-xs"><?= nl2br(h((string) ($s['respuesta'] ?? ''))) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($activas)): ?>
            <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">No tiene solicitudes activas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="mb-10 rounded-xl border border-green-100 bg-green-50/40 p-6 shadow-sm">
    <h2 class="mb-4 text-base font-semibold text-green-950">Aprobadas</h2>
    <div class="overflow-x-auto rounded-lg border border-green-200/80 bg-white">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-green-100/80"><tr>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Fecha</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Tipo</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Estado</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Descripción</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Anexos</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Respuesta</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($aprobadas ?? [] as $s): ?>
            <?php $idSol = (int) ($s['id_solicitud'] ?? 0); $ax = $s['anexos_archivos'] ?? []; ?>
            <tr>
              <td class="whitespace-nowrap px-3 py-2"><?= h((string) ($s['fecha_registro'] ?? '')) ?></td>
              <td class="px-3 py-2"><?= h(tipo_solicitud_nombre((int) ($s['id_tipo_solicitud'] ?? 0))) ?></td>
              <td class="px-3 py-2"><?= h(solicitud_estado_nombre((string) ($s['estado'] ?? ''))) ?></td>
              <td class="max-w-md px-3 py-2 text-xs text-gray-800"><?= nl2br(h((string) ($s['descripcion'] ?? ''))) ?></td>
              <td class="max-w-[7rem] px-3 py-2 text-xs">
                <?php if (is_array($ax) && $ax !== []): ?>
                  <?php foreach ($ax as $i => $m): ?>
                    <a class="text-academic hover:underline" href="<?= h(url('descargar_anexo.php?s=' . $idSol . '&f=' . $i)) ?>"><?= h((string) ($m['original'] ?? 'archivo')) ?></a><br>
                  <?php endforeach; ?>
                <?php else: ?>—<?php endif; ?>
              </td>
              <td class="max-w-xs px-3 py-2 text-xs"><?= nl2br(h((string) ($s['respuesta'] ?? ''))) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($aprobadas)): ?>
            <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Aún no tiene solicitudes aprobadas.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 text-base font-semibold text-gray-800">Rechazadas u otras</h2>
    <div class="overflow-x-auto rounded-lg border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50"><tr>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Fecha</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Tipo</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Estado</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Descripción</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Anexos</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Respuesta</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($rechazadasOtras ?? [] as $s): ?>
            <?php $idSol = (int) ($s['id_solicitud'] ?? 0); $ax = $s['anexos_archivos'] ?? []; ?>
            <tr>
              <td class="whitespace-nowrap px-3 py-2"><?= h((string) ($s['fecha_registro'] ?? '')) ?></td>
              <td class="px-3 py-2"><?= h(tipo_solicitud_nombre((int) ($s['id_tipo_solicitud'] ?? 0))) ?></td>
              <td class="px-3 py-2"><?= h(solicitud_estado_nombre((string) ($s['estado'] ?? ''))) ?></td>
              <td class="max-w-md px-3 py-2 text-xs text-gray-800"><?= nl2br(h((string) ($s['descripcion'] ?? ''))) ?></td>
              <td class="max-w-[7rem] px-3 py-2 text-xs">
                <?php if (is_array($ax) && $ax !== []): ?>
                  <?php foreach ($ax as $i => $m): ?>
                    <a class="text-academic hover:underline" href="<?= h(url('descargar_anexo.php?s=' . $idSol . '&f=' . $i)) ?>"><?= h((string) ($m['original'] ?? 'archivo')) ?></a><br>
                  <?php endforeach; ?>
                <?php else: ?>—<?php endif; ?>
              </td>
              <td class="max-w-xs px-3 py-2 text-xs"><?= nl2br(h((string) ($s['respuesta'] ?? ''))) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($rechazadasOtras)): ?>
            <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Sin registros en esta categoría.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
