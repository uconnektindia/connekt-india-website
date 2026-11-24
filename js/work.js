/* =====================================================================
   Connekt India: work.js (portfolio filters + case-study modal)
   ===================================================================== */
(function () {
  'use strict';
  var d = document, CK = window.CK || {};
  var $$ = function (s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); };
  var esc = CK.esc || function (s) { return String(s); };
  var dataEl = d.getElementById('workData');
  var work = dataEl ? JSON.parse(dataEl.textContent) : [];
  var byId = {}; work.forEach(function (w) { byId[w.id] = w; });

  // filters
  var btns = $$('.filter-btn');
  btns.forEach(function (b) {
    b.addEventListener('click', function () {
      var f = b.getAttribute('data-filter');
      btns.forEach(function (x) { var on = x === b; x.classList.toggle('is-active', on); x.setAttribute('aria-pressed', on ? 'true' : 'false'); });
      $$('.work-card[data-cats]').forEach(function (c, i) {
        var show = f === 'all' || c.getAttribute('data-cats').split(' ').indexOf(f) !== -1;
        c.classList.toggle('is-filtered', !show);
        if (show && c.animate && !d.documentElement.classList.contains('reduce-motion')) {
          c.animate([{ opacity: 0, transform: 'translateY(20px) scale(.97)' }, { opacity: 1, transform: 'none' }], { duration: 500, delay: i * 40, easing: 'cubic-bezier(.22,1,.36,1)', fill: 'backwards' });
        }
      });
    });
  });

  // case study modal
  function open(id) {
    var w = byId[id]; if (!w || !CK.openModal) return;
    var gallery = (w.gallery && w.gallery.length ? w.gallery : [null, null, null]).slice(0, 3).map(function (g, i) {
      return '<div>' + (g ? '<img src="' + esc(CK.url(g)) + '" alt="' + esc(w.client) + ' project image ' + (i + 1) + '" loading="lazy">' : '<span style="font-weight:800;opacity:.85">' + esc(w.client) + '</span>') + '</div>';
    }).join('');
    var results = (w.results || []).map(function (r) { return '<div><strong>' + esc(r.value) + '</strong><span>' + esc(r.label) + '</span></div>'; }).join('');
    CK.openModal(
      '<p class="eyebrow" style="color:#155dfc">' + esc(w.industry) + ' · ' + esc((w.servicesLabel || []).join(', ')) + '</p>' +
      '<h2 id="modalTitle">' + esc(w.client) + '</h2><p class="modal-role">' + esc(w.title) + '</p>' +
      '<h3>The challenge</h3><p>' + esc(w.challenge) + '</p>' +
      '<h3>Our solution</h3><p>' + esc(w.solution) + '</p>' +
      '<h3>Results</h3>' + (results ? '<div class="results">' + results + '</div>' : '') +
      (w.resultsNote ? '<p style="margin-top:10px;font-size:13px;color:#586174">' + esc(w.resultsNote) + '</p>' : '') +
      '<h3>Gallery</h3><div class="gallery">' + gallery + '</div>' +
      '<div class="btn-row" style="margin-top:26px"><a class="btn btn-primary" href="' + CK.url('contact') + '">Start a similar project <span class="arr">↗</span></a></div>'
    );
  }
  $$('.work-card[data-id]').forEach(function (c) { c.addEventListener('click', function () { open(c.getAttribute('data-id')); }); });
  var hash = location.hash.replace('#', ''); if (byId[hash]) setTimeout(function () { open(hash); }, 600);
})();
