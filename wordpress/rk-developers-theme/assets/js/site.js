/**
 * RK Developers — small shared effects that were inline <script> blocks in the static pages:
 * scroll progress bar, cursor glow, magnetic buttons (home) and the timed pop-up.
 */
(function () {
  'use strict';
  var cfg = window.RK_THEME || {};

  /* ── Scroll Progress ── */
  (function () {
    var bar = document.getElementById('scroll-progress');
    if (!bar) return;
    window.addEventListener('scroll', function () {
      var st = window.scrollY, dh = document.documentElement.scrollHeight - window.innerHeight;
      bar.style.width = (dh > 0 ? (st / dh) * 100 : 0) + '%';
    }, { passive: true });
  })();

  /* ── Cursor Glow ── */
  (function () {
    var g = document.getElementById('cursorGlow');
    if (!g) return;
    var mx = window.innerWidth / 2, my = window.innerHeight / 2, cx = mx, cy = my;
    document.addEventListener('mousemove', function (e) { mx = e.clientX; my = e.clientY; });
    (function animate() {
      cx += (mx - cx) * 0.1; cy += (my - cy) * 0.1;
      g.style.left = cx + 'px'; g.style.top = cy + 'px';
      requestAnimationFrame(animate);
    })();
  })();

  /* ── Magnetic Buttons (home page, as on the static site) ── */
  if (cfg.layout === 'home') {
    document.querySelectorAll('.btn-hero-primary, .btn-hero-secondary, .btn-gold, .nav-cta-btn').forEach(function (btn) {
      btn.addEventListener('mousemove', function (e) {
        var r = btn.getBoundingClientRect();
        var x = e.clientX - r.left - r.width / 2;
        var y = e.clientY - r.top - r.height / 2;
        btn.style.transform = 'translate(' + (x * 0.18) + 'px, ' + (y * 0.18) + 'px) scale(1.04)';
      });
      btn.addEventListener('mouseleave', function () { btn.style.transform = ''; });
    });
  }

  /* ── "Interested in a Property?" pop-up ── */
  (function () {
    var overlay = document.getElementById('rk-popup-overlay');
    if (!overlay) return;
    var delay = cfg.popupDelay || 60000;
    function show() { overlay.style.display = 'flex'; }
    setTimeout(show, delay);
    setInterval(show, delay);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) overlay.style.display = 'none'; });
  })();
})();
