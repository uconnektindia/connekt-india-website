<?php
/** Reusable section components shared across pages */

function home_faqs(): array
{
    return [
        ['What does Connekt India do?', 'We are a digital marketing, creative and technology agency in Bhubaneswar. We help brands grow with digital marketing, social media, performance ads, branding, websites and custom software.'],
        ['What is your pricing?', "Pricing depends on your goals and scope. Share your requirements and we'll send a custom quote, usually within 24 hours."],
        ['Do you offer monthly packages?', 'Yes. We offer monthly retainer plans for social media and marketing, as well as one-time project pricing for websites, branding and software.'],
        ['Do you work with small businesses and startups?', 'Absolutely. We work with startups, local businesses and growing brands, and we plan around your budget.'],
        ['How soon will I see results from ads?', 'Ads can bring leads within days. Steady performance usually builds over 4–8 weeks of testing and optimisation.'],
        ['How long does a website take?', 'A business website typically takes 1–3 weeks. Larger or e-commerce projects take longer depending on features.'],
        ['Will I own my website and content?', 'Yes. Once the project is complete and paid for, the website, designs and content are yours.'],
        ['Do you work with clients outside Odisha?', 'Yes. We work with clients across India and abroad through online meetings.'],
    ];
}

function faq_jsonld(array $faqs): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn($f) => [
            '@type' => 'Question',
            'name' => $f[0],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
        ], $faqs),
    ];
}

function breadcrumb_jsonld(array $items): array
{
    $list = [];
    foreach ($items as $i => [$name, $path]) {
        $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => abs_url($path)];
    }
    return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list];
}

function render_faq(array $faqs, string $theme = 'sec-dark', string $title = 'QUESTIONS, <span class="blue">ANSWERED.</span>'): void
{ ?>
<section class="sec <?= e($theme) ?>" id="faq">
  <div class="wrap faq-grid">
    <div class="sec-head" data-reveal>
      <p class="eyebrow">FAQ</p>
      <h2 class="h2" data-split><?= $title ?></h2>
      <p class="lead muted">Can't find what you're looking for? Ask Dibya in the chat, or message us on WhatsApp for a quick answer.</p>
      <div class="btn-row" style="margin-top:26px">
        <a class="btn btn-wa" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener"><?= icon('whatsapp', '', 18) ?> WhatsApp us</a>
      </div>
    </div>
    <div class="faq" data-stagger="70">
      <?php foreach ($faqs as $i => [$q, $a]): ?>
        <div class="faq-item<?= $i === 0 ? ' is-open' : '' ?>" data-reveal>
          <button class="faq-q" type="button" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="faq-a-<?= $i ?>" id="faq-q-<?= $i ?>">
            <span><?= e($q) ?></span><span class="pm" aria-hidden="true"></span>
          </button>
          <div class="faq-a" id="faq-a-<?= $i ?>" role="region" aria-labelledby="faq-q-<?= $i ?>"><div><p><?= e($a) ?></p></div></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php }

function render_process(string $theme = 'sec-dark'): void
{
    $steps = [
        ['01', 'Discover', 'We learn your brand, audience, competitors and goals through a focused discovery call and audit.'],
        ['02', 'Plan', 'We build a clear strategy, content calendar, media plan or technical scope, with timelines and KPIs.'],
        ['03', 'Execute', 'Our in-house team designs, shoots, writes, builds and launches, with your approval at every key step.'],
        ['04', 'Improve', 'We track results, report monthly and keep optimising what works so your growth compounds.'],
    ]; ?>
<section class="sec <?= e($theme) ?> grid-bg" id="process">
  <div class="wrap">
    <div class="sec-head center" data-reveal>
      <p class="eyebrow">Our process</p>
      <h2 class="h2" data-split>FOUR STEPS TO <span class="blue">REAL GROWTH.</span></h2>
    </div>
    <div class="process" data-inview data-stagger="140">
      <div class="process-line" aria-hidden="true"><i></i></div>
      <?php foreach ($steps as [$n, $t, $d]): ?>
        <div class="step" data-reveal>
          <div class="step-num"><?= $n ?></div>
          <h3><?= e($t) ?></h3>
          <p class="muted"><?= e($d) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php }

function render_cta(string $title = 'READY TO GROW <br>YOUR BRAND?', string $text = "Tell us where you want to go. We'll show you how to get there, with a free strategy call and a clear plan."): void
{ ?>
<section class="cta-band">
  <?= logo_svg('hex-deco', true) ?>
  <div class="wrap cta-inner">
    <div data-reveal="left">
      <p class="eyebrow">Let's work together</p>
      <h2 class="h2" data-split><?= $title ?></h2>
      <p style="margin-top:14px;max-width:520px;color:#dbe6ff"><?= e($text) ?></p>
    </div>
    <div class="btn-row" data-reveal="right">
      <a class="btn btn-primary" href="<?= url('contact') ?>">Start a Conversation <span class="arr">↗</span></a>
      <a class="btn btn-ghost" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener"><?= icon('whatsapp', '', 18) ?> WhatsApp</a>
    </div>
  </div>
</section>
<?php }

