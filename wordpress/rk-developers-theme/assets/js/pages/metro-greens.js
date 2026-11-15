/* RK Developers — metro-greens page scripts (moved from the static metro-greens.html) */
(function() {
  /* ── Crop Data ── */
  const CROPS = [
    {
      name: 'Teakwood', sci: 'Tectona grandis',
      img: RK_THEME.img + 'metro-greens-crops/10.png',
      price: '₹2,000/kg', time: '10–12 years',
      color: '#D4A843', colorDark: '#92400E',
      facts: [
        { icon: 'fas fa-chart-bar', text: '<strong>Market Value:</strong> Mature trees yield 100–150 kg each. One of India\'s most stable high-demand hardwoods.' },
        { icon: 'fas fa-thermometer-half', text: '<strong>Climate Fit:</strong> 22°C–34°C ideal — perfect for Chikkaballapur\'s steady trunk growth and dense fiber formation.' },
        { icon: 'fas fa-leaf', text: '<strong>Benefits:</strong> Low maintenance, termite resistant, long life cycle, strong resale demand.' },
        { icon: 'fas fa-chart-line', text: '<strong>Future:</strong> Limited natural supply and rising timber demand push annual price appreciation steadily upward.', pot: true }
      ]
    },
    {
      name: 'Sandalwood', sci: 'Santalum album',
      img: RK_THEME.img + 'metro-greens-crops/2.png',
      price: '₹1,000/kg', time: '10–12 years',
      color: '#F0C060', colorDark: '#7C5E00',
      facts: [
        { icon: 'fas fa-chart-bar', text: '<strong>Market Value:</strong> Mature trees yield 100–150 kg each — demand and long-term value consistently high.' },
        { icon: 'fas fa-thermometer-half', text: '<strong>Climate Fit:</strong> 22°C–34°C supports steady trunk growth and healthy heartwood density.' },
        { icon: 'fas fa-leaf', text: '<strong>Benefits:</strong> Long life cycle, strong resale, low maintenance, natural pest resistance, domestic & international buyers.' },
        { icon: 'fas fa-chart-line', text: '<strong>Future:</strong> Legal supply limited — prices continue to appreciate year after year, strengthening long-term returns.', pot: true }
      ]
    },
    {
      name: 'Agarwood (Oud)', sci: 'Aquilaria malaccensis',
      img: RK_THEME.img + 'metro-greens-crops/6.png',
      price: '₹10,000/kg', time: '10–12 years',
      color: '#A78BFA', colorDark: '#5B21B6',
      facts: [
        { icon: 'fas fa-chart-bar', text: '<strong>Market Value:</strong> Each tree yields 100–150 kg of resin-rich wood at ₹10,000/kg — exceptional returns.' },
        { icon: 'fas fa-thermometer-half', text: '<strong>Climate Fit:</strong> Red, well-drained soil ideal; Chikkaballapur\'s climate (22°C–34°C) supports resin development.' },
        { icon: 'fas fa-leaf', text: '<strong>Benefits:</strong> Strong demand from perfume, oud oil, and incense industries — stable global buying at premium pricing.' },
        { icon: 'fas fa-chart-line', text: '<strong>Future:</strong> Worldwide demand increasing 8–12% per year from perfume, pharmaceuticals, and spiritual markets.', pot: true }
      ]
    },
    {
      name: 'Rosewood', sci: 'Dalbergia latifolia',
      img: RK_THEME.img + 'metro-greens-crops/5.png',
      price: '₹8,000–12,000/kg', time: '10–12 years',
      color: '#FB7185', colorDark: '#9F1239',
      facts: [
        { icon: 'fas fa-chart-bar', text: '<strong>Market Value:</strong> ₹8,000–12,000/kg based on grade — high-density commercial harvest after full growth.' },
        { icon: 'fas fa-thermometer-half', text: '<strong>Climate Fit:</strong> 22°C–35°C perfectly matched with Chikkaballapur\'s climate for steady trunk growth.' },
        { icon: 'fas fa-leaf', text: '<strong>Benefits:</strong> Long life cycle, low maintenance, high-density wood, strong resale value, consistent upward price trend.' },
        { icon: 'fas fa-chart-line', text: '<strong>Future:</strong> Scarcity and export demand push yearly price growth by 8–12% on average.', pot: true }
      ]
    },
    {
      name: 'Passion Fruit', sci: 'Passiflora edulis',
      img: RK_THEME.img + 'metro-greens-crops/8.png',
      price: '₹180–300/kg', time: '12–16 months',
      color: '#C084FC', colorDark: '#6B21A8',
      facts: [
        { icon: 'fas fa-chart-bar', text: '<strong>Market Value:</strong> Average retail price ₹180–300/kg in Bengaluru depending on season and quality.' },
        { icon: 'fas fa-thermometer-half', text: '<strong>Climate Fit:</strong> 18°C–28°C matches Chikkaballapur\'s climate perfectly for flowering.' },
        { icon: 'fas fa-leaf', text: '<strong>Benefits:</strong> High in Vitamin C, antioxidant-rich, heavily used in juice bars, desserts, and health products.' },
        { icon: 'fas fa-chart-line', text: '<strong>Future:</strong> Rapidly rising demand from beverage brands and export buyers increasing year-on-year prices.', pot: true }
      ]
    },
    {
      name: 'Guava', sci: 'Psidium guajava',
      img: RK_THEME.img + 'metro-greens-crops/3.png',
      price: '₹2–5 L/acre/yr', time: '2–3 years',
      color: '#4ADE80', colorDark: '#15803D',
      facts: [
        { icon: 'fas fa-chart-bar', text: '<strong>Market Value:</strong> ₹2–5 lakh/acre yearly in Chikkaballapur\'s active fruit market — strong local demand.' },
        { icon: 'fas fa-thermometer-half', text: '<strong>Climate Fit:</strong> 23°C–28°C perfectly aligned with Metro Greens\' climate for fast establishment.' },
        { icon: 'fas fa-leaf', text: '<strong>Benefits:</strong> High Vitamin C, long harvesting cycle, strong local demand — one of the most reliable cash crops.' },
        { icon: 'fas fa-chart-line', text: '<strong>Future:</strong> Rising demand from fresh fruit, juice & pulp processing units creating consistent market access.', pot: true }
      ]
    },
    {
      name: 'Grapes', sci: 'Vitis vinifera',
      img: RK_THEME.img + 'metro-greens-crops/1.png',
      price: '₹120–200/kg', time: '2–3 years',
      color: '#818CF8', colorDark: '#3730A3',
      facts: [
        { icon: 'fas fa-chart-bar', text: '<strong>Market Value:</strong> ₹120–200/kg in Bengaluru depending on variety and season — full stable yields from year 4.' },
        { icon: 'fas fa-thermometer-half', text: '<strong>Climate Fit:</strong> 18°C–32°C perfectly matches Chikkaballapur weather for healthy vine growth.' },
        { icon: 'fas fa-leaf', text: '<strong>Benefits:</strong> Rich in antioxidants and natural sugars — strong demand in fresh markets, juice units, and raisin processing.' },
        { icon: 'fas fa-chart-line', text: '<strong>Future:</strong> Urban markets, juice brands, and premium fruit retailers continue to push demand and pricing upward.', pot: true }
      ]
    },
    {
      name: 'Pomegranate', sci: 'Punica granatum',
      img: RK_THEME.img + 'metro-greens-crops/crop-collage.png',
      price: '₹150–250/kg', time: '2.5–3 years',
      color: '#F87171', colorDark: '#991B1B',
      facts: [
        { icon: 'fas fa-chart-bar', text: '<strong>Market Value:</strong> ₹150–250/kg depending on grade and season — consistent seasonal yields from year three.' },
        { icon: 'fas fa-thermometer-half', text: '<strong>Climate Fit:</strong> 20°C–34°C closely matches Chikkaballapur\'s climate for stable, reliable fruiting cycles.' },
        { icon: 'fas fa-leaf', text: '<strong>Benefits:</strong> High antioxidants, long shelf life, strong demand in fresh and processed markets, low-maintenance crop.' },
        { icon: 'fas fa-chart-line', text: '<strong>Future:</strong> Rising demand from juice brands, health stores, and premium retail pushing long-term price growth.', pot: true }
      ]
    }
  ];

  let current = 0, autoTimer = null, progressTimer = null;
  const INTERVAL = 3500; // ms per slide

  const csMain    = document.getElementById('csMain');
  const csGlow    = document.getElementById('csGlow');
  const csImg     = document.getElementById('csImg');
  const csImgPrev = document.getElementById('csImgPrev');
  const csImgNext = document.getElementById('csImgNext');
  const csCur   = document.getElementById('csCur');
  const csSci   = document.getElementById('csSci');
  const csName  = document.getElementById('csName');
  const csBadges= document.getElementById('csBadges');
  const csFacts = document.getElementById('csFacts');
  const csInfo  = document.getElementById('csInfo');
  const csAutoFill = document.getElementById('csAutoFill');
  const thumbsWrap = document.getElementById('csThumbs');

  if (!csMain) return;

  /* Build thumbnails */
  CROPS.forEach((c, i) => {
    const t = document.createElement('button');
    t.className = 'cs-thumb' + (i === 0 ? ' active' : '');
    t.innerHTML = `<span class="cs-thumb-dot"></span>${c.name}`;
    t.addEventListener('click', () => goTo(i));
    thumbsWrap.appendChild(t);
  });

  function startProgress() {
    stopProgress();
    let start = null;
    csAutoFill.style.transition = 'none';
    csAutoFill.style.width = '0%';
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        csAutoFill.style.transition = `width ${INTERVAL}ms linear`;
        csAutoFill.style.width = '100%';
      });
    });
    autoTimer = setTimeout(() => goTo(current + 1), INTERVAL);
  }
  function stopProgress() {
    clearTimeout(autoTimer);
    clearInterval(progressTimer);
    csAutoFill.style.transition = 'none';
    csAutoFill.style.width = '0%';
  }

  function goTo(n) {
    // Detect direction: 1 = forward/next, -1 = backward/prev
    const raw = n - current;
    const half = CROPS.length / 2;
    let dir = raw >= 0 ? 1 : -1;
    if (Math.abs(raw) > half) dir = -dir; // handle wrap-around
    current = (n + CROPS.length) % CROPS.length;
    render(current, dir);
  }

  function render(i, dir = 1) {
    const c = CROPS[i];

    /* Color variables */
    csMain.style.setProperty('--cs-color', c.color);
    csMain.style.setProperty('--cs-color-dark', c.colorDark);
    document.documentElement.style.setProperty('--cs-color', c.color);

    /* Ghost prev/next images — briefly dim during swap */
    const prevCrop = CROPS[(i - 1 + CROPS.length) % CROPS.length];
    const nextCrop = CROPS[(i + 1) % CROPS.length];
    csImgPrev.classList.add('ghost-swap');
    csImgNext.classList.add('ghost-swap');
    setTimeout(() => {
      csImgPrev.src = prevCrop.img;
      csImgNext.src = nextCrop.img;
      csImgPrev.classList.remove('ghost-swap');
      csImgNext.classList.remove('ghost-swap');
    }, 280);

    /* Directional slide swap */
    const exitAnim   = dir > 0 ? 'csSlideOutLeft 0.3s ease forwards'      : 'csSlideOutRight 0.3s ease forwards';
    const enterAnim  = dir > 0 ? 'csSlideInFromRight 0.35s ease forwards'  : 'csSlideInFromLeft 0.35s ease forwards';
    csImg.style.animation = exitAnim;
    csInfo.classList.add('switching');
    setTimeout(() => {
      csImg.src = c.img;
      csImg.alt = c.name;
      csImg.style.animation = enterAnim;
      setTimeout(() => {
        csImg.style.animation = ''; // restore CSS imgFloat
      }, 380);
    }, 300);

    /* Glow color */
    csGlow.style.background = `radial-gradient(circle at 50% 50%, ${c.color} 0%, transparent 65%)`;

    /* Counter */
    csCur.textContent = String(i + 1).padStart(2, '0');

    /* Info */
    setTimeout(() => {
      csSci.textContent  = c.sci;
      csName.innerHTML   = `<span class="cs-name-accent">${c.sci}</span>${c.name}`;
      csSci.textContent  = '';
      csBadges.innerHTML = `
        <div style="display:flex;flex-direction:column;align-items:flex-start;gap:3px;">
          <span class="cs-badge price"><i class="fas fa-rupee-sign"></i> ${c.price} ★</span>
          <span style="font-size:0.68rem;color:#92400E;font-style:italic;padding-left:4px;">*T&amp;C applied</span>
        </div>
        <span class="cs-badge time"><i class="fas fa-clock"></i> ${c.time}</span>`;
      csFacts.innerHTML  = c.facts.map(f => `
        <div class="cs-fact ${f.pot ? 'cs-fact-potential' : ''}">
          <i class="${f.icon}"></i>
          <span>${f.text}</span>
        </div>`).join('');
      csInfo.classList.remove('switching');
    }, 250);

    /* Thumbnails */
    document.querySelectorAll('.cs-thumb').forEach((t, ti) =>
      t.classList.toggle('active', ti === i));

    /* Nav button colors */
    document.querySelectorAll('.cs-nav').forEach(b => {
      b.style.setProperty('--cs-color', c.color);
    });

    startProgress();
    if(window.csStartEffect) window.csStartEffect(i);
  }

  document.getElementById('csPrev').addEventListener('click', () => { stopProgress(); goTo(current - 1); });
  document.getElementById('csNext').addEventListener('click', () => { stopProgress(); goTo(current + 1); });

  /* Swipe support */
  let touchStart = null;
  csMain.addEventListener('touchstart', e => { touchStart = e.touches[0].clientX; }, { passive: true });
  csMain.addEventListener('touchend',   e => {
    if (touchStart === null) return;
    const dx = e.changedTouches[0].clientX - touchStart;
    if (Math.abs(dx) > 40) { stopProgress(); goTo(current + (dx < 0 ? 1 : -1)); }
    touchStart = null;
  });

  /* ── Pause / Play toggle button ── */
  let isPaused = false;
  const pauseBtn  = document.getElementById('csPauseBtn');
  const pauseIcon = document.getElementById('csPauseBtnIcon');
  if(pauseBtn){
    pauseBtn.addEventListener('click', function(){
      isPaused = !isPaused;
      if(isPaused){
        stopProgress();
        pauseIcon.className = 'fas fa-play';
        pauseBtn.title = 'Play slideshow';
      } else {
        startProgress();
        pauseIcon.className = 'fas fa-pause';
        pauseBtn.title = 'Pause slideshow';
      }
    });
  }


  /* Initial render */
  render(0);
})();


function openAroundLightbox(src, caption) {
  var lb = document.getElementById('around-lightbox');
  var img = document.getElementById('around-lb-img');
  var cap = document.getElementById('around-lb-caption');
  img.src = src;
  cap.innerHTML = caption;
  lb.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}
function closeAroundLightbox() {
  document.getElementById('around-lightbox').style.display = 'none';
  document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeAroundLightbox();
});


// Parallax hero
window.addEventListener('scroll', () => {
  const bg = document.getElementById('mgHeroBg');
  if (bg) bg.style.transform = `scale(1.05) translateY(${window.scrollY * 0.3}px)`;
});
// Plot filter
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filter = btn.dataset.filter;
    document.querySelectorAll('.plot-card').forEach(card => {
      card.style.display = (filter === 'all' || card.dataset.size === filter) ? '' : 'none';
    });
  });
});

