<?php
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
$ro = $inp . ' bg-gray-50 text-gray-800';
$mWarn = ($mensaje ?? '') !== '' && ($tipoMsg ?? '') === 'warning';
$alertClass = $mWarn ? 'border-amber-200 bg-amber-50 text-amber-900' : 'border-green-200 bg-green-50 text-green-900';
if (($tipoMsg ?? '') === 'warning' && !$mWarn) {
    $alertClass = 'border-amber-200 bg-amber-50 text-amber-900';
}
$yo = $yo ?? [];
$eaCod = strtoupper(trim((string) ($yo['estado_academico'] ?? 'REGULAR')));
$defPeriodo = date('Y') . '-1';
if ((int) date('n') >= 7) {
    $defPeriodo = date('Y') . '-2';
}
$materiasPrograma = $materiasPrograma ?? [];
?>
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold text-academic">Mis solicitudes</h1>
      <p class="mt-1 text-sm text-gray-600">Radique trámites según el catálogo institucional. Los datos de identificación se toman de su sesión.</p>
    </div>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('estudiante/dashboard.php')) ?>">Volver al inicio</a>
  </div>

  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="mb-10 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Nueva solicitud</h2>
    <form method="post" enctype="multipart/form-data" class="space-y-8">
      <input type="hidden" name="accion" value="nueva_solicitud">

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">1. Perfil del estudiante (identificación)</legend>
        <p class="mb-3 text-xs text-gray-500">Tomado de su cuenta para evitar suplantación; no puede modificarse en este formulario.</p>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <span class="<?= h($lbl) ?>">ID estudiantil / documento</span>
            <div class="<?= h($ro) ?>"><?= h((string) ($yo['documento'] ?? '—')) ?></div>
          </div>
          <div>
            <span class="<?= h($lbl) ?>">Programa académico</span>
            <div class="<?= h($ro) ?>"><?= h((string) ($yo['programa'] ?? programa_label_by_id((int) ($yo['id_programa'] ?? 0)))) ?></div>
          </div>
          <div>
            <span class="<?= h($lbl) ?>">Estado académico</span>
            <div class="<?= h($ro) ?>"><?= h(estado_academico_estudiante_nombre($eaCod)) ?></div>
          </div>
          <div>
            <span class="<?= h($lbl) ?>">Semestre actual</span>
            <div class="<?= h($ro) ?>"><?= h((string) ((int) ($yo['semestre'] ?? 0) > 0 ? (int) $yo['semestre'] : '—')) ?></div>
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">2. Clasificación de la solicitud</legend>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Tipo de solicitud</label>
            <select name="id_tipo_solicitud" id="fld_tipo_solicitud" class="<?= h($inp) ?>" required>
              <option value="">Seleccione…</option>
              <?php foreach (diccionario_tipos_solicitud() as $t): ?>
                <option value="<?= (int) $t['id'] ?>"><?= h((string) $t['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Periodo académico</label>
            <input type="text" name="periodo_academico" class="<?= h($inp) ?>" required placeholder="Ej. 2026-1" value="<?= h($defPeriodo) ?>" pattern="\d{4}-\d{1,2}" title="Formato AAAA-S">
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Sede (petición)</label>
            <select name="id_sede_solicitud" class="<?= h($inp) ?>" required>
              <option value="">Seleccione…</option>
              <?php foreach (diccionario_sedes() as $sd): ?>
                <option value="<?= (int) $sd['id'] ?>" <?= ((int) ($yo['id_sede'] ?? 0) === (int) $sd['id']) ? 'selected' : '' ?>><?= h((string) $sd['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Jornada (petición)</label>
            <select name="id_jornada_solicitud" class="<?= h($inp) ?>" required>
              <option value="">Seleccione…</option>
              <?php foreach (diccionario_jornadas() as $j): ?>
                <option value="<?= (int) $j['id'] ?>" <?= ((int) ($yo['id_jornada'] ?? 0) === (int) $j['id']) ? 'selected' : '' ?>><?= h((string) $j['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">3. Cuerpo de la petición</legend>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div>
            <label class="<?= h($lbl) ?>">Motivo (estadístico)</label>
            <select name="motivo_solicitud" id="fld_motivo" class="<?= h($inp) ?>" required>
              <option value="">Seleccione…</option>
              <?php foreach (diccionario_motivos_solicitud_estudiante() as $m): ?>
                <option value="<?= h((string) $m['codigo']) ?>"><?= h((string) $m['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Exposición de motivos <span class="text-gray-500">(mín. 10 caracteres)</span></label>
            <textarea name="exposicion" class="<?= h($inp) ?>" rows="5" required placeholder="Explique su situación con el detalle necesario"></textarea>
          </div>
          <div id="bloque-materias" class="md:col-span-2 hidden rounded-md border border-dashed border-amber-300 bg-amber-50/50 p-3">
            <p class="mb-2 text-xs font-medium text-amber-900">Asignaturas afectadas (obligatorio para cancelación de semestre, curso dirigido o cancelación de asignaturas)</p>
            <div class="max-h-48 space-y-2 overflow-y-auto text-sm">
              <?php if ($materiasPrograma === []): ?>
                <p class="text-xs text-gray-600">No hay materias registradas para su programa en el sistema. Contacte gestión académica.</p>
              <?php else: ?>
                <?php foreach ($materiasPrograma as $mat): ?>
                  <?php $idM = (int) ($mat['id_materia'] ?? 0); ?>
                  <label class="flex cursor-pointer items-start gap-2 rounded border border-transparent px-2 py-1 hover:bg-white">
                    <input type="checkbox" name="ids_materias[]" value="<?= $idM ?>" class="mt-1">
                    <span><span class="font-mono text-xs text-gray-600"><?= h((string) ($mat['codigo'] ?? '')) ?></span> — <?= h((string) ($mat['nombre'] ?? '')) ?></span>
                  </label>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Documento del docente relacionado <span class="text-gray-500">(opcional)</span></label>
            <input type="text" name="documento_docente_relacionado" class="<?= h($inp) ?>" placeholder="Solo números, sin puntos" inputmode="numeric">
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">4. Soportes obligatorios (evidencias)</legend>
        <p class="mb-3 text-xs text-gray-600">Según el tipo y el motivo, el sistema exigirá: soporte médico (motivo salud), carta (transferencia/traslado) o recibo (trámites con costo). Adjunte también evidencias generales si aplica.</p>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Evidencias generales <span class="text-gray-500">(PDF o imágenes, máx. 15 archivos en total, 5 MB c/u)</span></label>
            <input type="file" name="anexos[]" class="<?= h($inp) ?>" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,application/pdf,image/*" multiple>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Soporte médico <span class="text-gray-500" id="hint-medico">(obligatorio si motivo es salud)</span></label>
            <input type="file" name="soporte_medico" class="<?= h($inp) ?>" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,application/pdf,image/*">
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Carta de aceptación / orden <span class="text-gray-500" id="hint-carta">(transferencia interna o traslado de sede)</span></label>
            <input type="file" name="carta_aceptacion" class="<?= h($inp) ?>" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,application/pdf,image/*">
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Recibo de pago <span class="text-gray-500" id="hint-recibo">(pago créditos adicionales, constancia o certificado de notas)</span></label>
            <input type="file" name="recibo_pago" class="<?= h($inp) ?>" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp,application/pdf,image/*">
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">5. Declaración de veracidad</legend>
        <label class="flex cursor-pointer items-start gap-3 text-sm text-gray-800">
          <input type="checkbox" name="consentimiento_veracidad" value="1" required class="mt-1">
          <span>Declaro que la información suministrada es verídica y que conozco el reglamento estudiantil aplicable a este trámite.</span>
        </label>
      </fieldset>

      <div>
        <button type="submit" class="inline-flex rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark">Enviar solicitud</button>
      </div>
    </form>
  </div>

  <?php
  $tblDesc = static function (array $s): void {
      $txt = solicitud_resumen_texto($s);
      echo '<td class="max-w-md px-3 py-2 text-xs text-gray-800">' . nl2br(h($txt)) . '</td>';
  };
  $tblAnexos = static function (array $s): void {
      $idSol = (int) ($s['id_solicitud'] ?? 0);
      $ax = $s['anexos_archivos'] ?? [];
      if (!is_array($ax) || $ax === []) {
          echo '<td class="max-w-[10rem] px-3 py-2 text-xs">—</td>';
          return;
      }
      echo '<td class="max-w-[10rem] px-3 py-2 text-xs">';
      foreach ($ax as $i => $m) {
          $cat = (string) ($m['categoria'] ?? 'general');
          $badge = $cat !== '' && $cat !== 'general' ? ' <span class="text-gray-500">(' . h(solicitud_etiqueta_categoria_anexo($cat)) . ')</span>' : '';
          echo '<a class="text-academic hover:underline" href="' . h(url('descargar_anexo.php?s=' . $idSol . '&f=' . $i)) . '">' . h((string) ($m['original'] ?? 'archivo')) . '</a>' . $badge . '<br>';
      }
      echo '</td>';
  };
  ?>

  <div class="mb-10 rounded-xl border border-amber-100 bg-amber-50/40 p-6 shadow-sm">
    <h2 class="mb-4 text-base font-semibold text-amber-950">En trámite (pendiente o en revisión)</h2>
    <div class="overflow-x-auto rounded-lg border border-amber-200/80 bg-white">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-amber-100/80"><tr>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Fecha</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Tipo</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Estado</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Exposición</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Anexos</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Respuesta</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($activas ?? [] as $s): ?>
            <tr>
              <td class="whitespace-nowrap px-3 py-2"><?= h((string) ($s['fecha_registro'] ?? '')) ?></td>
              <td class="px-3 py-2"><?= h(tipo_solicitud_nombre((int) ($s['id_tipo_solicitud'] ?? 0))) ?></td>
              <td class="px-3 py-2"><?= h(solicitud_estado_nombre((string) ($s['estado'] ?? ''))) ?></td>
              <?php $tblDesc($s); ?>
              <?php $tblAnexos($s); ?>
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
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Exposición</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Anexos</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-800">Respuesta</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($aprobadas ?? [] as $s): ?>
            <tr>
              <td class="whitespace-nowrap px-3 py-2"><?= h((string) ($s['fecha_registro'] ?? '')) ?></td>
              <td class="px-3 py-2"><?= h(tipo_solicitud_nombre((int) ($s['id_tipo_solicitud'] ?? 0))) ?></td>
              <td class="px-3 py-2"><?= h(solicitud_estado_nombre((string) ($s['estado'] ?? ''))) ?></td>
              <?php $tblDesc($s); ?>
              <?php $tblAnexos($s); ?>
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
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Exposición</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Anexos</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Respuesta</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($rechazadasOtras ?? [] as $s): ?>
            <tr>
              <td class="whitespace-nowrap px-3 py-2"><?= h((string) ($s['fecha_registro'] ?? '')) ?></td>
              <td class="px-3 py-2"><?= h(tipo_solicitud_nombre((int) ($s['id_tipo_solicitud'] ?? 0))) ?></td>
              <td class="px-3 py-2"><?= h(solicitud_estado_nombre((string) ($s['estado'] ?? ''))) ?></td>
              <?php $tblDesc($s); ?>
              <?php $tblAnexos($s); ?>
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
<script>
(function () {
  var sel = document.getElementById('fld_tipo_solicitud');
  var box = document.getElementById('bloque-materias');
  function updTipo() {
    if (!sel || !box) return;
    var v = parseInt(sel.value, 10);
    box.classList.toggle('hidden', v !== 1 && v !== 2 && v !== 3);
  }
  if (sel) {
    sel.addEventListener('change', updTipo);
    updTipo();
  }
})();
</script>
