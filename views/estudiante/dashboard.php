<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <section class="mb-8 rounded-xl border-l-4 border-academic bg-gradient-to-r from-blue-50 to-white p-6 shadow-sm">
    <h1 class="text-xl font-semibold text-academic">Hola, <?= h(explode(' ', (string) ($yo['nombre'] ?? 'Estudiante'))[0]) ?></h1>
    <p class="mt-1 text-gray-600">Este espacio está pensado para sus <strong>solicitudes académicas</strong> y la actualización de sus datos de contacto.</p>
    <p class="mt-2 text-sm text-gray-500">Resumen: activas (pendiente) <strong class="text-gray-800"><?= (int) ($nActivas ?? 0) ?></strong> · en revisión <strong class="text-gray-800"><?= (int) ($nEnRevision ?? 0) ?></strong> · aprobadas <strong class="text-gray-800"><?= (int) ($nAprobadas ?? 0) ?></strong> · rechazadas <strong class="text-gray-800"><?= (int) ($nRechazadas ?? 0) ?></strong></p>
  </section>

  <h2 class="mb-4 text-lg font-semibold text-gray-800">Accesos</h2>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <a href="<?= h(url('estudiante/nueva_solicitud.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-academic">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
      </div>
      <strong class="block text-center text-gray-900">Nueva solicitud</strong>
      <span class="mt-1 block text-center text-xs text-gray-500">Radicar un trámite con el formulario completo</span>
    </a>
    <a href="<?= h(url('estudiante/mis_solicitudes.php?tab=activas')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-academic">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
      </div>
      <strong class="block text-center text-gray-900">Mis solicitudes</strong>
      <span class="mt-1 block text-center text-xs text-gray-500">Ver todo filtrado por estado</span>
    </a>
    <a href="<?= h(url('estudiante/perfil.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-emerald-600">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      </div>
      <strong class="block text-center text-gray-900">Mi perfil</strong>
      <span class="mt-1 block text-center text-xs text-gray-500">Actualizar datos personales y de contacto</span>
    </a>
  </div>
</main>
