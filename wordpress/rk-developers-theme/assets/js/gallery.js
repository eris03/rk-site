/* RK Developers — project gallery lightbox (moved from the static index.html / projects.html).
   Gallery data: window._galleries = { key: { title, images: [] } }, printed by the theme from the Projects. */
window._galleries = window._galleries || {};
/* ─── Gallery Data ─── */

/* ─── Mini-preview hover ─── */
let _pvTimer = null, _pvIdx = 0, _pvKey = null;

function showGallery(e, key) {
  if (_pvKey === key) return; // already showing
  _pvKey = key; _pvIdx = 0;
  const g = _galleries[key];
  const box = document.getElementById('pg-preview');
  const img = document.getElementById('pg-prev-img');
  const lbl = document.getElementById('pg-prev-label');
  const dotsEl = document.getElementById('pg-prev-dots');

  // Build dots
  dotsEl.innerHTML = '';
  g.images.forEach(function(_, i) {
    const d = document.createElement('div');
    d.className = 'pg-prev-dot' + (i === 0 ? ' on' : '');
    dotsEl.appendChild(d);
  });

  img.style.opacity = 0;
  img.src = g.images[0];
  img.onload = function() { img.style.opacity = 1; };
  lbl.innerHTML = '<strong>' + g.title + '</strong> &nbsp;&nbsp;·&nbsp;&nbsp; ' + g.images.length + ' photos &nbsp;&nbsp;·&nbsp;&nbsp; <span style="color:rgba(255,255,255,0.5)">click to open full gallery</span>';

  box.classList.add('show');
  document.getElementById('pg-preview-backdrop').classList.add('show');
  _pvTick();
}

function _pvUpdateDots() {
  const dots = document.querySelectorAll('.pg-prev-dot');
  dots.forEach(function(d, i) { d.classList.toggle('on', i === _pvIdx); });
}

function _pvTick() {
  clearTimeout(_pvTimer);
  if (!_pvKey) return;
  _pvTimer = setTimeout(function() {
    const g = _galleries[_pvKey]; if (!g) return;
    _pvIdx = (_pvIdx + 1) % g.images.length;
    const img = document.getElementById('pg-prev-img');
    img.style.opacity = 0;
    setTimeout(function(){
      img.src = g.images[_pvIdx];
      img.style.opacity = 1;
      _pvUpdateDots();
    }, 250);
    _pvTick();
  }, 1800);
}

function hideGallery() {
  _pvKey = null; clearTimeout(_pvTimer);
  document.getElementById('pg-preview').classList.remove('show');
  document.getElementById('pg-preview-backdrop').classList.remove('show');
}

/* ─── Full-screen Slideshow ─── */
let _ss = { key: null, idx: 0, total: 0, playing: true, timer: null, progressAnim: null };
const SLIDE_DUR = 4000; // ms per slide

function openLightbox(key) {
  hideGallery();
  const g = _galleries[key];
  _ss.key = key; _ss.idx = 0; _ss.total = g.images.length; _ss.playing = true;

  // Build slides
  const wrap = document.getElementById('pgSlides');
  wrap.innerHTML = '';
  g.images.forEach(function(src, i) {
    const div = document.createElement('div');
    div.className = 'pg-slide' + (i === 0 ? ' pg-active' : ' pg-enter-right');
    div.style.setProperty('--bg', 'url(' + src + ')');
    div.style.cssText += ';--bg:url(' + src + ')';
    div.setAttribute('data-bg', src);
    const img = document.createElement('img');
    img.src = src; img.alt = g.title + ' ' + (i+1);
    div.appendChild(img);
    // set blurred bg via pseudo via inline bg-image trick
    div.style.background = 'none';
    img.onload = function() {
      div.style.setProperty('background', 'none');
    };
    wrap.appendChild(div);
  });
  // Apply blurred bg to ::before via JS workaround (data attr + CSS)
  _applyBgBlur();

  // Build dots
  const dotsEl = document.getElementById('pgDots');
  dotsEl.innerHTML = '';
  for (let i = 0; i < g.images.length; i++) {
    const d = document.createElement('button');
    d.className = 'pg-dot' + (i === 0 ? ' active' : '');
    d.setAttribute('aria-label', 'Go to slide ' + (i+1));
    (function(idx){ d.onclick = function(){ _ssGoTo(idx, 'right'); }; })(i);
    dotsEl.appendChild(d);
  }

  document.getElementById('pgSsTitle').textContent = g.title;
  _updateCounter();
  _updatePlayBtn();
  document.getElementById('pg-slideshow').classList.add('open');
  document.body.style.overflow = 'hidden';
  _ssStartProgress();
}

function closeLightbox() {
  _ssClearTimers();
  document.getElementById('pg-slideshow').classList.remove('open');
  document.body.style.overflow = '';
  _ss.key = null; _ss.playing = false;
}

