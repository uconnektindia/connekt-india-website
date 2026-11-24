<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/components.php';

$posts = load_json('blogs');
usort($posts, fn($a, $b) => strcmp($b['date'], $a['date']));
$cats = array_values(array_unique(array_map(fn($p) => $p['category'], $posts)));

$page = [
    'title' => 'Blog | Digital Marketing, Social Media & Web Tips | Connekt India',
    'description' => 'Practical guides on digital marketing, Instagram reels, Meta and Google Ads, branding, websites and automation for businesses in Bhubaneswar, Odisha and India.',
    'path' => 'blog',
    'nav' => 'blog',
    'jsonld' => [
        breadcrumb_jsonld([['Home', ''], ['Blog', 'blog']]),
        ['@context' => 'https://schema.org', '@type' => 'Blog', 'name' => 'Connekt India Blog', 'url' => abs_url('blog'),
         'blogPost' => array_map(fn($p) => ['@type' => 'BlogPosting', 'headline' => $p['title'], 'url' => abs_url('blog/' . $p['slug']), 'datePublished' => $p['date']], $posts)],
    ],
    'scripts' => ['blog.js'],
];
require __DIR__ . '/includes/header.php';

render_page_hero('Insights', 'IDEAS THAT <span class="blue">GROW BRANDS.</span>', 'Practical, no-fluff guides on marketing, content, ads, branding and technology for Indian businesses.', [['Blog', null]]);
?>

<section class="sec sec-light">
  <div class="wrap">
    <div class="blog-tools" data-reveal>
      <div class="search-box">
        <?= icon('search') ?>
        <label class="sr-only" for="blogSearch">Search articles</label>
        <input id="blogSearch" type="search" placeholder="Search articles…" autocomplete="off">
      </div>
      <div class="filters" style="margin:0" role="group" aria-label="Filter by category">
        <button type="button" class="filter-btn is-active" data-cat="all" aria-pressed="true">All</button>
        <?php foreach ($cats as $c): ?>
          <button type="button" class="filter-btn" data-cat="<?= e(slugify($c)) ?>" aria-pressed="false"><?= e($c) ?></button>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="blog-grid" id="blogGrid" data-stagger="80">
      <?php foreach ($posts as $p):
        $img = 'assets/blog/' . $p['image'];
        $search = strtolower($p['title'] . ' ' . $p['excerpt'] . ' ' . $p['keywords'] . ' ' . $p['category']); ?>
        <a class="post-card" href="<?= url('blog/' . $p['slug']) ?>" data-cat="<?= e(slugify($p['category'])) ?>" data-search="<?= e($search) ?>" data-reveal>
          <div class="post-thumb">
            <?php if (asset_exists($img)): ?>
              <img src="<?= e(asset($img)) ?>" alt="<?= e($p['imageAlt']) ?>" width="1200" height="675" loading="lazy" decoding="async">
            <?php endif; ?>
          </div>
          <div class="post-body">
            <div class="post-meta"><span class="post-cat"><?= e($p['category']) ?></span><span><?= e(date('j M Y', strtotime($p['date']))) ?></span><span><?= e($p['readTime']) ?></span></div>
            <h3><?= e($p['title']) ?></h3>
            <p><?= e($p['excerpt']) ?></p>
            <span class="link-arrow">Read article <span class="arr">↗</span></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <p class="no-results" id="noResults">No articles match your search. Try another keyword.</p>
    <nav class="pagination" id="blogPager" aria-label="Blog pages"></nav>
  </div>
</section>

<?php render_cta('NEED HELP <br>PUTTING IT TO WORK?'); ?>
<?php require __DIR__ . '/includes/footer.php';
