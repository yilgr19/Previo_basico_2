<?php
declare(strict_types=1);
$u = auth_user();
$home = $u ? dashboard_url_for_user() : url('index.php');
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
    </div>
    <?php endif; ?>
    <?php if ($u && (string) ($u['rol'] ?? '') === ROLE_ADMIN): ?>
    <div class="order-last flex w-full flex-wrap items-center gap-2 border-t border-gray-100 pt-3 text-sm md:order-none md:w-auto md:border-0 md:pt-0">
      <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Gestión</span>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('gestion/dashboard.php')) ?>">Panel</a>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('gestion/solicitudes.php')) ?>">Bandeja de solicitudes</a>
      <a class="rounded-lg px-2 py-1 text-gray-700 hover:bg-gray-100" href="<?= h(url('gestion/reportes.php')) ?>">Reportes</a>
    </div>
    <?php endif; ?>
    <?php if ($u): ?>
    <div class="flex items-center gap-4">
      <span class="flex items-center gap-2">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-academic text-white" aria-hidden="true">
          <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        </span>
        <span class="font-semibold text-gray-800"><?= h($u['nombre'] ?? 'Usuario') ?></span>
      </span>
      <a class="inline-flex items-center rounded-lg border border-red-200 bg-white px-3 py-1.5 text-sm font-medium text-red-700 shadow-sm transition hover:bg-red-50" href="<?= h(url('logout.php')) ?>">Cerrar sesión</a>
    </div>
    <?php endif; ?>
  </div>
</nav>
