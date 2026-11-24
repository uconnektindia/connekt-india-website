<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/components.php';

$page = [
    'title' => 'Contact Connekt India | Start a Project or Apply for a Job | Bhubaneswar',
    'description' => 'Contact Connekt India at 446, Saheed Nagar, Bhubaneswar. Send a project enquiry, apply for a job or internship, email uconnekt.india@gmail.com or WhatsApp us.',
    'path' => 'contact',
    'nav' => 'contact',
    'jsonld' => [breadcrumb_jsonld([['Home', ''], ['Contact', 'contact']])],
];
$positions = ['Digital Marketing Executive', 'Social Media Manager', 'Graphic Designer', 'Video Editor', 'Web Developer', 'Software Developer', 'Intern', 'Other'];
$budgets = ['Under ₹25,000', '₹25,000 – ₹50,000', '₹50,000 – ₹1,00,000', '₹1,00,000 – ₹3,00,000', 'Above ₹3,00,000', 'Not sure yet'];
$mapQ = rawurlencode('446, Saheed Nagar, Bhubaneswar, Odisha 751007');
require __DIR__ . '/includes/header.php';

render_page_hero('Contact us', "LET'S TALK ABOUT YOUR <span class=\"blue\">NEXT BIG IDEA.</span>", 'Tell us about your project or your career goals. We reply to every message, usually within 24 hours.', [['Contact', null]]);
?>

