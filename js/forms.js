/* =====================================================================
   Connekt India: forms.js
   Contact (Project Enquiry | Career) + newsletter: validation, upload,
   reCAPTCHA v3 (optional), animated success screen
   ===================================================================== */
(function () {
  'use strict';
  var d = document, CK = window.CK || {};
  var $ = function (s, c) { return (c || d).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || d).querySelectorAll(s)); };
  CK.csrf = CK.csrf || function () { var m = $('meta[name="csrf-token"]'); return m ? m.content : ''; };

  var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  function cleanPhone(v) { return String(v || '').replace(/\D/g, '').replace(/^(91|0)(?=\d{10}$)/, ''); }
  function validPhone(v) { return /^[6-9]\d{9}$/.test(cleanPhone(v)); }
  function validUrl(v) { if (!v) return true; try { var u = new URL(/^https?:\/\//i.test(v) ? v : 'https://' + v); return /\./.test(u.hostname); } catch (e) { return false; } }

  function setErr(field, msg) {
    var wrap = field.closest('.field'); if (!wrap) return;
    var err = $('.err', wrap);
    if (msg) { wrap.classList.add('has-error'); if (err) err.textContent = msg; field.setAttribute('aria-invalid', 'true'); }
    else { wrap.classList.remove('has-error'); if (err) err.textContent = ''; field.removeAttribute('aria-invalid'); }
  }
  function check(field) {
    if (field.disabled || field.closest('[hidden]')) return true;
    var v = (field.type === 'checkbox') ? field.checked : field.value.trim();
    var req = field.hasAttribute('required'), rule = field.getAttribute('data-rule'), msg = '';
    if (req && (field.type === 'checkbox' ? !v : v === '')) msg = field.getAttribute('data-msg') || 'This field is required.';
    else if (v && rule === 'email' && !EMAIL_RE.test(v)) msg = 'Please enter a valid email address.';
    else if (v && rule === 'phone' && !validPhone(v)) msg = 'Enter a valid 10-digit Indian mobile number.';
    else if (v && rule === 'name' && v.length < 2) msg = 'Please enter your full name.';
    else if (v && rule === 'url' && !validUrl(v)) msg = 'Please enter a valid URL.';
    else if (rule === 'file' && field.files && field.files[0]) {
      var f = field.files[0], ok = /\.(pdf|doc|docx)$/i.test(f.name);
      if (!ok) msg = 'Upload a PDF, DOC or DOCX file.';
      else if (f.size > 5 * 1024 * 1024) msg = 'File is larger than 5 MB.';
    } else if (rule === 'file' && req && !(field.files && field.files[0])) msg = 'Please upload your resume.';
    else if (v && rule === 'message' && v.length < 10) msg = 'Please write at least 10 characters.';
    setErr(field, msg);
    return !msg;
  }

  function recaptcha(action) {
    return new Promise(function (resolve) {
      if (!CK.recaptcha || !window.grecaptcha) return resolve('');
      window.grecaptcha.ready(function () { window.grecaptcha.execute(CK.recaptcha, { action: action }).then(resolve, function () { resolve(''); }); });
    });
  }

  /* ---------- Contact form ---------- */
  var form = d.getElementById('contactForm');
  if (form) {
    var toggle = $('.toggle', form.parentNode), panes = $$('.pane', form);
    var typeInput = $('input[name="form_type"]', form), alertBox = $('.form-alert', form), btn = $('button[type="submit"]', form);
    var success = d.getElementById('formSuccess');

    function setMode(mode) {
      toggle.setAttribute('data-mode', mode);
      $$('button', toggle).forEach(function (b) { var on = b.getAttribute('data-mode') === mode; b.classList.toggle('is-active', on); b.setAttribute('aria-selected', on ? 'true' : 'false'); });
      panes.forEach(function (p) {
        var on = p.getAttribute('data-pane') === mode;
        p.hidden = !on;
        $$('input, select, textarea', p).forEach(function (f) { f.disabled = !on; });
      });
      typeInput.value = mode;
      $('.btn-label', btn).textContent = mode === 'career' ? 'Submit Application' : 'Send Enquiry';
      alertBox.classList.remove('is-show');
    }
    $$('button', toggle).forEach(function (b) { b.addEventListener('click', function () { setMode(b.getAttribute('data-mode')); }); });

    // initial mode + pre-selected service from URL
    var qs = new URLSearchParams(location.search);
    setMode(qs.get('type') === 'career' ? 'career' : 'project');
    var svc = qs.get('service'), sel = $('select[name="service"]', form);
    if (svc && sel) { $$('option', sel).forEach(function (o) { if (o.value === svc || o.getAttribute('data-id') === svc) sel.value = o.value; }); }

    $$('input, select, textarea', form).forEach(function (f) {
      f.addEventListener('blur', function () { if (f.value || f.type === 'file') check(f); });
      f.addEventListener('input', function () { if (f.closest('.field.has-error')) check(f); });
      f.addEventListener('change', function () { check(f); });
    });

    // resume dropzone label
    var file = $('input[type="file"]', form);
    if (file) {
      var drop = file.closest('.file-drop'), label = $('strong', drop);
      file.addEventListener('change', function () { label.textContent = file.files[0] ? file.files[0].name : 'Upload your resume'; });
      ['dragenter', 'dragover'].forEach(function (ev) { drop.addEventListener(ev, function () { drop.classList.add('is-drag'); }); });
      ['dragleave', 'drop'].forEach(function (ev) { drop.addEventListener(ev, function () { drop.classList.remove('is-drag'); }); });
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      alertBox.classList.remove('is-show');
      var fields = $$('input, select, textarea', form).filter(function (f) { return !f.disabled && f.type !== 'hidden' && !f.closest('.hp'); });
      var ok = true, first = null;
      fields.forEach(function (f) { if (!check(f)) { ok = false; first = first || f; } });
      if (!ok) { first.focus(); return; }

      var mode = typeInput.value;
      var endpoint = CK.url(mode === 'career' ? 'api/career.php' : 'api/contact.php');
      btn.classList.add('is-loading'); btn.disabled = true;

      recaptcha(mode === 'career' ? 'career' : 'contact').then(function (token) {
        var fd = new FormData(form);
        fd.set('csrf', CK.csrf());
        if (token) fd.set('recaptcha_token', token);
        var ph = fd.get('phone'); if (ph) fd.set('phone', cleanPhone(ph));
        return fetch(endpoint, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
      }).then(function (r) {
        return r.json().catch(function () { return { success: false, message: 'Server error (' + r.status + '). Please try again or WhatsApp us.' }; });
      }).then(function (j) {
        btn.classList.remove('is-loading'); btn.disabled = false;
        if (j.success) {
          $('h3', success).textContent = mode === 'career' ? 'Application received!' : 'Message sent!';
          $('p', success).textContent = j.message || 'Thank you! We will get back to you within 24 hours.';
          success.classList.add('is-show');
          var la = $('.success-anim', success);
          if (window.lottie && la && la.getAttribute('data-lottie')) {
            la.innerHTML = ''; CK.lottie && CK.lottie(la, { loop: false, autoplay: true });
          }
          form.reset(); if (file) $('strong', file.closest('.file-drop')).textContent = 'Upload your resume';
          success.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
          if (j.errors) Object.keys(j.errors).forEach(function (k) { var f = form.elements[k]; if (f && f.nodeType) setErr(f, j.errors[k]); });
          alertBox.textContent = j.message || 'Something went wrong. Please try again.';
          alertBox.classList.add('is-show');
        }
      }).catch(function () {
        btn.classList.remove('is-loading'); btn.disabled = false;
        alertBox.textContent = 'Network error. Please check your connection or WhatsApp us.';
        alertBox.classList.add('is-show');
      });
    });

    var again = d.getElementById('formAgain');
    if (again) again.addEventListener('click', function () { success.classList.remove('is-show'); });
  }

  /* ---------- Newsletter ---------- */
  var news = d.getElementById('newsForm');
  if (news) {
    news.addEventListener('submit', function (e) {
      e.preventDefault();
      var msg = $('.news-msg', news), email = news.email.value.trim(), b = $('button', news);
      msg.className = 'news-msg';
      if (!EMAIL_RE.test(email)) { msg.textContent = 'Please enter a valid email address.'; msg.classList.add('bad'); news.email.focus(); return; }
      b.disabled = true;
      var fd = new FormData(news); fd.set('csrf', CK.csrf());
      fetch(CK.url('api/newsletter.php'), { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (j) { msg.textContent = j.message; msg.classList.add(j.success ? 'ok' : 'bad'); if (j.success) news.reset(); })
        .catch(function () { msg.textContent = 'Network error. Please try again.'; msg.classList.add('bad'); })
        .then(function () { b.disabled = false; });
    });
  }
})();
