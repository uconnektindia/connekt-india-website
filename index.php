<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/components.php';

$faqs = home_faqs();
$page = [
    'title' => 'Connekt India | Digital Marketing Agency in Bhubaneswar, Odisha',
    'description' => 'Connekt India is a digital marketing, creative and technology agency in Bhubaneswar. Social media, Meta & Google Ads, branding, websites and custom software that grow your brand.',
    'path' => '',
    'nav' => 'home',
    'body_class' => 'page-home',
    'jsonld' => [faq_jsonld($faqs), [
        '@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => SITE_NAME, 'url' => abs_url(),
    ]],
];
$clients = load_json('clients');
$stats = load_json('stats');
$work = array_values(array_filter(load_json('work'), fn($w) => !empty($w['featured'])));
$testimonials = load_json('testimonials');
require __DIR__ . '/includes/header.php';
?>

<!-- ===================== HERO ===================== -->
<section class="hero" id="top">
  <div class="hero-media" style="background-image:url('<?= asset('assets/video/hero-poster.jpg') ?>')">
    <video id="heroVideo" muted loop playsinline autoplay preload="metadata"
      poster="<?= asset('assets/video/hero-poster.jpg') ?>"
      data-src="<?= asset('assets/video/hero.mp4') ?>"
      data-src-mobile="<?= asset_exists('assets/video/hero-mobile.mp4') ? asset('assets/video/hero-mobile.mp4') : asset('assets/video/hero.mp4') ?>"
      aria-hidden="true"></video>
  </div>
  <div class="hero-overlay"></div>
  <div class="wrap hero-grid">
    <div class="hero-copy">
      <p class="eyebrow" data-reveal>Digital marketing agency · Bhubaneswar, Odisha</p>
      <h1 class="h1" data-split>WE BUILD BRANDS THAT <span class="blue">MOVE FORWARD.</span></h1>
      <p class="lead" data-reveal style="--rd:250">Strategy, content and code under one roof. We help ambitious businesses across Odisha and India get noticed, win customers and scale with social media, performance ads, branding, websites and custom software.</p>
      <div class="btn-row" data-reveal style="--rd:400">
        <a class="btn btn-primary" href="<?= url('contact') ?>">Start a Project <span class="arr">↗</span></a>
        <a class="btn btn-ghost" href="#work">Explore Our Work <span aria-hidden="true">↓</span></a>
      </div>
      <div class="hero-proof" data-reveal style="--rd:550">
        <span><?= icon('badge-check') ?> In-house creative & tech team</span>
        <span><?= icon('map-pin') ?> Saheed Nagar, Bhubaneswar</span>
      </div>
    </div>
    <?php render_tiles(); ?>
  </div>
  <a class="scroll-cue" href="#clients" aria-label="Scroll to content"></a>
</section>

<!-- ===================== CLIENTS ===================== -->
<section class="sec-dark clients" id="clients">
  <div class="wrap">
    <p class="eyebrow clients-title center" style="display:flex;justify-content:center" data-reveal>Brands that trust us</p>
  </div>
  <div class="marquee" data-reveal>
    <div class="marquee-track">
      <?php for ($g = 0; $g < 2; $g++): ?>
        <div class="marquee-group" <?= $g ? 'aria-hidden="true"' : '' ?>>
          <?php for ($rep = 0; $rep < 2; $rep++): foreach ($clients as $c): ?>
            <div class="client" title="<?= e($c['name']) ?>"><?= client_mark($c) ?></div>
          <?php endforeach; endfor; ?>
        </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- ===================== STATS ===================== -->