function render_page_hero(string $eyebrow, string $titleHtml, string $lead, array $crumbs = []): void
{ ?>
<section class="page-hero grid-bg">
  <div class="glow-blob" aria-hidden="true"></div>
  <?= logo_svg('page-hero-hex', true) ?>
  <div class="wrap">
    <?php if ($crumbs): ?>
    <nav class="crumbs" aria-label="Breadcrumb" data-reveal>
      <a href="<?= url('') ?>">Home</a>
      <?php foreach ($crumbs as [$name, $href]): ?>
        <span aria-hidden="true">/</span>
        <?php if ($href !== null): ?><a href="<?= url($href) ?>"><?= e($name) ?></a><?php else: ?><span aria-current="page"><?= e($name) ?></span><?php endif; ?>
      <?php endforeach; ?>
    </nav>
    <?php endif; ?>
    <p class="eyebrow" data-reveal><?= e($eyebrow) ?></p>
    <h1 class="h1" data-split><?= $titleHtml ?></h1>
    <p class="lead" data-reveal style="--rd:200"><?= e($lead) ?></p>
  </div>
</section>
<?php }

/** Team member photo (or animated initials if the photo file is missing) */
function member_photo(array $m, int $size = 200): string
{
    $file = 'assets/team/' . ($m['photo'] ?? ($m['id'] . '.jpg'));
    if (!empty($m['photo']) && asset_exists($file)) {
        return '<img src="' . e(asset($file)) . '" alt="' . e($m['name'] . ', ' . $m['role'] . ' at Connekt India') . '" width="' . $size . '" height="' . $size . '" loading="lazy" decoding="async" onerror="this.outerHTML=\'<span class=&quot;initials&quot;>' . e(initials($m['name'])) . '</span>\'">';
    }
    return '<span class="initials" aria-hidden="true">' . e(initials($m['name'])) . '</span>';
}

function member_photo_url(array $m): string
{
    $file = 'assets/team/' . ($m['photo'] ?? '');
    return (!empty($m['photo']) && asset_exists($file)) ? asset($file) : '';
}

function render_member(array $m, bool $lead = false, string $extraClass = ''): void
{
    $skills = array_slice($m['skills'] ?? [], 0, 4);
    $lvl = [1 => 'Leadership', 2 => 'Specialist', 3 => 'Production'][(int)$m['level']] ?? '';
    ?>
    <article class="member<?= $lead ? ' lead' : '' ?> <?= e($extraClass) ?>" data-id="<?= e($m['id']) ?>" data-reveal tabindex="0" role="button" aria-label="Read more about <?= e($m['name']) ?>">
      <span class="lvl-badge"><?= e($lvl) ?></span>
      <div class="frame"><div class="frame-in"><?= member_photo($m, $lead ? 240 : 200) ?></div></div>
      <div class="m-info">
        <h3><?= e($m['name']) ?></h3>
        <p class="role"><?= e($m['role']) ?><?= !empty($m['experience']) ? ' <span style="white-space:nowrap">' . e($m['experience']) . '</span>' : '' ?></p>
      </div>
      <p class="bio"><?= e($m['shortBio'] ?? '') ?></p>
      <div class="chips">
        <?php foreach ($skills as $ci => $s): ?><span class="chip" style="--ci:<?= $ci ?>"><?= e($s) ?></span><?php endforeach; ?>
      </div>
      <?php $hasSocial = !empty($m['linkedin']) || !empty($m['instagram']) || !empty($m['email']); ?>
      <?php if ($hasSocial): ?>
      <div class="member-social">
        <?php if (!empty($m['linkedin'])): ?><a href="<?= e($m['linkedin']) ?>" target="_blank" rel="noopener" aria-label="<?= e($m['name']) ?> on LinkedIn"><?= icon('linkedin', '', 16) ?></a><?php endif; ?>
        <?php if (!empty($m['instagram'])): ?><a href="<?= e($m['instagram']) ?>" target="_blank" rel="noopener" aria-label="<?= e($m['name']) ?> on Instagram"><?= icon('instagram', '', 16) ?></a><?php endif; ?>
        <?php if (!empty($m['email'])): ?><a href="mailto:<?= e($m['email']) ?>" aria-label="Email <?= e($m['name']) ?>"><?= icon('mail', '', 16) ?></a><?php endif; ?>
      </div>
      <?php endif; ?>
      <span class="more">Read full bio <span aria-hidden="true">↗</span></span>
    </article>
<?php }

function sorted_team(): array
{
    $team = load_json('team');
    usort($team, fn($a, $b) => [$a['level'], $a['order'] ?? 99] <=> [$b['level'], $b['order'] ?? 99]);
    return $team;
}

