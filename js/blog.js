/* =====================================================================
   Connekt India: blog.js
   List: search + category filter + pagination · Post: reading progress
   ===================================================================== */
(function () {
  'use strict';
  var d = document;
  var $ = function (s, c) { return (c || d).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); };

  /* ---------- Blog list ---------- */
  var grid = d.getElementById('blogGrid');
  if (grid) {
    var cards = $$('.post-card', grid), search = d.getElementById('blogSearch');
    var cats = $$('.filter-btn[data-cat]'), pager = d.getElementById('blogPager'), empty = d.getElementById('noResults');
    var PER = 6, page = 1, cat = 'all', q = '';
    function matches(c) {
      var okCat = cat === 'all' || c.getAttribute('data-cat') === cat;
      var okQ = !q || c.getAttribute('data-search').indexOf(q) !== -1;
      return okCat && okQ;
    }
    function render() {
      var list = cards.filter(matches), pages = Math.max(1, Math.ceil(list.length / PER));
      if (page > pages) page = pages;
      cards.forEach(function (c) { c.hidden = true; });
      list.slice((page - 1) * PER, page * PER).forEach(function (c, i) {
        c.hidden = false;
        if (c.animate && !d.documentElement.classList.contains('reduce-motion')) c.animate([{ opacity: 0, transform: 'translateY(18px)' }, { opacity: 1, transform: 'none' }], { duration: 500, delay: i * 50, easing: 'cubic-bezier(.22,1,.36,1)', fill: 'backwards' });
      });
      empty.style.display = list.length ? 'none' : 'block';
      pager.innerHTML = '';
      if (pages > 1) for (var p = 1; p <= pages; p++) {
        var b = d.createElement('button'); b.type = 'button'; b.textContent = p; b.setAttribute('aria-label', 'Page ' + p);
        if (p === page) { b.classList.add('is-active'); b.setAttribute('aria-current', 'page'); }
        (function (n) { b.addEventListener('click', function () { page = n; render(); grid.scrollIntoView({ behavior: 'smooth', block: 'start' }); }); })(p);
        pager.appendChild(b);
      }
    }
    var t; if (search) search.addEventListener('input', function () { clearTimeout(t); t = setTimeout(function () { q = search.value.trim().toLowerCase(); page = 1; render(); }, 150); });
    cats.forEach(function (b) { b.addEventListener('click', function () { cats.forEach(function (x) { x.classList.toggle('is-active', x === b); x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); }); cat = b.getAttribute('data-cat'); page = 1; render(); }); });
    render();
  }

  /* ---------- Reading progress ---------- */
  var bar = d.getElementById('readProgress'), article = d.getElementById('article');
  if (bar && article) {
    var tick = false;
    var upd = function () {
      var r = article.getBoundingClientRect(), total = r.height - window.innerHeight;
      var p = total > 0 ? Math.min(1, Math.max(0, -r.top / total)) : 1;
      bar.style.transform = 'scaleX(' + p.toFixed(4) + ')'; tick = false;
    };
    window.addEventListener('scroll', function () { if (!tick) { requestAnimationFrame(upd); tick = true; } }, { passive: true });
    upd();
  }
})();
