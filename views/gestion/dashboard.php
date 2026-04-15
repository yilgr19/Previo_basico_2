<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <section class="mb-8 rounded-xl border-l-4 border-academic bg-gradient-to-r from-blue-50 to-white p-6 shadow-sm">
    <h1 class="text-xl font-semibold text-academic">Panel de gestión</h1>
    <p class="mt-1 text-gray-600">Registro de estudiantes y docentes, y atención de <strong>solicitudes estudiantiles</strong>.</p>
  </section>

  <div id="panel-carrusel" class="relative mb-8 overflow-hidden rounded-xl shadow-md ring-1 ring-gray-200/80">
    <div class="relative h-64 md:h-72">
      <div data-carousel-track class="flex h-full transition-transform duration-500 ease-out">
        <div data-carousel-slide class="relative min-w-full shrink-0">
          <img src="<?= h(url('imagen/1.jpg')) ?>" class="h-full w-full object-cover" alt="">
          <div class="absolute inset-0 hidden flex-col justify-end bg-academic/45 p-6 md:flex">
            <div class="max-w-lg rounded-lg bg-black/25 px-4 py-3 text-white backdrop-blur-sm">
              <h2 class="text-lg font-semibold">Solicitudes académicas</h2>
              <p class="text-sm opacity-95">Radicación y seguimiento de trámites estudiantiles</p>
            </div>
          </div>
        </div>
        <div data-carousel-slide class="relative min-w-full shrink-0">
          <img src="<?= h(url('imagen/2.jpg')) ?>" class="h-full w-full object-cover" alt="">
          <div class="absolute inset-0 hidden flex-col justify-end bg-academic/45 p-6 md:flex">
            <div class="max-w-lg rounded-lg bg-black/25 px-4 py-3 text-white backdrop-blur-sm">
              <h2 class="text-lg font-semibold">Comunidad universitaria</h2>
              <p class="text-sm opacity-95">Estudiantes y docentes en un solo sistema</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <button type="button" data-carousel-prev class="absolute left-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/90 p-2 text-academic shadow hover:bg-white" aria-label="Anterior">
      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </button>
    <button type="button" data-carousel-next class="absolute right-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/90 p-2 text-academic shadow hover:bg-white" aria-label="Siguiente">
      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2">
      <button type="button" data-carousel-dot class="h-2 w-2 rounded-full bg-white shadow" aria-label="Slide 1"></button>
      <button type="button" data-carousel-dot class="h-2 w-2 rounded-full bg-white/40 shadow" aria-label="Slide 2"></button>
    </div>
  </div>

  <h2 class="mb-4 text-lg font-semibold text-gray-800">Accesos</h2>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <a href="<?= h(url('gestion/solicitudes.php')) ?>" class="group rounded-xl border-2 border-academic/30 bg-blue-50/80 p-6 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-academic">
        <svg class="h-12 w-12" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
      </div>
      <strong class="block text-gray-900">Solicitudes estudiantiles</strong>
      <span class="mt-1 block text-xs text-gray-600">Filtrar, buscar y responder trámites</span>
    </a>
    <a href="<?= h(url('gestion/estudiantes.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-blue-600">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
      </div>
      <strong class="block text-gray-900">Estudiantes</strong>
      <span class="text-xs text-gray-500">Registro y edición</span>
    </a>
    <a href="<?= h(url('gestion/docentes.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-emerald-600">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      </div>
      <strong class="block text-gray-900">Docentes</strong>
      <span class="text-xs text-gray-500">Alta y actualización</span>
    </a>
  </div>
</main>
<script src="<?= h(asset_url('js/panel-carrusel.js')) ?>"></script>
