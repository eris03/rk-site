<?php
/**
 * Project gallery: hover preview + full-screen slideshow (opened with openLightbox(key)).
 * Gallery data comes from the Projects (window._galleries, see inc/shortcodes.php).
 *
 * @package RK_Developers
 */
?>
  <!-- Hover centre-screen preview -->
  <div id="pg-preview-backdrop"></div>
  <div id="pg-preview">
    <img id="pg-prev-img" src="" alt="" />
    <div class="pg-prev-label" id="pg-prev-label"></div>
    <div id="pg-prev-dots"></div>
  </div>

  <!-- Floating Modal Slideshow -->
  <div id="pg-slideshow" onclick="if(event.target===this)closeLightbox()">
    <div id="pg-modal-card">
      <div class="pg-slides" id="pgSlides">
        <!-- slides injected by JS -->
      </div>

      <!-- Top bar -->
      <div class="pg-topbar">
        <div class="pg-topbar-title">
          <i class="fas fa-images" style="color:#22C55E;"></i>
          <span id="pgSsTitle">Project Gallery</span>
          <span class="pg-counter" id="pgCounter">1 / 5</span>
        </div>
        <button class="pg-close-btn" id="pgCloseBtn" title="Close (Esc)">&#x2715;</button>
      </div>

      <!-- Side arrows -->
      <button class="pg-arrow pg-arrow-l" id="pgPrev">&#8249;</button>
      <button class="pg-arrow pg-arrow-r" id="pgNext">&#8250;</button>

      <!-- Bottom bar -->
      <div class="pg-bottombar">
        <div class="pg-progress-track"><div class="pg-progress-fill" id="pgProgress"></div></div>
        <div class="pg-dots" id="pgDots"></div>
        <div class="pg-controls">
          <button class="pg-btn" id="pgPrevBtn" title="Previous">&#8249;</button>
          <button class="pg-btn pg-play-btn" id="pgPlayBtn" title="Play / Pause">
            <i class="fas fa-pause" id="pgPlayIcon"></i>
          </button>
          <button class="pg-btn" id="pgNextBtn" title="Next">&#8250;</button>
        </div>
      </div>
    </div>
  </div>
