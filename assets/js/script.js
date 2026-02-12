document.addEventListener("DOMContentLoaded", () => {
  // --- Sidebar Toggle for Mobile ---
  window.toggleSidebar = function() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
      sidebar.classList.toggle('open');
    }
  };

  // --- Mobile Nav Menu Toggle (Landing Page) ---
  window.toggleMobileMenu = function() {
    const navMenu = document.getElementById('navMenu');
    const menuIcon = document.getElementById('menuIcon');
    if (navMenu) {
      navMenu.classList.toggle('mobile-open');
      if (menuIcon) {
        if (navMenu.classList.contains('mobile-open')) {
          menuIcon.className = 'ri-close-line';
        } else {
          menuIcon.className = 'ri-menu-line';
        }
      }
    }
  };

  // Close sidebar and user dropdown when clicking outside
  document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    const mobileToggle = document.querySelector('.mobile-toggle');
    const userDropdown = document.getElementById('userDropdown');
    const userProfile = document.querySelector('.user-profile');

    // Sidebar Close Logic
    if (sidebar && sidebar.classList.contains('open')) {
      if (!sidebar.contains(e.target) && (!mobileToggle || !mobileToggle.contains(e.target))) {
        sidebar.classList.remove('open');
      }
    }

    // User Dropdown Close Logic
    if (userDropdown && userDropdown.classList.contains('show')) {
      if (!userProfile || !userProfile.contains(e.target)) {
        userDropdown.classList.remove('show');
      }
    }

    // Mobile nav menu close on link click
    const navMenu = document.getElementById('navMenu');
    const menuIcon = document.getElementById('menuIcon');
    if (navMenu && navMenu.classList.contains('mobile-open')) {
      if (e.target.classList.contains('nav-link') || e.target.classList.contains('btn-primary')) {
        navMenu.classList.remove('mobile-open');
        if (menuIcon) menuIcon.className = 'ri-menu-line';
      }
    }
  });

  // --- Position helper for fixed dropdowns ---
  function _positionDropdown(triggerEl, dropdown, offsetRight) {
    const isMobile = window.innerWidth <= 480;
    if (isMobile) {
      // Mobile: bottom sheet, CSS handles it
      dropdown.style.top = '';
      dropdown.style.right = '';
      return;
    }
    const rect = triggerEl.getBoundingClientRect();
    dropdown.style.top = (rect.bottom + 4) + 'px';
    // Align right edge of dropdown with right edge of trigger (+ offset)
    const rightPos = window.innerWidth - rect.right + (offsetRight || 0);
    dropdown.style.right = Math.max(8, rightPos) + 'px';
  }

  // --- User Dropdown Toggle ---
  window.toggleUserDropdown = function(event) {
    event.stopPropagation();
    
    const dropdown = document.getElementById('userDropdown');
    if (!dropdown) return;

    // If clicking inside the dropdown content, do not toggle
    if (dropdown.contains(event.target)) {
        return;
    }

    // Close notification dropdown if open
    const notifDD = document.getElementById('notifDropdown');
    if (notifDD && notifDD.classList.contains('show')) {
      notifDD.classList.remove('show');
    }

    // Position before showing
    const trigger = document.querySelector('.user-profile');
    if (trigger) _positionDropdown(trigger, dropdown, -8);
    
    dropdown.classList.toggle('show');
  };

  // --- Notification Dropdown Toggle with Unique Sounds ---
  var _notifAudioCtx = null;
  var _notifMasterGain = null;
  var _notifMasterComp = null;

  function _initNotifMaster(ctx) {
    if (_notifMasterGain) return;
    try {
      _notifMasterComp = ctx.createDynamicsCompressor();
      _notifMasterComp.threshold.setValueAtTime(-24, ctx.currentTime);
      _notifMasterComp.knee.setValueAtTime(12, ctx.currentTime);
      _notifMasterComp.ratio.setValueAtTime(12, ctx.currentTime);
      _notifMasterComp.attack.setValueAtTime(0.003, ctx.currentTime);
      _notifMasterComp.release.setValueAtTime(0.15, ctx.currentTime);
      _notifMasterGain = ctx.createGain();
      _notifMasterGain.gain.setValueAtTime(_isMobileNotif ? 4.0 : 3.0, ctx.currentTime);
      _notifMasterGain.connect(_notifMasterComp);
      _notifMasterComp.connect(_notifAudioCtx.destination);
    } catch(e) { _notifMasterGain = null; }
  }

  function _getNotifOut() {
    var ctx = _getNotifCtx();
    if (!ctx) return null;
    if (!_notifMasterGain) _initNotifMaster(ctx);
    return _notifMasterGain || _getNotifOut();
  }

  function _getNotifCtx() {
    if (!_notifAudioCtx) {
      try { _notifAudioCtx = new (window.AudioContext || window.webkitAudioContext)(); } catch(e) { return null; }
    }
    if (_notifAudioCtx && _notifAudioCtx.state === 'suspended') {
      _notifAudioCtx.resume().catch(function(){});
    }
    return _notifAudioCtx;
  }

  var _isMobileNotif = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
    || ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
  var _nVol = _isMobileNotif ? 12.0 : 10.0;
  function _nv(b) { return Math.min(b * _nVol, 1.0); }

  // Sound: Bell ring — two-tone metallic chime (for opening dropdown)
  function _playBellRing() {
    var ctx = _getNotifCtx(); if (!ctx) return;
    try {
      // Metallic high tone
      var o1 = ctx.createOscillator(), g1 = ctx.createGain();
      o1.connect(g1); g1.connect(_getNotifOut());
      o1.type = 'sine';
      o1.frequency.setValueAtTime(1400, ctx.currentTime);
      o1.frequency.exponentialRampToValueAtTime(1800, ctx.currentTime + 0.05);
      o1.frequency.exponentialRampToValueAtTime(1200, ctx.currentTime + 0.2);
      g1.gain.setValueAtTime(_nv(0.08), ctx.currentTime);
      g1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
      o1.start(ctx.currentTime); o1.stop(ctx.currentTime + 0.3);
      // Undertone
      var o2 = ctx.createOscillator(), g2 = ctx.createGain();
      o2.connect(g2); g2.connect(_getNotifOut());
      o2.type = 'triangle';
      o2.frequency.setValueAtTime(700, ctx.currentTime);
      o2.frequency.exponentialRampToValueAtTime(900, ctx.currentTime + 0.08);
      g2.gain.setValueAtTime(_nv(0.04), ctx.currentTime);
      g2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.2);
      o2.start(ctx.currentTime); o2.stop(ctx.currentTime + 0.2);
      // High shimmer
      var o3 = ctx.createOscillator(), g3 = ctx.createGain();
      o3.connect(g3); g3.connect(_getNotifOut());
      o3.type = 'sine';
      o3.frequency.setValueAtTime(2200, ctx.currentTime + 0.05);
      g3.gain.setValueAtTime(0.001, ctx.currentTime);
      g3.gain.setValueAtTime(_nv(0.03), ctx.currentTime + 0.05);
      g3.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25);
      o3.start(ctx.currentTime + 0.05); o3.stop(ctx.currentTime + 0.25);
    } catch(e) {}
  }

  // Sound: Bell dismiss — soft descending close
  function _playBellClose() {
    var ctx = _getNotifCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(_getNotifOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(900, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(400, ctx.currentTime + 0.12);
      g.gain.setValueAtTime(_nv(0.05), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.15);
    } catch(e) {}
  }

  // Sound: Notification item hover — crystal tick
  function _playNotifItemHover() {
    var ctx = _getNotifCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(_getNotifOut());
      o.type = 'sine';
      o.frequency.setValueAtTime(1800, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(2200, ctx.currentTime + 0.03);
      g.gain.setValueAtTime(_nv(0.025), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.04);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.04);
    } catch(e) {}
  }

  // Sound: Notification item click — warm confirmation (different per type)
  function _playNotifItemClick(type) {
    var ctx = _getNotifCtx(); if (!ctx) return;
    try {
      var freq1 = 600, freq2 = 900;
      if (type === 'invitation')      { freq1 = 523; freq2 = 784; }  // C5→G5 (hopeful)
      if (type === 'message')         { freq1 = 440; freq2 = 660; }  // A4→E5 (communicative)
      if (type === 'announcement')    { freq1 = 660; freq2 = 880; }  // E5→A5 (attention)
      if (type === 'review' || type === 'admin_review') { freq1 = 350; freq2 = 700; }  // F4→F5 (official)
      if (type === 'project_status')  { freq1 = 494; freq2 = 740; }  // B4→F#5 (result)
      if (type === 'new_team')        { freq1 = 392; freq2 = 587; }  // G4→D5 (teamwork)
      if (type === 'new_user' || type === 'new_student') { freq1 = 466; freq2 = 698; }  // Bb4→F5 (welcome)
      if (type === 'schedule')        { freq1 = 554; freq2 = 831; }  // C#5→G#5 (alert)
      if (type === 'stats')           { freq1 = 415; freq2 = 622; }  // Ab4→Eb5 (insight)

      var o1 = ctx.createOscillator(), g1 = ctx.createGain();
      o1.connect(g1); g1.connect(_getNotifOut());
      o1.type = 'sine';
      o1.frequency.setValueAtTime(freq1, ctx.currentTime);
      g1.gain.setValueAtTime(_nv(0.07), ctx.currentTime);
      g1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
      o1.start(ctx.currentTime); o1.stop(ctx.currentTime + 0.12);

      var o2 = ctx.createOscillator(), g2 = ctx.createGain();
      o2.connect(g2); g2.connect(_getNotifOut());
      o2.type = 'sine';
      o2.frequency.setValueAtTime(freq2, ctx.currentTime + 0.08);
      g2.gain.setValueAtTime(0.001, ctx.currentTime);
      g2.gain.setValueAtTime(_nv(0.06), ctx.currentTime + 0.08);
      g2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.22);
      o2.start(ctx.currentTime + 0.08); o2.stop(ctx.currentTime + 0.22);
    } catch(e) {}
  }

  // Sound: Empty state — soft hollow tone
  function _playEmptyNotif() {
    var ctx = _getNotifCtx(); if (!ctx) return;
    try {
      var o = ctx.createOscillator(), g = ctx.createGain();
      o.connect(g); g.connect(_getNotifOut());
      o.type = 'triangle';
      o.frequency.setValueAtTime(300, ctx.currentTime);
      o.frequency.exponentialRampToValueAtTime(200, ctx.currentTime + 0.15);
      g.gain.setValueAtTime(_nv(0.04), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.2);
      o.start(ctx.currentTime); o.stop(ctx.currentTime + 0.2);
    } catch(e) {}
  }

  // Sound: Scroll within dropdown — soft whisper
  function _playNotifScroll() {
    var ctx = _getNotifCtx(); if (!ctx) return;
    try {
      var bufSize = ctx.sampleRate * 0.06;
      var buf = ctx.createBuffer(1, bufSize, ctx.sampleRate);
      var d = buf.getChannelData(0);
      for (var i = 0; i < bufSize; i++) d[i] = (Math.random() * 2 - 1) * 0.2;
      var n = ctx.createBufferSource(); n.buffer = buf;
      var f = ctx.createBiquadFilter();
      f.type = 'bandpass'; f.frequency.value = 1200; f.Q.value = 3;
      var g = ctx.createGain();
      g.gain.setValueAtTime(_nv(0.02), ctx.currentTime);
      g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.08);
      n.connect(f); f.connect(g); g.connect(_getNotifOut());
      n.start(ctx.currentTime); n.stop(ctx.currentTime + 0.08);
    } catch(e) {}
  }

  // Toggle notification dropdown
  window.toggleNotifDropdown = function(event) {
    event.stopPropagation();

    const dropdown = document.getElementById('notifDropdown');
    if (!dropdown) return;

    // Don't toggle if clicking inside dropdown content
    if (dropdown.contains(event.target) && dropdown.classList.contains('show')) {
      return;
    }

    // Close user dropdown if open
    const userDD = document.getElementById('userDropdown');
    if (userDD && userDD.classList.contains('show')) {
      userDD.classList.remove('show');
    }

    const isOpening = !dropdown.classList.contains('show');

    // Position before showing
    const trigger = document.querySelector('.notification-bell-wrapper');
    if (trigger) _positionDropdown(trigger, dropdown, -40);

    dropdown.classList.toggle('show');

    if (isOpening) {
      _playBellRing();
      if (dropdown.querySelector('.notif-empty')) {
        setTimeout(_playEmptyNotif, 300);
      }
    } else {
      _playBellClose();
    }
  };

  // Close dropdowns on outside click
  document.addEventListener('click', function(e) {
    const notifDD = document.getElementById('notifDropdown');
    const notifWrapper = document.querySelector('.notification-bell-wrapper');
    if (notifDD && notifDD.classList.contains('show')) {
      if (!notifWrapper || !notifWrapper.contains(e.target)) {
        notifDD.classList.remove('show');
        _playBellClose();
      }
    }

    const userDD = document.getElementById('userDropdown');
    const userWrapper = document.querySelector('.user-profile');
    if (userDD && userDD.classList.contains('show')) {
      if (!userWrapper || !userWrapper.contains(e.target)) {
        userDD.classList.remove('show');
      }
    }
  });

  // Close dropdowns on Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const notifDD = document.getElementById('notifDropdown');
      const userDD = document.getElementById('userDropdown');
      if (notifDD && notifDD.classList.contains('show')) {
        notifDD.classList.remove('show');
        _playBellClose();
      }
      if (userDD && userDD.classList.contains('show')) {
        userDD.classList.remove('show');
      }
    }
  });

  // Reposition dropdowns on window resize/scroll
  window.addEventListener('resize', function() {
    const notifDD = document.getElementById('notifDropdown');
    const userDD = document.getElementById('userDropdown');
    if (notifDD && notifDD.classList.contains('show')) {
      const trigger = document.querySelector('.notification-bell-wrapper');
      if (trigger) _positionDropdown(trigger, notifDD, -40);
    }
    if (userDD && userDD.classList.contains('show')) {
      const trigger = document.querySelector('.user-profile');
      if (trigger) _positionDropdown(trigger, userDD, -8);
    }
  });

  // Notification item hover sounds
  document.addEventListener('mouseenter', function(e) {
    var item = e.target.closest('.notif-item');
    if (item) _playNotifItemHover();
  }, true);

  // Mobile: touch on notif items
  if (_isMobileNotif) {
    document.addEventListener('touchstart', function(e) {
      var item = e.target.closest('.notif-item');
      if (item) _playNotifItemHover();
    }, { capture: true, passive: true });
  }

  // Notification item click sounds (different per type)
  document.addEventListener('click', function(e) {
    var item = e.target.closest('.notif-item');
    if (item) {
      var type = item.getAttribute('data-notif-type') || 'default';
      _playNotifItemClick(type);
    }
    // Footer link click
    var footer = e.target.closest('.notif-dropdown-footer a');
    if (footer) {
      _playNotifItemClick('announcement');
    }
  });

  // Notification dropdown scroll sound (throttled)
  var _notifScrollCooldown = false;
  document.addEventListener('scroll', function(e) {
    var body = e.target.closest('.notif-dropdown-body');
    if (!body) return;
    if (_notifScrollCooldown) return;
    _notifScrollCooldown = true;
    _playNotifScroll();
    setTimeout(function() { _notifScrollCooldown = false; }, 150);
  }, true);

  // --- Smooth Scrolling ---
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      document.querySelector(this.getAttribute("href")).scrollIntoView({
        behavior: "smooth",
      });
    });
  });

  // --- Simple Scroll Fade In ---
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
          entry.target.style.opacity = 1;
          entry.target.style.transform = "translateY(0)";
        }
      });
    },
    { threshold: 0.1 },
  );

  const fadeElements = document.querySelectorAll(
    ".feature-card, .track-card, .t-item, .cta-box",
  );

  fadeElements.forEach((el) => {
    el.style.opacity = 0;
    el.style.transform = "translateY(20px)";
    el.style.transition = "all 0.6s ease-out";
    observer.observe(el);
  });

  // --- Stats Counter Animation ---
  const statsSection = document.querySelector(".stats-strip");
  let counted = false;

  if (statsSection) {
    const statsObserver = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting && !counted) {
        counted = true;
        // Animate all stat items dynamically using data-target attributes
        document.querySelectorAll(".stats-strip [data-target]").forEach((el) => {
          const target = parseInt(el.getAttribute("data-target")) || 0;
          animateValue(el.id, 0, target, 2000);
        });
      }
    });
    statsObserver.observe(statsSection);
  }

  function animateValue(id, start, end, duration) {
    const obj = document.getElementById(id);
    if (!obj) return;
    let startTimestamp = null;
    const step = (timestamp) => {
      if (!startTimestamp) startTimestamp = timestamp;
      const progress = Math.min((timestamp - startTimestamp) / duration, 1);
      const current = Math.floor(progress * (end - start) + start);
      obj.innerHTML = current + (end > 0 ? "+" : "");
      if (progress < 1) {
        window.requestAnimationFrame(step);
      }
    };
    window.requestAnimationFrame(step);
  }

  // --- Tracks Accordion ---
  const panels = document.querySelectorAll(".track-panel");

  if (panels.length > 0) {
    panels.forEach((panel) => {
      panel.addEventListener("click", () => {
        removeActiveClasses();
        panel.classList.add("active");
      });
    });

    function removeActiveClasses() {
      panels.forEach((panel) => {
        panel.classList.remove("active");
      });
    }
  }
});