function _applyBgBlur() {
  const slides = document.querySelectorAll('.pg-slide');
  slides.forEach(function(s) {
    const src = s.querySelector('img').src;
    s.style.backgroundImage = 'url(' + src + ')';
    s.style.backgroundSize = 'cover';
    s.style.backgroundPosition = 'center';
  });
  // The ::before blur is handled via CSS filter: blur on the element background
  // We'll use a real overlay div instead
  document.querySelectorAll('.pg-slide').forEach(function(s) {
    if (!s.querySelector('.pg-blur-bg')) {
      const blur = document.createElement('div');
      blur.className = 'pg-blur-bg';
      blur.style.cssText = 'position:absolute;inset:0;background:inherit;filter:blur(28px) brightness(0.35);transform:scale(1.1);z-index:0;opacity:0;transition:opacity 0.55s ease;';
      s.insertBefore(blur, s.firstChild);
      s.querySelector('img').style.position = 'relative';
      s.querySelector('img').style.zIndex = '1';
    }
  });
  // Show blur only on active
  _refreshBlur();
}

function _refreshBlur() {
  document.querySelectorAll('.pg-slide').forEach(function(s) {
    const b = s.querySelector('.pg-blur-bg');
    if (b) b.style.opacity = s.classList.contains('pg-active') ? '1' : '0';
  });
}

function _ssGoTo(newIdx, dir) {
  _ssClearTimers();
  if (!_ss.key) return;
  const slides = document.querySelectorAll('.pg-slide');
  const dots   = document.querySelectorAll('.pg-dot');
  const old = _ss.idx;
  _ss.idx = (newIdx + _ss.total) % _ss.total;
  if (old === _ss.idx) { if (_ss.playing) _ssStartProgress(); return; }

  // Animate out old
  const leaveClass = (dir === 'right') ? 'pg-leave-left' : 'pg-leave-right';
  const enterClass = (dir === 'right') ? 'pg-enter-right' : 'pg-enter-left';
  slides[old].classList.remove('pg-active');
  slides[old].classList.add(leaveClass);
  slides[_ss.idx].classList.remove(enterClass, 'pg-enter-left', 'pg-enter-right', 'pg-leave-left', 'pg-leave-right');
  // Force reflow
  void slides[_ss.idx].offsetWidth;
  slides[_ss.idx].classList.add(enterClass);
  void slides[_ss.idx].offsetWidth;
  slides[_ss.idx].classList.remove(enterClass);
  slides[_ss.idx].classList.add('pg-active');

  // Clean up leave after transition
  setTimeout(function() {
    slides[old].classList.remove(leaveClass);
    slides[old].classList.add(dir === 'right' ? 'pg-enter-right' : 'pg-enter-left');
  }, 600);

  // Dots
  dots.forEach(function(d,i){ d.classList.toggle('active', i === _ss.idx); });
  _refreshBlur();
  _updateCounter();
  if (_ss.playing) _ssStartProgress();
}

function lbStep(dir) { _ssGoTo(_ss.idx + dir, dir > 0 ? 'right' : 'left'); }

function _togglePlay() {
  _ss.playing = !_ss.playing;
  _updatePlayBtn();
  if (_ss.playing) { _ssStartProgress(); }
  else { _ssClearTimers(); }
}

function _updatePlayBtn() {
  const icon = document.getElementById('pgPlayIcon');
  if (!icon) return;
  icon.className = _ss.playing ? 'fas fa-pause' : 'fas fa-play';
}

function _updateCounter() {
  document.getElementById('pgCounter').textContent = (_ss.idx + 1) + ' / ' + _ss.total;
}

function _ssClearTimers() {
  clearTimeout(_ss.timer);
  cancelAnimationFrame(_ss.progressAnim);
  const pf = document.getElementById('pgProgress');
  if (pf) { pf.style.transition = 'none'; pf.style.width = '0%'; }
}

function _ssStartProgress() {
  _ssClearTimers();
  if (!_ss.playing) return;
  const pf = document.getElementById('pgProgress');
  if (pf) {
    pf.style.transition = 'none'; pf.style.width = '0%';
    void pf.offsetWidth;
    pf.style.transition = 'width ' + SLIDE_DUR + 'ms linear';
    pf.style.width = '100%';
  }
  _ss.timer = setTimeout(function() {
    lbStep(1);
  }, SLIDE_DUR);
}

/* ─── Wire up buttons ─── */
document.getElementById('pgCloseBtn').onclick = closeLightbox;
document.getElementById('pgPrev').onclick     = function(){ lbStep(-1); };
document.getElementById('pgNext').onclick     = function(){ lbStep(1);  };
document.getElementById('pgPrevBtn').onclick  = function(){ lbStep(-1); };
document.getElementById('pgNextBtn').onclick  = function(){ lbStep(1);  };
document.getElementById('pgPlayBtn').onclick  = _togglePlay;

// Keyboard
document.addEventListener('keydown', function(e) {
  if (!_ss.key) return;
  if (e.key === 'Escape')      closeLightbox();
  if (e.key === 'ArrowLeft')   lbStep(-1);
  if (e.key === 'ArrowRight')  lbStep(1);
  if (e.key === ' ') { e.preventDefault(); _togglePlay(); }
});

// Swipe support
(function() {
  let tx = 0;
  const el = document.getElementById('pg-modal-card');
  el.addEventListener('touchstart', function(e){ tx = e.touches[0].clientX; }, {passive:true});
  el.addEventListener('touchend',   function(e){
    const dx = e.changedTouches[0].clientX - tx;
    if (Math.abs(dx) > 50) { if (dx < 0) lbStep(1); else lbStep(-1); }
  }, {passive:true});
})();
