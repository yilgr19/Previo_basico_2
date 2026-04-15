<?php
$yo = $yo ?? [];
$nActivas = (int) ($nActivas ?? 0);
$nEnRevision = (int) ($nEnRevision ?? 0);
$nAprobadas = (int) ($nAprobadas ?? 0);
$nRechazadas = (int) ($nRechazadas ?? 0);
$primerNombre = trim(explode(' ', (string) ($yo['nombre'] ?? 'Estudiante'))[0]);
$ini = strtoupper(substr((string) ($yo['nombre'] ?? 'E'), 0, 1) . substr((string) ($yo['apellido'] ?? ''), 0, 1));
if (strlen(trim($ini)) < 2) {
    $ini = strtoupper(substr(preg_replace('/\s+/', '', trim((string) ($yo['nombre'] ?? '') . (string) ($yo['apellido'] ?? ''))) ?: 'ES', 0, 2));
}
$idProg = (int) ($yo['id_programa'] ?? 0);
$progTxt = (string) ($yo['programa'] ?? '') !== '' ? (string) $yo['programa'] : ($idProg > 0 ? programa_label_by_id($idProg) : '');
$idSede = (int) ($yo['id_sede'] ?? 0);
$sedeTxt = $idSede > 0 ? sede_nombre($idSede) : '';
?>
<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <section class="relative mb-10 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-[0_8px_30px_rgb(0,0,0,0.06)] ring-1 ring-slate-900/[0.04]">
    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(135deg,rgb(239_246_255)_0%,rgb(255_255_255)_45%,rgb(248_250_252)_100%)]"></div>
    <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-academic/[0.07] blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-16 -left-16 h-56 w-56 rounded-full bg-emerald-200/20 blur-2xl"></div>

    <div class="relative px-5 py-8 sm:px-8 sm:py-10">
      <div class="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex min-w-0 flex-1 gap-5 sm:gap-6">
          <div class="flex h-[4.5rem] w-[4.5rem] shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-academic to-academic-dark text-lg font-bold tracking-tight text-white shadow-lg shadow-academic/25 ring-4 ring-white">
            <?= h(strlen($ini) >= 2 ? $ini : substr($ini . 'S', 0, 2)) ?>
          </div>
          <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center rounded-full bg-emerald-600/10 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-emerald-800">Estudiante</span>
            </div>
            <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Hola, <?= h($primerNombre) ?></h1>
            <p class="mt-3 max-w-2xl text-[15px] leading-relaxed text-slate-600">
              Este es su espacio para <strong class="font-semibold text-slate-800">solicitudes académicas</strong> y datos de contacto. Radique trámites con <strong class="font-semibold text-slate-800">Nueva solicitud</strong> y consulte el estado en <strong class="font-semibold text-slate-800">Mis solicitudes</strong>.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
              <a href="<?= h(url('estudiante/nueva_solicitud.php')) ?>" class="inline-flex items-center gap-2 rounded-xl bg-academic px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-academic/20 transition hover:bg-academic-dark">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                Nueva solicitud
              </a>
              <a href="<?= h(url('estudiante/mis_solicitudes.php?tab=activas')) ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-academic/40 hover:bg-slate-50">
                <svg class="h-4 w-4 text-academic" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
                Mis solicitudes
              </a>
            </div>
          </div>
        </div>

        <div class="grid w-full grid-cols-2 gap-3 sm:max-w-md sm:grid-cols-2 lg:w-[min(100%,20rem)] lg:shrink-0">
          <div class="rounded-xl border border-amber-100 bg-amber-50/90 px-3 py-3 shadow-sm">
            <div class="flex items-center gap-2 text-amber-800/90">
              <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
              <span class="text-[11px] font-semibold uppercase tracking-wide">Activas</span>
            </div>
            <p class="mt-1 text-2xl font-bold tabular-nums text-amber-950"><?= $nActivas ?></p>
            <p class="text-[10px] text-amber-800/70">Pendiente</p>
          </div>
          <div class="rounded-xl border border-sky-100 bg-sky-50/90 px-3 py-3 shadow-sm">
            <div class="flex items-center gap-2 text-sky-800/90">
              <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/></svg>
              <span class="text-[11px] font-semibold uppercase tracking-wide">En revisión</span>
            </div>
            <p class="mt-1 text-2xl font-bold tabular-nums text-sky-950"><?= $nEnRevision ?></p>
            <p class="text-[10px] text-sky-800/70">En análisis</p>
          </div>
          <div class="rounded-xl border border-emerald-100 bg-emerald-50/90 px-3 py-3 shadow-sm">
            <div class="flex items-center gap-2 text-emerald-800/90">
              <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
              <span class="text-[11px] font-semibold uppercase tracking-wide">Aprobadas</span>
            </div>
            <p class="mt-1 text-2xl font-bold tabular-nums text-emerald-950"><?= $nAprobadas ?></p>
            <p class="text-[10px] text-emerald-800/70">Resueltas</p>
          </div>
          <div class="rounded-xl border border-rose-100 bg-rose-50/90 px-3 py-3 shadow-sm">
            <div class="flex items-center gap-2 text-rose-800/90">
              <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
              <span class="text-[11px] font-semibold uppercase tracking-wide">Rechazadas</span>
            </div>
            <p class="mt-1 text-2xl font-bold tabular-nums text-rose-950"><?= $nRechazadas ?></p>
            <p class="text-[10px] text-rose-800/70">No procedieron</p>
          </div>
        </div>
      </div>

      <?php if ($progTxt !== '' || $sedeTxt !== ''): ?>
      <div class="mt-8 flex flex-col gap-3 border-t border-slate-200/80 pt-6 sm:flex-row sm:flex-wrap sm:items-center sm:gap-4">
        <?php if ($sedeTxt !== ''): ?>
        <div class="flex items-start gap-3 rounded-xl bg-slate-50/90 px-4 py-3 ring-1 ring-slate-200/60 sm:inline-flex sm:items-center">
          <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-academic shadow-sm ring-1 ring-slate-200/80" aria-hidden="true">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          </span>
          <div>
            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Sede</p>
            <p class="text-sm font-medium text-slate-900"><?= h($sedeTxt) ?></p>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($progTxt !== ''): ?>
        <div class="flex items-start gap-3 rounded-xl bg-slate-50/90 px-4 py-3 ring-1 ring-slate-200/60 sm:inline-flex sm:max-w-xl sm:items-center">
          <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-academic shadow-sm ring-1 ring-slate-200/80" aria-hidden="true">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/></svg>
          </span>
          <div class="min-w-0">
            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Programa académico</p>
            <p class="text-sm font-medium leading-snug text-slate-900"><?= h($progTxt) ?></p>
          </div>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <h2 class="mb-4 text-lg font-semibold tracking-tight text-slate-800">Accesos rápidos</h2>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <a href="<?= h(url('estudiante/nueva_solicitud.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/[0.03] transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-academic transition group-hover:scale-105">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
      </div>
      <strong class="block text-center text-gray-900">Nueva solicitud</strong>
      <span class="mt-1 block text-center text-xs text-gray-500">Radicar un trámite con el formulario completo</span>
    </a>
    <a href="<?= h(url('estudiante/mis_solicitudes.php?tab=activas')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/[0.03] transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-academic transition group-hover:scale-105">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
      </div>
      <strong class="block text-center text-gray-900">Mis solicitudes</strong>
      <span class="mt-1 block text-center text-xs text-gray-500">Ver todo filtrado por estado</span>
    </a>
    <a href="<?= h(url('estudiante/perfil.php')) ?>" class="group rounded-xl border border-gray-100 bg-white p-6 shadow-sm ring-1 ring-slate-900/[0.03] transition hover:-translate-y-0.5 hover:shadow-md">
      <div class="mb-2 flex justify-center text-emerald-600 transition group-hover:scale-105">
        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
      </div>
      <strong class="block text-center text-gray-900">Mi perfil</strong>
      <span class="mt-1 block text-center text-xs text-gray-500">Actualizar datos personales y de contacto</span>
    </a>
  </div>
</main>
