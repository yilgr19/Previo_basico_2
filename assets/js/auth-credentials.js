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
    } catch (e) {}
  }

  document.addEventListener('DOMContentLoaded', function () {
    ensureSeeded();
  });
})();
