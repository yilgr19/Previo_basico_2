<?php
declare(strict_types=1);
/** @var array<string, mixed> $re */
$dec = strtolower(trim((string) ($re['decision'] ?? '')));
$borde = match ($dec) {
    'aprobado' => 'border-emerald-200 bg-emerald-50/80',
    'rechazado' => 'border-rose-200 bg-rose-50/80',
    default => 'border-amber-200 bg-amber-50/80',
};
?>
<div class="mt-2 rounded-lg border <?= h($borde) ?> p-3 text-left text-xs text-gray-800 shadow-sm">
  <p class="mb-2 font-semibold text-gray-900">Resolución formal</p>
  <dl class="grid gap-1 border-b border-gray-200/80 pb-2 text-[11px]">
    <div class="flex flex-wrap gap-x-2"><dt class="font-medium text-gray-600">N.º resolución</dt><dd class="font-mono"><?= h((string) ($re['numero_respuesta'] ?? '—')) ?></dd></div>
    <div class="flex flex-wrap gap-x-2"><dt class="font-medium text-gray-600">Solicitud ref.</dt><dd class="font-mono">#<?= (int) ($re['id_solicitud'] ?? 0) ?></dd></div>
    <div class="flex flex-wrap gap-x-2"><dt class="font-medium text-gray-600">Emitido</dt><dd><?= h((string) ($re['emitido_en'] ?? '—')) ?> <span class="text-gray-500">(<?= h(etiqueta_hora_colombia()) ?>)</span></dd></div>
    <div class="flex flex-wrap gap-x-2"><dt class="font-medium text-gray-600">Decisión</dt><dd><?= h(solicitud_decision_resolucion_nombre((string) ($re['decision'] ?? ''))) ?></dd></div>
  </dl>
  <?php if (trim((string) ($re['justificacion'] ?? '')) !== ''): ?>
    <div class="mt-2">
      <p class="font-semibold text-gray-700">Justificación / considerandos</p>
      <p class="mt-0.5 whitespace-pre-wrap text-gray-800"><?= h((string) $re['justificacion']) ?></p>
    </div>
  <?php endif; ?>
  <?php if (trim((string) ($re['normativas'] ?? '')) !== ''): ?>
    <div class="mt-2">
      <p class="font-semibold text-gray-700">Normativa citada</p>
      <p class="mt-0.5 whitespace-pre-wrap text-gray-800"><?= h((string) $re['normativas']) ?></p>
    </div>
  <?php endif; ?>
  <?php if ($dec === 'pendiente_informacion' || trim((string) ($re['subsanacion_items'] ?? '')) !== '' || trim((string) ($re['subsanacion_error_doc'] ?? '')) !== '' || trim((string) ($re['subsanacion_fecha_limite'] ?? '')) !== ''): ?>
    <div class="mt-2 rounded border border-amber-200/80 bg-white/60 p-2">
      <p class="font-semibold text-amber-950">Subsanación</p>
      <?php if (trim((string) ($re['subsanacion_items'] ?? '')) !== ''): ?>
        <p class="mt-1 font-medium text-gray-700">Ítems faltantes</p>
        <p class="whitespace-pre-wrap text-gray-800"><?= h((string) $re['subsanacion_items']) ?></p>
      <?php endif; ?>
      <?php if (trim((string) ($re['subsanacion_error_doc'] ?? '')) !== ''): ?>
        <p class="mt-2 font-medium text-gray-700">Sobre documentación previa</p>
        <p class="whitespace-pre-wrap text-gray-800"><?= h((string) $re['subsanacion_error_doc']) ?></p>
      <?php endif; ?>
      <?php if (trim((string) ($re['subsanacion_fecha_limite'] ?? '')) !== ''): ?>
        <p class="mt-2"><span class="font-medium text-gray-700">Fecha límite:</span> <?= h((string) $re['subsanacion_fecha_limite']) ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <?php if (trim((string) ($re['instrucciones_cierre'] ?? '')) !== ''): ?>
    <div class="mt-2">
      <p class="font-semibold text-gray-700">Instrucciones de cierre</p>
      <p class="mt-0.5 whitespace-pre-wrap text-gray-800"><?= h((string) $re['instrucciones_cierre']) ?></p>
    </div>
  <?php endif; ?>
  <?php if (trim((string) ($re['recursos_apelacion'] ?? '')) !== ''): ?>
    <div class="mt-2">
      <p class="font-semibold text-gray-700">Recursos / apelaciones</p>
      <p class="mt-0.5 whitespace-pre-wrap text-gray-800"><?= h((string) $re['recursos_apelacion']) ?></p>
    </div>
  <?php endif; ?>
  <?php if (trim((string) ($re['funcionario_nombre'] ?? '')) !== '' || trim((string) ($re['funcionario_cargo'] ?? '')) !== ''): ?>
    <div class="mt-3 border-t border-gray-200 pt-2 text-[11px]">
      <p class="font-semibold text-gray-800">Firma y autoridad</p>
      <p><?= h(trim((string) ($re['funcionario_nombre'] ?? ''))) ?></p>
      <p class="text-gray-600"><?= h(trim((string) ($re['funcionario_cargo'] ?? ''))) ?></p>
    </div>
  <?php endif; ?>
  <?php if (trim((string) ($re['codigo_verificacion'] ?? '')) !== ''): ?>
    <p class="mt-2 font-mono text-[10px] text-gray-600">Verificación: <?= h((string) $re['codigo_verificacion']) ?></p>
  <?php endif; ?>
</div>
