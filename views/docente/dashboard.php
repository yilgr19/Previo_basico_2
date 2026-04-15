<main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-12 sm:px-6 lg:px-8">
  <section class="mb-8 rounded-xl border-l-4 border-academic bg-gradient-to-r from-blue-50 to-white p-6 shadow-sm">
    <h1 class="text-xl font-semibold text-academic">Bienvenido, <?= h($d['nombre'] ?? 'Docente') ?></h1>
    <p class="mt-1 text-gray-600">Use el apartado de <strong>solicitudes</strong> para radicar trámites y consultar menciones de forma confidencial.</p>
    <p class="mt-2 text-sm text-gray-600">
      <strong class="text-gray-800">Sede:</strong> <?= h(sede_nombre(docente_sede_efectiva($d))) ?>
    </p>
    <?php if (!empty($d['programa']) || !empty($d['id_programa'])): ?>
      <p class="mt-1 text-sm text-gray-600">
        <strong class="text-gray-800">Carrera a la que dicta clase:</strong>
        <?= h($d['programa'] ?? programa_label_by_id((int) ($d['id_programa'] ?? 0))) ?>
      </p>
    <?php endif; ?>
  </section>

  <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
    <a href="<?= h(url('docente/solicitudes.php')) ?>" class="rounded-xl border-2 border-academic/30 bg-blue-50/80 p-5 shadow-sm transition hover:shadow-md">
      <h2 class="text-base font-semibold text-academic">Solicitudes institucionales</h2>
      <p class="mt-1 text-sm text-gray-600">Radicar trámites, adjuntar evidencias y ver menciones sin datos del estudiante.</p>
    </a>
  </div>
</main>
