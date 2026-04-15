<?php
declare(strict_types=1);
$emptyHint = $emptyHint ?? 'No hay solicitudes con los filtros indicados.';
?>
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
                      <dt class="font-semibold text-gray-600">NRC / asignatura</dt><dd><?= h(trim((string) ($carga['nrc'] ?? '') . ' — ' . (string) ($carga['nombre_materia'] ?? ''))) ?></dd>
                      <dt class="font-semibold text-gray-600">Periodo (fechas)</dt><dd><?= h((string) ($cuer['fecha_inicio'] ?? '—') . ' → ' . (string) ($cuer['fecha_fin'] ?? '—')) ?></dd>
                    </dl>
                  <?php endif; ?>
                </div>
              </details>
              <?php
                $Er = is_array($s['respuesta_elaborada'] ?? null) ? $s['respuesta_elaborada'] : [];
                $elabOpen = $Er !== [];
                $respCerrada = solicitud_tiene_respuesta_cerrada($s);
                $momResp = solicitud_texto_momento_respuesta($s);
              ?>
              <?php if ($respCerrada): ?>
              <div class="space-y-2 rounded border border-gray-200 bg-gray-100/80 p-2 text-xs text-gray-800">
                <p class="font-semibold text-gray-900">Respuesta registrada — bloqueada</p>
                <p class="text-[11px] text-gray-600">Esta solicitud ya fue contestada por la universidad. No se permiten cambios para preservar la trazabilidad.</p>
                <?php if ($momResp !== ''): ?>
                  <p class="font-mono text-[11px] text-gray-800"><span class="font-sans font-medium text-gray-600">Fecha y hora:</span> <?= h($momResp) ?> <span class="font-sans text-[10px] text-gray-500">· <?= h(etiqueta_hora_colombia()) ?></span></p>
                <?php endif; ?>
                <p class="text-[10px] font-medium text-gray-500">Estado actual: <?= h(solicitud_estado_nombre((string) ($s['estado'] ?? ''))) ?></p>
                <?php if (trim((string) ($s['respuesta'] ?? '')) !== ''): ?>
                  <div class="rounded border border-white bg-white/90 p-2 text-gray-800">
                    <span class="text-[10px] uppercase text-gray-500">Respuesta breve</span>
                    <p class="mt-0.5 whitespace-pre-wrap"><?= h((string) $s['respuesta']) ?></p>
                  </div>
                <?php endif; ?>
                <?php if ($Er !== []): ?>
                  <div class="rounded border border-indigo-100 bg-white p-2">
                    <?php $re = $Er; require dirname(__DIR__, 2) . '/partials/bloque_respuesta_elaborada_leer.php'; ?>
                  </div>
                <?php endif; ?>
              </div>
              <?php else: ?>
              <form method="post" class="space-y-2 rounded border border-gray-100 bg-gray-50 p-2">
                <input type="hidden" name="accion" value="cambiar_estado">
                <input type="hidden" name="id_solicitud" value="<?= $idSol ?>">
                <select name="estado" class="block w-full rounded border border-gray-300 px-2 py-1 text-xs">
                  <?php foreach (diccionario_estados_solicitud() as $opt): ?>
                    <option value="<?= h($opt['codigo']) ?>" <?= ((string) ($s['estado'] ?? '') === $opt['codigo']) ? 'selected' : '' ?>><?= h($opt['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
                <label class="block text-[10px] font-medium text-gray-600">Respuesta breve al radicante</label>
                <textarea name="respuesta" rows="2" class="block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Texto corto (vista rápida en el panel del usuario)"><?= h((string) ($s['respuesta'] ?? '')) ?></textarea>
                <label class="flex cursor-pointer items-start gap-2 text-[11px] text-gray-700">
                  <input type="checkbox" name="incluir_elaborada" value="1" class="mt-0.5 rounded border-gray-300" <?= $elabOpen ? ' checked' : '' ?>>
                  <span>Guardar también <strong>resolución formal</strong> (bloque ampliado abajo). Si no marca la casilla, no se actualiza la resolución guardada.</span>
                </label>
                <p class="rounded bg-amber-50/90 px-2 py-1 text-[10px] text-amber-950">La primera vez que envíe <strong>respuesta breve</strong> o marque y guarde la <strong>resolución formal</strong>, la solicitud quedará <strong>cerrada</strong> (sin nuevas ediciones) y se registrará la fecha y hora exactas.</p>
                <details class="rounded border border-indigo-100 bg-white" <?= $elabOpen ? ' open' : '' ?>>
                  <summary class="cursor-pointer select-none rounded px-2 py-1.5 text-[11px] font-semibold text-indigo-900 hover:bg-indigo-50">Resolución formal (opcional)</summary>
                  <div class="space-y-2 border-t border-indigo-100 p-2 text-[11px]">
                    <p class="text-gray-600">Estructura tipo carta de resolución. Puede dejar campos vacíos; lo guardado se emite con fecha y hora al enviar el formulario.</p>
                    <div>
                      <label class="font-medium text-gray-700">Estado de la decisión (narrativo)</label>
                      <select name="elab_decision" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs">
                        <?php foreach (diccionario_decision_resolucion_formal() as $d): ?>
                          <option value="<?= h($d['codigo']) ?>" <?= ((string) ($Er['decision'] ?? '') === $d['codigo']) ? 'selected' : '' ?>><?= h($d['nombre']) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div>
                      <label class="font-medium text-gray-700">Justificación / considerandos</label>
                      <textarea name="elab_justificacion" rows="3" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Motivo de la decisión, reglamento estudiantil o docente..."><?= h((string) ($Er['justificacion'] ?? '')) ?></textarea>
                    </div>
                    <div>
                      <label class="font-medium text-gray-700">Normativa citada (artículos, acuerdos)</label>
                      <textarea name="elab_normativas" rows="2" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Opcional"><?= h((string) ($Er['normativas'] ?? '')) ?></textarea>
                    </div>
                    <p class="font-semibold text-amber-900">Subsanación (si aplica — p. ej. decisión «Pendiente de información»)</p>
                    <div>
                      <label class="font-medium text-gray-700">Lista de ítems faltantes</label>
                      <textarea name="elab_subsanacion_items" rows="2" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Un ítem por línea"><?= h((string) ($Er['subsanacion_items'] ?? '')) ?></textarea>
                    </div>
                    <div>
                      <label class="font-medium text-gray-700">Descripción del problema con documentación previa</label>
                      <textarea name="elab_subsanacion_error_doc" rows="2" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Ej. archivo ilegible, vencido..."><?= h((string) ($Er['subsanacion_error_doc'] ?? '')) ?></textarea>
                    </div>
                    <div>
                      <label class="font-medium text-gray-700">Fecha límite de subsanación</label>
                      <input type="date" name="elab_subsanacion_fecha_limite" value="<?= h((string) ($Er['subsanacion_fecha_limite'] ?? '')) ?>" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs">
                    </div>
                    <div>
                      <label class="font-medium text-gray-700">Instrucciones de cierre</label>
                      <textarea name="elab_instrucciones_cierre" rows="2" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Pasos siguientes para el usuario"><?= h((string) ($Er['instrucciones_cierre'] ?? '')) ?></textarea>
                    </div>
                    <div>
                      <label class="font-medium text-gray-700">Recursos de apelación</label>
                      <textarea name="elab_recursos_apelacion" rows="2" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Ante quién reclamar"><?= h((string) ($Er['recursos_apelacion'] ?? '')) ?></textarea>
                    </div>
                    <p class="font-semibold text-gray-800">Firma y autoridad responsable</p>
                    <div>
                      <label class="font-medium text-gray-700">Nombre del funcionario</label>
                      <input type="text" name="elab_funcionario_nombre" value="<?= h((string) ($Er['funcionario_nombre'] ?? '')) ?>" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Nombre completo">
                    </div>
                    <div>
                      <label class="font-medium text-gray-700">Cargo y dependencia</label>
                      <input type="text" name="elab_funcionario_cargo" value="<?= h((string) ($Er['funcionario_cargo'] ?? '')) ?>" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Ej. Coordinación de Registro y Control">
                    </div>
                    <div>
                      <label class="font-medium text-gray-700">Código / sello de verificación (texto)</label>
                      <input type="text" name="elab_codigo_verificacion" value="<?= h((string) ($Er['codigo_verificacion'] ?? '')) ?>" class="mt-0.5 block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Referencia o hash para autenticidad (opcional)">
                    </div>
                  </div>
                </details>
                <button type="submit" class="w-full rounded bg-academic py-1.5 text-xs font-semibold text-white hover:bg-academic-dark">Guardar</button>
              </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$items): ?>
          <tr><td colspan="8" class="px-3 py-8 text-center text-gray-500"><?= h($emptyHint) ?></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
