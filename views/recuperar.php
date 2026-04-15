<main class="flex w-full flex-1 flex-col justify-center px-4 py-10">
  <div class="mx-auto w-full max-w-md">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">
      <div class="p-6 sm:p-8">
        <h1 class="mb-2 text-center text-xl font-bold text-academic">Recuperar contraseña</h1>
        <p class="mb-6 text-center text-sm text-gray-600">Estudiante o docente: indique documento y correo tal como están registrados.</p>
        <?php if ($error): ?>
          <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><?= h($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($claveTemporal)): ?>
          <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-900">
            Contraseña restablecida. Su clave temporal es: <strong class="font-mono"><?= h($claveTemporal) ?></strong>. Inicie sesión y cámbiela desde su perfil si lo desea.
          </div>
        <?php endif; ?>
        <form method="post" class="space-y-4" autocomplete="off">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Tipo</label>
            <select name="tipo" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm" required>
              <option value="estudiante">Estudiante</option>
              <option value="docente">Docente</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Documento</label>
            <input type="text" name="documento" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm" required>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Correo registrado</label>
            <input type="email" name="correo" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm" required>
          </div>
          <button type="submit" class="mt-2 w-full rounded-lg bg-academic py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-academic-dark">Generar nueva clave</button>
        </form>
        <p class="mt-6 text-center text-sm">
          <a href="<?= h(url('login.php')) ?>" class="font-medium text-academic hover:underline">Volver al inicio de sesión</a>
        </p>
      </div>
    </div>
  </div>
</main>
