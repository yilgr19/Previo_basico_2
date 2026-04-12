<?php
declare(strict_types=1);
/** Vista: panel principal del administrador. */
?>
<main class="flex-1 w-full mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
  <section class="mb-8 rounded-xl border-l-4 border-academic bg-gradient-to-r from-blue-50 to-white p-6 shadow-sm">
    <h1 class="text-xl font-semibold text-academic">Bienvenido al Sistema Académico</h1>
    <p class="mt-1 text-gray-600">Gestiona estudiantes, asignaturas y matrículas desde este panel central.</p>
  </section>

  <div id="admin-carousel" class="relative mb-8 overflow-hidden rounded-xl shadow-md ring-1 ring-gray-200/80">
    <div class="relative h-64 md:h-72">
      <div data-carousel-track class="flex h-full transition-transform duration-500 ease-out">
        <div data-carousel-slide class="relative min-w-full shrink-0">
          <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1200&q=80" class="h-full w-full object-cover" alt="">
          <div class="absolute inset-0 hidden flex-col justify-end bg-academic/45 p-6 md:flex">
            <div class="max-w-lg rounded-lg bg-black/25 px-4 py-3 text-white backdrop-blur-sm">
              <h2 class="text-lg font-semibold">Gestión integral académica</h2>
              <p class="text-sm opacity-95">Todo lo que necesitas para administrar tu institución</p>
            </div>
          </div>
        </div>
        <div data-carousel-slide class="relative min-w-full shrink-0">
          <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1200&q=80" class="h-full w-full object-cover" alt="">
          <div class="absolute inset-0 hidden flex-col justify-end bg-academic/45 p-6 md:flex">
            <div class="max-w-lg rounded-lg bg-black/25 px-4 py-3 text-white backdrop-blur-sm">
              <h2 class="text-lg font-semibold">Estudiantes y matrículas</h2>
              <p class="text-sm opacity-95">Registro alineado al diccionario de datos</p>
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

  <h2 class="mb-4 text-lg font-semibold text-gray-800">Accesos rápidos</h2>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
    <a href="<?= h(url('admin/estudiantes.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-5 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-blue-600">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
      </div>
      <strong class="block text-gray-900">Estudiantes</strong>
      <span class="text-xs text-gray-500">Registro y edición</span>
    </a>
    <a href="<?= h(url('admin/docentes.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-5 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-emerald-600">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      </div>
      <strong class="block text-gray-900">Docentes</strong>
      <span class="text-xs text-gray-500">Gestión de docentes</span>
    </a>
    <a href="<?= h(url('admin/materias.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-5 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-amber-500">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/></svg>
      </div>
      <strong class="block text-gray-900">Asignaturas</strong>
      <span class="text-xs text-gray-500">Catálogo y docente asignado</span>
    </a>
    <a href="<?= h(url('admin/matricular.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-5 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-sky-600">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
      </div>
      <strong class="block text-gray-900">Matricular</strong>
      <span class="text-xs text-gray-500">Inscripción de cursos</span>
    </a>
    <a href="<?= h(url('admin/reportes.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-5 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:col-span-2 lg:col-span-1">
      <div class="mb-2 flex justify-center text-gray-600">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
      </div>
      <strong class="block text-gray-900">Reportes</strong>
      <span class="text-xs text-gray-500">Listados y acciones</span>
    </a>
  </div>
</main>
<script src="<?= h(asset_url('js/admin-dashboard-carousel.js')) ?>"></script>
