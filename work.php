<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/components.php';

$work = load_json('work');
$page = [
    'title' => 'Our Work | Portfolio & Case Studies | Connekt India',
    'description' => 'Case studies from Connekt India: social media, branding, performance marketing, websites and software for WarZone, Iron Empire, Fitness Hour, Novus, Carbon and Akhandalamani Jewellers.',
    'path' => 'work',
    'nav' => 'work',
    'jsonld' => [breadcrumb_jsonld([['Home', ''], ['Work', 'work']])],
    'scripts' => ['work.js'],
];
require __DIR__ . '/includes/header.php';

render_page_hero('Our work', 'WORK THAT <span class="blue">SPEAKS.</span>', 'A look at how we have helped brands across fitness, jewellery, entertainment and business grow with content, ads, design and technology.', [['Work', null]]);

$filters = ['all' => 'All', 'social' => 'Social Media', 'branding' => 'Branding', 'performance' => 'Performance', 'web' => 'Web', 'software' => 'Software'];
?>

<section class="sec sec-light">
  <div class="wrap">
    <div class="filters" role="group" aria-label="Filter projects" data-reveal>
      <?php foreach ($filters as $k => $label): ?>
        <button type="button" class="filter-btn<?= $k === 'all' ? ' is-active' : '' ?>" data-filter="<?= e($k) ?>" aria-pressed="<?= $k === 'all' ? 'true' : 'false' ?>"><?= e($label) ?></button>
      <?php endforeach; ?>
    </div>
    <div class="work-grid" data-stagger="90">
      <?php foreach ($work as $w) work_card($w, 'light'); ?>
    </div>
  </div>
  <script type="application/json" id="workData"><?= work_js_data($work) ?></script>
</section>

<section class="sec sec-dark">
  <div class="wrap">
    <div class="sec-head center" data-reveal>
      <p class="eyebrow">Clients</p>
      <h2 class="h2" data-split>BRANDS WE'RE <span class="blue">PROUD OF.</span></h2>
    </div>
  </div>
  <div class="marquee" data-reveal>
    <div class="marquee-track">
      <?php for ($g = 0; $g < 2; $g++): ?>
        <div class="marquee-group" <?= $g ? 'aria-hidden="true"' : '' ?>>
          <?php for ($rep = 0; $rep < 2; $rep++): foreach (load_json('clients') as $c): ?>
            <div class="client"><?= client_mark($c) ?></div>
          <?php endforeach; endfor; ?>
        </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<?php render_process('sec-light'); ?>
<?php render_cta('YOUR BRAND <br>COULD BE NEXT.'); ?>
<?php require __DIR__ . '/includes/footer.php';
