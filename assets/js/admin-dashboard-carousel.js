(function () {
  var root = document.getElementById('admin-carousel');
  if (!root) return;
  var track = root.querySelector('[data-carousel-track]');
  var slides = root.querySelectorAll('[data-carousel-slide]');
  var prev = root.querySelector('[data-carousel-prev]');
  var next = root.querySelector('[data-carousel-next]');
  var dots = root.querySelectorAll('[data-carousel-dot]');
  var i = 0;
  var n = slides.length;
  if (!track || n === 0) return;

  function apply() {
    var w = root.offsetWidth;
    track.style.transform = 'translateX(-' + i * w + 'px)';
    dots.forEach(function (d, j) {
      d.classList.toggle('bg-white', j === i);
      d.classList.toggle('bg-white/40', j !== i);
    });
  }

  function go(idx) {
    i = ((idx % n) + n) % n;
    apply();
  }

  if (prev) prev.addEventListener('click', function () { go(i - 1); });
  if (next) next.addEventListener('click', function () { go(i + 1); });
  dots.forEach(function (d, j) {
    d.addEventListener('click', function () { go(j); });
  });
  window.addEventListener('resize', apply);
  go(0);
  setInterval(function () { go(i + 1); }, 7000);
})();
