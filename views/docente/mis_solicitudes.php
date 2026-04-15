<?php
$mWarn = ($mensaje ?? '') !== '' && ($tipoMsg ?? '') === 'warning';
$alertClass = $mWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
if (($tipoMsg ?? '') === 'warning' && !$mWarn) {
    $alertClass = 'border-amber-200 bg-amber-50 text-amber-900';
}
$tab = $tab ?? 'activas';
$listaTab = $listaTab ?? [];
$conteosSolicitudes = $conteosSolicitudes ?? ['activas' => 0, 'en_revision' => 0, 'aprobadas' => 0, 'rechazadas' => 0];
$uSolic = h(url('docente/mis_solicitudes.php'));
?>
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold text-academic">Mis solicitudes</h1>
      <p class="mt-1 text-sm text-gray-600">Historial de lo radicado y menciones en solicitudes de estudiantes (vista confidencial).</p>
    </div>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('docente/dashboard.php')) ?>">Volver al panel</a>
  </div>

  <?php require dirname(__DIR__) . '/partials/sol_nav_docente.php'; ?>

  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <section class="mb-10 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
    <h2 class="mb-2 text-base font-semibold text-academic">Solicitudes radicadas</h2>
    <p class="mb-4 text-xs text-gray-500">Consulte lo enviado filtrando por estado. <strong>Activas</strong> son solicitudes pendientes de gestión.</p>
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
        <a href="<?= $uSolic ?>?tab=<?= h($k) ?>" title="<?= h($info['hint']) ?>" class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium <?= h($cls) ?>">
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
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Respuesta de la universidad</th>
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
              <td class="max-w-xs px-3 py-2 text-xs">
                <?php
                $txtCorto = trim((string) ($s['respuesta'] ?? ''));
                $re = $s['respuesta_elaborada'] ?? null;
                $mom = solicitud_texto_momento_respuesta($s);
                ?>
                <?php if ($mom !== ''): ?>
                  <p class="mb-1 text-[10px] text-gray-600"><span class="font-mono">Registro de respuesta: <?= h($mom) ?></span> <span class="text-gray-500">· <?= h(etiqueta_hora_colombia()) ?></span></p>
                <?php endif; ?>
                <?php if ($txtCorto !== ''): ?>
                  <div class="mb-1 whitespace-pre-wrap text-gray-800"><?= nl2br(h($txtCorto)) ?></div>
                <?php endif; ?>
                <?php if (is_array($re)): ?>
                  <details class="mt-1 max-w-md">
                    <summary class="cursor-pointer font-medium text-academic hover:underline">Resolución formal<?php if (!empty($re['numero_respuesta'])): ?> (<?= h((string) $re['numero_respuesta']) ?>)<?php endif; ?></summary>
                    <div class="mt-2">
                      <?php require dirname(__DIR__) . '/partials/bloque_respuesta_elaborada_leer.php'; ?>
                    </div>
                  </details>
                <?php elseif ($txtCorto === '' && $mom === ''): ?>
                  <span class="text-gray-400">—</span>
                <?php endif; ?>
              </td>
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
            <tr><td colspan="7" class="px-3 py-8 text-center text-gray-500">No hay solicitudes en esta categoría.</td></tr>
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
