/* =====================================================================
   Connekt India: animations.js
   Scroll reveals (IntersectionObserver core, works even if a CDN is blocked)
   + word-by-word headings + count-up + GSAP ScrollTrigger enhancements
   ===================================================================== */
(function () {
  'use strict';
  var d = document, html = d.documentElement, CK = window.CK || {};
  var reduce = html.classList.contains('reduce-motion');
  var $$ = function (s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); };

  /* ---------- 1. Split headings into words ---------- */
  function splitWords(el) {
    var wi = 0;
    (function walk(node) {
      Array.prototype.slice.call(node.childNodes).forEach(function (n) {
        if (n.nodeType === 3) {
          var parts = n.textContent.split(/(\s+)/), frag = d.createDocumentFragment();
          parts.forEach(function (p) {
            if (!p) return;
            if (/^\s+$/.test(p)) { frag.appendChild(d.createTextNode(' ')); return; }
            var w = d.createElement('span'); w.className = 'w';
            var i = d.createElement('i'); i.textContent = p; i.style.setProperty('--wi', wi++);
            w.appendChild(i); frag.appendChild(w);
          });
          node.replaceChild(frag, n);
        } else if (n.nodeType === 1 && n.tagName !== 'BR') { walk(n); }
      });
    })(el);
    el.setAttribute('aria-label', el.textContent.replace(/\s+/g, ' ').trim());
  }
  if (!reduce) $$('[data-split]').forEach(splitWords);

  /* ---------- 2. Stagger children ---------- */
  $$('[data-stagger]').forEach(function (group) {
    var step = parseInt(group.getAttribute('data-stagger'), 10) || 90;
    $$('[data-reveal]', group).forEach(function (el, i) { if (!el.style.getPropertyValue('--rd')) el.style.setProperty('--rd', i * step); });
  });

  if (reduce || !('IntersectionObserver' in window)) {
    $$('[data-reveal], [data-inview], [data-split]').forEach(function (el) { el.classList.add('is-in'); });
    $$('[data-count]').forEach(function (el) { el.textContent = el.getAttribute('data-count'); });
    return;
  }

  /* ---------- 3. Reveal on scroll ---------- */
  html.classList.add('anim-ready');

  function reveal(el) {
    if (el.classList.contains('is-in')) return;
    el.classList.add('is-in');
    $$('[data-count]', el).concat(el.hasAttribute('data-count') ? [el] : []).forEach(function (c) {
      if (!c._counted) { c._counted = true; CK.countUp ? CK.countUp(c) : (c.textContent = c.getAttribute('data-count')); }
    });
    if (el.hasAttribute('data-reveal')) {
      var delay = parseInt(el.style.getPropertyValue('--rd'), 10) || 0;
      // hand the element back to its own hover transitions once the reveal is done
      setTimeout(function () { el.removeAttribute('data-reveal'); }, 1300 + delay);
    }
  }

  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (!e.isIntersecting) return;
      var el = e.target;
      if (el.closest('.hero') && !CK.ready) return; // hero waits for preloader
      reveal(el); io.unobserve(el);
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });

  $$('[data-reveal], [data-inview], [data-split]').forEach(function (el) { io.observe(el); });

  function revealHero() {
    $$('.hero [data-reveal], .hero [data-split], .hero [data-inview], .page-hero [data-reveal], .page-hero [data-split]').forEach(function (el) {
      var r = el.getBoundingClientRect();
      if (r.top < window.innerHeight && r.bottom > 0) { reveal(el); io.unobserve(el); }
    });
  }
  if (CK.ready) revealHero(); else d.addEventListener('ck:ready', revealHero);

  /* ---------- 4. GSAP + ScrollTrigger extras (progressive enhancement) ---------- */
  window.addEventListener('load', function () {
    if (!window.gsap || !window.ScrollTrigger) return;
    var gsap = window.gsap; gsap.registerPlugin(window.ScrollTrigger);

    // hero media drifts + fades as you scroll away
    var media = d.querySelector('.hero-media');
    if (media) gsap.to(media, { yPercent: 12, scale: 1.06, ease: 'none', scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true } });
    var tiles = d.querySelector('.hero .tiles-wrap');
    if (tiles) gsap.to(tiles, { yPercent: -10, ease: 'none', scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom top', scrub: true } });

    // glow blobs float with scroll
    $$('.glow-blob').forEach(function (b) {
      gsap.fromTo(b, { yPercent: -15 }, { yPercent: 25, ease: 'none', scrollTrigger: { trigger: b.parentElement, start: 'top bottom', end: 'bottom top', scrub: true } });
    });

    // service visuals: gentle parallax on the glass orb
    $$('.svc-visual').forEach(function (v) {
      var orb = v.querySelector('.svc-orb'); if (!orb) return;
      gsap.fromTo(orb, { y: 40, rotate: -6 }, { y: -40, rotate: 6, ease: 'none', scrollTrigger: { trigger: v, start: 'top bottom', end: 'bottom top', scrub: true } });
    });

    // process line scrubs with scroll on desktop
    var pl = d.querySelector('.process-line i');
    if (pl && window.innerWidth > 900) {
      gsap.fromTo(pl, { scaleX: 0 }, { scaleX: 1, ease: 'none', scrollTrigger: { trigger: '.process', start: 'top 80%', end: 'top 30%', scrub: true } });
    }
    window.ScrollTrigger.refresh();
  });
})();
