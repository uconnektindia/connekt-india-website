<?php /** Shared footer + global widgets + scripts */ ?>
</main>

<footer class="site-footer">
  <div class="footer-glow" aria-hidden="true"></div>
  <div class="wrap">
    <div class="footer-news" data-reveal>
      <div>
        <p class="eyebrow">Newsletter</p>
        <h2 class="footer-news-title">Growth ideas, once a month. <span class="blue">No spam.</span></h2>
      </div>
      <form class="news-form" id="newsForm" novalidate>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
        <label class="sr-only" for="newsEmail">Email address</label>
        <input id="newsEmail" type="email" name="email" placeholder="you@business.com" required autocomplete="email">
        <button class="btn btn-primary" type="submit">Subscribe <span class="arr">↗</span></button>
        <p class="news-msg" role="status" aria-live="polite"></p>
      </form>
    </div>

    <div class="footer-grid">
      <div class="f-col f-brand" data-reveal>
        <a class="brand" href="<?= url('') ?>" aria-label="Connekt India home">
          <?= logo_svg('brand-mark') ?>
          <span class="brand-text"><span class="brand-name">Connekt <b>India</b></span><span class="brand-tag">Ideas · Content · Growth</span></span>
        </a>
        <p>A digital marketing, creative and technology agency in Bhubaneswar, helping brands across Odisha and India grow with strategy, content and code.</p>
        <div class="socials">
          <?php foreach (social_links() as [$key, $label, $ico, $href]): ?>
            <a href="<?= $href !== '' ? e($href) : '#' ?>" data-social="<?= e($key) ?>" class="social s-<?= e($key) ?><?= $href === '' ? ' is-empty' : '' ?>" aria-label="<?= e($label) ?>" <?= $href !== '' ? 'target="_blank" rel="noopener"' : '' ?>><?= icon($ico, '', 18) ?></a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="f-col" data-reveal>
        <h3>Quick links</h3>
        <ul>
          <li><a href="<?= url('') ?>">Home</a></li>
          <li><a href="<?= url('about') ?>">About us</a></li>
          <li><a href="<?= url('team') ?>">Our team</a></li>
          <li><a href="<?= url('work') ?>">Our work</a></li>
          <li><a href="<?= url('blog') ?>">Blog</a></li>
          <li><a href="<?= url('contact?type=career') ?>">Careers</a></li>
          <li><a href="<?= url('contact') ?>">Contact</a></li>
        </ul>
      </div>

      <div class="f-col" data-reveal>
        <h3>Services</h3>
        <ul>
          <?php foreach (services() as $s): ?>
            <li><a href="<?= url('services#' . $s['id']) ?>"><?= e($s['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="f-col f-contact" data-reveal>
        <h3>Get in touch</h3>
        <ul>
          <li><?= icon('mail', '', 18) ?><a href="mailto:<?= e(BUSINESS_EMAIL) ?>"><?= e(BUSINESS_EMAIL) ?></a></li>
          <li><?= icon('phone', '', 18) ?><a href="<?= e(tel_link()) ?>"><?= e(PHONE_DISPLAY) ?></a></li>
          <li><?= icon('map-pin', '', 18) ?><span><?= e(BUSINESS_ADDRESS) ?></span></li>
          <li><?= icon('clock', '', 18) ?><span><?= e(WORKING_HOURS) ?></span></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© <?= date('Y') ?> Connekt India. All rights reserved.</p>
      <p><a href="<?= url('privacy-policy') ?>">Privacy Policy</a><span aria-hidden="true">·</span><a href="<?= url('terms') ?>">Terms</a></p>
    </div>
  </div>
</footer>

<?php require __DIR__ . '/chatbot.php'; ?>

<div class="modal" id="modal" role="dialog" aria-modal="true" aria-hidden="true" aria-labelledby="modalTitle">
  <div class="modal-backdrop" data-close></div>
  <div class="modal-card" role="document">
    <button class="modal-x" data-close aria-label="Close"><?= icon('x', '', 22) ?></button>
    <div class="modal-content" id="modalContent"></div>
  </div>
</div>

<div class="cursor" aria-hidden="true"><span class="cursor-dot"></span><span class="cursor-ring"></span></div>

<script>window.CK.icons = <?= json_encode([
  'linkedin' => icon('linkedin', '', 18), 'instagram' => icon('instagram', '', 18), 'mail' => icon('mail', '', 18),
  'award' => icon('award', '', 15), 'users' => icon('users', '', 15), 'check' => icon('check-circle', '', 18),
  'target' => icon('target', '', 18), 'trending' => icon('trending-up', '', 18), 'sparkles' => icon('sparkles', '', 18),
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>;</script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/lottie-web@5.12.2/build/player/lottie_light.min.js" defer></script>
<?php if (RECAPTCHA_SITE_KEY !== ''): ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?= e(RECAPTCHA_SITE_KEY) ?>" defer></script>
<?php endif; ?>
<script src="<?= asset('js/main.js') ?>" defer></script>
<script src="<?= asset('js/animations.js') ?>" defer></script>
<script src="<?= asset('js/forms.js') ?>" defer></script>
<script src="<?= asset('js/chatbot.js') ?>" defer></script>
<?php foreach (($page['scripts'] ?? []) as $s): ?>
<script src="<?= asset('js/' . $s) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
