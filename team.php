<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/components.php';

$team = sorted_team();
$levels = [];
foreach ($team as $m) { $levels[(int)$m['level']][] = $m; }
ksort($levels);

$people = array_map(fn($m) => [
    '@type' => 'Person', 'name' => $m['name'], 'jobTitle' => $m['role'],
    'worksFor' => ['@id' => abs_url('#business')],
], $team);
$page = [
    'title' => 'Our Team | The People Behind Connekt India, Bhubaneswar',
    'description' => 'Meet the Connekt India team: operations, social media, SEO & Meta Ads, tech & AI, design, videography and video editing specialists in Bhubaneswar.',
    'path' => 'team',
    'nav' => 'team',
    'jsonld' => [breadcrumb_jsonld([['Home', ''], ['Our Team', 'team']]), ['@context' => 'https://schema.org', '@graph' => $people]],
    'scripts' => ['team.js'],
];
require __DIR__ . '/includes/header.php';

render_page_hero('Our team', 'THE MINDS BEHIND <span class="blue">THE MAGIC.</span>', 'Strategists, creators and builders who care about your growth as much as you do. Tap any card to read the full story.', [['Our Team', null]]);
?>

<section class="sec sec-dark grid-bg">
  <div class="glow-blob" style="left:20%;top:0" aria-hidden="true"></div>
  <div class="wrap">
    <div class="tree" data-stagger="110">
      <svg class="tree-svg" aria-hidden="true"></svg>
      <?php foreach ($levels as $lvl => $members): ?>
        <div class="tree-row" data-level="<?= (int)$lvl ?>">
          <?php foreach ($members as $m) render_member($m, $lvl === 1); ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <script type="application/json" id="teamData"><?= team_js_data($team) ?></script>
</section>

<section class="sec sec-light">
  <div class="wrap split">
    <div data-reveal="left">
      <p class="eyebrow">Careers</p>
      <h2 class="h2" data-split>WANT TO JOIN <span class="blue">THE TEAM?</span></h2>
      <p class="muted" style="margin-top:16px">We're always looking for curious marketers, designers, video editors and developers in Bhubaneswar. Internships are open too.</p>
      <div class="btn-row" style="margin-top:26px">
        <a class="btn btn-primary" href="<?= url('contact?type=career') ?>">Apply now <span class="arr">↗</span></a>
      </div>
    </div>
    <div class="grid-2" data-stagger="90">
      <?php foreach ([['megaphone', 'Marketing'], ['palette', 'Design'], ['film', 'Video'], ['code', 'Development']] as [$ic, $t]): ?>
        <div class="card draw" data-reveal data-inview data-tilt>
          <span class="icon-tile"><?= icon($ic) ?></span>
          <h3><?= e($t) ?></h3>
          <p>Roles & internships</p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php render_cta(); ?>
<?php require __DIR__ . '/includes/footer.php';
