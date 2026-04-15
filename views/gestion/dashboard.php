<?php
$adminNombre = $adminNombre ?? 'Administrador';
$conteosSolicitudesPorSede = $conteosSolicitudesPorSede ?? [
    1 => ['total' => 0, 'pendiente_revision' => 0],
    2 => ['total' => 0, 'pendiente_revision' => 0],
];
$nCucutaTotal = (int) ($conteosSolicitudesPorSede[1]['total'] ?? 0);
$nOcanaTotal = (int) ($conteosSolicitudesPorSede[2]['total'] ?? 0);
$nCucutaPendRev = (int) ($conteosSolicitudesPorSede[1]['pendiente_revision'] ?? 0);
$nOcanaPendRev = (int) ($conteosSolicitudesPorSede[2]['pendiente_revision'] ?? 0);
$parts = preg_split('/\s+/', $adminNombre, -1, PREG_SPLIT_NO_EMPTY);
$primer = $parts[0] ?? $adminNombre;
$ini = '';
if (count($parts) >= 2) {
    $ini = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
} else {
    $ini = strtoupper(substr(preg_replace('/\s+/', '', $adminNombre) ?: 'AD', 0, 2));
}
?>
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <section class="relative mb-10 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.06)] ring-1 ring-slate-900/[0.04]">
    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(135deg,rgb(239_246_255)_0%,rgb(255_255_255)_45%,rgb(248_250_252)_100%)]"></div>
    <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-academic/[0.07] blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-16 -left-16 h-56 w-56 rounded-full bg-violet-200/25 blur-2xl"></div>

    <div class="relative px-5 py-8 sm:px-8 sm:py-10">
      <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex min-w-0 flex-1 gap-5 sm:gap-6">
          <div class="flex h-[4.5rem] w-[4.5rem] shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-academic to-academic-dark text-lg font-bold tracking-tight text-white shadow-lg shadow-academic/25 ring-4 ring-white">
            <?= h(strlen($ini) >= 2 ? $ini : substr($ini . 'A', 0, 2)) ?>
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center rounded-full bg-violet-600/10 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-violet-900">Administración</span>
            </div>
            <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Panel de gestión</h1>
            <p class="mt-1 text-base font-medium text-slate-700">Hola, <?= h($primer) ?></p>
            <p class="mt-3 max-w-2xl text-[15px] leading-relaxed text-slate-600">
              Coordine el registro de <strong class="font-semibold text-slate-800">estudiantes</strong> y <strong class="font-semibold text-slate-800">docentes</strong>, y atienda las <strong class="font-semibold text-slate-800">bandejas de solicitudes por sede</strong> (Cúcuta y Ocaña) desde un solo lugar.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
              <a href="<?= h(url('gestion/solicitudes.php')) ?>" class="inline-flex items-center gap-2 rounded-xl bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-academic/20 transition hover:bg-academic-dark">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                Bandeja sede Cúcuta
              </a>
              <a href="<?= h(url('gestion/solicitudes_sede_ocana.php')) ?>" class="inline-flex items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-900 shadow-sm transition hover:bg-sky-100">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                Bandeja sede Ocaña
              </a>
              <a href="<?= h(url('gestion/solicitudes_revision.php')) ?>" class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-900 shadow-sm transition hover:bg-amber-100">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                En revisión Cúcuta
              </a>
              <a href="<?= h(url('gestion/solicitudes_revision_ocana.php')) ?>" class="inline-flex items-center gap-2 rounded-xl border border-amber-200/90 bg-amber-50/90 px-4 py-2.5 text-sm font-semibold text-amber-950 shadow-sm transition hover:bg-amber-100">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                En revisión Ocaña
              </a>
              <a href="<?= h(url('gestion/estudiantes.php')) ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-academic/40 hover:bg-slate-50">
                <svg class="h-4 w-4 text-academic" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
                Estudiantes
              </a>
              <a href="<?= h(url('gestion/docentes.php')) ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-academic/40 hover:bg-slate-50">
                <svg class="h-4 w-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                Docentes
              </a>
            </div>
          </div>
        </div>

        <div class="grid w-full max-w-2xl grid-cols-2 gap-3 sm:gap-4 lg:w-[min(100%,42rem)] lg:shrink-0">
          <a href="<?= h(url('gestion/solicitudes.php')) ?>" class="group rounded-xl border border-violet-100 bg-violet-50/90 px-3 py-4 text-center shadow-sm transition hover:border-violet-200 hover:shadow-md sm:px-4">
            <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-lg bg-white text-violet-600 shadow-sm ring-1 ring-violet-100">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
            </div>
            <p class="mt-3 text-3xl font-bold tabular-nums text-slate-900 group-hover:text-academic"><?= $nCucutaTotal ?></p>
            <p class="mt-1 text-[11px] font-semibold uppercase tracking-wide text-violet-900/90">Total solicitudes</p>
            <p class="mt-0.5 text-[10px] font-medium leading-tight text-violet-800/80">Sede Cúcuta</p>
          </a>
          <a href="<?= h(url('gestion/solicitudes_sede_ocana.php')) ?>" class="group rounded-xl border border-violet-100 bg-violet-50/90 px-3 py-4 text-center shadow-sm transition hover:border-violet-200 hover:shadow-md sm:px-4">
            <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-lg bg-white text-violet-600 shadow-sm ring-1 ring-violet-100">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
            </div>
            <p class="mt-3 text-3xl font-bold tabular-nums text-slate-900 group-hover:text-academic"><?= $nOcanaTotal ?></p>
            <p class="mt-1 text-[11px] font-semibold uppercase tracking-wide text-violet-900/90">Total solicitudes</p>
            <p class="mt-0.5 text-[10px] font-medium leading-tight text-violet-800/80">Sede Ocaña</p>
          </a>
          <a href="<?= h(url('gestion/solicitudes.php')) ?>" class="group rounded-xl border border-amber-200 bg-amber-50/90 px-3 py-4 text-center shadow-sm transition hover:border-amber-300 hover:shadow-md sm:px-4">
            <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-lg bg-white text-amber-600 shadow-sm ring-1 ring-amber-100">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
            <p class="mt-3 text-3xl font-bold tabular-nums text-slate-900 group-hover:text-amber-800"><?= $nCucutaPendRev ?></p>
            <p class="mt-1 text-[11px] font-semibold uppercase tracking-wide text-amber-900/90">Pendientes / en revisión</p>
            <p class="mt-0.5 text-[10px] leading-tight text-amber-800/75">Por atender o en análisis</p>
            <p class="mt-1 text-[10px] font-medium text-amber-900/80">Sede Cúcuta</p>
          </a>
          <a href="<?= h(url('gestion/solicitudes_sede_ocana.php')) ?>" class="group rounded-xl border border-amber-200 bg-amber-50/90 px-3 py-4 text-center shadow-sm transition hover:border-amber-300 hover:shadow-md sm:px-4">
            <div class="mx-auto flex h-9 w-9 items-center justify-center rounded-lg bg-white text-amber-600 shadow-sm ring-1 ring-amber-100">
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
            <p class="mt-3 text-3xl font-bold tabular-nums text-slate-900 group-hover:text-amber-800"><?= $nOcanaPendRev ?></p>
            <p class="mt-1 text-[11px] font-semibold uppercase tracking-wide text-amber-900/90">Pendientes / en revisión</p>
            <p class="mt-0.5 text-[10px] leading-tight text-amber-800/75">Por atender o en análisis</p>
            <p class="mt-1 text-[10px] font-medium text-amber-900/80">Sede Ocaña</p>
          </a>
        </div>
      </div>
    </div>
  </section>

  <div class="relative mb-10 overflow-hidden rounded-2xl shadow-lg ring-1 ring-slate-900/[0.06]">
    <div class="relative h-56 sm:h-64 md:h-72">
      <img src="<?= h(url('imagen/1.jpg')) ?>" class="h-full w-full object-cover" alt="">
      <div class="absolute inset-0 bg-gradient-to-t from-academic/90 via-academic/35 to-academic/20"></div>
      <div class="absolute inset-0 flex flex-col justify-end p-5 sm:p-8 md:flex">
        <div class="max-w-xl rounded-xl border border-white/10 bg-black/20 px-4 py-3 text-white shadow-lg backdrop-blur-md sm:px-5 sm:py-4">
          <h2 class="text-lg font-bold tracking-tight sm:text-xl">Solicitudes académicas</h2>
          <p class="mt-1 text-sm text-white/95">Radicación y seguimiento de trámites estudiantiles y docentes.</p>
        </div>
      </div>
    </div>
  </div>

  <h2 class="mb-4 text-lg font-semibold tracking-tight text-slate-800">Accesos rápidos</h2>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
    <a href="<?= h(url('gestion/solicitudes.php')) ?>" class="group rounded-xl border border-academic/25 bg-gradient-to-b from-blue-50/90 to-white p-6 text-center shadow-sm ring-1 ring-academic/10 transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-academic transition group-hover:scale-105">
        <svg class="h-12 w-12" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
      </div>
      <strong class="block text-gray-900">Bandeja de solicitudes sede Cúcuta</strong>
      <span class="mt-1 block text-xs text-gray-600">Filtrar, buscar y responder trámites de esta sede</span>
    </a>
    <a href="<?= h(url('gestion/solicitudes_sede_ocana.php')) ?>" class="group rounded-xl border border-sky-200/80 bg-gradient-to-b from-sky-50/90 to-white p-6 text-center shadow-sm ring-1 ring-sky-100 transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-sky-700 transition group-hover:scale-105">
        <svg class="h-12 w-12" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
      </div>
      <strong class="block text-gray-900">Bandeja de solicitudes por sedes — Ocaña</strong>
      <span class="mt-1 block text-xs text-gray-600">Mismos filtros que la bandeja de Cúcuta</span>
    </a>
    <a href="<?= h(url('gestion/solicitudes_revision.php')) ?>" class="group rounded-xl border border-amber-200/80 bg-gradient-to-b from-amber-50/90 to-white p-6 text-center shadow-sm ring-1 ring-amber-100 transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-amber-600 transition group-hover:scale-105">
        <svg class="h-12 w-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
      </div>
      <strong class="block text-gray-900">En revisión — Cúcuta</strong>
      <span class="mt-1 block text-xs text-gray-600">Solo estado «En revisión», sede Cúcuta</span>
    </a>
    <a href="<?= h(url('gestion/solicitudes_revision_ocana.php')) ?>" class="group rounded-xl border border-amber-200/80 bg-gradient-to-b from-amber-50/90 to-white p-6 text-center shadow-sm ring-1 ring-amber-100 transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-amber-700 transition group-hover:scale-105">
        <svg class="h-12 w-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
      </div>
      <strong class="block text-gray-900">En revisión — Ocaña</strong>
      <span class="mt-1 block text-xs text-gray-600">Solo estado «En revisión», sede Ocaña</span>
    </a>
    <a href="<?= h(url('gestion/reportes.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 text-center shadow-sm ring-1 ring-slate-900/[0.03] transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-violet-600 transition group-hover:scale-105">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
      </div>
      <strong class="block text-gray-900">Reportes</strong>
      <span class="mt-1 block text-xs text-gray-600">Vista consolidada y enlaces a fichas</span>
    </a>
    <a href="<?= h(url('gestion/estudiantes.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 text-center shadow-sm ring-1 ring-slate-900/[0.03] transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-blue-600 transition group-hover:scale-105">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
      </div>
      <strong class="block text-gray-900">Estudiantes</strong>
      <span class="text-xs text-gray-500">Registro y edición</span>
    </a>
    <a href="<?= h(url('gestion/docentes.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 text-center shadow-sm ring-1 ring-slate-900/[0.03] transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-emerald-600 transition group-hover:scale-105">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      </div>
      <strong class="block text-gray-900">Docentes</strong>
      <span class="text-xs text-gray-500">Alta y actualización</span>
    </a>
  </div>
</main>
