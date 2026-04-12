<main class="flex w-full flex-1 flex-col justify-center px-4 py-10">
  <div class="mx-auto w-full max-w-md">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-lg">
      <div class="p-6 sm:p-8">
        <h1 class="mb-6 text-center text-xl font-bold text-academic">Sistema Académico</h1>
        <?php if ($error): ?>
          <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><?= h($error) ?></div>
        <?php endif; ?>
        <form id="form-login" method="post" action="<?= h(url('login.php')) ?>" autocomplete="off" class="space-y-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Rol</label>
            <select name="rol" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30" required>
              <option value="">Seleccione...</option>
              <option value="administrador">Administrador</option>
              <option value="docente">Docente</option>
              <option value="estudiante">Estudiante</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Usuario</label>
            <input type="text" name="usuario" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
              placeholder="Correo (admin) o documento"
              value="<?= h(post('usuario') ?? '') ?>" required>
            <p class="mt-1 text-xs text-gray-500">Administrador: correo. Docente y estudiante: documento o correo registrado.</p>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700">Contraseña</label>
            <input type="password" name="clave" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30" required>
          </div>
          <button type="submit" class="mt-2 w-full rounded-lg bg-academic py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-academic-dark focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Entrar</button>
        </form>
      </div>
    </div>
  </div>
</main>
<script src="<?= h(asset_url('js/auth-credentials.js')) ?>"></script>
