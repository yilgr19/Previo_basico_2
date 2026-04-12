(function () {
  var sel = document.getElementById('selMateria');
  var out = document.getElementById('semestreMateria');
  if (!sel || !out) return;
  sel.addEventListener('change', function () {
    var opt = sel.options[sel.selectedIndex];
    var s = opt && opt.getAttribute('data-semestre');
    out.value = s ? s : '';
  });
})();
