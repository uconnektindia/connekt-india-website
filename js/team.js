/* =====================================================================
   Connekt India: team.js
   Tree connector lines (drawn between levels) + member bio modal
   ===================================================================== */
(function () {
  'use strict';
  var d = document, CK = window.CK || {};
  var $$ = function (s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); };
  var esc = CK.esc || function (s) { return String(s); };
  var dataEl = d.getElementById('teamData');
  var team = dataEl ? JSON.parse(dataEl.textContent) : [];
  var byId = {}; team.forEach(function (m) { byId[m.id] = m; });
  var reduce = d.documentElement.classList.contains('reduce-motion');

  /* ---------- Connector lines ---------- */
  var tree = d.querySelector('.tree');
  function draw() {
    if (!tree) return;
    var svg = tree.querySelector('.tree-svg');
    if (!svg) return;
    if (window.innerWidth <= 760) { svg.innerHTML = ''; return; }
    var box = tree.getBoundingClientRect();
    var rows = $$('.tree-row', tree);
    var defs = '<defs><linearGradient id="treeGrad" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#155dfc"/><stop offset="1" stop-color="#80a6ff"/></linearGradient></defs>';
    var paths = '';
    for (var i = 0; i < rows.length - 1; i++) {
      var up = $$('.member', rows[i]), down = $$('.member', rows[i + 1]);
      if (!up.length || !down.length) continue;
      // parent = the card closest to the centre of the upper row
      var centre = box.width / 2, parent = up[0], best = 1e9;
      up.forEach(function (c) { var r = c.getBoundingClientRect(); var dx = Math.abs(r.left + r.width / 2 - box.left - centre); if (dx < best) { best = dx; parent = c; } });
      var pr = parent.getBoundingClientRect();
      var px = pr.left + pr.width / 2 - box.left, pb = pr.bottom - box.top;
      var firstTop = Math.min.apply(null, down.map(function (c) { return c.getBoundingClientRect().top; })) - box.top;
      var mid = pb + (firstTop - pb) / 2;
      down.forEach(function (c) {
        var r = c.getBoundingClientRect(), cx = r.left + r.width / 2 - box.left, ct = r.top - box.top;
        var rad = Math.min(14, Math.abs(cx - px) / 2), dir = cx > px ? 1 : -1, p;
        if (Math.abs(cx - px) < 2) p = 'M' + px + ' ' + pb + ' V' + ct;
        else p = 'M' + px + ' ' + pb + ' V' + (mid - rad) + ' Q' + px + ' ' + mid + ' ' + (px + dir * rad) + ' ' + mid +
          ' H' + (cx - dir * rad) + ' Q' + cx + ' ' + mid + ' ' + cx + ' ' + (mid + rad) + ' V' + ct;
        paths += '<path d="' + p + '"/>';
      });
    }
    svg.setAttribute('viewBox', '0 0 ' + box.width + ' ' + box.height);
    svg.innerHTML = defs + paths;
    $$('path', svg).forEach(function (p, i) {
      var len = p.getTotalLength();
      p.style.strokeDasharray = len; p.style.strokeDashoffset = tree.classList.contains('lines-in') || reduce ? 0 : len;
      p.style.transition = 'stroke-dashoffset 1.4s cubic-bezier(.22,1,.36,1) ' + (0.25 + i * 0.12) + 's';
    });
  }
  if (tree) {
    draw();
    var rt; window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(draw, 120); });
    window.addEventListener('load', draw);
    if (d.fonts && d.fonts.ready) d.fonts.ready.then(draw);
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (en, obs) {
        en.forEach(function (e) {
          if (!e.isIntersecting) return;
          tree.classList.add('lines-in');
          setTimeout(function () { $$('.tree-svg path', tree).forEach(function (p) { p.style.strokeDashoffset = 0; }); }, 50);
          obs.disconnect();
        });
      }, { threshold: 0.15 }).observe(tree);
    } else tree.classList.add('lines-in');
    // re-measure once card reveal transforms finish
    setTimeout(draw, 1600);
  }

  /* ---------- Member modal ---------- */
  function photoHtml(m) {
    var img = m.photoUrl ? '<img src="' + esc(m.photoUrl) + '" alt="' + esc(m.name) + '" width="220" height="220" onerror="this.outerHTML=\'<span class=&quot;initials&quot;>' + esc(m.initials) + '</span>\'">' : '<span class="initials">' + esc(m.initials) + '</span>';
    return '<div class="frame"><div class="frame-in">' + img + '</div></div>';
  }
  function open(id) {
    var m = byId[id]; if (!m || !CK.openModal) return;
    var socials = '';
    if (m.linkedin) socials += '<a href="' + esc(m.linkedin) + '" target="_blank" rel="noopener" aria-label="LinkedIn">' + (CK.icons.linkedin || 'in') + '</a>';
    if (m.instagram) socials += '<a href="' + esc(m.instagram) + '" target="_blank" rel="noopener" aria-label="Instagram">' + (CK.icons.instagram || 'ig') + '</a>';
    if (m.email) socials += '<a href="mailto:' + esc(m.email) + '" aria-label="Email">' + (CK.icons.mail || '@') + '</a>';
    var bio = String(m.bio || '').split(/\n\s*\n/).map(function (p) { return '<p>' + esc(p) + '</p>'; }).join('');
    var html =
      '<div class="modal-head">' + photoHtml(m) +
      '<div><h2 id="modalTitle">' + esc(m.name) + '</h2><p class="modal-role">' + esc(m.role) + '</p>' +
      '<div class="modal-meta">' + (m.experience ? '<span style="white-space:nowrap">' + (CK.icons.award || '') + esc(m.experience) + ' experience</span>' : '') +
      '<span>' + (CK.icons.users || '') + ['', 'Leadership', 'Specialist', 'Production team'][m.level] + '</span></div></div></div>' +
      '<h3>About</h3>' + bio +
      '<h3>Skills</h3><div class="chips" style="justify-content:flex-start">' + (m.skills || []).map(function (s) { return '<span class="chip">' + esc(s) + '</span>'; }).join('') + '</div>' +
      (socials ? '<div class="modal-social">' + socials + '</div>' : '');
    CK.openModal(html);
  }
  $$('.member[data-id]').forEach(function (card) {
    card.addEventListener('click', function (e) { if (e.target.closest('.member-social a')) return; open(card.getAttribute('data-id')); });
    card.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(card.getAttribute('data-id')); } });
  });
})();
