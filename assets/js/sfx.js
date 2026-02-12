/* =============================================
   SPARK'26 — Global Sound Effects & Click Ripple
   Works on every page (dashboards, auth, landing)
   Volume: MAX LOUDNESS · Master Gain + Compressor
   Touch · Virtual Keyboard · Scroll · Nav · Loading · Table
   ============================================= */

(function () {
  'use strict';

  var audioCtx = null;
  var masterGain = null;
  var masterCompressor = null;

  // ---- MOBILE DETECTION ----
  var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    || ('ontouchstart' in window)
    || (navigator.maxTouchPoints > 0);

  // Master volume multiplier — maximized
  var VOL = isMobile ? 12.0 : 10.0;

  // ---- Master output chain: GainNode → DynamicsCompressor → destination ----
  // The compressor acts as a loudness maximizer — prevents clipping while
  // allowing gain values well above 1.0 to produce maximum perceived volume.
  function _initMasterChain(ctx) {
    if (masterGain) return;
    try {
      masterCompressor = ctx.createDynamicsCompressor();
      masterCompressor.threshold.setValueAtTime(-24, ctx.currentTime); // start compressing at -24dB
      masterCompressor.knee.setValueAtTime(12, ctx.currentTime);       // soft knee
      masterCompressor.ratio.setValueAtTime(12, ctx.currentTime);      // heavy compression
      masterCompressor.attack.setValueAtTime(0.003, ctx.currentTime);  // fast attack
      masterCompressor.release.setValueAtTime(0.15, ctx.currentTime);  // quick release

      masterGain = ctx.createGain();
      // Push gain high — compressor will tame peaks, keeping loudness maximal
      masterGain.gain.setValueAtTime(isMobile ? 4.0 : 3.0, ctx.currentTime);

      masterGain.connect(masterCompressor);
      masterCompressor.connect(ctx.destination);
    } catch (e) {
      masterGain = null;
      masterCompressor = null;
    }
  }

  // Get the master output node — all sounds connect to this instead of getMasterOut()
  function getMasterOut() {
    var ctx = getAudioCtx();
    if (!ctx) return null;
    if (!masterGain) _initMasterChain(ctx);
    return masterGain || getMasterOut();
  }

  // ---- AudioContext with mobile unlock ----
  var _ctxUnlocked = false;

  function getAudioCtx() {
    if (!audioCtx) {
      try {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
      } catch (e) {
        return null;
      }
    }
    // iOS / Android keeps AudioContext suspended until a user gesture
    if (audioCtx && audioCtx.state === 'suspended') {
      audioCtx.resume().catch(function () {});
    }
    return audioCtx;
  }

  // Unlock AudioContext on first touch/click (required by mobile browsers)
  function unlockAudioCtx() {
    if (_ctxUnlocked) return;
    var ctx = getAudioCtx();
    if (!ctx) return;
    if (ctx.state === 'suspended') {
      ctx.resume().then(function () { _ctxUnlocked = true; }).catch(function () {});
    } else {
      _ctxUnlocked = true;
    }
    // Initialize master chain on first interaction
    _initMasterChain(ctx);
    // Create & immediately stop a silent buffer to fully unlock on iOS
    try {
      var b = ctx.createBuffer(1, 1, 22050);
      var s = ctx.createBufferSource();
      s.buffer = b;
      s.connect(getMasterOut());
      s.start(0);
      s.stop(0.001);
    } catch (e) {}
  }

  // Attach unlock to the first user interaction
  ['touchstart', 'touchend', 'click', 'keydown'].forEach(function (evt) {
    document.addEventListener(evt, unlockAudioCtx, { once: true, passive: true });
  });

  // Helper: scale gain — no longer capped at 1.0, compressor handles peaks
  function v(base) {
    return Math.min(base * VOL, 1.0);
  }

  /* ==========================================
     SOUND LIBRARY — 500% volume via v()
  ========================================== */

  // 1. Soft pop — general page clicks
  function playSoftPop() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(600, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(200, ctx.currentTime + 0.08);
      g.gain.setValueAtTime(v(0.06), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.1);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.1);
    } catch (e) {}
  }

  // 2. Button click — brighter tone
  function playButtonClick() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(800, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(400, ctx.currentTime + 0.1);
      g.gain.setValueAtTime(v(0.10), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.12);
    } catch (e) {}
  }

  // 3. Hover tick — nav links, cards, sidebar
  function playHoverTick() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(1000, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.04);
      g.gain.setValueAtTime(v(0.03), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.05);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.05);
    } catch (e) {}
  }

  // 4. Table row hover — gentle sweep
  function playTableRowHover() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'triangle';
      o.frequency.setValueAtTime(500, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(700, ctx.currentTime + 0.06);
      g.gain.setValueAtTime(v(0.04), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.08);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.08);
    } catch (e) {}
  }

  // 5. Scroll whoosh — wind burst
  function playScrollWhoosh(direction) {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var bufferSize = ctx.sampleRate * 0.1;
      var buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
      var data = buffer.getChannelData(0);
      for (var i = 0; i < bufferSize; i++) data[i] = (Math.random() * 2 - 1) * 0.4;
      var noise = ctx.createBufferSource();
      noise.buffer = buffer;
      var filter = ctx.createBiquadFilter();
      filter.type = 'bandpass';
      filter.frequency.setValueAtTime(direction === 'down' ? 600 : 900, ctx.currentTime);
      filter.frequency.exponentialRampToValueAtTime(direction === 'down' ? 250 : 1400, ctx.currentTime + 0.1);
      filter.Q.value = 2.5;
      var g = ctx.createGain();
      g.gain.setValueAtTime(v(0.08), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
      noise.connect(filter); filter.connect(g); g.connect(getMasterOut());
      noise.start(ctx.currentTime); noise.stop(ctx.currentTime + 0.12);
    } catch (e) {}
  }

  // 6. Form input focus — warm chime
  function playInputFocus() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(880, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(660, ctx.currentTime + 0.12);
      g.gain.setValueAtTime(v(0.06), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.15);
    } catch (e) {}
  }

  // 7. Toggle/switch — chirp up or down
  function playToggle(on) {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(on ? 400 : 900, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(on ? 900 : 400, ctx.currentTime + 0.1);
      g.gain.setValueAtTime(v(0.07), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.12);
    } catch (e) {}
  }

  // 8. Success ding — two ascending notes (C5 → E5)
  function playSuccessDing() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o1 = ctx.createOscillator(), g1 = ctx.createGain();
      o1.connect(g1); g1.connect(getMasterOut());
      o1.type = 'sine';
      o1.frequency.setValueAtTime(523, ctx.currentTime);
      g1.gain.setValueAtTime(v(0.08), ctx.currentTime);
      g1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
      o1.start(ctx.currentTime); o1.stop(ctx.currentTime + 0.15);
      var o2 = ctx.createOscillator(), g2 = ctx.createGain();
      o2.connect(g2); g2.connect(getMasterOut());
      o2.type = 'sine';
      o2.frequency.setValueAtTime(659, ctx.currentTime + 0.12);
      g2.gain.setValueAtTime(0.001, ctx.currentTime);
      g2.gain.setValueAtTime(v(0.08), ctx.currentTime + 0.12);
      g2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
      o2.start(ctx.currentTime + 0.12); o2.stop(ctx.currentTime + 0.3);
    } catch (e) {}
  }

  // 9. Error buzz — low rumble
  function playErrorBuzz() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sawtooth';
      o.frequency.setValueAtTime(150, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(80, ctx.currentTime + 0.2);
      g.gain.setValueAtTime(v(0.06), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.25);
    } catch (e) {}
  }

  // 10. Notification ping — bright bell
  function playNotificationPing() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(1320, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
      g.gain.setValueAtTime(v(0.07), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.2);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.2);
    } catch (e) {}
  }

  // 11. Sidebar slide — expand/collapse whoosh
  function playSidebarSlide(opening) {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'triangle';
      o.frequency.setValueAtTime(opening ? 200 : 500, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(opening ? 500 : 200, ctx.currentTime + 0.15);
      g.gain.setValueAtTime(v(0.06), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.18);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.18);
    } catch (e) {}
  }

  // 12. Keystroke tick — typing
  function playKeystroke() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'square';
      o.frequency.setValueAtTime(800 + Math.random() * 400, ctx.currentTime);
      g.gain.setValueAtTime(v(0.015), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.03);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.03);
    } catch (e) {}
  }

  // 12b. Erase tick — descending tone for delete
  function playEraseTick() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'triangle';
      o.frequency.setValueAtTime(600 + Math.random() * 200, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(250, ctx.currentTime + 0.05);
      g.gain.setValueAtTime(v(0.02), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.06);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.06);
    } catch (e) {}
  }

  // 12c. Clear sweep — long descend for clearing an entire field
  function playClearSweep() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(700, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(150, ctx.currentTime + 0.15);
      g.gain.setValueAtTime(v(0.05), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.18);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.18);
    } catch (e) {}
  }

  // 13. Modal/Popup open — ascending chime
  function playModalOpen() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(350, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(700, ctx.currentTime + 0.15);
      g.gain.setValueAtTime(v(0.06), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.2);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.2);
    } catch (e) {}
  }

  // 14. Dropdown blip
  function playDropdown(opening) {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(opening ? 500 : 700, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(opening ? 700 : 500, ctx.currentTime + 0.06);
      g.gain.setValueAtTime(v(0.05), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.08);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.08);
    } catch (e) {}
  }

  // 15. PAGE LOAD — warm welcome chime (3 ascending notes: C5 → E5 → G5)
  function playPageLoadChime() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var notes = [523, 659, 784]; // C5, E5, G5
      notes.forEach(function (freq, i) {
        var o = ctx.createOscillator(), g = ctx.createGain();
        o.connect(g); g.connect(getMasterOut());
        o.type = 'sine';
        var t = ctx.currentTime + i * 0.1;
        o.frequency.setValueAtTime(freq, t);
        g.gain.setValueAtTime(0.001, ctx.currentTime);
        g.gain.setValueAtTime(v(0.07), t);
        g.gain.exponentialRampToValueAtTime(0.001, t + 0.2);
        o.start(t); o.stop(t + 0.2);
      });
    } catch (e) {}
  }

  // 16. NAVIGATION — departing swoosh (plays when leaving page)
  function playNavigationSwoosh() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      // Descending tone
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(800, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(200, ctx.currentTime + 0.15);
      g.gain.setValueAtTime(v(0.08), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.18);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.18);
      // Noise tail
      var bufSize = ctx.sampleRate * 0.12;
      var buf = ctx.createBuffer(1, bufSize, ctx.sampleRate);
      var d = buf.getChannelData(0);
      for (var i = 0; i < bufSize; i++) d[i] = (Math.random() * 2 - 1) * 0.25;
      var n = ctx.createBufferSource(); n.buffer = buf;
      var f = ctx.createBiquadFilter();
      f.type = 'highpass'; f.frequency.value = 500;
      var gn = ctx.createGain();
      gn.gain.setValueAtTime(v(0.04), ctx.currentTime);
      gn.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
      n.connect(f); f.connect(gn); gn.connect(getMasterOut());
      n.start(ctx.currentTime); n.stop(ctx.currentTime + 0.15);
    } catch (e) {}
  }

  // 17. SCROLL EDGE — thud when reaching top or bottom of page
  function playScrollEdge() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(120, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(60, ctx.currentTime + 0.1);
      g.gain.setValueAtTime(v(0.08), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.15);
    } catch (e) {}
  }

  // 18. SECTION ENTER — subtle ding when scrolling into a new section
  function playSectionEnter() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(660, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.08);
      g.gain.setValueAtTime(v(0.04), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.12);
    } catch (e) {}
  }

  // 19. FORM SUBMIT — confirmation whoosh
  function playFormSubmit() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(400, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(1000, ctx.currentTime + 0.15);
      g.gain.setValueAtTime(v(0.08), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.2);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.2);
      // Second harmonic
      var o2 = ctx.createOscillator(), g2 = ctx.createGain();
      o2.connect(g2); g2.connect(getMasterOut());
      o2.type = 'sine';
      o2.frequency.setValueAtTime(600, ctx.currentTime + 0.05);
      g2.gain.setValueAtTime(0.001, ctx.currentTime);
      g2.gain.setValueAtTime(v(0.05), ctx.currentTime + 0.05);
      g2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.22);
      o2.start(ctx.currentTime + 0.05); o2.stop(ctx.currentTime + 0.22);
    } catch (e) {}
  }

  // 20. WARNING tone — for caution/attention
  function playWarningTone() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      // Two quick pulses
      for (var p = 0; p < 2; p++) {
        var o = ctx.createOscillator(), g = ctx.createGain();
        o.connect(g); g.connect(getMasterOut());
        o.type = 'triangle';
        var t = ctx.currentTime + p * 0.12;
        o.frequency.setValueAtTime(440, t);
        g.gain.setValueAtTime(0.001, ctx.currentTime);
        g.gain.setValueAtTime(v(0.07), t);
        g.gain.exponentialRampToValueAtTime(0.001, t + 0.08);
        o.start(t); o.stop(t + 0.08);
      }
    } catch (e) {}
  }

  // 21. COPY — clipboard copy sound
  function playCopySound() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(1200, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(800, ctx.currentTime + 0.06);
      g.gain.setValueAtTime(v(0.05), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.08);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.08);
      // Second blip
      var o2 = ctx.createOscillator(), g2 = ctx.createGain();
      o2.connect(g2); g2.connect(getMasterOut());
      o2.type = 'sine';
      o2.frequency.setValueAtTime(1400, ctx.currentTime + 0.06);
      g2.gain.setValueAtTime(0.001, ctx.currentTime);
      g2.gain.setValueAtTime(v(0.04), ctx.currentTime + 0.06);
      g2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
      o2.start(ctx.currentTime + 0.06); o2.stop(ctx.currentTime + 0.12);
    } catch (e) {}
  }

  // 22. PASTE — clipboard paste sound
  function playPasteSound() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(800, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.06);
      g.gain.setValueAtTime(v(0.05), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.1);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.1);
    } catch (e) {}
  }

  // 23. ESCAPE — dismissal tone
  function playEscapeSound() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(600, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(300, ctx.currentTime + 0.1);
      g.gain.setValueAtTime(v(0.06), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.12);
    } catch (e) {}
  }

  // 24. ENTER — confirmation press
  function playEnterKey() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(500, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(750, ctx.currentTime + 0.06);
      g.gain.setValueAtTime(v(0.06), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.1);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.1);
    } catch (e) {}
  }

  // 25. PAGE VISIBILITY — return to tab ping
  function playReturnToTab() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(440, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(660, ctx.currentTime + 0.1);
      g.gain.setValueAtTime(v(0.05), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.15);
    } catch (e) {}
  }

  // 26. LEAVE TAB — descending tone
  function playLeaveTab() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(660, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(440, ctx.currentTime + 0.1);
      g.gain.setValueAtTime(v(0.04), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.12);
    } catch (e) {}
  }

  // 27. TABLE CELL micro-tick
  function playCellTick() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(1100 + Math.random() * 200, ctx.currentTime);
      g.gain.setValueAtTime(v(0.012), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.025);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.025);
    } catch (e) {}
  }

  // 28. FILE UPLOAD — ascending sparkle
  function playFileUpload() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var notes = [600, 800, 1100];
      notes.forEach(function (freq, i) {
        var o = ctx.createOscillator(), g = ctx.createGain();
        o.connect(g); g.connect(getMasterOut());
        o.type = 'sine';
        var t = ctx.currentTime + i * 0.07;
        o.frequency.setValueAtTime(freq, t);
        g.gain.setValueAtTime(0.001, ctx.currentTime);
        g.gain.setValueAtTime(v(0.04), t);
        g.gain.exponentialRampToValueAtTime(0.001, t + 0.1);
        o.start(t); o.stop(t + 0.1);
      });
    } catch (e) {}
  }

  // 29. LOADING TICK — rhythmic loading indicator
  var loadingInterval = null;
  function startLoadingSound() {
    if (loadingInterval) return;
    var tick = 0;
    loadingInterval = setInterval(function () {
      var ctx = getAudioCtx(); if (!ctx) return;
      try {
        var o = ctx.createOscillator(), g = ctx.createGain();
        o.connect(g); g.connect(getMasterOut());
        o.type = 'sine';
        var freq = tick % 2 === 0 ? 500 : 600;
        o.frequency.setValueAtTime(freq, ctx.currentTime);
        g.gain.setValueAtTime(v(0.025), ctx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.05);
        o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.05);
        tick++;
      } catch (e) {}
    }, 200);
  }
  function stopLoadingSound() {
    if (loadingInterval) { clearInterval(loadingInterval); loadingInterval = null; }
  }

  /* ==========================================
     MOBILE-ONLY SOUNDS (30-39)
  ========================================== */

  // 30. PINCH ZOOM — spread chord
  function playPinchZoom(zoomIn) {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(zoomIn ? 400 : 800, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(zoomIn ? 800 : 400, ctx.currentTime + 0.12);
      g.gain.setValueAtTime(v(0.06), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.15);
      // Harmonic layer
      var o2 = ctx.createOscillator(), g2 = ctx.createGain();
      o2.connect(g2); g2.connect(getMasterOut());
      o2.type = 'triangle';
      o2.frequency.setValueAtTime(zoomIn ? 600 : 1000, ctx.currentTime);
      o2.frequency.exponentialRampToValueAtTime(zoomIn ? 1000 : 600, ctx.currentTime + 0.12);
      g2.gain.setValueAtTime(v(0.03), ctx.currentTime);
      g2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.14);
      o2.start(ctx.currentTime); o2.stop(ctx.currentTime + 0.14);
    } catch (e) {}
  }

  // 31. SHAKE — rapid rattle
  function playShakeRattle() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      for (var s = 0; s < 4; s++) {
        var o = ctx.createOscillator(), g = ctx.createGain();
        o.connect(g); g.connect(getMasterOut());
        o.type = 'sawtooth';
        var t = ctx.currentTime + s * 0.04;
        o.frequency.setValueAtTime(200 + Math.random() * 300, t);
        g.gain.setValueAtTime(0.001, ctx.currentTime);
        g.gain.setValueAtTime(v(0.04), t);
        g.gain.exponentialRampToValueAtTime(0.001, t + 0.035);
        o.start(t); o.stop(t + 0.035);
      }
    } catch (e) {}
  }

  // 32. SCROLL SNAP — magnetic click
  function playScrollSnap() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(900, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(600, ctx.currentTime + 0.04);
      g.gain.setValueAtTime(v(0.07), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.06);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.06);
    } catch (e) {}
  }

  // 33. RAPID TAP — quick triple tick for fast tapping
  function playRapidTap() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      for (var r = 0; r < 3; r++) {
        var o = ctx.createOscillator(), g = ctx.createGain();
        o.connect(g); g.connect(getMasterOut());
        o.type = 'sine';
        var t = ctx.currentTime + r * 0.035;
        o.frequency.setValueAtTime(700 + r * 150, t);
        g.gain.setValueAtTime(0.001, ctx.currentTime);
        g.gain.setValueAtTime(v(0.05), t);
        g.gain.exponentialRampToValueAtTime(0.001, t + 0.03);
        o.start(t); o.stop(t + 0.03);
      }
    } catch (e) {}
  }

  // 34. TOUCH HOLD RELEASE — bubble pop
  function playTouchRelease() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(300, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(900, ctx.currentTime + 0.06);
      g.gain.setValueAtTime(v(0.08), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.1);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.1);
    } catch (e) {}
  }

  // 35. BOTTOM NAV TAP — deep warm tap
  function playBottomNavTap() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(350, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(550, ctx.currentTime + 0.08);
      g.gain.setValueAtTime(v(0.09), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.12);
      // Sub-bass thump
      var o2 = ctx.createOscillator(), g2 = ctx.createGain();
      o2.connect(g2); g2.connect(getMasterOut());
      o2.type = 'sine';
      o2.frequency.setValueAtTime(80, ctx.currentTime);
      g2.gain.setValueAtTime(v(0.06), ctx.currentTime);
      g2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.08);
      o2.start(ctx.currentTime); o2.stop(ctx.currentTime + 0.08);
    } catch (e) {}
  }

  // 36. CARD SWIPE — left/right card dismiss/accept
  function playCardSwipe(direction) {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      var startFreq = direction === 'left' ? 700 : 500;
      var endFreq = direction === 'left' ? 300 : 900;
      o.frequency.setValueAtTime(startFreq, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(endFreq, ctx.currentTime + 0.15);
      g.gain.setValueAtTime(v(0.07), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.18);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.18);
      // Noise swoosh layer
      var bufSize = ctx.sampleRate * 0.08;
      var buf = ctx.createBuffer(1, bufSize, ctx.sampleRate);
      var d = buf.getChannelData(0);
      for (var i = 0; i < bufSize; i++) d[i] = (Math.random() * 2 - 1) * 0.3;
      var n = ctx.createBufferSource(); n.buffer = buf;
      var fl = ctx.createBiquadFilter();
      fl.type = 'bandpass'; fl.frequency.value = 800; fl.Q.value = 1.5;
      var gn = ctx.createGain();
      gn.gain.setValueAtTime(v(0.04), ctx.currentTime);
      gn.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.1);
      n.connect(fl); fl.connect(gn); gn.connect(getMasterOut());
      n.start(ctx.currentTime); n.stop(ctx.currentTime + 0.1);
    } catch (e) {}
  }

  // 37. AUTOCOMPLETE SELECT — soft chime confirming selection
  function playAutocompleteSelect() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(800, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(1100, ctx.currentTime + 0.06);
      g.gain.setValueAtTime(v(0.05), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.1);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.1);
    } catch (e) {}
  }

  // 38. HAPTIC TICK — ultra-short percussive tap for feedback
  function playHapticTick() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'square';
      o.frequency.setValueAtTime(150, ctx.currentTime);
      g.gain.setValueAtTime(v(0.10), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.015);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.015);
    } catch (e) {}
  }

  // 39. PULL DOWN — elastic stretch sound
  function playPullStretch() {
    var ctx = getAudioCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(getMasterOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(600, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(200, ctx.currentTime + 0.2);
      g.gain.setValueAtTime(v(0.06), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.25);
    } catch (e) {}
  }

  /* ==========================================
     VISUAL RIPPLE
  ========================================== */
  function createRipple(x, y) {
    var ripple = document.createElement('div');
    ripple.className = 'click-ripple';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    document.body.appendChild(ripple);
    setTimeout(function () {
      if (ripple.parentNode) ripple.parentNode.removeChild(ripple);
    }, 500);
  }

  /* ==========================================
     EVENT LISTENERS
  ========================================== */

  // ==== PAGE LOAD — skip initial chime (handled by loader.php) ====
  // The loader component plays its own loading ticks + completion chime.
  // sfx.js only fires the page load chime on AJAX-based page transitions.
  window.addEventListener('load', function () {
    stopLoadingSound();
  });

  // ==== NAVIGATION — swoosh when clicking/tapping links that navigate away ====
  function handleNavClick(e) {
    var link = e.target.closest('a[href]');
    if (!link) return;
    var href = link.getAttribute('href');
    // Skip anchors, javascript:, and external links opened in new tabs
    if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
    if (link.target === '_blank') return;
    // This is a real navigation — play departing swoosh
    playNavigationSwoosh();
  }
  document.addEventListener('click', handleNavClick);
  // Mobile: also fire on touchend for immediate feedback
  if (isMobile) {
    document.addEventListener('touchend', handleNavClick, { passive: true });
  }

  // ==== BROWSER BACK/FORWARD — popstate navigation sound ====
  window.addEventListener('popstate', function () {
    playNavigationSwoosh();
  });

  // ==== GLOBAL CLICK — sound + ripple on every click ====
  var BUTTON_SEL = 'a, button, .btn-primary, .btn-submit, .btn-outline, .btn-view, .btn-success, ' +
    '.btn-danger, .menu-item, .action-card, .stat-card, .feature-card, .sponsor-card, ' +
    '.announcement-card, .team-card, .department-card, .nav-link, .chat-toggle-btn, ' +
    '.chat-send-btn, .chat-option-btn, .chat-suggestion-btn, .tab-link, .sfx-btn, ' +
    '.swal2-confirm, .swal2-cancel, .track-panel, input[type="submit"]';

  function handleClickSound(e) {
    var isButton = e.target.closest(BUTTON_SEL);
    if (isButton) {
      playButtonClick();
    } else {
      playSoftPop();
    }
    var cx = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
    var cy = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : 0);
    if (cx || cy) createRipple(cx, cy);
  }

  document.addEventListener('click', handleClickSound);

  // ==== TOUCH — mirror click sounds for instant response on mobile ====
  if (isMobile) {
    var _lastTouchSoundTime = 0;
    document.addEventListener('touchstart', function (e) {
      // Debounce to avoid double-fire with click
      var now = Date.now();
      if (now - _lastTouchSoundTime < 80) return;
      _lastTouchSoundTime = now;
      handleClickSound(e);
    }, { passive: true });
  }

  // ==== HOVER — interactive elements (desktop: mouseenter, mobile: touchstart) ====
  var HOVER_SEL = '.menu-item, .action-card, .stat-card, .feature-card, .sponsor-card, ' +
    '.announcement-card, .team-card, .department-card, .nav-link, ' +
    '.btn-primary, .btn-submit, .btn-outline, .btn-view, .btn-success, ' +
    '.btn-danger, .tab-link, .chat-toggle-btn, .chat-option-btn, .chat-suggestion-btn, ' +
    '.notification-item, .dropdown-item, .leaderboard-item, .auth-card, .welcome-card';

  document.addEventListener('mouseenter', function (e) {
    var hovered = e.target.closest(HOVER_SEL);
    if (hovered) playHoverTick();
  }, true);

  // Mobile: touchstart acts as hover
  if (isMobile) {
    document.addEventListener('touchstart', function (e) {
      var hovered = e.target.closest(HOVER_SEL);
      if (hovered) playHoverTick();
    }, { capture: true, passive: true });
  }

  // ==== TABLE ROW HOVER ====
  document.addEventListener('mouseenter', function (e) {
    var row = e.target.closest('table tbody tr, .data-table tbody tr, .table tbody tr');
    if (row) playTableRowHover();
  }, true);

  // ==== TABLE CELL HOVER ====
  document.addEventListener('mouseenter', function (e) {
    if (e.target.tagName === 'TD' || e.target.tagName === 'TH') playCellTick();
  }, true);

  // Mobile: touch on table rows/cells
  if (isMobile) {
    document.addEventListener('touchstart', function (e) {
      var row = e.target.closest('table tbody tr, .data-table tbody tr, .table tbody tr');
      if (row) playTableRowHover();
      if (e.target.tagName === 'TD' || e.target.tagName === 'TH') playCellTick();
    }, { capture: true, passive: true });
  }

  // ==== SCROLL — whoosh (throttled, works with touch scroll on mobile) ====
  var lastScrollY = window.scrollY || window.pageYOffset;
  var scrollTimeout = null;
  var scrollCooldown = false;
  var prevAtTop = true;
  var prevAtBottom = false;
  // Mobile: slightly longer cooldown for smooth touch scrolling
  var SCROLL_COOLDOWN_MS = isMobile ? 180 : 120;

  function handleScroll() {
    if (scrollCooldown) return;
    scrollCooldown = true;

    var currentY = window.scrollY || window.pageYOffset;
    var delta = currentY - lastScrollY;
    var maxScroll = document.documentElement.scrollHeight - window.innerHeight;
    var atTop = currentY <= 5;
    var atBottom = currentY >= maxScroll - 5;

    // Scroll whoosh for movement
    if (Math.abs(delta) > (isMobile ? 15 : 20)) {
      playScrollWhoosh(delta > 0 ? 'down' : 'up');
    }

    // Edge thud when hitting top or bottom
    if (atTop && !prevAtTop) playScrollEdge();
    if (atBottom && !prevAtBottom) playScrollEdge();

    prevAtTop = atTop;
    prevAtBottom = atBottom;
    lastScrollY = currentY;

    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(function () {
      scrollCooldown = false;
    }, SCROLL_COOLDOWN_MS);
  }

  window.addEventListener('scroll', handleScroll, { passive: true });

  // Mobile: also listen to touchmove for scroll sound (fires during momentum scroll)
  if (isMobile) {
    var _touchScrollCooldown = false;
    document.addEventListener('touchmove', function () {
      if (_touchScrollCooldown) return;
      _touchScrollCooldown = true;
      handleScroll();
      setTimeout(function () { _touchScrollCooldown = false; }, SCROLL_COOLDOWN_MS);
    }, { passive: true });
  }

  // ==== SCROLLABLE CONTAINERS — sidebar, chat, modals ====
  document.addEventListener('scroll', function (e) {
    if (e.target === document || e.target === document.documentElement) return;
    if (scrollCooldown) return;
    scrollCooldown = true;
    playScrollWhoosh('down');
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(function () { scrollCooldown = false; }, 120);
  }, true);

  // ==== SECTION INTERSECTION — ding when scrolling into content sections ====
  var sectionObserved = new Set();
  function observeSections() {
    var sections = document.querySelectorAll(
      '.dashboard-content, .welcome-card, .stat-card, .auth-card, ' +
      'section, .content-section, .card, .data-table'
    );
    if (!sections.length) return;
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting && !sectionObserved.has(entry.target)) {
          sectionObserved.add(entry.target);
          playSectionEnter();
        }
      });
    }, { threshold: 0.3 });
    sections.forEach(function (s) { io.observe(s); });
  }
  // Run after DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', observeSections);
  } else {
    setTimeout(observeSections, 100);
  }

  // ==== INPUT FOCUS — warm chime ====
  document.addEventListener('focusin', function (e) {
    var isInput = e.target.matches(
      'input[type="text"], input[type="email"], input[type="password"], input[type="search"], ' +
      'input[type="tel"], input[type="url"], input[type="number"], textarea, ' +
      '.form-input, .form-select, .chat-input, select'
    );
    if (isInput) playInputFocus();
  });

  // ==== KEYSTROKE + ERASE ====
  var INPUT_SEL = 'input[type="text"], input[type="email"], input[type="password"], input[type="search"], ' +
    'input[type="tel"], input[type="url"], input[type="number"], textarea, .chat-input, ' +
    '[contenteditable="true"]';

  // Physical keyboard handler (works on desktop, partially on mobile)
  document.addEventListener('keydown', function (e) {
    if (!e.target.matches(INPUT_SEL)) return;
    if (e.key === 'Backspace' || e.key === 'Delete') {
      playEraseTick();
    } else if (e.key === 'Enter') {
      playEnterKey();
    } else if (e.key === 'Escape') {
      playEscapeSound();
    } else if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'C')) {
      playCopySound();
    } else if ((e.ctrlKey || e.metaKey) && (e.key === 'v' || e.key === 'V')) {
      playPasteSound();
    } else if ((e.ctrlKey || e.metaKey) && (e.key === 'x' || e.key === 'X')) {
      playClearSweep();
    } else if (e.key.length === 1) {
      playKeystroke();
    }
  });

  // ==== MOBILE VIRTUAL KEYBOARD — 'input' + 'beforeinput' events ====
  // Mobile virtual keyboards often DON'T fire keydown reliably.
  // We use beforeinput/input events which always fire on mobile.
  if (isMobile) {
    // Track previous input values to detect deletion on mobile
    var _prevInputValues = new WeakMap();

    // beforeinput fires BEFORE the value changes — best for mobile typing detection
    document.addEventListener('beforeinput', function (e) {
      if (!e.target.matches || !e.target.matches(INPUT_SEL)) return;
      var inputType = e.inputType || '';

      if (inputType === 'insertText' || inputType === 'insertCompositionText') {
        playKeystroke();
      } else if (inputType === 'insertLineBreak' || inputType === 'insertParagraph') {
        playEnterKey();
      } else if (inputType === 'deleteContentBackward' || inputType === 'deleteWordBackward'
        || inputType === 'deleteSoftLineBackward' || inputType === 'deleteHardLineBackward') {
        playEraseTick();
      } else if (inputType === 'deleteContentForward' || inputType === 'deleteWordForward') {
        playEraseTick();
      } else if (inputType === 'insertFromPaste' || inputType === 'insertFromDrop') {
        playPasteSound();
      } else if (inputType === 'deleteByCut') {
        playClearSweep();
      } else if (inputType === 'insertReplacementText') {
        playKeystroke();
      }
    }, { passive: true });

    // Fallback: use 'input' event for browsers that lack beforeinput support
    document.addEventListener('input', function (e) {
      if (!e.target.matches || !e.target.matches(INPUT_SEL)) return;
      var val = e.target.value || '';
      var prev = _prevInputValues.get(e.target) || '';

      // Detect if field was cleared entirely
      if (val === '' && prev.length > 0) {
        playClearSweep();
      }

      _prevInputValues.set(e.target, val);
    }, { passive: true });

    // Capture initial values when focusing inputs
    document.addEventListener('focusin', function (e) {
      if (e.target.matches && e.target.matches(INPUT_SEL)) {
        _prevInputValues.set(e.target, e.target.value || '');
      }
    }, { passive: true });
  }

  // ==== GLOBAL KEYBOARD — Escape, Copy, Paste outside inputs too ====
  document.addEventListener('keydown', function (e) {
    if (e.target.matches(INPUT_SEL)) return; // handled above
    if (e.key === 'Escape') {
      playEscapeSound();
    } else if ((e.ctrlKey || e.metaKey) && (e.key === 'c' || e.key === 'C')) {
      playCopySound();
    } else if ((e.ctrlKey || e.metaKey) && (e.key === 'v' || e.key === 'V')) {
      playPasteSound();
    }
  });

  // ==== INPUT CLEARED (value becomes empty) — desktop fallback ====
  if (!isMobile) {
    document.addEventListener('input', function (e) {
      if (e.target.matches(INPUT_SEL) && e.target.value === '') {
        playClearSweep();
      }
    });
  }

  // ==== FILE INPUT — upload sparkle ====
  document.addEventListener('change', function (e) {
    if (e.target.type === 'file' && e.target.files && e.target.files.length > 0) {
      playFileUpload();
    } else if (e.target.type === 'checkbox') {
      playToggle(e.target.checked);
    } else if (e.target.type === 'radio') {
      playToggle(true);
    } else if (e.target.tagName === 'SELECT') {
      playDropdown(true);
    }
  });

  // ==== FORM SUBMIT ====
  document.addEventListener('submit', function () {
    playFormSubmit();
  });

  // ==== SIDEBAR TOGGLE (mobile hamburger) — works on touch too ====
  function handleSidebarToggle(e) {
    var toggler = e.target.closest('.mobile-toggle, .hamburger, .sidebar-toggle, #menuToggle');
    if (toggler) {
      var sidebar = document.querySelector('.sidebar');
      if (sidebar) {
        var isOpen = sidebar.classList.contains('open') || sidebar.classList.contains('active');
        setTimeout(function () { playSidebarSlide(!isOpen); }, 50);
      }
    }
  }
  document.addEventListener('click', handleSidebarToggle);
  if (isMobile) {
    document.addEventListener('touchend', handleSidebarToggle, { passive: true });
  }

  // ==== NOTIFICATION BELL ====
  document.addEventListener('click', function (e) {
    var bell = e.target.closest('.notification-btn, .notification-bell, [class*="notification"] > button, .chat-notification-btn');
    if (bell) playNotificationPing();
  });

  // ==== SWEETALERT2 / CHAT MODAL OBSERVER ====
  var swalObserver = new MutationObserver(function (mutations) {
    mutations.forEach(function (mutation) {
      mutation.addedNodes.forEach(function (node) {
        if (node.nodeType !== 1) return;
        if (node.classList && node.classList.contains('swal2-container')) {
          playModalOpen();
          setTimeout(function () {
            var icon = node.querySelector('.swal2-icon');
            if (icon) {
              if (icon.classList.contains('swal2-success')) playSuccessDing();
              else if (icon.classList.contains('swal2-error')) playErrorBuzz();
              else if (icon.classList.contains('swal2-warning')) playWarningTone();
            }
          }, 100);
        }
        if (node.classList && node.classList.contains('chat-window')) playModalOpen();
      });
    });
  });
  swalObserver.observe(document.body, { childList: true, subtree: true });

  // ==== DROPDOWN / PROFILE MENU ====
  document.addEventListener('click', function (e) {
    var dd = e.target.closest('.user-profile, .dropdown-toggle, .profile-dropdown, [data-toggle="dropdown"]');
    if (dd) playDropdown(true);
  });

  // ==== CHAT TOGGLE ====
  document.addEventListener('click', function (e) {
    var chatToggle = e.target.closest('.chat-toggle-btn');
    if (chatToggle) {
      var cw = document.querySelector('.chat-window');
      if (cw) {
        var vis = cw.style.display !== 'none' && cw.offsetParent !== null;
        playSidebarSlide(!vis);
      }
    }
  });

  // ==== TAB SWITCH ====
  document.addEventListener('click', function (e) {
    var tab = e.target.closest('.tab-link, .nav-tab, [role="tab"]');
    if (tab) playToggle(true);
  });

  // ==== PAGE VISIBILITY — sound when user returns to tab ====
  document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
      playLeaveTab();
    } else {
      playReturnToTab();
    }
  });

  // ==== LOADING INDICATOR — fetch/XHR interception ====
  // Monkey-patch fetch to play loading sounds during AJAX
  if (window.fetch) {
    var origFetch = window.fetch;
    window.fetch = function () {
      startLoadingSound();
      return origFetch.apply(this, arguments).then(function (resp) {
        stopLoadingSound();
        return resp;
      }).catch(function (err) {
        stopLoadingSound();
        throw err;
      });
    };
  }

  // Monkey-patch XMLHttpRequest for jQuery $.ajax calls
  var origOpen = XMLHttpRequest.prototype.open;
  var origSend = XMLHttpRequest.prototype.send;
  XMLHttpRequest.prototype.open = function () {
    this._sfxOpened = true;
    return origOpen.apply(this, arguments);
  };
  XMLHttpRequest.prototype.send = function () {
    var xhr = this;
    if (xhr._sfxOpened) {
      startLoadingSound();
      xhr.addEventListener('loadend', function () {
        stopLoadingSound();
      });
    }
    return origSend.apply(this, arguments);
  };

  /* ==========================================
     MOBILE-SPECIFIC ENHANCEMENTS
  ========================================== */
  if (isMobile) {

    // ==== LONG PRESS — play modal open sound on long-press (context menu) ====
    var _longPressTimer = null;
    var _longPressTriggered = false;
    document.addEventListener('touchstart', function (e) {
      _longPressTriggered = false;
      _longPressTimer = setTimeout(function () {
        _longPressTriggered = true;
        playModalOpen();
      }, 500);
    }, { passive: true });
    document.addEventListener('touchend', function () {
      if (_longPressTimer) { clearTimeout(_longPressTimer); _longPressTimer = null; }
      // Play release pop if it was a held touch
      if (_longPressTriggered) {
        playTouchRelease();
        _longPressTriggered = false;
      }
    }, { passive: true });
    document.addEventListener('touchmove', function () {
      if (_longPressTimer) { clearTimeout(_longPressTimer); _longPressTimer = null; }
    }, { passive: true });

    // ==== ORIENTATION CHANGE — notification ping ====
    window.addEventListener('orientationchange', function () {
      setTimeout(function () { playNotificationPing(); }, 300);
    });

    // ==== PINCH ZOOM DETECTION ====
    var _pinchStartDist = 0;
    var _pinchCooldown = false;
    document.addEventListener('touchstart', function (e) {
      if (e.touches && e.touches.length === 2) {
        var dx = e.touches[1].clientX - e.touches[0].clientX;
        var dy = e.touches[1].clientY - e.touches[0].clientY;
        _pinchStartDist = Math.sqrt(dx * dx + dy * dy);
      }
    }, { passive: true });
    document.addEventListener('touchend', function (e) {
      if (_pinchStartDist > 0 && !_pinchCooldown) {
        // Pinch gesture ended — detect zoom in/out by comparing with viewport changes
        _pinchCooldown = true;
        setTimeout(function () {
          _pinchCooldown = false;
          _pinchStartDist = 0;
        }, 300);
      }
    }, { passive: true });
    document.addEventListener('touchmove', function (e) {
      if (e.touches && e.touches.length === 2 && _pinchStartDist > 0 && !_pinchCooldown) {
        var dx = e.touches[1].clientX - e.touches[0].clientX;
        var dy = e.touches[1].clientY - e.touches[0].clientY;
        var dist = Math.sqrt(dx * dx + dy * dy);
        var delta = dist - _pinchStartDist;
        if (Math.abs(delta) > 40) {
          _pinchCooldown = true;
          playPinchZoom(delta > 0);
          _pinchStartDist = dist;
          setTimeout(function () { _pinchCooldown = false; }, 250);
        }
      }
    }, { passive: true });

    // ==== SHAKE DETECTION — device motion ====
    var _lastShakeTime = 0;
    var _shakeThreshold = 20;
    var _lastAccel = { x: 0, y: 0, z: 0 };
    window.addEventListener('devicemotion', function (e) {
      var accel = e.accelerationIncludingGravity;
      if (!accel) return;
      var dx = Math.abs(accel.x - _lastAccel.x);
      var dy = Math.abs(accel.y - _lastAccel.y);
      var dz = Math.abs(accel.z - _lastAccel.z);
      _lastAccel = { x: accel.x || 0, y: accel.y || 0, z: accel.z || 0 };
      if ((dx + dy + dz) > _shakeThreshold) {
        var now = Date.now();
        if (now - _lastShakeTime > 600) {
          _lastShakeTime = now;
          playShakeRattle();
        }
      }
    }, { passive: true });

    // ==== PULL-TO-REFRESH DETECTION — elastic stretch + confirmation ====
    var _touchStartY = 0;
    var _touchStartX = 0;
    var _pullPlayed = false;
    document.addEventListener('touchstart', function (e) {
      if (e.touches && e.touches[0]) {
        _touchStartY = e.touches[0].clientY;
        _touchStartX = e.touches[0].clientX;
        _pullPlayed = false;
      }
    }, { passive: true });
    document.addEventListener('touchmove', function (e) {
      if (!e.touches || !e.touches[0] || _pullPlayed) return;
      var dy = e.touches[0].clientY - _touchStartY;
      var scrollTop = window.scrollY || window.pageYOffset;
      // Play stretch sound while pulling at top
      if (scrollTop <= 5 && dy > 50 && !_pullPlayed) {
        _pullPlayed = true;
        playPullStretch();
      }
    }, { passive: true });
    document.addEventListener('touchend', function (e) {
      if (!e.changedTouches || !e.changedTouches[0]) return;
      var dy = e.changedTouches[0].clientY - _touchStartY;
      var scrollTop = window.scrollY || window.pageYOffset;
      // If at top and pulled down > 80px, play refresh confirmation
      if (scrollTop <= 5 && dy > 80) {
        playFormSubmit();
      }
    }, { passive: true });

    // ==== SWIPE NAVIGATION + CARD SWIPE — left/right swipe ====
    document.addEventListener('touchend', function (e) {
      if (!e.changedTouches || !e.changedTouches[0]) return;
      var dx = e.changedTouches[0].clientX - _touchStartX;
      var dy = e.changedTouches[0].clientY - _touchStartY;
      // Only trigger for horizontal swipes (not vertical scroll)
      if (Math.abs(dx) > 80 && Math.abs(dx) > Math.abs(dy) * 1.5) {
        var dir = dx > 0 ? 'right' : 'left';
        // Check if swiping a card
        var card = e.target.closest('.action-card, .stat-card, .feature-card, .team-card, .department-card, .announcement-card, .sponsor-card');
        if (card) {
          playCardSwipe(dir);
        } else {
          playNavigationSwoosh();
        }
      }
    }, { passive: true });

    // ==== DOUBLE-TAP — success ding ====
    var _lastTapTime = 0;
    document.addEventListener('touchend', function () {
      var now = Date.now();
      if (now - _lastTapTime < 300) {
        playSuccessDing();
      }
      _lastTapTime = now;
    }, { passive: true });

    // ==== RAPID TAP DETECTION — 3+ taps in quick succession ====
    var _tapTimes = [];
    document.addEventListener('touchstart', function () {
      var now = Date.now();
      _tapTimes.push(now);
      // Keep only last 4 taps
      if (_tapTimes.length > 4) _tapTimes.shift();
      // Check for 3 rapid taps within 500ms
      if (_tapTimes.length >= 3) {
        var span = _tapTimes[_tapTimes.length - 1] - _tapTimes[_tapTimes.length - 3];
        if (span < 500) {
          playRapidTap();
          _tapTimes = [];
        }
      }
    }, { passive: true });

    // ==== KEYBOARD SHOW/HIDE DETECTION (resize-based) ====
    var _prevInnerH = window.innerHeight;
    window.addEventListener('resize', function () {
      var h = window.innerHeight;
      if (h < _prevInnerH - 100) {
        // Keyboard likely opened
        playToggle(true);
      } else if (h > _prevInnerH + 100) {
        // Keyboard likely closed
        playToggle(false);
      }
      _prevInnerH = h;
    });

    // ==== BOTTOM NAV / FIXED FOOTER TAPS ====
    document.addEventListener('touchstart', function (e) {
      var bottomNav = e.target.closest('.bottom-nav, .mobile-nav, .fixed-bottom, footer nav, nav.mobile-bottom-nav');
      if (bottomNav) playBottomNavTap();
    }, { capture: true, passive: true });

    // ==== MOBILE SELECT (datalist/autocomplete) ====
    document.addEventListener('change', function (e) {
      if (e.target.tagName === 'SELECT') {
        playAutocompleteSelect();
      }
    });

    // ==== SCROLL SNAP — detect snap points (CSS scroll-snap) ====
    var _scrollSnapTimer = null;
    document.addEventListener('scroll', function (e) {
      if (e.target === document || e.target === document.documentElement) return;
      // Detect scroll-snap containers
      var style = window.getComputedStyle(e.target);
      if (style.scrollSnapType && style.scrollSnapType !== 'none') {
        clearTimeout(_scrollSnapTimer);
        _scrollSnapTimer = setTimeout(function () {
          playScrollSnap();
        }, 80);
      }
    }, true);

    // ==== HAPTIC TICK on every touch for interactive elements ====
    document.addEventListener('touchstart', function (e) {
      var interactable = e.target.closest(
        'input[type="checkbox"], input[type="radio"], .toggle-switch, .switch, ' +
        'input[type="range"], .slider'
      );
      if (interactable) playHapticTick();
    }, { capture: true, passive: true });

    // ==== IMAGE / MEDIA TAP — sparkle on tapping images ====
    document.addEventListener('touchstart', function (e) {
      if (e.target.tagName === 'IMG' || e.target.tagName === 'VIDEO' || e.target.closest('.image-container, .media-card, .gallery-item')) {
        playFileUpload();
      }
    }, { capture: true, passive: true });

    // ==== ACCORDION / COLLAPSIBLE ====
    document.addEventListener('touchstart', function (e) {
      var acc = e.target.closest('.accordion-header, .collapse-toggle, [data-toggle="collapse"], .faq-toggle, details summary');
      if (acc) {
        var parent = acc.closest('.accordion-item, .collapse-container, details');
        var isOpen = parent && (parent.classList.contains('open') || parent.classList.contains('show') || parent.open);
        playDropdown(!isOpen);
      }
    }, { capture: true, passive: true });

  }

})();
