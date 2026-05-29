<?php
declare(strict_types=1);
/** @var array{solicitud: array, estudiante: ?array} $row */
$s = $row['solicitud'] ?? [];
$idSol = (int) ($s['id_solicitud'] ?? 0);
$pendientes = \App\Services\SolicitudDocumentosService::listarPendientes($s, true);
if ($idSol <= 0 || (int) ($s['id_estudiante'] ?? 0) <= 0) {
    return;
}
?>
<details class="mb-2 rounded border border-violet-200 bg-violet-50/50">
  <summary class="cursor-pointer px-2 py-1.5 text-[11px] font-semibold text-violet-900 hover:bg-violet-100/80">Solicitar documento al estudiante</summary>
  <div class="space-y-2 border-t border-violet-100 p-2">
    <?php if ($pendientes !== []): ?>
      <p class="text-[10px] font-medium text-violet-800">Pendientes de cargar:</p>
      <ul class="space-y-1 text-[10px] text-gray-700">
        <?php foreach ($pendientes as $p): ?>
          <li class="rounded border border-violet-100 bg-white px-2 py-1">
            <strong><?= h(solicitud_etiqueta_categoria_anexo((string) ($p['categoria'] ?? ''))) ?></strong>
            <?php if (($p['mensaje'] ?? '') !== ''): ?> — <?= h((string) $p['mensaje']) ?><?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <form method="post" class="space-y-2">
      <input type="hidden" name="accion" value="solicitar_documento">
      <input type="hidden" name="id_solicitud" value="<?= $idSol ?>">
      <div>
        <label class="block text-[10px] font-medium text-gray-700">Tipo de documento</label>
        <select name="doc_categoria" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs" required>
          <option value="">Seleccione…</option>
          <?php foreach (\App\Services\SolicitudDocumentosService::categoriasSolicitables() as $c): ?>
            <option value="<?= h((string) $c['codigo']) ?>"><?= h((string) $c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-[10px] font-medium text-gray-700">Mensaje para el estudiante</label>
        <textarea name="doc_mensaje" rows="2" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs" required placeholder="Indique qué debe adjuntar y cualquier detalle importante"></textarea>
      </div>
      <button type="submit" class="w-full rounded bg-violet-700 py-1.5 text-xs font-semibold text-white hover:bg-violet-800">Solicitar documento</button>
    </form>
  </div>
</details>
