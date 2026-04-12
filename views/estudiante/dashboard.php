<?php
$inp = 'mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30';
$lbl = 'mb-1 block text-sm font-medium text-gray-700';
?>
<main class="flex-1 w-full mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
  <section class="mb-8 rounded-xl border-l-4 border-academic bg-gradient-to-r from-blue-50 to-white p-6 shadow-sm">
    <h1 class="text-xl font-semibold text-academic">Hola, <?= h(explode(' ', (string) ($yo['nombre'] ?? 'Estudiante'))[0]) ?></h1>
    <p class="mt-1 text-gray-600">Consulta tu matrícula y envía solicitudes académicas según el diccionario de datos.</p>
  </section>

  <?php if ($mensaje): ?>
    <div class="mb-6 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900"><?= h($mensaje) ?></div>
  <?php endif; ?>

  <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="flex h-full flex-col rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
      <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Mis datos</h2>
      <p class="mb-1 text-sm text-gray-700"><strong class="text-gray-800">Identificación:</strong> <?= h(tipo_identificacion_nombre((string) ($yo['tipo_identificacion'] ?? ''))) ?> <?= h($yo['documento'] ?? '') ?></p>
      <p class="mb-1 text-sm text-gray-700"><strong class="text-gray-800">Correo:</strong> <?= h($yo['correo'] ?? '') ?></p>
      <p class="mb-1 text-sm text-gray-700"><strong class="text-gray-800">Sexo:</strong> <?= h(sexo_nombre((string) ($yo['sexo'] ?? ''))) ?></p>
      <p class="mb-1 text-sm text-gray-700"><strong class="text-gray-800">Programa:</strong> <?= h($yo['programa'] ?? '') ?></p>
      <p class="mb-1 text-sm text-gray-700"><strong class="text-gray-800">Semestre:</strong> <?= h((string) ($yo['semestre'] ?? '')) ?> ·
        <strong>Sede:</strong> <?= h(sede_nombre(isset($yo['id_sede']) ? (int) $yo['id_sede'] : null)) ?> ·
        <strong>Jornada:</strong> <?= h(jornada_nombre(isset($yo['id_jornada']) ? (int) $yo['id_jornada'] : null)) ?>
      </p>
      <?php if (!empty($yo['fecha_nacimiento'])): ?>
        <p class="mb-1 text-sm text-gray-700"><strong class="text-gray-800">Fecha de nacimiento:</strong> <?= h($yo['fecha_nacimiento'] ?? '') ?>
          <?php if (isset($yo['edad'])): ?> · <strong>Edad:</strong> <?= (int) $yo['edad'] ?> años<?php endif; ?>
        </p>
      <?php endif; ?>
      <?php if (trim((string) ($yo['direccion'] ?? '')) !== '' || trim((string) ($yo['barrio'] ?? '')) !== '' || trim((string) ($yo['telefono'] ?? '')) !== ''): ?>
        <p class="mb-1 text-sm text-gray-700"><strong class="text-gray-800">Dirección:</strong> <?= h($yo['direccion'] ?? '') ?></p>
        <p class="text-sm text-gray-700"><strong class="text-gray-800">Barrio:</strong> <?= h($yo['barrio'] ?? '') ?> · <strong>Teléfono:</strong> <?= h($yo['telefono'] ?? '') ?></p>
      <?php endif; ?>
    </div>
    <div class="flex h-full flex-col rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
      <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Asignaturas matriculadas</h2>
      <?php if (!$matsEst): ?>
        <p class="text-sm text-gray-500">Aún no tiene matrículas registradas por administración.</p>
      <?php else: ?>
        <ul class="divide-y divide-gray-100">
          <?php foreach ($matsEst as $row): ?>
            <?php
            $x = $row['matricula'];
            $mat = $row['materia'];
            ?>
            <li class="py-3 first:pt-0">
              <?php if ($mat): ?>
                <div class="font-semibold text-gray-900"><?= h(materia_nombre((int) ($mat['id_materia'] ?? 0))) ?></div>
                <div class="mt-1 text-sm text-gray-600">
                  <?= h(materia_programa_label($mat)) ?> · <?= h(materia_horario_resumen($mat)) ?>
                  · <span class="inline-flex rounded-full bg-gray-200 px-2 py-0.5 text-xs font-medium text-gray-800"><?= h(materia_modalidad_etiqueta($mat)) ?></span>
                  <?php if (($mat['modalidad'] ?? '') === 'presencial' && trim((string) ($mat['salon'] ?? '')) !== ''): ?>
                    · Salón <?= h($mat['salon']) ?>
                  <?php endif; ?>
                  · Matrícula: <?= h($x['fecha'] ?? '') ?>
                </div>
              <?php else: ?>
                <div class="font-semibold text-amber-700">Asignatura no encontrada (ID <?= (int) ($x['id_materia'] ?? 0) ?>)</div>
                <div class="text-sm text-gray-600">Matrícula: <?= h($x['fecha'] ?? '') ?></div>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <div class="mt-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Nueva solicitud (tipos de solicitud)</h2>
    <form method="post" class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <input type="hidden" name="accion" value="nueva_solicitud">
      <div>
        <label class="<?= h($lbl) ?>">Tipo de solicitud</label>
        <select name="id_tipo_solicitud" class="<?= h($inp) ?>" required>
          <option value="">Seleccione...</option>
          <?php foreach (diccionario_tipos_solicitud() as $t): ?>
            <option value="<?= (int) $t['id'] ?>"><?= h($t['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="<?= h($lbl) ?>">Descripción</label>
        <textarea name="descripcion" class="<?= h($inp) ?>" rows="3" required placeholder="Detalle su solicitud"></textarea>
      </div>
      <div class="md:col-span-2">
        <button type="submit" class="inline-flex rounded-lg bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark">Enviar solicitud</button>
      </div>
    </form>
  </div>

  <div class="mt-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
    <h2 class="mb-4 border-b border-blue-100 pb-2 text-base font-semibold text-academic">Mis solicitudes</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50"><tr>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Fecha</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Tipo</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Estado</th>
          <th class="px-3 py-2 text-left font-semibold text-gray-700">Descripción</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($solicitudes as $s): ?>
            <tr>
              <td class="px-3 py-2"><?= h($s['fecha'] ?? '') ?></td>
              <td class="px-3 py-2"><?= h(tipo_solicitud_nombre((int) ($s['id_tipo_solicitud'] ?? 0))) ?></td>
              <td class="px-3 py-2"><?= h($s['estado'] ?? '') ?></td>
              <td class="max-w-md px-3 py-2"><?= h($s['descripcion'] ?? '') ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$solicitudes): ?>
            <tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">Sin solicitudes aún.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
