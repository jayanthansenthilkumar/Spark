<!-- SPARK'26 Page Loader -->
<div class="spark-loader-overlay" id="sparkLoader">
    <div class="spark-loader-spinner"></div>
    <div class="spark-loader-dots">
        <span></span><span></span><span></span>
    </div>
    <div class="spark-loader-text">Loading</div>
    <div class="spark-loader-bar">
        <div class="spark-loader-bar-fill" id="sparkLoaderBar"></div>
    </div>
</div>
<script>
(function(){
  // Loader progress + sound
  var bar = document.getElementById('sparkLoaderBar');
  var overlay = document.getElementById('sparkLoader');
  if (!bar || !overlay) return;

  var progress = 0;
  var audioCtx = null;
  var tickInterval = null;
  var VOL = 5.0;

  function v(base) { return Math.min(base * VOL, 1.0); }

  function getCtx() {
    if (!audioCtx) {
      try { audioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch(e) { return null; }
    }
    return audioCtx;
  }

  // Rhythmic loading tick — alternating pitch
  var tickCount = 0;
  function playLoaderTick() {
    var ctx = getCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(ctx.destination);
      o.type = 'sine';
      o.frequency.setValueAtTime(tickCount % 2 === 0 ? 500 : 600, ctx.currentTime);
      g.gain.setValueAtTime(v(0.02), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.04);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.04);
      tickCount++;
    } catch(e) {}
  }

  // Completion chime — ascending three-note
  function playLoaderComplete() {
    var ctx = getCtx(); if (!ctx) return;
    try {
      var notes = [523, 659, 784];
      notes.forEach(function(freq, i) {
        var o = ctx.createOscillator(), g = ctx.createGain();
        o.connect(g); g.connect(ctx.destination);
        o.type = 'sine';
        var t = ctx.currentTime + i * 0.08;
        o.frequency.setValueAtTime(freq, t);
        g.gain.setValueAtTime(0.001, ctx.currentTime);
        g.gain.setValueAtTime(v(0.06), t);
        g.gain.exponentialRampToValueAtTime(0.001, t + 0.15);
        o.start(t); o.stop(t + 0.15);
      });
    } catch(e) {}
  }

  // Simulate fast progress ticks
  tickInterval = setInterval(function() {
    if (progress < 85) {
      progress += Math.random() * 12 + 3;
      if (progress > 85) progress = 85;
      bar.style.width = progress + '%';
      playLoaderTick();
    }
  }, 120);

  // When page fully loaded — complete progress and fade out
  function finishLoader() {
    clearInterval(tickInterval);
    progress = 100;
    bar.style.width = '100%';
    playLoaderComplete();
    setTimeout(function() {
      overlay.classList.add('loaded');
      // Remove from DOM after transition
      setTimeout(function() {
        if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
      }, 500);
    }, 350);
  }

  if (document.readyState === 'complete') {
    finishLoader();
  } else {
    window.addEventListener('load', finishLoader);
  }
})();
</script>
