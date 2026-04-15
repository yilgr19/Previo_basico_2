<?php
declare(strict_types=1);
$u = auth_user();
$home = $u ? dashboard_url_for_user() : url('index.php');
$nNotif = 0;
$resumenNotif = [];
if ($u && in_array((string) ($u['rol'] ?? ''), [ROLE_ESTUDIANTE, ROLE_DOCENTE], true)) {
    $nNotif = \App\Services\SolicitudesService::conteoNotificacionesParaUsuario($u);
    if ($nNotif > 0) {
        $resumenNotif = \App\Services\SolicitudesService::resumenNotificacionesPendientes($u, 6);
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
<body class="flex min-h-screen flex-col bg-gray-50 text-gray-900 antialiased">
<nav class="mb-6 border-b border-gray-200 bg-white shadow-sm">
  <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
    <a class="text-lg font-bold text-academic hover:text-academic-dark" href="<?= h($home) ?>">Inicio</a>
    <?php if ($u && (string) ($u['rol'] ?? '') === ROLE_ESTUDIANTE): ?>
    <div class="order-last flex w-full flex-wrap items-center gap-2 border-t border-gray-100 pt-3 text-sm md:order-none md:w-auto md:border-0 md:pt-0">
      <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Estudiante</span>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('estudiante/dashboard.php')) ?>">Inicio</a>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('estudiante/nueva_solicitud.php')) ?>">Nueva solicitud</a>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('estudiante/mis_solicitudes.php?tab=activas')) ?>">Mis solicitudes</a>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('estudiante/perfil.php')) ?>">Mi perfil</a>
    </div>
    <?php endif; ?>
    <?php if ($u && (string) ($u['rol'] ?? '') === ROLE_DOCENTE): ?>
    <div class="order-last flex w-full flex-wrap items-center gap-2 border-t border-gray-100 pt-3 text-sm md:order-none md:w-auto md:border-0 md:pt-0">
      <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Docente</span>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('docente/dashboard.php')) ?>">Panel</a>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('docente/nueva_solicitud.php')) ?>">Nueva solicitud</a>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('docente/mis_solicitudes.php?tab=activas')) ?>">Mis solicitudes</a>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('docente/perfil.php')) ?>">Mi perfil</a>
    </div>
    <?php endif; ?>
    <?php if ($u && (string) ($u['rol'] ?? '') === ROLE_ADMIN): ?>
    <div class="order-last flex w-full flex-wrap items-center gap-2 border-t border-gray-100 pt-3 text-sm md:order-none md:w-auto md:border-0 md:pt-0">
      <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Gestión</span>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('gestion/dashboard.php')) ?>">Panel</a>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('gestion/solicitudes.php')) ?>">Bandeja de solicitudes</a>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('gestion/solicitudes_revision.php')) ?>">En revisión</a>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('gestion/reportes.php')) ?>">Reportes</a>
    </div>
    <?php endif; ?>
    <?php if ($u): ?>
    <div class="flex items-center gap-3 sm:gap-4">
      <?php if ($uMisSolic !== ''): ?>
      <details class="relative z-[60]">
        <summary class="flex cursor-pointer list-none items-center rounded-lg p-1 text-gray-600 outline-none ring-academic/30 hover:bg-gray-100 marker:content-none [&::-webkit-details-marker]:hidden focus-visible:ring-2">
          <span class="relative inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white shadow-sm" title="Notificaciones de solicitudes" aria-label="Notificaciones de solicitudes">
            <svg class="h-5 w-5 text-academic" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
            <?php if ($nNotif > 0): ?>
              <span class="absolute right-1 top-1 h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white" title="<?= (int) $nNotif ?> actualización(es)" aria-hidden="true"></span>
            <?php endif; ?>
          </span>
        </summary>
        <div class="absolute right-0 mt-2 w-[min(100vw-2rem,20rem)] rounded-xl border border-gray-200 bg-white p-3 shadow-lg">
          <p class="mb-2 text-sm font-semibold text-gray-900">Respuestas de la universidad</p>
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
          <a class="mt-3 block w-full rounded-lg bg-academic py-2 text-center text-sm font-semibold text-white hover:bg-academic-dark" href="<?= h($uMisSolic) ?>">Ver en Mis solicitudes</a>
        </div>
      </details>
      <?php endif; ?>
      <span class="flex items-center gap-2">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-academic text-white" aria-hidden="true">
          <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        </span>
        <span class="max-w-[10rem] truncate font-semibold text-gray-800 sm:max-w-none"><?= h($u['nombre'] ?? 'Usuario') ?></span>
      </span>
      <a class="inline-flex items-center rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-medium text-red-700 shadow-sm transition hover:bg-red-50" href="<?= h(url('logout.php')) ?>">Cerrar sesión</a>
    </div>
    <?php endif; ?>
  </div>
</nav>
