<?php
declare(strict_types=1);
$u = auth_user();
$home = $u ? dashboard_url_for_user() : url('index.php');
$nNotif = 0;
$resumenNotif = [];
$nGestionEst = 0;
$nGestionDoc = 0;
$resumenNotifGestion = [];
if ($u && in_array((string) ($u['rol'] ?? ''), [ROLE_ESTUDIANTE, ROLE_DOCENTE], true)) {
    $nNotif = \App\Services\SolicitudesService::conteoNotificacionesParaUsuario($u);
    if ($nNotif > 0) {
        $resumenNotif = \App\Services\SolicitudesService::resumenNotificacionesPendientes($u, 6);
    }
}
if ($u && (string) ($u['rol'] ?? '') === ROLE_ADMIN) {
    $cg = \App\Services\SolicitudesService::conteoNotificacionesGestionPorRadicante();
    $nGestionEst = (int) ($cg['estudiantes'] ?? 0);
    $nGestionDoc = (int) ($cg['docentes'] ?? 0);
    if ($nGestionEst + $nGestionDoc > 0) {
        $resumenNotifGestion = \App\Services\SolicitudesService::resumenNotificacionesGestion(8);
    }
}
$uMisSolic = '';
if ($u && (string) ($u['rol'] ?? '') === ROLE_ESTUDIANTE) {
    $uMisSolic = url('estudiante/mis_solicitudes.php');
} elseif ($u && (string) ($u['rol'] ?? '') === ROLE_DOCENTE) {
    $uMisSolic = url('docente/mis_solicitudes.php');
}
?>
<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($pageTitle ?? 'Sistema Académico') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            academic: { DEFAULT: '#0d47a1', light: '#e3f2fd', dark: '#0a3a82' }
          },
          fontFamily: {
            sans: ['Inter', 'system-ui', 'Segoe UI', 'Roboto', 'sans-serif'],
          },
        },
      },
    };
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= h(asset_url('css/main.css')) ?>">
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-gray-900 antialiased">
<nav class="sticky top-0 z-50 mb-6 border-b border-slate-200/90 bg-white/90 shadow-[0_4px_30px_-8px_rgba(15,23,42,0.1)] backdrop-blur-md">
  <div class="pointer-events-none absolute inset-x-0 top-0 h-[3px] bg-gradient-to-r from-academic via-sky-500/75 to-emerald-500/70 opacity-95"></div>
  <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-slate-300/40 to-transparent"></div>
  <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between gap-4 py-3.5 sm:py-4">
      <a class="group flex min-w-0 items-center gap-3 rounded-2xl py-1.5 pl-1 pr-2 transition hover:bg-slate-50/90" href="<?= h($home) ?>">
        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-academic to-academic-dark text-white shadow-lg shadow-academic/25 ring-2 ring-white">
          <svg class="h-6 w-6 opacity-95" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/></svg>
        </span>
        <span class="min-w-0 text-left">
          <span class="block truncate text-[15px] font-bold tracking-tight text-slate-900">Sistema Académico</span>
          <span class="block text-[11px] font-medium text-slate-500">Portal institucional</span>
        </span>
      </a>

    <?php if ($u): ?>
    <div class="flex shrink-0 flex-wrap items-center justify-end gap-2 sm:gap-3">
      <?php if ($u && (string) ($u['rol'] ?? '') === ROLE_ADMIN): ?>
      <details class="relative z-[60]">
        <summary class="flex cursor-pointer list-none items-center rounded-xl p-1 text-slate-600 outline-none ring-academic/30 hover:bg-slate-100 marker:content-none [&::-webkit-details-marker]:hidden focus-visible:ring-2">
          <span class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200/90 bg-white shadow-sm shadow-slate-900/5" title="Nuevas solicitudes radicadas" aria-label="Nuevas solicitudes radicadas">
            <svg class="h-5 w-5 text-academic" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
            <?php if ($nGestionEst > 0): ?>
              <span class="absolute -bottom-0.5 -right-0.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-blue-600 px-1 text-[9px] font-bold leading-none text-white ring-2 ring-white" title="Nuevas de estudiantes"><?= (int) $nGestionEst ?></span>
            <?php endif; ?>
            <?php if ($nGestionDoc > 0): ?>
              <span class="absolute -left-0.5 -top-0.5 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-emerald-600 px-1 text-[9px] font-bold leading-none text-white ring-2 ring-white" title="Nuevas de docentes"><?= (int) $nGestionDoc ?></span>
            <?php endif; ?>
          </span>
        </summary>
        <div class="absolute right-0 mt-2 w-[min(100vw-2rem,20rem)] rounded-xl border border-slate-200/90 bg-white p-3 shadow-[0_12px_40px_-12px_rgba(15,23,42,0.18)] ring-1 ring-slate-900/[0.04]">
          <p class="mb-2 text-sm font-semibold text-slate-900">Nuevas solicitudes</p>
          <p class="mb-2 text-[11px] text-slate-500">Radicaciones recientes sin abrir en una bandeja de gestión. <span class="font-medium text-blue-700">Azul: estudiantes</span> · <span class="font-medium text-emerald-700">Verde: docentes</span>.</p>
          <?php if ($nGestionEst + $nGestionDoc === 0): ?>
            <p class="text-xs text-gray-500">No hay solicitudes nuevas pendientes de revisar en el panel.</p>
          <?php else: ?>
            <ul class="max-h-56 space-y-2 overflow-y-auto text-xs">
              <?php foreach ($resumenNotifGestion as $rg): ?>
                <?php
                $esEst = ($rg['radicante'] ?? '') === 'estudiante';
                $borde = $esEst ? 'border-blue-600' : 'border-emerald-600';
                $etiq = $esEst ? 'Estudiante' : 'Docente';
                $etiqCls = $esEst ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-900';
                ?>
                <li class="rounded-lg border border-gray-100 bg-gray-50 px-2.5 py-2 border-l-4 <?= h($borde) ?>">
                  <div class="flex items-start justify-between gap-2">
                    <span class="font-mono text-[10px] text-gray-500">#<?= (int) $rg['id_solicitud'] ?></span>
                    <span class="shrink-0 rounded px-1.5 py-0.5 text-[10px] font-semibold <?= h($etiqCls) ?>"><?= h($etiq) ?></span>
                  </div>
                  <p class="mt-1 text-gray-700"><?= h($rg['tipo']) ?></p>
                  <?php if (($rg['fecha'] ?? '') !== ''): ?>
                    <p class="mt-0.5 text-[10px] text-gray-400">Registro: <?= h($rg['fecha']) ?></p>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
          <a class="mt-3 block w-full rounded-xl bg-academic py-2.5 text-center text-sm font-semibold text-white shadow-md shadow-academic/20 hover:bg-academic-dark" href="<?= h(url('gestion/solicitudes.php')) ?>">Abrir bandeja Cúcuta</a>
          <a class="mt-2 block w-full rounded-xl border border-sky-200/90 bg-sky-50 py-2 text-center text-xs font-semibold text-sky-900 hover:bg-sky-100" href="<?= h(url('gestion/solicitudes_sede_ocana.php')) ?>">Bandeja Ocaña</a>
        </div>
      </details>
      <?php elseif ($uMisSolic !== ''): ?>
      <details class="relative z-[60]">
        <summary class="flex cursor-pointer list-none items-center rounded-xl p-1 text-slate-600 outline-none ring-academic/30 hover:bg-slate-100 marker:content-none [&::-webkit-details-marker]:hidden focus-visible:ring-2">
          <span class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200/90 bg-white shadow-sm shadow-slate-900/5" title="Notificaciones de solicitudes" aria-label="Notificaciones de solicitudes">
            <svg class="h-5 w-5 text-academic" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
            <?php if ($nNotif > 0): ?>
              <span class="absolute right-1 top-1 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white" title="<?= (int) $nNotif ?> actualización(es)" aria-hidden="true"></span>
            <?php endif; ?>
          </span>
        </summary>
        <div class="absolute right-0 mt-2 w-[min(100vw-2rem,20rem)] rounded-xl border border-slate-200/90 bg-white p-3 shadow-[0_12px_40px_-12px_rgba(15,23,42,0.18)] ring-1 ring-slate-900/[0.04]">
          <p class="mb-2 text-sm font-semibold text-slate-900">Respuestas de la universidad</p>
          <?php if ($nNotif === 0): ?>
            <p class="text-xs text-gray-500">No hay cambios de estado sin revisar en sus solicitudes.</p>
          <?php else: ?>
            <ul class="max-h-56 space-y-2 overflow-y-auto text-xs">
              <?php foreach ($resumenNotif as $r): ?>
                <li class="rounded-lg border border-gray-100 bg-gray-50 px-2.5 py-2">
                  <div class="flex items-start justify-between gap-2">
                    <span class="font-mono text-[10px] text-gray-500">#<?= (int) $r['id_solicitud'] ?></span>
                    <span class="shrink-0 rounded bg-academic/10 px-1.5 py-0.5 text-[10px] font-medium text-academic"><?= h($r['estado']) ?></span>
                  </div>
                  <p class="mt-1 text-gray-700"><?= h($r['tipo']) ?></p>
                  <?php if (($r['fecha'] ?? '') !== ''): ?>
                    <p class="mt-0.5 text-[10px] text-gray-400"><?= h($r['fecha']) ?></p>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
          <a class="mt-3 block w-full rounded-xl bg-academic py-2.5 text-center text-sm font-semibold text-white shadow-md shadow-academic/20 hover:bg-academic-dark" href="<?= h($uMisSolic) ?>">Ver en Mis solicitudes</a>
        </div>
      </details>
      <?php endif; ?>
      <span class="inline-flex max-w-[min(100%,14rem)] items-center gap-2.5 rounded-xl border border-slate-200/90 bg-gradient-to-br from-slate-50 to-white py-1.5 pl-1.5 pr-3 shadow-sm shadow-slate-900/5 sm:max-w-none">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-academic to-academic-dark text-white shadow-md shadow-academic/20" aria-hidden="true">
          <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        </span>
        <span class="max-w-[9rem] truncate text-sm font-semibold text-slate-900 sm:max-w-[12rem] lg:max-w-none"><?= h($u['nombre'] ?? 'Usuario') ?></span>
      </span>
      <a class="inline-flex items-center rounded-xl border border-red-200/90 bg-white px-3 py-2 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-50" href="<?= h(url('logout.php')) ?>">Cerrar sesión</a>
    </div>
    <?php endif; ?>
    </div>
  </div>
</nav>
