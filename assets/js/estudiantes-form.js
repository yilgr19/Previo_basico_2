(function () {
  function calcEdad(ymd) {
    if (!ymd || ymd.length < 8) return '';
    var p = ymd.split('-');
    if (p.length !== 3) return '';
    var y = parseInt(p[0], 10);
    var m = parseInt(p[1], 10) - 1;
    var d = parseInt(p[2], 10);
    var birth = new Date(y, m, d);
    if (isNaN(birth.getTime())) return '';
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    var age = today.getFullYear() - birth.getFullYear();
    var mo = today.getMonth() - birth.getMonth();
    if (mo < 0 || (mo === 0 && today.getDate() < birth.getDate())) age--;
    return age >= 0 ? String(age) : '';
  }
  var inpFecha = document.querySelector('input[name="fecha_nacimiento"]');
  if (!inpFecha) return;
  inpFecha.addEventListener('change', function () {
    var v = calcEdad(inpFecha.value);
    if (v && window.console) console.log('Edad aproximada: ' + v);
  });
})();