function team_js_data(array $team): string
{
    $out = array_map(fn($m) => [
        'id' => $m['id'], 'name' => $m['name'], 'role' => $m['role'], 'level' => (int)$m['level'],
        'experience' => $m['experience'] ?? '', 'skills' => $m['skills'] ?? [], 'bio' => $m['bio'] ?? '',
        'linkedin' => $m['linkedin'] ?? '', 'instagram' => $m['instagram'] ?? '', 'email' => $m['email'] ?? '',
        'photoUrl' => member_photo_url($m), 'initials' => initials($m['name']),
    ], $team);
    return json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
}

/** Client logo or styled wordmark fallback */
function client_mark(array $c): string
{
    $file = 'assets/clients/' . ($c['logo'] ?? '');
    if (!empty($c['logo']) && asset_exists($file)) {
        return '<img src="' . e(asset($file)) . '" alt="' . e($c['name']) . ' logo" width="180" height="48" loading="lazy" decoding="async">';
    }
    return '<span class="client-word"><i>' . e(mb_substr($c['name'], 0, 1)) . '</i>' . e($c['name']) . '</span>';
}

function work_card(array $w, string $theme = 'dark'): void
{
    $logo = 'assets/clients/' . ($w['logo'] ?? '');
    $thumb = !empty($w['thumb']) && asset_exists('assets/work/' . $w['thumb']) ? asset('assets/work/' . $w['thumb']) : '';
    ?>
    <button type="button" class="work-card" data-id="<?= e($w['id']) ?>" data-cats="<?= e(implode(' ', $w['cats'])) ?>" data-reveal data-tilt aria-label="Open case study: <?= e($w['client']) ?>">
      <div class="work-thumb" style="--thumb:<?= e($w['gradient'] ?? '') ?>">
        <span class="work-tag"><?= e($w['industry']) ?></span>
        <?php if ($thumb): ?>
          <img src="<?= e($thumb) ?>" alt="<?= e($w['client'] . ' project by Connekt India') ?>" loading="lazy" width="640" height="440">
        <?php else: ?>
          <div class="thumb-art">
            <?php if (!empty($w['logo']) && asset_exists($logo)): ?>
              <img class="thumb-logo" src="<?= e(asset($logo)) ?>" alt="<?= e($w['client']) ?> logo" loading="lazy">
            <?php else: ?>
              <span class="thumb-word"><?= e($w['client']) ?></span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="work-body">
        <h3><?= e($w['client']) ?></h3>
        <p><?= e($w['summary']) ?></p>
        <span class="link-arrow">View case study <span class="arr">↗</span></span>
      </div>
    </button>
<?php }

function work_js_data(array $work): string
{
    return json_encode($work, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
}

/** Hex tile grid for the hero / about visuals (server-rendered, JS animates) */
function render_tiles(bool $badges = true): void
{
    $colors = ['#0b3dce', '#155dfc', '#3b7bff', '#80a6ff'];
    $cols = 7; $rows = 8;
    $w = 100 / ($cols - 0.5);           // tile width in % of the layer
    $h = $w * 1.1547;                   // pointy hex height
    mt_srand(7);
    ?>
    <div class="tiles-wrap" data-reveal="zoom" style="--rd:300">
      <div class="tiles" aria-hidden="true">
        <div class="tiles-layer">
          <?php for ($r = 0; $r < $rows; $r++): for ($c = 0; $c < $cols; $c++):
              $left = $c * $w - ($r % 2 ? $w / 2 : 0);
              $top  = $r * $h * 0.75 - $h * 0.25;
              $col  = $colors[mt_rand(0, 3)];
              $o    = number_format(mt_rand(18, 85) / 100, 2);
              $dur  = mt_rand(40, 90) / 10;
              $dl   = -mt_rand(0, 80) / 10;
          ?><span class="tile" style="left:<?= round($left, 3) ?>%;top:<?= round($top, 3) ?>%;width:<?= round($w, 3) ?>%;--c:<?= $col ?>;--o:<?= $o ?>;--d:<?= $dur ?>s;--dl:<?= $dl ?>s"></span><?php endfor; endfor; ?>
        </div>
        <div class="tiles-center">
          <span class="tile-halo"></span>
          <div class="tile-plate"><span class="tile-plate-inner"></span><?= logo_svg('tile-logo') ?></div>
        </div>
      </div>
      <?php if ($badges): ?>
      <div class="tiles-badge tb-1"><span class="icon-tile"><?= icon('trending-up') ?></span><div>Performance<small>Ads that convert</small></div></div>
      <div class="tiles-badge tb-2"><span class="icon-tile"><?= icon('sparkles') ?></span><div>Creative<small>Reels · Branding</small></div></div>
      <?php endif; ?>
    </div>
<?php }
