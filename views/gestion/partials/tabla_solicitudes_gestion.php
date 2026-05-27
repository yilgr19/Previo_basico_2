<?php
declare(strict_types=1);
$emptyHint = $emptyHint ?? 'No hay solicitudes con los filtros indicados.';
$gestionRepoblar = $gestionRepoblar ?? null;
?>
  <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">ID</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Enviada</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Radicante</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Tipo</th>
          <th class="px-3 py-3 text-left font-semibold text-gray-700">Motivo</th>
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
            <td class="whitespace-nowrap px-3 py-2 font-mono text-[11px]"><?= h(solicitud_texto_momento_radicacion($s) ?: '—') ?></td>
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
            <td class="max-w-[9rem] px-3 py-2 text-xs font-medium text-gray-900"><?= h(solicitud_motivo_admin_etiqueta($s)) ?></td>
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
              <details class="mb-2" open>
                <summary class="cursor-pointer text-xs font-semibold text-academic hover:underline">Ver solicitud completa</summary>
                <div class="mt-2 max-h-[28rem] overflow-y-auto rounded border border-slate-200 bg-slate-50/80 p-2">
                  <?php require __DIR__ . '/detalle_solicitud_completo.php'; ?>
                </div>
              </details>
              <?php
                $Er = is_array($s['respuesta_elaborada'] ?? null) ? $s['respuesta_elaborada'] : [];
                $respCerrada = solicitud_tiene_respuesta_cerrada($s);
                $momResp = solicitud_texto_momento_respuesta($s);

                $estForm = (string) ($s['estado'] ?? '');
                $respForm = (string) ($s['respuesta'] ?? '');
                $chkIncluirElab = is_array($s['respuesta_elaborada'] ?? null) && ($s['respuesta_elaborada'] ?? []) !== [];
                $rep = null;
                if (
                    !$respCerrada
                    && is_array($gestionRepoblar)
                    && (int) ($gestionRepoblar['id_solicitud'] ?? 0) === $idSol
                ) {
                    $rep = $gestionRepoblar;
                }
                if ($rep !== null) {
                    $estForm = (string) ($rep['estado'] ?? '');
                    $respForm = (string) ($rep['respuesta'] ?? '');
                    $chkIncluirElab = !empty($rep['incluir_elaborada']);
                    foreach (($rep['elab'] ?? []) as $ek => $ev) {
                        $Er[$ek] = $ev;
                    }
                }
                $elabTieneTexto = false;
                foreach ($Er as $ev) {
                    if (is_string($ev) && trim($ev) !== '') {
                        $elabTieneTexto = true;
                        break;
                    }
                }
                $elabOpen = $chkIncluirElab || $elabTieneTexto;
              ?>
              <form method="post" class="space-y-2 rounded border border-gray-100 bg-gray-50 p-2">
                <?php if ($respCerrada): ?>
                  <p class="rounded border border-blue-100 bg-blue-50/90 px-2 py-1.5 text-[11px] text-blue-950">
                    Ya hay respuesta registrada<?php if ($momResp !== ''): ?> (<?= h($momResp) ?>)<?php endif; ?>.
                    Puede <strong>corregir</strong> el estado, la respuesta breve o la resolución formal y guardar de nuevo.
                  </p>
                <?php else: ?>
                  <p class="rounded bg-amber-50/90 px-2 py-1 text-[10px] text-amber-950">Al enviar respuesta breve o resolución formal se registrará la fecha y hora de respuesta.</p>
                <?php endif; ?>
                <input type="hidden" name="accion" value="cambiar_estado">
                <input type="hidden" name="id_solicitud" value="<?= $idSol ?>">
                <select name="estado" class="block w-full rounded border border-gray-300 px-2 py-1 text-xs">
                  <?php foreach (diccionario_estados_solicitud() as $opt): ?>
                    <option value="<?= h($opt['codigo']) ?>" <?= ($estForm === $opt['codigo']) ? 'selected' : '' ?>><?= h($opt['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
                <label class="block text-[10px] font-medium text-gray-600">Respuesta breve al radicante</label>
                <textarea name="respuesta" rows="2" class="block w-full rounded border border-gray-300 px-2 py-1 text-xs" placeholder="Texto corto (vista rápida en el panel del usuario)"><?= h($respForm) ?></textarea>
                <label class="flex cursor-pointer items-start gap-2 text-[11px] text-gray-700">
                  <input type="checkbox" name="incluir_elaborada" value="1" class="mt-0.5 rounded border-gray-300" <?= $chkIncluirElab ? ' checked' : '' ?>>
                  <span>Guardar también <strong>resolución formal</strong> (bloque ampliado abajo). Si no marca la casilla, no se actualiza la resolución guardada.</span>
                </label>
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
                <button type="submit" class="w-full rounded bg-academic py-1.5 text-xs font-semibold text-white hover:bg-academic-dark">Guardar cambios</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$items): ?>
          <tr><td colspan="8" class="px-3 py-8 text-center text-gray-500"><?= h($emptyHint) ?></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
