/* RK Developers — rk-avenue page scripts (moved from the static rk-avenue.html) */
/* ── Hero slideshow (3.5s per slide, 1.2s crossfade) ── */
(function(){
  var slides = document.querySelectorAll('.av-hero-slide');
  if(!slides.length) return;
  var idx = 0;
  setInterval(function(){
    slides[idx].classList.remove('active');
    idx = (idx + 1) % slides.length;
    slides[idx].classList.add('active');
  }, 3500);
})();

