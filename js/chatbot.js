/* =====================================================================
   Connekt India: chatbot "Connekt" (assistant persona: Dibya)
   Keyword + fuzzy matching over data/chatbot-faq.json, Hinglish synonyms,
   sessionStorage memory, WhatsApp hand-off and mini lead form.
   ===================================================================== */
(function () {
  'use strict';
  var d = document, html = d.documentElement, CK = window.CK || {};
  var toggle = d.getElementById('chatToggle'), panel = d.getElementById('chatPanel');
  if (!toggle || !panel) return;
  var bodyEl = d.getElementById('chatBody'), form = d.getElementById('chatForm'), input = d.getElementById('chatText');
  var closeBtn = d.getElementById('chatClose'), dot = d.getElementById('chatDot'), chips = d.getElementById('chatChips');
  var KEY = 'ck_chat_v1', faq = null, history = [], greeted = false;
  var GREETING = 'Hi, I am Dibya 👋 How can I help you?';
  var FALLBACK = "I'm not sure about that, but our team can help! Chat with us on WhatsApp or leave your number.";
  var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };

  /* ---------- Synonyms & Hinglish → canonical words ---------- */
  var SYN = {
    pricing: ['price', 'prices', 'pricing', 'cost', 'costs', 'costing', 'kitna', 'kitne', 'kitni', 'rate', 'rates', 'charge', 'charges', 'fee', 'fees', 'budget', 'paisa', 'paise', 'quote', 'quotation', 'package', 'packages', 'plan', 'plans', 'kharcha', 'amount', 'expensive', 'cheap', 'affordable', 'dam', 'daam'],
    services: ['service', 'services', 'offer', 'offering', 'offerings', 'provide', 'kya karte', 'kaam', 'solutions'],
    contact: ['contact', 'reach', 'call', 'phone', 'number', 'mobile', 'email', 'mail', 'baat', 'connect', 'sampark'],
    location: ['where', 'office', 'address', 'location', 'located', 'kahan', 'kaha', 'visit', 'map', 'directions'],
    careers: ['career', 'careers', 'job', 'jobs', 'hiring', 'hire', 'vacancy', 'vacancies', 'naukri', 'opening', 'openings', 'resume', 'cv', 'apply', 'work with you', 'join'],
    intern: ['intern', 'internship', 'internships', 'trainee', 'fresher', 'student'],
    website: ['website', 'websites', 'site', 'web', 'webpage', 'landing', 'wordpress'],
    ecommerce: ['ecommerce', 'e-commerce', 'online store', 'shop', 'store', 'shopify', 'razorpay', 'payment gateway', 'cart'],
    software: ['software', 'app', 'apps', 'application', 'crm', 'erp', 'dashboard', 'automation', 'android', 'ios', 'mobile app'],
    social: ['instagram', 'insta', 'facebook', 'fb', 'social', 'page', 'handle', 'posts', 'post', 'content calendar'],
    reels: ['reel', 'reels', 'video', 'videos', 'shoot', 'shooting', 'editing', 'edit', 'youtube', 'shorts', 'motion'],
    ads: ['ads', 'ad', 'advertising', 'advertisement', 'meta ads', 'facebook ads', 'google ads', 'ppc', 'campaign', 'campaigns', 'boost', 'paid', 'promotion', 'promote'],
    seo: ['seo', 'ranking', 'rank', 'google search', 'search engine', 'first page', 'organic', 'keywords', 'gmb', 'google business'],
    branding: ['logo', 'brand', 'branding', 'identity', 'design', 'designing', 'graphic', 'graphics', 'packaging', 'visiting card', 'brochure'],
    time: ['how long', 'how soon', 'time', 'timeline', 'days', 'weeks', 'kitne din', 'kab tak', 'duration', 'deadline'],
    results: ['result', 'results', 'leads', 'sales', 'roi', 'growth'],
    hours: ['hours', 'timing', 'timings', 'open', 'working', 'samay', 'closed', 'sunday', 'saturday'],
    clients: ['client', 'clients', 'portfolio', 'worked', 'brands', 'customers', 'our work', 'case study', 'examples'],
    small: ['small', 'startup', 'startups', 'new business', 'local', 'shop owner', 'chhota'],
    own: ['own', 'owns', 'owner', 'owned', 'ownership', 'mine', 'rights', 'source code', 'transfer'],
    maintenance: ['maintenance', 'maintain', 'support', 'update', 'updates', 'backup', 'backups'],
    hosting: ['hosting', 'domain', 'server', 'ssl', 'business email', 'hostinger'],
    outside: ['outside', 'other city', 'other state', 'abroad', 'international', 'remote', 'online meeting', 'other cities', 'delhi', 'mumbai', 'bangalore'],
    process: ['process', 'steps', 'approach', 'method', 'how do you work', 'workflow'],
    reports: ['report', 'reports', 'reporting', 'analytics', 'insights', 'track'],
    consultation: ['consultation', 'consult', 'free', 'discovery', 'meeting', 'demo', 'discuss', 'call back', 'callback'],
    start: ['start', 'begin', 'get started', 'onboard', 'shuru', 'kaise'],
    monthly: ['monthly', 'retainer', 'per month', 'mahina', 'mahine', 'subscription'],
    about: ['who are you', 'what do you do', 'about', 'company', 'agency', 'connekt'],
  };
  var GREET = ['hi', 'hii', 'hiii', 'hello', 'helo', 'hey', 'heya', 'namaskar', 'namaste', 'namastey', 'good morning', 'good afternoon', 'good evening', 'yo', 'hola', 'sup'];
  var THANKS = ['thanks', 'thank you', 'thankyou', 'thx', 'ty', 'dhanyavad', 'dhanyawad', 'shukriya', 'great', 'awesome', 'ok thanks', 'cool'];
  var BYE = ['bye', 'goodbye', 'see you', 'tata', 'alvida', 'good night', 'gn'];

  function norm(s) { return ' ' + String(s).toLowerCase().replace(/[^a-z0-9ऀ-ॿ\s-]/g, ' ').replace(/\s+/g, ' ').trim() + ' '; }
  function has(text, phrase) { return text.indexOf(' ' + phrase + ' ') !== -1; }
  function lev(a, b) {
    if (Math.abs(a.length - b.length) > 2) return 9;
    var m = [], i, j;
    for (i = 0; i <= b.length; i++) m[i] = [i];
    for (j = 0; j <= a.length; j++) m[0][j] = j;
    for (i = 1; i <= b.length; i++) for (j = 1; j <= a.length; j++)
      m[i][j] = b[i - 1] === a[j - 1] ? m[i - 1][j - 1] : Math.min(m[i - 1][j - 1] + 1, m[i][j - 1] + 1, m[i - 1][j] + 1);
    return m[b.length][a.length];
  }
  function fuzzyHas(text, word) {
    if (has(text, word)) return true;
    if (word.indexOf(' ') !== -1 || word.length < 4) return false;
    var toks = text.trim().split(' '), max = word.length >= 7 ? 2 : 1;
    for (var i = 0; i < toks.length; i++) if (toks[i].length >= 3 && lev(toks[i], word) <= max) return true;
    return false;
  }
  /* add canonical concept words to the text (e.g. "kitna" → also "pricing") */
  function expand(text) {
    var t = norm(text), extra = '';
    Object.keys(SYN).forEach(function (k) {
      for (var i = 0; i < SYN[k].length; i++) { if (fuzzyHas(t, SYN[k][i])) { extra += ' ' + k; break; } }
    });
    return norm(t + extra);
  }
  function anyOf(text, list) { for (var i = 0; i < list.length; i++) if (has(text, list[i])) return true; return false; }

  function findAnswer(raw) {
    var t = norm(raw), words = t.trim().split(' ');
    if (anyOf(t, GREET) && words.length <= 4) return { a: 'Hello! 👋 I am Dibya from Connekt India. Ask me about our services, pricing, work or careers.' };
    if (anyOf(t, BYE)) return { a: 'Thanks for stopping by! 👋 If you need anything, we are one WhatsApp message away.', actions: ['whatsapp'] };
    if (anyOf(t, THANKS) && words.length <= 5) return { a: "You're welcome! 😊 Anything else I can help you with?" };
    var x = expand(raw), best = null, bestScore = 0;
    (faq || []).forEach(function (item) {
      var score = 0;
      (item.keywords || []).forEach(function (k) {
        k = String(k).toLowerCase();
        if (has(x, k)) score += k.indexOf(' ') !== -1 ? 3 : 2;
        else if (fuzzyHas(x, k)) score += 1;
      });
      if (score > bestScore) { bestScore = score; best = item; }
    });
    return bestScore >= 2 ? best : null;
  }

  function fill(s) {
    return esc(s)
      .replace(/\{\{hours\}\}/g, esc(CK.hours || ''))
      .replace(/\{\{phone\}\}/g, esc(CK.phone || ''))
      .replace(/\{\{email\}\}/g, esc(CK.email || ''))
      .replace(/([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/gi, '<a href="mailto:$1">$1</a>');
  }

  /* ---------- Rendering ---------- */
  function actionHtml(list) {
    if (!list || !list.length) return '';
    var map = {
      whatsapp: '<a class="btn btn-wa" href="' + esc(CK.wa) + '" target="_blank" rel="noopener">WhatsApp us ↗</a>',
      contact: '<a class="btn btn-primary" href="' + CK.url('contact') + '">Contact form ↗</a>',
      career: '<a class="btn btn-primary" href="' + CK.url('contact?type=career') + '">Apply now ↗</a>',
      services: '<a class="btn btn-primary" href="' + CK.url('services') + '">View services ↗</a>',
      work: '<a class="btn btn-primary" href="' + CK.url('work') + '">See our work ↗</a>',
      map: '<a class="btn btn-primary" href="https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent('446, Saheed Nagar, Bhubaneswar, Odisha 751007') + '" target="_blank" rel="noopener">Directions ↗</a>',
      lead: 'LEAD'
    };
    var out = '';
    list.forEach(function (k) {
      if (k === 'lead') return;
      if (map[k]) out += map[k];
    });
    var lead = list.indexOf('lead') !== -1 ? leadForm() : '';
    return (out ? '<div class="msg-actions">' + out + '</div>' : '') + lead;
  }
  function leadForm() {
    return '<form class="chat-lead" data-lead novalidate>' +
      '<input name="name" type="text" placeholder="Your name" required maxlength="80" autocomplete="name">' +
      '<input name="phone" type="tel" placeholder="Phone (10 digits)" required maxlength="15" inputmode="tel" autocomplete="tel">' +
      '<button class="btn btn-primary" type="submit">Request a call back ↗</button></form>';
  }
  function addMsg(who, text, actions, save) {
    var el = d.createElement('div');
    el.className = 'msg msg-' + who;
    el.innerHTML = (who === 'bot' ? fill(text) : esc(text)) + (who === 'bot' ? actionHtml(actions) : '');
    bodyEl.appendChild(el);
    bodyEl.scrollTop = bodyEl.scrollHeight;
    if (save !== false) { history.push({ w: who, t: text, a: actions || null }); persist(); }
    return el;
  }
  function typing(ms, cb) {
    var t = d.createElement('div'); t.className = 'msg msg-bot typing'; t.innerHTML = '<i></i><i></i><i></i>';
    bodyEl.appendChild(t); bodyEl.scrollTop = bodyEl.scrollHeight;
    setTimeout(function () { t.remove(); cb(); }, ms);
  }
  function persist() { try { sessionStorage.setItem(KEY, JSON.stringify({ h: history.slice(-40), g: greeted })); } catch (e) {} }
  function restore() {
    try {
      var s = JSON.parse(sessionStorage.getItem(KEY) || 'null');
      if (s && s.h) { history = s.h; greeted = !!s.g; history.forEach(function (m) { addMsg(m.w, m.t, m.a, false); }); }
    } catch (e) {}
  }

  function loadFaq() {
    if (faq) return Promise.resolve(faq);
    return fetch(CK.url('data/chatbot-faq.json'), { credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (j) { faq = Array.isArray(j) ? j : (j.items || []); return faq; })
      .catch(function () { faq = []; return faq; });
  }

  function reply(q) {
    loadFaq().then(function () {
      var hit = findAnswer(q);
      typing(500 + Math.min(900, q.length * 12), function () {
        if (hit) {
          addMsg('bot', hit.a, hit.actions || null);
        } else {
          // ---- Optional AI hook (disabled). Connect your own server endpoint here later. ----
          // askAI(q).then(function (ans) { addMsg('bot', ans); }).catch(function () { addMsg('bot', FALLBACK, ['whatsapp', 'lead']); });
          addMsg('bot', FALLBACK, ['whatsapp', 'lead']);
        }
      });
    });
  }
  /*
  function askAI(q) {
    // Example: POST to your own PHP proxy (never expose an API key in JS)
    return fetch(CK.url('api/chatbot-ai.php'), { method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ q: q, csrf: CK.csrf() }) }).then(function (r) { return r.json(); }).then(function (j) { return j.answer; });
  }
  */

  /* ---------- Open / close ---------- */
  function setOpen(open) {
    html.classList.toggle('chat-open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    toggle.setAttribute('aria-label', open ? 'Close chat' : 'Open chat with Connekt');
    panel.setAttribute('aria-hidden', open ? 'false' : 'true');
    if (window.innerWidth <= 640) d.body.classList.toggle('is-locked', open);
    if (open) {
      dot.classList.add('is-hidden');
      try { sessionStorage.setItem('ck_chat_seen', '1'); } catch (e) {}
      loadFaq();
      if (!greeted) { greeted = true; persist(); typing(900, function () { addMsg('bot', GREETING); }); }
      if (window.innerWidth > 640) setTimeout(function () { input.focus({ preventScroll: true }); }, 350);
    }
  }
  toggle.addEventListener('click', function () { setOpen(!html.classList.contains('chat-open')); });
  closeBtn.addEventListener('click', function () { setOpen(false); toggle.focus(); });
  d.addEventListener('keydown', function (e) { if (e.key === 'Escape' && html.classList.contains('chat-open')) setOpen(false); });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var q = input.value.trim(); if (!q) return;
    input.value = '';
    addMsg('user', q); reply(q);
  });
  chips.addEventListener('click', function (e) {
    var b = e.target.closest('button'); if (!b) return;
    if (b.hasAttribute('data-wa')) { addMsg('user', 'Talk on WhatsApp'); window.open(CK.wa, '_blank', 'noopener'); addMsg('bot', 'Opening WhatsApp for you… 💬 If it did not open, tap below.', ['whatsapp']); return; }
    addMsg('user', b.textContent); reply(b.getAttribute('data-q'));
  });

  /* ---------- Lead form inside chat ---------- */
  bodyEl.addEventListener('submit', function (e) {
    var f = e.target.closest('[data-lead]'); if (!f) return;
    e.preventDefault();
    var name = f.name.value.trim(), phone = f.phone.value.replace(/\D/g, '').replace(/^(91|0)(?=\d{10}$)/, '');
    if (name.length < 2) { f.name.focus(); f.name.style.borderColor = '#fb7185'; return; }
    if (!/^[6-9]\d{9}$/.test(phone)) { f.phone.focus(); f.phone.style.borderColor = '#fb7185'; return; }
    var lastQ = ''; for (var i = history.length - 1; i >= 0; i--) if (history[i].w === 'user') { lastQ = history[i].t; break; }
    var btn = f.querySelector('button'); btn.disabled = true; btn.textContent = 'Sending…';
    var fd = new FormData(); fd.append('name', name); fd.append('phone', phone); fd.append('question', lastQ); fd.append('csrf', CK.csrf()); fd.append('website', '');
    fetch(CK.url('api/chatbot-log.php'), { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        f.remove();
        addMsg('bot', j.success ? 'Thank you, ' + name + '! 🙌 Our team will call you shortly on +91 ' + phone + '.' : (j.message || 'Sorry, that did not go through. Please WhatsApp us.'), j.success ? null : ['whatsapp']);
      })
      .catch(function () { btn.disabled = false; btn.textContent = 'Request a call back ↗'; addMsg('bot', 'Network issue. Please try again or WhatsApp us.', ['whatsapp']); });
  });

  /* ---------- Init ---------- */
  CK.csrf = CK.csrf || function () { var m = d.querySelector('meta[name="csrf-token"]'); return m ? m.content : ''; };
  restore();
  try { if (sessionStorage.getItem('ck_chat_seen')) dot.classList.add('is-hidden'); } catch (e) {}
})();
