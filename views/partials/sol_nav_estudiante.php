<?php
declare(strict_types=1);
$activa = $solNavActiva ?? 'lista';
$bn = 'inline-flex items-center rounded-lg px-4 py-2 text-sm ';
$nuevaCls = $activa === 'nueva' ? $bn . 'font-semibold text-academic bg-blue-50' : $bn . 'font-medium text-gray-700 hover:bg-gray-50';
$listaCls = $activa === 'lista' ? $bn . 'font-semibold text-academic bg-blue-50' : $bn . 'font-medium text-gray-700 hover:bg-gray-50';
?>
<nav class="mb-6 flex flex-wrap gap-2 rounded-xl border border-gray-200 bg-white p-2 shadow-sm" aria-label="Solicitudes">
  <a href="<?= h(url('estudiante/nueva_solicitud.php')) ?>" class="<?= h($nuevaCls) ?>">Nueva solicitud</a>
  <a href="<?= h(url('estudiante/mis_solicitudes.php')) ?>" class="<?= h($listaCls) ?>">Mis solicitudes</a>
</nav>
