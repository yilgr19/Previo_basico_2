<?php
/** Vista: formulario de inicio de sesión. Variables: $error */
?>
<div class="container login-card">
  <div class="card shadow">
    <div class="card-body p-4">
      <h1 class="h4 text-center mb-4" style="color:#0d47a1;">Sistema Académico</h1>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= h($error) ?></div>
      <?php endif; ?>
      <form id="form-login" method="post" action="<?= h(url('login.php')) ?>" autocomplete="off">
        <div class="mb-3">
          <label class="form-label">Rol</label>
          <select name="rol" class="form-select" required>
            <option value="">Seleccione...</option>
            <option value="administrador">Administrador</option>
            <option value="docente">Docente</option>
            <option value="estudiante">Estudiante</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Usuario</label>
          <input type="text" name="usuario" class="form-control"
            placeholder="Correo (admin) o documento"
            value="<?= h(post('usuario') ?? '') ?>" required>
          <div class="form-text">Administrador: correo. Docente y estudiante: documento o correo registrado.</div>
        </div>
        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" name="clave" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
      </form>
      <p class="small text-muted mt-3 mb-0">
        El inicio de sesión usa los datos de <code>data/administradores.json</code>, <code>data/docentes.json</code> y <code>data/estudiantes.json</code>.
        Opcionalmente hay una semilla demo en <code>localStorage</code> (<code>academic_credentials</code>).
      </p>
    </div>
  </div>
</div>
<script src="<?= h(asset_url('js/auth-credentials.js')) ?>"></script>
