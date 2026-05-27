<?php
declare(strict_types=1);
$s = $s ?? [];
$e = $e ?? null;
$ds = $ds ?? null;
$radicadoEn = solicitud_texto_momento_radicacion($s);
$respondidoEn = solicitud_texto_momento_respuesta($s);
?>
<div class="space-y-3 text-xs text-gray-800">
  <p class="font-semibold text-gray-900">Datos de radicación</p>
  <dl class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">
    <dt class="font-semibold text-gray-600">ID solicitud</dt><dd class="font-mono"><?= (int) ($s['id_solicitud'] ?? 0) ?></dd>
    <dt class="font-semibold text-gray-600">Enviada el</dt><dd class="font-mono"><?= h($radicadoEn !== '' ? $radicadoEn : '—') ?> <span class="font-sans text-[10px] text-gray-500"><?= h(etiqueta_hora_colombia()) ?></span></dd>
    <dt class="font-semibold text-gray-600">Estado actual</dt><dd><?= h(solicitud_estado_nombre((string) ($s['estado'] ?? ''))) ?></dd>
    <dt class="font-semibold text-gray-600">Tipo de trámite</dt><dd><?= h(solicitud_tipo_etiqueta($s)) ?></dd>
    <dt class="font-semibold text-gray-600">Motivo / asunto</dt><dd class="font-medium text-gray-900"><?= h(solicitud_motivo_admin_etiqueta($s)) ?></dd>
    <?php if ($respondidoEn !== ''): ?>
      <dt class="font-semibold text-gray-600">Respondida el</dt><dd class="font-mono"><?= h($respondidoEn) ?></dd>
    <?php endif; ?>
  </dl>

  <?php if ($e): ?>
    <p class="border-t border-gray-200 pt-2 font-semibold text-emerald-900">Radicante — estudiante</p>
    <dl class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">
      <dt class="font-semibold text-gray-600">Nombre</dt><dd><?= h(trim(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? ''))) ?></dd>
      <dt class="font-semibold text-gray-600">Documento</dt><dd class="font-mono"><?= h((string) ($e['documento'] ?? '')) ?></dd>
      <dt class="font-semibold text-gray-600">Correo</dt><dd><?= h((string) ($e['correo'] ?? '—')) ?></dd>
      <dt class="font-semibold text-gray-600">Programa</dt><dd><?= h((string) ($e['programa'] ?? programa_label_by_id((int) ($e['id_programa'] ?? 0)))) ?></dd>
      <dt class="font-semibold text-gray-600">Semestre</dt><dd><?= h((string) ($e['semestre'] ?? '—')) ?></dd>
      <dt class="font-semibold text-gray-600">Sede matrícula</dt><dd><?= h(sede_nombre((int) ($e['id_sede'] ?? 0))) ?></dd>
    </dl>
    <?php
    $de = $s['detalle_estudiante'] ?? null;
    if (is_array($de)):
        $cl = $de['clasificacion'] ?? [];
        $cu = $de['cuerpo'] ?? [];
        $ps = $de['perfil_snapshot'] ?? [];
    ?>
      <p class="font-semibold text-gray-700">Clasificación de la petición</p>
      <dl class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">
        <dt class="font-semibold text-gray-600">Periodo académico</dt><dd><?= h((string) ($cl['periodo_academico'] ?? '—')) ?></dd>
        <dt class="font-semibold text-gray-600">Sede / jornada (petición)</dt><dd><?= h(sede_nombre((int) ($cl['id_sede_solicitud'] ?? 0))) ?> / <?= h(jornada_nombre((int) ($cl['id_jornada_solicitud'] ?? 0))) ?></dd>
        <dt class="font-semibold text-gray-600">Motivo (estadístico)</dt><dd class="font-medium"><?= h((string) ($cu['motivo_label'] ?? solicitud_motivo_admin_etiqueta($s))) ?></dd>
        <dt class="font-semibold text-gray-600">Estado académico (al radicar)</dt><dd><?= h((string) ($ps['estado_academico_label'] ?? $ps['estado_academico'] ?? '—')) ?></dd>
      </dl>
      <p class="font-semibold text-gray-700">Exposición de motivos</p>
      <div class="rounded border border-gray-200 bg-white p-2 whitespace-pre-wrap"><?= h((string) ($cu['exposicion'] ?? solicitud_resumen_texto($s))) ?></div>
    <?php endif; ?>
  <?php elseif ($ds): ?>
    <p class="border-t border-gray-200 pt-2 font-semibold text-academic">Radicante — docente</p>
    <dl class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">
      <dt class="font-semibold text-gray-600">Nombre</dt><dd><?= h(trim(($ds['nombre'] ?? '') . ' ' . ($ds['apellido'] ?? ''))) ?></dd>
      <dt class="font-semibold text-gray-600">Documento</dt><dd class="font-mono"><?= h((string) ($ds['documento'] ?? '')) ?></dd>
      <dt class="font-semibold text-gray-600">Correo</dt><dd><?= h((string) ($ds['correo'] ?? '—')) ?></dd>
      <dt class="font-semibold text-gray-600">Unidad académica</dt><dd><?= h((string) ($ds['unidad_academica'] ?? '—')) ?></dd>
    </dl>
    <?php
    $dd = $s['detalle_docente'] ?? null;
    if (is_array($dd)):
        $cla = $dd['clasificacion'] ?? [];
        $carga = $dd['carga_afectada'] ?? [];
        $cuer = $dd['cuerpo'] ?? [];
        $psd = $dd['perfil_snapshot'] ?? [];
    ?>
      <dl class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">
        <dt class="font-semibold text-gray-600">Asunto</dt><dd class="font-medium"><?= h((string) ($cla['asunto'] ?? '—')) ?></dd>
        <dt class="font-semibold text-gray-600">Prioridad</dt><dd><?= h((string) ($cla['prioridad_label'] ?? '—')) ?></dd>
        <dt class="font-semibold text-gray-600">Categoría / contrato</dt><dd><?= h((string) ($psd['categoria_docente_label'] ?? '—')) ?> · <?= h((string) ($psd['tipo_contrato_label'] ?? '—')) ?></dd>
        <dt class="font-semibold text-gray-600">NRC / asignatura</dt><dd><?= h(trim((string) ($carga['nrc'] ?? '') . ' — ' . (string) ($carga['nombre_materia'] ?? ''))) ?></dd>
        <dt class="font-semibold text-gray-600">Horario impactado</dt><dd class="whitespace-pre-wrap"><?= h((string) ($carga['horario_impactado'] ?? '—')) ?></dd>
        <dt class="font-semibold text-gray-600">Plan de contingencia</dt><dd class="whitespace-pre-wrap"><?= h((string) ($carga['plan_contingencia'] ?? '—')) ?></dd>
        <dt class="font-semibold text-gray-600">Fechas del requerimiento</dt><dd><?= h((string) ($cuer['fecha_inicio'] ?? '—') . ' → ' . (string) ($cuer['fecha_fin'] ?? '—')) ?></dd>
      </dl>
      <p class="font-semibold text-gray-700">Descripción detallada</p>
      <div class="rounded border border-gray-200 bg-white p-2 whitespace-pre-wrap"><?= h((string) ($cuer['descripcion_detallada'] ?? solicitud_resumen_texto($s))) ?></div>
      <?php if (trim((string) ($cuer['sustento_legal'] ?? '')) !== ''): ?>
        <p class="font-semibold text-gray-700">Sustento legal</p>
        <div class="rounded border border-gray-200 bg-white p-2 whitespace-pre-wrap"><?= h((string) $cuer['sustento_legal']) ?></div>
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>

  <?php
  $anexos = $s['anexos_archivos'] ?? [];
  if (is_array($anexos) && $anexos !== []):
      $idSol = (int) ($s['id_solicitud'] ?? 0);
  ?>
    <p class="border-t border-gray-200 pt-2 font-semibold text-gray-700">Anexos</p>
    <ul class="list-inside list-disc space-y-0.5">
      <?php foreach ($anexos as $i => $m): ?>
        <?php $cat = (string) ($m['categoria'] ?? 'general'); ?>
        <li>
          <a class="text-academic hover:underline" href="<?= h(url('descargar_anexo.php?s=' . $idSol . '&f=' . $i)) ?>"><?= h((string) ($m['original'] ?? 'archivo')) ?></a>
          <?php if ($cat !== '' && $cat !== 'general'): ?>
            <span class="text-gray-500">(<?= h(solicitud_etiqueta_categoria_anexo($cat)) ?>)</span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
