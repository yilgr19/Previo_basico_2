/**
 * Opcional: semilla demo en localStorage (clave academic_credentials).
 * El inicio de sesión lo valida el servidor con data/*.json — no se bloquea el envío del formulario.
 */
(function () {
  var STORAGE_KEY = 'academic_credentials';
  var DEFAULTS = {
    administradores: [
      { id_admin: 1, nombre: 'Administrador Principal', correo: 'admin@academico.edu', clave: 'admin123' },
    ],
    docentes: [],
    estudiantes: [],
  };

  function ensureSeeded() {
    try {
      if (!localStorage.getItem(STORAGE_KEY)) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(DEFAULTS));
      }
    } catch (e) {
      /* modo privado u otro bloqueo */
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    ensureSeeded();
  });
})();
