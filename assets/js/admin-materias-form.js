(function () {
  function syncSalon() {
    var m = document.getElementById('fld-modalidad');
    var s = document.getElementById('fld-salon');
    if (!m || !s) return;
    var pres = m.value === 'presencial';
    s.required = pres;
    if (!pres) {
      s.value = '';
      s.setAttribute('disabled', 'disabled');
    } else {
      s.removeAttribute('disabled');
    }
  }
  var sel = document.getElementById('fld-modalidad');
  if (sel) {
    sel.addEventListener('change', syncSalon);
    syncSalon();
  }
  var btn = document.getElementById('btn-buscar-docente');
  if (btn) {
    btn.addEventListener('click', function () {
      var docInput = document.getElementById('inp-doc-docente');
      var hid = document.getElementById('hid-id-docente');
      var nom = document.getElementById('txt-nombre-docente');
      var doc = docInput ? docInput.value.trim() : '';
      var progSel = document.getElementById('fld-id-programa');
      var idProg = progSel ? parseInt(progSel.value, 10) : 0;
      if (!idProg) {
        window.alert('Seleccione primero la carrera de la asignatura.');
        return;
      }
      var list = window.DOCENTES_MATERIAS || [];
      var found = null;
      for (var i = 0; i < list.length; i++) {
        if (String(list[i].documento) === doc) {
          found = list[i];
          break;
        }
      }
      if (!found) {
        window.alert('No se encontró un docente con ese documento.');
        if (hid) hid.value = '';
        if (nom) nom.value = '';
        return;
      }
      var fp = parseInt(found.id_programa, 10) || 0;
      if (fp !== idProg) {
        window.alert('Ese docente no está registrado para esa carrera. Verifique el programa o el documento.');
        if (hid) hid.value = '';
        if (nom) nom.value = '';
        return;
      }
      var opt = progSel && progSel.options[progSel.selectedIndex];
      var sedeProg = opt ? parseInt(opt.getAttribute('data-sede'), 10) || 1 : 1;
      var sedeDoc = parseInt(found.id_sede, 10) || 1;
      if (sedeProg !== sedeDoc) {
        window.alert('La sede del docente no coincide con la carrera seleccionada (Cúcuta / Ocaña).');
        if (hid) hid.value = '';
        if (nom) nom.value = '';
        return;
      }
      if (hid) hid.value = found.id;
      if (nom) nom.value = found.nombre;
    });
  }
  var progChange = document.getElementById('fld-id-programa');
  if (progChange) {
    progChange.addEventListener('change', function () {
      var hid = document.getElementById('hid-id-docente');
      var nom = document.getElementById('txt-nombre-docente');
      if (hid) {
        hid.value = '';
      }
      if (nom) {
        nom.value = '';
      }
    });
  }
  var formM = document.getElementById('form-materia');
  if (formM) {
    formM.addEventListener('submit', function (e) {
      var hid = document.getElementById('hid-id-docente');
      if (!hid || !parseInt(hid.value, 10)) {
        e.preventDefault();
        window.alert('Busque al docente por documento y use Buscar antes de guardar.');
      }
    });
  }
})();
