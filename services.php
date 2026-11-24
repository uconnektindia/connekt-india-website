<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/components.php';

$faqs = [
    ['Which service should I start with?', 'It depends on your goal. For quick leads, start with performance marketing. For long-term trust, start with social media and branding. If you have no website or an outdated one, start there. We will recommend the right mix in a free discovery call.'],
    ['What is your pricing?', "Pricing depends on your goals and scope. Share your requirements and we'll send a custom quote, usually within 24 hours."],
    ['Do you offer monthly packages?', 'Yes. We offer monthly retainer plans for social media and marketing, as well as one-time project pricing for websites, branding and software.'],
    ['Do you run Meta Ads and Google Ads?', 'Yes. We plan, run and optimise Meta (Facebook/Instagram) and Google Ads with proper conversion tracking.'],
    ['Do you build e-commerce websites?', 'Yes. We build online stores with payment gateways (Razorpay and others), product management and order tracking.'],
    ['Will I get reports?', 'Yes. You get clear monthly reports covering reach, engagement, leads, ad spend and next steps.'],
];
$svcLd = array_map(fn($s) => [
    '@context' => 'https://schema.org', '@type' => 'Service', 'name' => $s['name'], 'description' => $s['desc'],
    'provider' => ['@id' => abs_url('#business')], 'areaServed' => 'India', 'url' => abs_url('services#' . $s['id']),
], services());
$page = [
    'title' => 'Services | Digital Marketing, Social Media, Ads, Branding, Web & Software | Connekt India',
    'description' => 'Explore Connekt India services: digital marketing & SEO, social media management, Meta & Google Ads, creative & branding, web development and custom software in Bhubaneswar.',
    'path' => 'services',
    'nav' => 'services',
    'jsonld' => array_merge([breadcrumb_jsonld([['Home', ''], ['Services', 'services']]), faq_jsonld($faqs)], $svcLd),
];
require __DIR__ . '/includes/header.php';

render_page_hero('Our services', 'DIGITAL SOLUTIONS FOR <span class="blue">REAL GOALS.</span>', 'Six focused services, one connected team. Pick what you need today and add more as you grow. Everything is planned around your business goals.', [['Services', null]]);
?>

<section class="sec sec-light">
  <div class="wrap">
    <nav class="svc-nav" aria-label="Jump to a service" data-reveal>
      <?php foreach (services() as $s): ?>
        <a href="#<?= e($s['id']) ?>"><?= icon($s['icon']) ?><?= e($s['name']) ?></a>
      <?php endforeach; ?>
    </nav>

    <div style="margin-top:clamp(40px,6vw,72px)">
    <?php
    $chips = [
      'digital-marketing' => ['SEO', 'Analytics'],
      'social-media' => ['Reels', 'Calendars'],
      'performance-marketing' => ['Meta Ads', 'Google Ads'],
      'creative-branding' => ['Logo', 'Motion'],
      'web-development' => ['Fast', 'SEO-ready'],
      'software-development' => ['CRM', 'Automation'],
    ];
    foreach (services() as $i => $s): ?>
      <article class="svc-block<?= $i % 2 ? ' rev' : '' ?>" id="<?= e($s['id']) ?>">
        <div class="svc-visual" data-reveal="<?= $i % 2 ? 'left' : 'right' ?>">
          <span class="svc-ring" aria-hidden="true"></span>
          <div class="svc-orb draw" data-inview aria-hidden="true"><?= icon($s['icon'], '', 64) ?></div>
          <span class="svc-chip c1"><?= e($chips[$s['id']][0]) ?></span>
          <span class="svc-chip c2"><?= e($chips[$s['id']][1]) ?></span>
        </div>
        <div class="svc-copy" data-reveal="<?= $i % 2 ? 'right' : 'left' ?>">
          <p class="eyebrow">Service 0<?= $i + 1 ?></p>
          <h2 class="h2"><?= e(strtoupper($s['name'])) ?></h2>
          <p><?= e($s['desc']) ?></p>
          <ul class="tick-list">
            <?php foreach ($s['items'] as $it): ?><li><?= icon('check-circle') ?><span><?= e($it) ?></span></li><?php endforeach; ?>
          </ul>
          <a class="btn btn-primary" href="<?= url('contact?service=' . $s['id']) ?>">Discuss this service <span class="arr">↗</span></a>
        </div>
      </article>
    <?php endforeach; ?>
    </div>
  </div>
</section>

<?php render_process('sec-dark'); ?>
<?php render_faq($faqs, 'sec-light'); ?>
<?php render_cta(); ?>
<?php require __DIR__ . '/includes/footer.php';
