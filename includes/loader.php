<!-- SPARK'26 Audio Unlock + Page Loader -->
<style>
.spark-audio-gate {
  position: fixed;
  inset: 0;
  z-index: 100000;
  background: #0a0a0a;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  -webkit-tap-highlight-color: transparent;
  user-select: none;
  transition: opacity 0.4s ease;
}
.spark-audio-gate.dismiss {
  opacity: 0;
  pointer-events: none;
}
.spark-gate-icon {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: rgba(217, 119, 6, 0.12);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
  animation: gatePulse 2s ease-in-out infinite;
}
.spark-gate-icon i {
  font-size: 2rem;
  color: #D97706;
}
@keyframes gatePulse {
  0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(217,119,6,0.3); }
  50% { transform: scale(1.08); box-shadow: 0 0 0 16px rgba(217,119,6,0); }
}
.spark-gate-title {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 1.5rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: 2px;
  margin-bottom: 0.5rem;
}
.spark-gate-sub {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.85rem;
  color: rgba(255,255,255,0.5);
  margin-bottom: 2rem;
}
.spark-gate-tap {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 0.9rem;
  font-weight: 600;
  color: #D97706;
  padding: 0.6rem 2rem;
  border: 2px solid rgba(217,119,6,0.3);
  border-radius: 50px;
  animation: tapBlink 1.5s ease-in-out infinite;
}
@keyframes tapBlink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
@media (max-width: 480px) {
  .spark-gate-icon { width: 60px; height: 60px; }
  .spark-gate-icon i { font-size: 1.6rem; }
  .spark-gate-title { font-size: 1.2rem; }
  .spark-gate-tap { font-size: 0.8rem; padding: 0.5rem 1.5rem; }
}
</style>

<!-- Audio Gate: Captures user gesture to unlock audio at max volume -->
<div class="spark-audio-gate" id="sparkAudioGate">
  <div class="spark-gate-icon"><i class="ri-volume-up-fill"></i></div>
  <div class="spark-gate-title">SPARK'26</div>
  <div class="spark-gate-sub">Project Expo 2026</div>
  <div class="spark-gate-tap">Tap to Enter</div>
</div>

<!-- Loader (hidden until gate is dismissed) -->
<div class="spark-loader-overlay" id="sparkLoader" style="display:none;">
    <div class="spark-loader-spinner"></div>
    <div class="spark-loader-dots">
        <span></span><span></span><span></span>
    </div>
    <div class="spark-loader-text">SPARK'26</div>
    <div class="spark-loader-bar">
        <div class="spark-loader-bar-fill" id="sparkLoaderBar"></div>
    </div>
