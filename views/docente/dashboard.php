<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <section class="mb-8 rounded-xl border-l-4 border-academic bg-gradient-to-r from-blue-50 to-white p-6 shadow-sm">
    <h1 class="text-xl font-semibold text-academic">Bienvenido, <?= h($d['nombre'] ?? 'Docente') ?></h1>
    <p class="mt-1 text-gray-600">Radique trámites desde <strong>Nueva solicitud</strong> y revise el historial en <strong>Mis solicitudes</strong> (por estado).</p>
    <p class="mt-2 text-sm text-gray-500">Resumen solicitudes propias: activas <strong class="text-gray-800"><?= (int) ($nActivas ?? 0) ?></strong> · en revisión <strong class="text-gray-800"><?= (int) ($nEnRevision ?? 0) ?></strong> · aprobadas <strong class="text-gray-800"><?= (int) ($nAprobadas ?? 0) ?></strong> · rechazadas <strong class="text-gray-800"><?= (int) ($nRechazadas ?? 0) ?></strong></p>
    <p class="mt-2 text-sm text-gray-600">
      <strong class="text-gray-800">Sede:</strong> <?= h(sede_nombre(docente_sede_efectiva($d))) ?>
    </p>
    <?php if (!empty($d['programa']) || !empty($d['id_programa'])): ?>
      <p class="mt-1 text-sm text-gray-600">
        <strong class="text-gray-800">Carrera a la que dicta clase:</strong>
        <?= h($d['programa'] ?? programa_label_by_id((int) ($d['id_programa'] ?? 0))) ?>
      </p>
    <?php endif; ?>
  </section>

  <h2 class="mb-4 text-lg font-semibold text-gray-800">Accesos</h2>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
    <a href="<?= h(url('docente/solicitudes.php#nueva-solicitud')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-academic">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
      </div>
      <strong class="block text-center text-gray-900">Nueva solicitud</strong>
      <span class="mt-1 block text-center text-xs text-gray-500">Formulario de solicitud docente (catálogo propio)</span>
    </a>
    <a href="<?= h(url('docente/solicitudes.php?tab=activas#mis-solicitudes')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-academic">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
      </div>
      <strong class="block text-center text-gray-900">Mis solicitudes</strong>
      <span class="mt-1 block text-center text-xs text-gray-500">Listado filtrado por activas, revisión, aprobadas o rechazadas</span>
    </a>
  </div>
  <p class="mt-4 text-xs text-gray-500">En la página de solicitudes también encontrará las <strong>menciones</strong> de estudiantes (vista confidencial).</p>
</main>
