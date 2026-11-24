/* =====================================================================
   Connekt India: main.js
   Preloader · header · mobile menu · hero video + tiles · modal · FAQ
   testimonials · counters · cursor · tilt · Lottie · dev side-gap check
   ===================================================================== */
(function () {
  'use strict';
  var d = document, html = d.documentElement, body = d.body;
  var CK = window.CK = window.CK || {};
  var reduce = html.classList.contains('reduce-motion');
  var fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  CK.$ = function (s, c) { return (c || d).querySelector(s); };
  CK.$$ = function (s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); };
  CK.url = function (p) { return (CK.base || '') + '/' + String(p || '').replace(/^\//, ''); };
  CK.esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };

  /* ---------- Preloader ---------- */
  (function preloader() {
    var pl = d.getElementById('preloader');
    var full = html.classList.contains('pl-full');
    var start = performance.now();
    var done = false;
    function finish() {
      if (done) return; done = true;
      if (pl) pl.classList.add('is-done');
      CK.ready = true;
      d.dispatchEvent(new CustomEvent('ck:ready'));
      setTimeout(function () { if (pl && pl.parentNode) pl.parentNode.removeChild(pl); }, 800);
    }
    if (!pl) { finish(); return; }
    if (full) {
      // type the tagline letter by letter
      var tag = pl.querySelector('.pl-tag');
      var text = tag ? tag.getAttribute('data-text') : '';
      var caret = d.createElement('span'); caret.className = 'caret';
      if (tag) {
        tag.appendChild(caret);
        var i = 0;
        setTimeout(function type() {
          if (i <= text.length) {
            tag.textContent = text.slice(0, i); tag.appendChild(caret); i++;
            setTimeout(type, reduce ? 0 : 34);
          }
        }, reduce ? 0 : 850);
      }
      var minShow = reduce ? 200 : 1900, maxShow = 2500;
      var tryFinish = function () {
        var elapsed = performance.now() - start;
        setTimeout(finish, Math.max(0, minShow - elapsed));
      };
      if (d.readyState === 'complete') tryFinish(); else window.addEventListener('load', tryFinish);
      setTimeout(finish, maxShow);
    } else {
      setTimeout(finish, 400);
    }
  })();

  /* ---------- Header: shrink, hide on scroll down, show on scroll up ---------- */
  (function header() {
    var h = d.getElementById('siteHeader'); if (!h) return;
    var last = window.scrollY, ticking = false;
    function update() {
      var y = window.scrollY;
      h.classList.toggle('is-scrolled', y > 20);
      if (!html.classList.contains('menu-open')) {
        if (y > last + 6 && y > 220) h.classList.add('is-hidden');
        else if (y < last - 6 || y < 220) h.classList.remove('is-hidden');
      }
      last = y; ticking = false;
    }
    window.addEventListener('scroll', function () { if (!ticking) { requestAnimationFrame(update); ticking = true; } }, { passive: true });
    update();
  })();

  /* ---------- Mobile menu ---------- */
  (function menu() {
    var btn = d.getElementById('burger'), mm = d.getElementById('mobileMenu');
    if (!btn || !mm) return;
    function set(open) {
      html.classList.toggle('menu-open', open);
      body.classList.toggle('is-locked', open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      btn.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      mm.setAttribute('aria-hidden', open ? 'false' : 'true');
    }
    btn.addEventListener('click', function () { set(!html.classList.contains('menu-open')); });
    CK.$$('a', mm).forEach(function (a) { a.addEventListener('click', function () { set(false); }); });
    d.addEventListener('keydown', function (e) { if (e.key === 'Escape' && html.classList.contains('menu-open')) set(false); });
    window.addEventListener('resize', function () { if (window.innerWidth > 1080) set(false); });
  })();

  /* ---------- Hero video: slow motion, data-saver aware ---------- */
  (function heroVideo() {
    var v = d.getElementById('heroVideo'); if (!v) return;
    var c = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    var slow = c && (c.saveData || /(^|-)2g$/.test(c.effectiveType || ''));
    if (slow || reduce) { v.parentNode.removeChild(v); return; }   // poster only
    var src = window.innerWidth < 768 ? v.getAttribute('data-src-mobile') : v.getAttribute('data-src');
    v.src = src;
    var slowDown = function () { try { v.defaultPlaybackRate = 0.5; v.playbackRate = 0.5; } catch (e) {} };
    v.addEventListener('loadedmetadata', slowDown);
    v.addEventListener('play', slowDown);
    v.addEventListener('playing', slowDown);
    slowDown();
    var p = v.play(); if (p && p.catch) p.catch(function () {});
    // pause when off-screen to save battery
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (en) {
        en.forEach(function (e) { if (e.isIntersecting) { var pp = v.play(); if (pp && pp.catch) pp.catch(function () {}); slowDown(); } else v.pause(); });
      }).observe(v);
    }
  })();

  /* ---------- Animated tiles: random flips + mouse parallax ---------- */
  CK.$$('.tiles').forEach(function (box) {
    var tiles = CK.$$('.tile', box), layer = CK.$('.tiles-layer', box);
    if (!tiles.length || reduce) return;
    setInterval(function () {
      if (d.hidden) return;
      var t = tiles[Math.floor(Math.random() * tiles.length)];
      t.classList.toggle('flip');
    }, 160);
    if (fine && layer) {
      var wrap = box.closest('.hero') || box;
      wrap.addEventListener('mousemove', function (e) {
        var r = box.getBoundingClientRect();
        var x = (e.clientX - (r.left + r.width / 2)) / r.width;
        var y = (e.clientY - (r.top + r.height / 2)) / r.height;
        layer.style.transform = 'translate3d(' + (x * -22).toFixed(1) + 'px,' + (y * -22).toFixed(1) + 'px,0)';
        var plate = CK.$('.tile-plate', box);
        if (plate) plate.style.translate = (x * 12).toFixed(1) + 'px ' + (y * 12).toFixed(1) + 'px';
      });
      wrap.addEventListener('mouseleave', function () { layer.style.transform = ''; });
    }
  });

  /* ---------- Modal ---------- */
  (function modal() {
    var m = d.getElementById('modal'); if (!m) return;
    var content = d.getElementById('modalContent'), lastFocus = null;
    CK.openModal = function (htmlStr) {
      lastFocus = d.activeElement;
      content.innerHTML = htmlStr;
      m.classList.add('is-open'); m.setAttribute('aria-hidden', 'false');
      body.classList.add('is-locked');
      var card = CK.$('.modal-card', m); if (card) card.scrollTop = 0;
      setTimeout(function () { var x = CK.$('.modal-x', m); if (x) x.focus(); }, 60);
    };
    CK.closeModal = function () {
      m.classList.remove('is-open'); m.setAttribute('aria-hidden', 'true');
      if (!html.classList.contains('menu-open')) body.classList.remove('is-locked');
      if (lastFocus && lastFocus.focus) lastFocus.focus();
    };
    m.addEventListener('click', function (e) { if (e.target.closest('[data-close]')) CK.closeModal(); });
    d.addEventListener('keydown', function (e) { if (e.key === 'Escape' && m.classList.contains('is-open')) CK.closeModal(); });
  })();

  /* ---------- FAQ accordion ---------- */
  CK.$$('.faq').forEach(function (faq) {
    CK.$$('.faq-q', faq).forEach(function (q) {
      q.addEventListener('click', function () {
        var item = q.closest('.faq-item'), open = item.classList.contains('is-open');
        CK.$$('.faq-item.is-open', faq).forEach(function (o) { o.classList.remove('is-open'); CK.$('.faq-q', o).setAttribute('aria-expanded', 'false'); });
        if (!open) { item.classList.add('is-open'); q.setAttribute('aria-expanded', 'true'); }
      });
    });
  });

  /* ---------- Testimonials carousel (auto-slide, swipe) ---------- */
  CK.$$('[data-carousel]').forEach(function (car) {
    var track = CK.$('.testi-track', car), slides = CK.$$('.testi-slide', car);
    var dotsBox = CK.$('.dots', car), idx = 0, timer = null;
    if (!track || !slides.length) return;
    function per() { var w = car.clientWidth; return w >= 1000 ? 3 : (w >= 680 ? 2 : 1); }
    function pages() { return Math.max(1, slides.length - per() + 1); }
    function buildDots() {
      dotsBox.innerHTML = '';
      for (var i = 0; i < pages(); i++) {
        var b = d.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', 'Go to slide ' + (i + 1));
        (function (n) { b.addEventListener('click', function () { go(n); restart(); }); })(i);
        dotsBox.appendChild(b);
      }
    }
    function go(n) {
      var p = pages(); idx = (n + p) % p;
      car.style.setProperty('--per', per());
      track.style.transform = 'translate3d(' + (-idx * (100 / per())) + '%,0,0)';
      CK.$$('button', dotsBox).forEach(function (b, i) { b.classList.toggle('is-active', i === idx); });
    }
    function restart() { clearInterval(timer); if (!reduce) timer = setInterval(function () { go(idx + 1); }, 5000); }
    var prev = CK.$('[data-prev]', car), next = CK.$('[data-next]', car);
    if (prev) prev.addEventListener('click', function () { go(idx - 1); restart(); });
    if (next) next.addEventListener('click', function () { go(idx + 1); restart(); });
    car.addEventListener('mouseenter', function () { clearInterval(timer); });
    car.addEventListener('mouseleave', restart);
    var sx = 0;
    track.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; clearInterval(timer); }, { passive: true });
    track.addEventListener('touchend', function (e) { var dx = e.changedTouches[0].clientX - sx; if (Math.abs(dx) > 40) go(idx + (dx < 0 ? 1 : -1)); restart(); });
    var rt; window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(function () { buildDots(); go(Math.min(idx, pages() - 1)); }, 150); });
    buildDots(); go(0); restart();
  });

  /* ---------- Count-up numbers ---------- */
  CK.countUp = function (el) {
    var target = parseFloat(el.getAttribute('data-count')) || 0;
    var dec = (String(target).split('.')[1] || '').length;
    if (reduce) { el.textContent = target.toFixed(dec); return; }
    var dur = 1800, t0 = null;
    function step(t) {
      if (!t0) t0 = t;
      var p = Math.min(1, (t - t0) / dur), e = 1 - Math.pow(1 - p, 4);
      el.textContent = (target * e).toFixed(dec);
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  };

  /* ---------- 3D tilt + glow on cards (desktop only) ---------- */
  if (fine && !reduce) {
    CK.$$('[data-tilt]').forEach(function (card) {
      var glow = d.createElement('span'); glow.className = 'card-glow'; card.appendChild(glow);
      card.addEventListener('mousemove', function (e) {
        var r = card.getBoundingClientRect();
        var x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
        card.style.setProperty('--mx', (x * 100) + '%'); card.style.setProperty('--my', (y * 100) + '%');
        card.style.transform = 'perspective(900px) rotateX(' + ((0.5 - y) * 7).toFixed(2) + 'deg) rotateY(' + ((x - 0.5) * 9).toFixed(2) + 'deg) translateY(-6px)';
      });
      card.addEventListener('mouseleave', function () { card.style.transform = ''; });
    });
  }

  /* ---------- Custom cursor (desktop only) ---------- */
  if (fine && !reduce) {
    var cur = CK.$('.cursor');
    if (cur) {
      html.classList.add('has-cursor');
      var dot = CK.$('.cursor-dot', cur), ring = CK.$('.cursor-ring', cur);
      var mx = -100, my = -100, rx = -100, ry = -100;
      d.addEventListener('mousemove', function (e) { mx = e.clientX; my = e.clientY; dot.style.transform = 'translate3d(' + mx + 'px,' + my + 'px,0)'; cur.classList.remove('is-hidden'); }, { passive: true });
      d.addEventListener('mouseleave', function () { cur.classList.add('is-hidden'); });
      (function loop() { rx += (mx - rx) * 0.18; ry += (my - ry) * 0.18; ring.style.transform = 'translate3d(' + rx.toFixed(1) + 'px,' + ry.toFixed(1) + 'px,0)'; requestAnimationFrame(loop); })();
      d.addEventListener('mouseover', function (e) { cur.classList.toggle('is-hover', !!e.target.closest('a, button, [data-tilt], .member, input, select, textarea, label')); });
    }
  }

  /* ---------- WhatsApp tooltip hint once ---------- */
  (function () {
    var wa = d.getElementById('fabWa'); if (!wa || !fine) return;
    setTimeout(function () { wa.classList.add('show-tip'); setTimeout(function () { wa.classList.remove('show-tip'); }, 3500); }, 4000);
  })();

  /* ---------- Lottie: play [data-lottie] when in view / on hover ---------- */
  CK.lottie = function (el, opts) {
    if (!window.lottie || !el) return null;
    try {
      return window.lottie.loadAnimation(Object.assign({ container: el, renderer: 'svg', loop: false, autoplay: true, path: el.getAttribute('data-lottie') }, opts || {}));
    } catch (e) { return null; }
  };
  window.addEventListener('load', function () {
    CK.$$('[data-lottie]:not([data-lottie-manual])').forEach(function (el) {
      if (!window.lottie) return;
      var hover = el.hasAttribute('data-lottie-hover');
      var anim = CK.lottie(el, { autoplay: false, loop: !hover });
      if (!anim) return;
      el.classList.add('has-lottie');
      var fb = el.querySelector('.lottie-fallback'); if (fb) fb.remove();
      if (hover) {
        var host = el.closest('.card, a, button') || el;
        host.addEventListener('mouseenter', function () { anim.goToAndPlay(0, true); });
      }
      new IntersectionObserver(function (en) { en.forEach(function (e) { if (e.isIntersecting) anim.play(); else if (!hover) anim.pause(); }); }).observe(el);
    });
  });

  /* ---------- Share / copy link ---------- */
  CK.$$('[data-copy]').forEach(function (b) {
    b.addEventListener('click', function () {
      var u = b.getAttribute('data-copy') || location.href;
      if (navigator.clipboard) navigator.clipboard.writeText(u).then(function () { b.setAttribute('aria-label', 'Link copied'); b.classList.add('copied'); });
    });
  });

  /* ---------- Dev-only: side gap / horizontal scroll check ---------- */
  if (CK.dev) {
    var check = function () {
      var sw = d.documentElement.scrollWidth, iw = window.innerWidth, cw = d.documentElement.clientWidth;
      var ok = sw <= cw;
      console[ok ? 'log' : 'warn']('[CK side-gap check] scrollWidth=' + sw + ' clientWidth=' + cw + ' innerWidth=' + iw + (ok ? ' ✓ no horizontal overflow' : ' ✗ OVERFLOW'));
      if (!ok) CK.$$('body *').forEach(function (el) { var r = el.getBoundingClientRect(); if (r.right > cw + 1 && getComputedStyle(el).position !== 'fixed') console.warn('overflowing →', el); });
    };
    window.addEventListener('load', function () { setTimeout(check, 800); });
    window.addEventListener('resize', function () { clearTimeout(CK._gc); CK._gc = setTimeout(check, 400); });
  }
})();
