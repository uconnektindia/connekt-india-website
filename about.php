<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/components.php';

$page = [
    'title' => 'About Connekt India | Creative & Tech Marketing Agency in Bhubaneswar',
    'description' => 'Meet Connekt India: a Bhubaneswar agency where creativity meets technology. Our mission, vision, values, journey and the team behind your brand growth.',
    'path' => 'about',
    'nav' => 'about',
    'jsonld' => [breadcrumb_jsonld([['Home', ''], ['About', 'about']])],
];
$team = sorted_team();
require __DIR__ . '/includes/header.php';

render_page_hero('About Connekt India', 'CREATIVITY MEETS <span class="blue">TECHNOLOGY.</span>', 'We are a digital marketing, creative and technology agency in Bhubaneswar. We combine ideas, content and code to help brands grow, from local favourites to fast-scaling startups.', [['About', null]]);
?>

<!-- Who we are -->
<section class="sec sec-light">
  <div class="wrap split">
    <div data-reveal="left">
      <p class="eyebrow">Who we are</p>
      <h2 class="h2" data-split>A BHUBANESWAR TEAM BUILT FOR <span class="blue">GROWTH.</span></h2>
      <p class="muted" style="margin-top:18px">Connekt India started with a simple belief: businesses grow faster when strategy, content and technology work as one. Too many brands juggle a designer here, an ads freelancer there and a web developer somewhere else, and nothing connects.</p>
      <p class="muted" style="margin-top:14px">So we built a single team that covers it all. Our marketers plan, our designers and video crew create, our ads specialists amplify, and our developers build the websites and software that turn attention into customers.</p>
      <ul class="tick-list check-cols">
        <?php foreach (['Strategy-first approach', 'In-house video production', 'Transparent monthly reports', 'Websites & custom software', 'Local market expertise', 'Quick WhatsApp support'] as $t): ?>
          <li><?= icon('check-circle') ?><span><?= e($t) ?></span></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="about-visual" data-reveal="right">
      <?php render_tiles(false); ?>
    </div>
  </div>
</section>

<!-- Mission / vision / values -->
<section class="sec sec-dark grid-bg">
  <div class="glow-blob" style="right:-10%;bottom:-20%" aria-hidden="true"></div>
  <div class="wrap">
    <div class="sec-head center" data-reveal>
      <p class="eyebrow">What drives us</p>
      <h2 class="h2" data-split>MISSION, VISION <span class="blue">& VALUES.</span></h2>
    </div>
    <div class="grid-3" data-stagger="120">
      <div class="card draw" data-reveal data-inview data-tilt>
        <span class="icon-tile"><?= icon('target') ?></span>
        <h3>Our mission</h3>
        <p>To help businesses across Odisha and India grow with honest strategy, standout creative and smart technology, measured by real results.</p>
      </div>
      <div class="card draw" data-reveal data-inview data-tilt>
        <span class="icon-tile"><?= icon('eye') ?></span>
        <h3>Our vision</h3>
        <p>To be the most trusted growth partner for ambitious brands from Bhubaneswar, proving world-class work can come from anywhere.</p>
      </div>
      <div class="card draw" data-reveal data-inview data-tilt>
        <span class="icon-tile"><?= icon('heart') ?></span>
        <h3>Our values</h3>
        <ul class="tick-list" style="margin-top:4px">
          <?php foreach (['Honesty over hype', 'Craft in every detail', 'Ownership of outcomes', 'Always keep learning'] as $v): ?>
            <li><?= icon('check') ?><span style="color:#c9d1e0"><?= e($v) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- Journey -->
<section class="sec sec-light">
  <div class="wrap">
    <div class="sec-head center" data-reveal>
      <p class="eyebrow">Our journey</p>
      <h2 class="h2" data-split>HOW WE <span class="blue">GOT HERE.</span></h2>
    </div>
    <div class="timeline" data-inview data-stagger="140">
      <div class="timeline-line" aria-hidden="true"><i></i></div>
      <?php
      $journey = [
        ['The beginning', 'Years of hands-on experience', 'Our founding team spent more than a decade running social media, operations and campaigns for brands, learning what actually moves the needle.'],
        ['Connekt India is born', 'One team, every skill', 'We brought marketers, designers, video creators and developers together in Bhubaneswar to offer strategy, content and technology under one roof.'],
        ['Growing with our clients', 'From gyms to jewellers', 'We partnered with brands like WarZone, Iron Empire, Fitness Hour, Novus, Carbon and Akhandalamani Jewellers across social, ads, branding and web.'],
        ['Today & tomorrow', 'Creativity meets technology', 'We are expanding into custom software, automation and AI tools, helping clients across India grow faster and work smarter.'],
      ];
      foreach ($journey as [$y, $t, $d]): ?>
        <div class="tl-item card" data-reveal>
          <span class="tl-year"><?= e($y) ?></span>
          <h3><?= e($t) ?></h3>
          <p><?= e($d) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Team preview -->
<section class="sec sec-dark">
  <div class="wrap">
    <div class="sec-head-row">
      <div class="sec-head" data-reveal>
        <p class="eyebrow">The people</p>
        <h2 class="h2" data-split>MEET THE <span class="blue">TEAM.</span></h2>
      </div>
      <a class="link-arrow" href="<?= url('team') ?>" data-reveal>Meet the full team <span class="arr">↗</span></a>
    </div>
    <div class="team-mini" data-stagger="100">
      <?php foreach (array_slice($team, 0, 4) as $m): ?>
        <a class="member" href="<?= url('team') ?>" data-reveal>
          <div class="frame"><div class="frame-in"><?= member_photo($m) ?></div></div>
          <div class="m-info"><h3><?= e($m['name']) ?></h3><p class="role"><?= e($m['role']) ?></p></div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php render_cta(); ?>
<?php require __DIR__ . '/includes/footer.php';