<section class="sec sec-light">
  <div class="wrap contact-grid">
    <div class="form-card" data-reveal="left">
      <div class="toggle" data-mode="project" role="tablist" aria-label="Form type">
        <span class="toggle-pill" aria-hidden="true"></span>
        <button type="button" role="tab" data-mode="project" class="is-active" aria-selected="true"><?= icon('briefcase', '', 18) ?> Project Enquiry</button>
        <button type="button" role="tab" data-mode="career" aria-selected="false"><?= icon('user', '', 18) ?> Career</button>
      </div>

      <form id="contactForm" novalidate enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="form_type" value="project">
        <div class="hp" aria-hidden="true"><label>Leave this empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

        <div class="form-grid">
          <!-- shared fields -->
          <div class="field">
            <label for="f-name">Full name <span class="req">*</span></label>
            <input id="f-name" name="name" type="text" required maxlength="100" autocomplete="name" data-rule="name" placeholder="Your full name">
            <span class="err" role="alert"></span>
          </div>
          <div class="field">
            <label for="f-email">Email <span class="req">*</span></label>
            <input id="f-email" name="email" type="email" required maxlength="150" autocomplete="email" data-rule="email" placeholder="you@company.com">
            <span class="err" role="alert"></span>
          </div>
          <div class="field">
            <label for="f-phone">Phone <span class="req">*</span></label>
            <div class="phone-wrap"><span>+91</span><input id="f-phone" name="phone" type="tel" required maxlength="14" inputmode="tel" autocomplete="tel-national" data-rule="phone" placeholder="98765 43210"></div>
            <span class="err" role="alert"></span>
          </div>

          <!-- PROJECT -->
          <div class="field pane" data-pane="project">
            <label for="f-company">Company / Organization</label>
            <input id="f-company" name="company" type="text" maxlength="150" autocomplete="organization" placeholder="Your business name">
            <span class="err" role="alert"></span>
          </div>

          <!-- CAREER -->
          <div class="field pane" data-pane="career" hidden>
            <label for="f-position">Position applying for <span class="req">*</span></label>
            <select id="f-position" name="position" required data-msg="Please choose a position.">
              <option value="">Select a position</option>
              <?php foreach ($positions as $p): ?><option><?= e($p) ?></option><?php endforeach; ?>
            </select>
            <span class="err" role="alert"></span>
          </div>
        </div>

        <div class="pane" data-pane="project">
          <div class="form-grid" style="margin-top:18px">
            <div class="field">
              <label for="f-service">Service required <span class="req">*</span></label>
              <select id="f-service" name="service" required data-msg="Please choose a service.">
                <option value="">Select a service</option>
                <?php foreach (services() as $s): ?><option value="<?= e($s['name']) ?>" data-id="<?= e($s['id']) ?>"><?= e($s['name']) ?></option><?php endforeach; ?>
                <option value="Other">Other</option>
              </select>
              <span class="err" role="alert"></span>
            </div>
            <div class="field">
              <label for="f-budget">Budget range</label>
              <select id="f-budget" name="budget">
                <option value="">Select a range</option>
                <?php foreach ($budgets as $b): ?><option><?= e($b) ?></option><?php endforeach; ?>
              </select>
              <span class="err" role="alert"></span>
            </div>
            <div class="field full">
              <label for="f-message">Message <span class="req">*</span></label>
              <textarea id="f-message" name="message" required maxlength="3000" data-rule="message" placeholder="Tell us about your business, goals and timeline…"></textarea>
              <span class="err" role="alert"></span>
            </div>
          </div>
        </div>

        <div class="pane" data-pane="career" hidden>
          <div class="form-grid" style="margin-top:18px">
            <div class="field">
              <label for="f-exp">Experience</label>
              <select id="f-exp" name="experience">
                <option value="">Select experience</option>
                <?php foreach (['Fresher', 'Less than 1 year', '1–2 years', '3–5 years', '5+ years'] as $x): ?><option><?= e($x) ?></option><?php endforeach; ?>
              </select>
              <span class="err" role="alert"></span>
            </div>
            <div class="field">
              <label for="f-portfolio">Portfolio / LinkedIn URL</label>
              <input id="f-portfolio" name="portfolio" type="url" maxlength="255" data-rule="url" placeholder="https://">
              <span class="err" role="alert"></span>
            </div>
            <div class="field full">
              <label for="f-resume">Resume (PDF/DOC, max 5 MB) <span class="req">*</span></label>
              <label class="file-drop">
                <span class="icon-tile"><?= icon('upload') ?></span>
                <span><strong>Upload your resume</strong><small>Click or drag a PDF, DOC or DOCX file here</small></span>
                <input id="f-resume" name="resume" type="file" required accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-rule="file">
              </label>
              <span class="err" role="alert"></span>
            </div>
            <div class="field full">
              <label for="f-cmsg">Message</label>
              <textarea id="f-cmsg" name="message" maxlength="3000" placeholder="Tell us a little about yourself and why you'd like to join…"></textarea>
              <span class="err" role="alert"></span>
            </div>
          </div>
        </div>

        <div class="field full" style="margin-top:18px">
          <label class="consent"><input type="checkbox" name="consent" value="1" required data-msg="Please accept to continue."> <span>I agree that Connekt India may contact me about this enquiry and store my details as described in the <a href="<?= url('privacy-policy') ?>">Privacy Policy</a>.</span></label>
          <span class="err" role="alert"></span>
        </div>

        <div class="form-foot">
          <p class="form-note"><?= icon('lock', '', 14) ?> Your details are safe with us. No spam.</p>
          <button class="btn btn-primary" type="submit"><span class="spin" aria-hidden="true"></span><span class="btn-label">Send Enquiry</span> <span class="arr">↗</span></button>
        </div>
        <p class="form-alert" role="alert"></p>
      </form>

      <div class="success" id="formSuccess" aria-live="polite">
        <div class="success-anim" data-lottie="<?= asset('assets/icons/success.json') ?>" data-lottie-manual>
          <svg class="check-svg" viewBox="0 0 120 120" aria-hidden="true"><circle cx="60" cy="60" r="54"/><path d="M36 62 l16 16 l32 -34"/></svg>
        </div>
        <h3>Message sent!</h3>
        <p>Thank you! We will get back to you within 24 hours.</p>
        <div class="btn-row" style="justify-content:center">
          <button class="btn btn-ghost" type="button" id="formAgain" style="color:#101827;border-color:#10182730">Send another</button>
          <a class="btn btn-wa" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener">WhatsApp us</a>
        </div>
      </div>
    </div>

    <div class="info-stack">
      <div class="info-card" data-reveal="right">
        <h3>Company details</h3>
        <ul class="info-list" style="list-style:none;padding:0">
          <li><span class="icon-tile"><?= icon('mail') ?></span><div><small>Email</small><a href="mailto:<?= e(BUSINESS_EMAIL) ?>"><?= e(BUSINESS_EMAIL) ?></a></div></li>
          <li><span class="icon-tile"><?= icon('phone') ?></span><div><small>Phone / WhatsApp</small><a href="<?= e(tel_link()) ?>"><?= e(PHONE_DISPLAY) ?></a></div></li>
          <li><span class="icon-tile"><?= icon('map-pin') ?></span><div><small>Address</small><span><?= e(BUSINESS_ADDRESS) ?></span></div></li>
          <li><span class="icon-tile"><?= icon('clock') ?></span><div><small>Working hours</small><span><?= e(WORKING_HOURS) ?></span></div></li>
        </ul>
        <div class="btn-row" style="margin-top:24px">
          <a class="btn btn-wa btn-block" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener"><?= icon('whatsapp', '', 18) ?> Chat on WhatsApp</a>
        </div>
      </div>
      <div class="card" data-reveal="right" style="--rd:120">
        <span class="icon-tile"><?= icon('sparkles') ?></span>
        <h3>Free discovery call</h3>
        <p>Not sure what you need? Book a free 20-minute call. We'll review your brand and share ideas, with no obligation.</p>
      </div>
    </div>
  </div>
</section>

<section class="sec sec-white" style="padding-top:0">
  <div class="wrap">
    <div class="map-wrap" data-reveal>
      <iframe title="Connekt India location on Google Maps: 446, Saheed Nagar, Bhubaneswar" src="https://www.google.com/maps?q=<?= $mapQ ?>&amp;output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
      <a class="btn btn-primary map-btn" href="https://www.google.com/maps/dir/?api=1&amp;destination=<?= $mapQ ?>" target="_blank" rel="noopener"><?= icon('navigation', '', 18) ?> Get Directions <span class="arr">↗</span></a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php';
