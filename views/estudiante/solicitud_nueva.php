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
$old = $old ?? [];
$eaCod = strtoupper(trim((string) ($yo['estado_academico'] ?? 'REGULAR')));
$defPeriodo = date('Y') . '-1';
if ((int) date('n') >= 7) {
    $defPeriodo = date('Y') . '-2';
}
$valPeriodo = ($old['periodo_academico'] ?? '') !== '' ? (string) $old['periodo_academico'] : $defPeriodo;
?>
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold text-academic">Nueva solicitud</h1>
      <p class="mt-1 text-sm text-gray-600">Radique un trámite según el catálogo institucional. Los soportes documentales solo se solicitan si gestión académica los requiere después de la radicación.</p>
    </div>
    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50" href="<?= h(url('estudiante/dashboard')) ?>">Volver al inicio</a>
  </div>

  <?php require dirname(__DIR__) . '/partials/sol_nav_estudiante.php'; ?>

  <?php if ($mensaje): ?>
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>">
      <?= h($mensaje) ?>
      <?php if (($tipoMsg ?? '') === 'warning'): ?>
        <span class="mt-2 block text-[13px] font-normal">Puede corregir y enviar de nuevo: <strong class="font-medium text-gray-800">conservamos lo que escribió</strong> en el formulario.</span>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Formulario de radicación</h2>
    <form method="post" enctype="multipart/form-data" action="<?= h(url('estudiante/nueva_solicitud')) ?>" class="space-y-8">
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
                <option value="<?= (int) $t['id'] ?>" <?= ((string) ($old['id_tipo_solicitud'] ?? '')) === (string) (int) $t['id'] ? 'selected' : '' ?>><?= h((string) $t['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
            <p id="hint_plazo_tipo" class="mt-1 text-xs text-gray-500"></p>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Periodo académico</label>
            <input type="text" name="periodo_academico" class="<?= h($inp) ?>" required placeholder="Ej. 2026-1" value="<?= h($valPeriodo) ?>" pattern="\d{4}-\d{1,2}" title="Formato AAAA-S">
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Sede (petición)</label>
            <select name="id_sede_solicitud" id="fld_sede_solicitud" class="<?= h($inp) ?>" required>
              <option value="">Seleccione…</option>
              <?php foreach (diccionario_sedes() as $sd): ?>
                <?php
                $selSede = ($old['id_sede_solicitud'] ?? '') !== ''
                    ? ((string) ($old['id_sede_solicitud'] ?? '')) === (string) (int) $sd['id']
                    : ((int) ($yo['id_sede'] ?? 0) === (int) $sd['id']);
                ?>
                <option value="<?= (int) $sd['id'] ?>" <?= $selSede ? 'selected' : '' ?>><?= h((string) $sd['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="<?= h($lbl) ?>">Jornada (petición)</label>
            <select name="id_jornada_solicitud" class="<?= h($inp) ?>" required>
              <option value="">Seleccione…</option>
              <?php foreach (diccionario_jornadas() as $j): ?>
                <?php
                $selJor = ($old['id_jornada_solicitud'] ?? '') !== ''
                    ? ((string) ($old['id_jornada_solicitud'] ?? '')) === (string) (int) $j['id']
                    : ((int) ($yo['id_jornada'] ?? 0) === (int) $j['id']);
                ?>
                <option value="<?= (int) $j['id'] ?>" <?= $selJor ? 'selected' : '' ?>><?= h((string) $j['nombre']) ?></option>
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
                <option value="<?= h((string) $m['codigo']) ?>" <?= ((string) ($old['motivo_solicitud'] ?? '')) === (string) $m['codigo'] ? 'selected' : '' ?>><?= h((string) $m['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="<?= h($lbl) ?>">Exposición de motivos <span class="text-gray-500">(mín. 10 caracteres)</span></label>
            <textarea name="exposicion" class="<?= h($inp) ?>" rows="5" required placeholder="Explique su situación con el detalle necesario"><?= h((string) ($old['exposicion'] ?? '')) ?></textarea>
          </div>
        </div>
      </fieldset>

      <fieldset class="rounded-lg border border-gray-200 p-4">
        <legend class="px-1 text-sm font-semibold text-academic">4. Declaración de veracidad</legend>
        <label class="flex cursor-pointer items-start gap-3 text-sm text-gray-800">
          <input type="checkbox" name="consentimiento_veracidad" value="1" required class="mt-1" <?= !empty($old['consentimiento_veracidad']) ? 'checked' : '' ?>>
          <span>Declaro que la información suministrada es verídica y que conozco el reglamento estudiantil aplicable a este trámite.</span>
        </label>
      </fieldset>

      <div>
        <button type="submit" class="inline-flex rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark">Enviar solicitud</button>
      </div>
    </form>
  </div>
</main>
<script>
(function () {
  var matrizPlazos = <?= json_encode($matrizPlazos ?? [], JSON_UNESCAPED_UNICODE) ?: '{}' ?>;
  var selTipo = document.getElementById('fld_tipo_solicitud');
  var selSede = document.getElementById('fld_sede_solicitud');
  var hint = document.getElementById('hint_plazo_tipo');
  if (!selTipo || !selSede || !hint) return;
  function actualizar() {
    var tipo = selTipo.value;
    var sede = selSede.value;
    if (!tipo || !sede) {
      hint.textContent = '';
      return;
    }
    var dias = matrizPlazos[tipo + '-' + sede];
    if (dias) {
      hint.textContent = 'Plazo estimado de respuesta: ' + dias + (dias === 1 ? ' día' : ' días') + '.';
    } else {
      hint.textContent = 'No hay plazo configurado para este tipo y sede. Contacte a gestión académica.';
    }
  }
  selTipo.addEventListener('change', actualizar);
  selSede.addEventListener('change', actualizar);
  actualizar();
})();
</script>
