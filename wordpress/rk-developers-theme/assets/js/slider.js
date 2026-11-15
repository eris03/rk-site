/**
 * RK Developers — project photo slideshow ([rk_project_slider]).
 * Same behaviour as the static Metro Greens / RK Avenue galleries:
 * 3.5 s autoplay, arrows, thumbnails, counter and progress bar.
 * Images come from the project's "Gallery images" (window.RK_SLIDERS).
 */
(function () {
  'use strict';
  var sliders = window.RK_SLIDERS || {};

  Object.keys(sliders).forEach(function (pre) {
    var imgs = sliders[pre].images || [];
    var active = sliders[pre].active || '#D4A843';
    if (!imgs.length || !document.getElementById(pre + 'MainImg')) return;
    var idx = 0, timer = null;

    function goTo(i) {
      idx = (i + imgs.length) % imgs.length;
      var main = document.getElementById(pre + 'MainImg');
      main.style.opacity = 0;
      main.style.transform = 'scale(1.04)';
      setTimeout(function () {
        main.src = imgs[idx];
        main.style.opacity = 1;
        main.style.transform = 'scale(1)';
      }, 300);
      document.getElementById(pre + 'CurNum').textContent = idx + 1;
      document.getElementById(pre + 'ProgressBar').style.width = ((idx + 1) / imgs.length * 100) + '%';
      document.querySelectorAll('.' + pre + '-thumb').forEach(function (t, n) {
        t.style.border = n === idx ? '2px solid ' + active : '2px solid transparent';
        t.style.opacity = n === idx ? '1' : '0.55';
        t.style.transform = n === idx ? 'scale(1.08)' : 'scale(1)';
      });
      clearInterval(timer);
      timer = setInterval(function () { goTo(idx + 1); }, 3500);
    }

    window[pre + 'GoTo'] = goTo;
    window[pre + 'Step'] = function (d) { goTo(idx + d); };
    timer = setInterval(function () { goTo(idx + 1); }, 3500);
  });
})();