<section class="sec sec-light" id="stats" style="padding-block:clamp(56px,7vw,90px)">
  <div class="wrap">
    <div class="stats" data-stagger="110">
      <?php foreach ($stats as $s): ?>
        <div class="stat" data-reveal data-inview>
          <div class="stat-num"><span data-count="<?= e((string)$s['value']) ?>"><?= e((string)$s['value']) ?></span><span class="suf"><?= e($s['suffix'] ?? '') ?></span></div>
          <p class="stat-label"><?= e($s['label']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== SERVICES ===================== -->
<section class="sec sec-dark grid-bg" id="services">
  <div class="glow-blob" style="left:-15%;top:10%" aria-hidden="true"></div>
  <div class="wrap">
    <div class="sec-head-row">
      <div class="sec-head" data-reveal>
        <p class="eyebrow">What we do</p>
        <h2 class="h2" data-split>SIX WAYS WE <span class="blue">GROW YOUR BRAND.</span></h2>
      </div>
      <a class="link-arrow" href="<?= url('services') ?>" data-reveal>View all services <span class="arr">↗</span></a>
    </div>
    <div class="grid-3" data-stagger="90">
      <?php foreach (services() as $i => $s): ?>
        <a class="card draw" href="<?= url('services#' . $s['id']) ?>" data-reveal data-inview data-tilt>
          <span class="card-num">0<?= $i + 1 ?></span>
          <span class="icon-tile"><?= icon($s['icon']) ?></span>
          <h3><?= e($s['name']) ?></h3>
          <p><?= e($s['short']) ?></p>
          <span class="link-arrow">Learn more <span class="arr">↗</span></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== FEATURED WORK ===================== -->
<section class="sec sec-light" id="work">
  <div class="wrap">
    <div class="sec-head-row">
      <div class="sec-head" data-reveal>
        <p class="eyebrow">Featured work</p>
        <h2 class="h2" data-split>RESULTS FOR <span class="blue">REAL BRANDS.</span></h2>
      </div>
      <a class="link-arrow" href="<?= url('work') ?>" data-reveal>See all projects <span class="arr">↗</span></a>
    </div>
    <div class="work-grid" data-stagger="100">
      <?php foreach (array_slice($work, 0, 3) as $w) work_card($w, 'light'); ?>
    </div>
  </div>
  <script type="application/json" id="workData"><?= work_js_data(load_json('work')) ?></script>
</section>

<!-- ===================== WHY US ===================== -->
<section class="sec sec-dark grid-bg" id="why">
  <div class="wrap why-grid">
    <div class="sec-head" style="margin-bottom:0" data-reveal="left">
      <p class="eyebrow">Why choose us</p>
      <h2 class="h2" data-split>ONE TEAM. <span class="blue">EVERY SKILL</span> YOUR BRAND NEEDS.</h2>
      <p class="lead muted" style="margin-top:18px">Marketers, designers, videographers, editors and developers working together in Bhubaneswar, so your strategy, content and technology always pull in the same direction.</p>
      <div class="btn-row" style="margin-top:28px">
        <a class="btn btn-primary" href="<?= url('about') ?>">About Connekt <span class="arr">↗</span></a>
      </div>
    </div>
    <div class="why-list" data-stagger="100">
      <?php
      $why = [
        ['layers', 'All-in-one team', 'Strategy, creative, ads, web and software under one roof. No juggling five vendors.'],
        ['line-chart', 'Results you can measure', 'Proper tracking, honest reporting and a focus on leads and sales, not vanity metrics.'],
        ['video', 'In-house production', 'We plan, shoot and edit reels and brand films ourselves for speed and consistency.'],
        ['handshake', 'Local & accessible', 'A Bhubaneswar team you can meet, call or WhatsApp, with quick replies and clear timelines.'],
      ];
      foreach ($why as [$ic, $t, $d]): ?>
        <div class="card draw" data-reveal data-inview data-tilt>
          <span class="icon-tile"><?= icon($ic) ?></span>
          <h3><?= e($t) ?></h3>
          <p><?= e($d) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== PROCESS ===================== -->
<?php render_process('sec-light'); ?>

<!-- ===================== TESTIMONIALS ===================== -->
<section class="sec sec-white" id="testimonials">
  <div class="wrap">
    <div class="sec-head center" data-reveal>
      <p class="eyebrow">Testimonials</p>
      <h2 class="h2" data-split>WHAT OUR <span class="blue">CLIENTS SAY.</span></h2>
    </div>
    <div class="testi" data-carousel data-reveal>
      <div class="testi-viewport">
        <div class="testi-track">
          <?php foreach ($testimonials as $t):
            $photo = 'assets/testimonials/' . ($t['photo'] ?? ''); ?>
            <div class="testi-slide">
              <figure class="testi-card">
                <?php if (!empty($t['sample'])): ?><span class="sample-badge" title="Replace with a real client quote before launch">Sample</span><?php endif; ?>
                <?= icon('quote', 'quote-ico') ?>
                <div class="stars" aria-label="<?= (int)$t['rating'] ?> out of 5 stars">
                  <?php for ($i = 1; $i <= 5; $i++) echo icon('star', $i <= (int)$t['rating'] ? '' : 'off', 17); ?>
                </div>
                <blockquote><?= e($t['quote']) ?></blockquote>
                <figcaption class="testi-person">
                  <?php if (!empty($t['photo']) && asset_exists($photo)): ?>
                    <img class="avatar" src="<?= e(asset($photo)) ?>" alt="<?= e($t['name']) ?>" width="52" height="52" loading="lazy">
                  <?php else: ?>
                    <span class="avatar" aria-hidden="true"><?= e(initials($t['name'])) ?></span>
                  <?php endif; ?>
                  <div><strong><?= e($t['name']) ?></strong><span><?= e($t['business']) ?></span></div>
                </figcaption>
              </figure>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="testi-nav">
        <button class="arrow" type="button" data-prev aria-label="Previous testimonial"><?= icon('chevron-left') ?></button>
        <div class="dots"></div>
        <button class="arrow" type="button" data-next aria-label="Next testimonial"><?= icon('chevron-right') ?></button>
      </div>
    </div>
  </div>
</section>

<!-- ===================== FAQ ===================== -->
<?php render_faq($faqs, 'sec-dark'); ?>

<!-- ===================== CTA ===================== -->
<?php render_cta(); ?>

<?php
$page['scripts'] = ['work.js'];
require __DIR__ . '/includes/footer.php';
