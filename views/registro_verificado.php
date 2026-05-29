<?php
$ok = !empty($ok);
$alertClass = $ok ? 'border-green-200 bg-green-50 text-green-900' : 'border-amber-200 bg-amber-50 text-amber-900';
?>
<main class="flex w-full flex-1 flex-col justify-center px-4 py-10">
  <div class="mx-auto w-full max-w-md">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">
      <div class="p-6 sm:p-8">
        <h1 class="mb-4 text-center text-xl font-bold text-academic">Verificación de cuenta</h1>
        <div class="mb-4 rounded-lg border px-4 py-3 text-sm <?= h($alertClass) ?>"><?= h($mensaje ?? '') ?></div>
        <p class="text-center text-sm">
          <a href="<?= h(url('login')) ?>" class="font-medium text-academic hover:underline">Ir a iniciar sesión</a>
          <?php if (!$ok): ?>
            · <a href="<?= h(url('registro_estudiante')) ?>" class="font-medium text-academic hover:underline">Volver a registrarse</a>
          <?php endif; ?>
        </p>
      </div>
    </div>
  </div>
</main>
