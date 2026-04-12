<?php
declare(strict_types=1);
$u = auth_user();
$rol = $u['rol'] ?? null;
$home = $u ? dashboard_url_for_role($rol) : url('index.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($pageTitle ?? 'Sistema Académico') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= h(asset_url('css/main.css')) ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white shadow-sm mb-3">
  <div class="container">
    <a class="navbar-brand" href="<?= h($home) ?>">Inicio</a>
    <?php if ($u): ?>
    <div class="d-flex align-items-center gap-3">
      <span class="d-flex align-items-center gap-2">
        <span class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width:36px;height:36px;">
          <i class="bi bi-person-fill"></i>
        </span>
        <span class="fw-semibold"><?= h($u['nombre'] ?? 'Usuario') ?></span>
      </span>
      <a class="btn btn-outline-danger btn-sm" href="<?= h(url('logout.php')) ?>">Cerrar sesión</a>
    </div>
    <?php endif; ?>
  </div>
</nav>