</div>
<script>
(function(){
  var gate = document.getElementById('sparkAudioGate');
  var bar = document.getElementById('sparkLoaderBar');
  var overlay = document.getElementById('sparkLoader');
  if (!bar || !overlay || !gate) return;

  var progress = 0;
  var audioCtx = null;
  var masterGain = null;
  var masterComp = null;
  var tickInterval = null;
  var gateUnlocked = false;

  // Mobile detection
  var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    || ('ontouchstart' in window)
    || (navigator.maxTouchPoints > 0);

  // Maximum volume
  var VOL = isMobile ? 12.0 : 10.0;
  function v(base) { return Math.min(base * VOL, 1.0); }

  function getCtx() {
    if (!audioCtx) {
      try { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch(e) { return null; }
    }
    if (audioCtx && audioCtx.state === 'suspended') {
      audioCtx.resume().catch(function(){});
    }
    return audioCtx;
  }

  // Master gain + compressor chain = loudness maximizer
  function initMaster(ctx) {
    if (masterGain) return;
    try {
      masterComp = ctx.createDynamicsCompressor();
      masterComp.threshold.setValueAtTime(-24, ctx.currentTime);
      masterComp.knee.setValueAtTime(12, ctx.currentTime);
      masterComp.ratio.setValueAtTime(12, ctx.currentTime);
      masterComp.attack.setValueAtTime(0.003, ctx.currentTime);
      masterComp.release.setValueAtTime(0.15, ctx.currentTime);
      masterGain = ctx.createGain();
      masterGain.gain.setValueAtTime(isMobile ? 4.0 : 3.0, ctx.currentTime);
      masterGain.connect(masterComp);
      masterComp.connect(ctx.destination);
    } catch(e) { masterGain = null; }
  }

  function getOut() {
    var ctx = getCtx();
    if (!ctx) return null;
    if (!masterGain) initMaster(ctx);
    return masterGain || ctx.destination;
  }

  // Full unlock: resume context + silent buffer trick (iOS)
  function fullUnlock() {
    var ctx = getCtx();
    if (!ctx) return;
    if (ctx.state === 'suspended') {
      ctx.resume().catch(function(){});
    }
    initMaster(ctx);
    try {
      var b = ctx.createBuffer(1, 1, 22050);
      var s = ctx.createBufferSource();
      s.buffer = b; s.connect(getOut());
      s.start(0); s.stop(0.001);
    } catch(e) {}
  }

  // Startup whoosh — powerful rising sweep + shimmer + sub bass
  function playStartupWhoosh() {
    var ctx = getCtx(); if (!ctx) return;
    var out = getOut();
    try {
      // Sub bass thud
      var oSub = ctx.createOscillator(), gSub = ctx.createGain();
      oSub.connect(gSub); gSub.connect(out);
      oSub.type = 'sine';
      oSub.frequency.setValueAtTime(60, ctx.currentTime);
      oSub.frequency.exponentialRampToValueAtTime(40, ctx.currentTime + 0.3);
      gSub.gain.setValueAtTime(v(0.15), ctx.currentTime);
      gSub.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
      oSub.start(ctx.currentTime); oSub.stop(ctx.currentTime + 0.35);

      // Rising sweep
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(out);
      o.type = 'sine';
      o.frequency.setValueAtTime(200, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(900, ctx.currentTime + 0.3);
      o.frequency.exponentialRampToValueAtTime(500, ctx.currentTime + 0.55);
      g.gain.setValueAtTime(v(0.12), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.55);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.55);

      // High shimmer
      var o2 = ctx.createOscillator(), g2 = ctx.createGain();
      o2.connect(g2); g2.connect(out);
      o2.type = 'triangle';
      o2.frequency.setValueAtTime(700, ctx.currentTime + 0.08);
      o2.frequency.exponentialRampToValueAtTime(1400, ctx.currentTime + 0.35);
      g2.gain.setValueAtTime(0.001, ctx.currentTime);
      g2.gain.setValueAtTime(v(0.06), ctx.currentTime + 0.08);
      g2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);
      o2.start(ctx.currentTime + 0.08); o2.stop(ctx.currentTime + 0.45);

      // Bright accent ping
      var o3 = ctx.createOscillator(), g3 = ctx.createGain();
      o3.connect(g3); g3.connect(out);
      o3.type = 'sine';
      o3.frequency.setValueAtTime(1200, ctx.currentTime + 0.2);
      g3.gain.setValueAtTime(0.001, ctx.currentTime);
      g3.gain.setValueAtTime(v(0.05), ctx.currentTime + 0.2);
      g3.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
      o3.start(ctx.currentTime + 0.2); o3.stop(ctx.currentTime + 0.4);
    } catch(e) {}
  }

  // Rhythmic loading tick
  var tickCount = 0;
  function playLoaderTick() {
    var ctx = getCtx(); if (!ctx) return;
    var out = getOut();
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(out);
      o.type = 'sine';
      o.frequency.setValueAtTime(tickCount % 2 === 0 ? 500 : 600, ctx.currentTime);
      g.gain.setValueAtTime(v(0.03), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.05);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.05);
      tickCount++;
    } catch(e) {}
  }

  // Completion chime — ascending three-note
  function playLoaderComplete() {
    var ctx = getCtx(); if (!ctx) return;
    var out = getOut();
    try {
      var notes = [523, 659, 784];
      notes.forEach(function(freq, i) {
        var o = ctx.createOscillator(), g = ctx.createGain();
        o.connect(g); g.connect(out);
        o.type = 'sine';
        var t = ctx.currentTime + i * 0.08;
        o.frequency.setValueAtTime(freq, t);
        g.gain.setValueAtTime(0.001, ctx.currentTime);
        g.gain.setValueAtTime(v(0.08), t);
        g.gain.exponentialRampToValueAtTime(0.001, t + 0.18);
        o.start(t); o.stop(t + 0.18);
      });
    } catch(e) {}
  }

  // --- GATE: User taps to unlock audio, then loader starts ---
  function onGateClick() {
    if (gateUnlocked) return;
    gateUnlocked = true;

    // 1. Unlock audio at max volume (this is the user gesture)
    fullUnlock();

    // 2. Play startup whoosh immediately (audio is now unlocked)
    playStartupWhoosh();

    // 3. Dismiss gate, show loader
    gate.classList.add('dismiss');
    setTimeout(function() {
      if (gate.parentNode) gate.parentNode.removeChild(gate);
    }, 450);
    overlay.style.display = '';

    // 4. Start loader ticks
    tickInterval = setInterval(function() {
      if (progress < 85) {
        progress += Math.random() * 12 + 3;
        if (progress > 85) progress = 85;
        bar.style.width = progress + '%';
        playLoaderTick();
      }
    }, 120);

    // 5. If page already loaded, finish quickly
    if (document.readyState === 'complete') {
      setTimeout(finishLoader, 600);
    }
  }

  gate.addEventListener('click', onGateClick);
  gate.addEventListener('touchend', function(e) {
    e.preventDefault();
    onGateClick();
  }, { passive: false });

  // When page fully loaded — complete progress and fade out
  function finishLoader() {
    if (!gateUnlocked) return; // wait for gate first
    clearInterval(tickInterval);
    progress = 100;
    bar.style.width = '100%';
    playLoaderComplete();
    setTimeout(function() {
      overlay.classList.add('loaded');
      setTimeout(function() {
        if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
      }, 500);
    }, 350);
  }

  if (document.readyState === 'complete') {
    // If page loaded before gate click, finishLoader waits for gate
  } else {
    window.addEventListener('load', function() {
      if (gateUnlocked) {
        setTimeout(finishLoader, 400);
      } else {
        // Page loaded before user tapped - will finish after gate click
        var waitForGate = setInterval(function() {
          if (gateUnlocked) {
            clearInterval(waitForGate);
            setTimeout(finishLoader, 600);
          }
        }, 100);
      }
    });
  }
})();
</script>
