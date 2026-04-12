(function () {
  var sedeSel = document.getElementById('fld-sede-docente');
  var progSel = document.getElementById('fld-programa-docente');
  if (!sedeSel || !progSel) return;

  function filterProgramas() {
    var sidRaw = sedeSel.value;
    var opts = progSel.querySelectorAll('option');
    if (!sidRaw) {
      for (var j = 0; j < opts.length; j++) {
        var o = opts[j];
        if (!o.value) {
          continue;
        }
        o.hidden = false;
        o.disabled = false;
      }
      return;
    }
    var sid = parseInt(sidRaw, 10);
    var keepVal = progSel.value;
    var stillOk = false;
    for (var i = 0; i < opts.length; i++) {
      var opt = opts[i];
      if (!opt.value) {
        continue;
      }
      var os = parseInt(opt.getAttribute('data-sede'), 10) || 1;
      var show = os === sid;
      opt.hidden = !show;
      opt.disabled = !show;
      if (show && opt.value === keepVal) {
        stillOk = true;
      }
    }
    if (!stillOk) {
      progSel.value = '';
    }
  }

  sedeSel.addEventListener('change', filterProgramas);
  filterProgramas();
})();
