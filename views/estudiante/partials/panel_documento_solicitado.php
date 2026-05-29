<?php
/** @var array<string, mixed> $panelDoc */
$panelDoc = $panelDoc ?? [];
$idSol = (int) ($panelDoc['id_solicitud'] ?? 0);
$categoria = (string) ($panelDoc['categoria'] ?? '');
$catNombre = (string) ($panelDoc['categoria_nombre'] ?? $categoria);
$mensajeAdmin = (string) ($panelDoc['mensaje'] ?? '');
$tipoSol = (string) ($panelDoc['tipo'] ?? '');
$grupo = \App\Services\SolicitudDocumentosService::grupoUploadParaCategoria($categoria);
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
?>
<section id="panel-doc-solicitado" class="mb-6 rounded-xl border-2 border-amber-300 bg-amber-50/90 p-5 shadow-sm">
  <div class="mb-3 flex flex-wrap items-start justify-between gap-2">
    <div>
      <h2 class="text-base font-semibold text-amber-950">Documento solicitado por gestión académica</h2>
      <p class="mt-1 text-sm text-amber-900">Solicitud #<?= $idSol ?><?php if ($tipoSol !== ''): ?> · <?= h($tipoSol) ?><?php endif; ?></p>
    </div>
    <span class="rounded-full bg-amber-200 px-3 py-1 text-xs font-semibold text-amber-950"><?= h($catNombre) ?></span>
  </div>
  <?php if ($mensajeAdmin !== ''): ?>
    <div class="mb-4 rounded-lg border border-amber-200 bg-white px-4 py-3 text-sm text-gray-800">
      <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-amber-800">Mensaje de gestión</p>
      <p class="whitespace-pre-wrap"><?= h($mensajeAdmin) ?></p>
    </div>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data" action="<?= h(url('estudiante/mis_solicitudes')) ?>" class="space-y-3">
    <input type="hidden" name="accion" value="subir_documento_solicitado">
    <input type="hidden" name="id_solicitud" value="<?= $idSol ?>">
    <input type="hidden" name="doc_categoria" value="<?= h($categoria) ?>">
    <div>
      <label class="<?= h($lbl) ?>">Adjuntar <?= h($catNombre) ?> <span class="text-gray-500">(PDF o imagen, máx. 5 MB<?= !empty($grupo['multiple']) ? ', puede seleccionar varios si son evidencias generales' : '' ?>)</span></label>
      <input type="file" name="<?= h($grupo['input']) ?><?= !empty($grupo['multiple']) ? '[]' : '' ?>" class="<?= h($inp) ?>" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,application/pdf,image/*" <?= !empty($grupo['multiple']) ? 'multiple' : '' ?> required>
    </div>
    <button type="submit" class="inline-flex rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark">Enviar documento</button>
  </form>
</section>
