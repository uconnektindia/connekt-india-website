<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/components.php';

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower((string)($_GET['slug'] ?? '')));
$posts = load_json('blogs');
$post = null;
foreach ($posts as $p) { if ($p['slug'] === $slug) { $post = $p; break; } }
if (!$post) { http_response_code(404); require __DIR__ . '/404.php'; exit; }

// content: resolve internal links + add ids to H2s for the table of contents
$content = str_replace('{{base}}', base_path(), $post['content']);
$toc = [];
$content = preg_replace_callback('/<h2>(.*?)<\/h2>/s', function ($m) use (&$toc) {
    $text = trim(strip_tags($m[1]));
    $id = slugify($text);
    $toc[] = [$id, $text];
    return '<h2 id="' . $id . '">' . $m[1] . '</h2>';
}, $content);

$url = abs_url('blog/' . $post['slug']);
$imgPath = 'assets/blog/' . $post['image'];
$imgAbs = asset_exists($imgPath) ? abs_url($imgPath) : abs_url('assets/video/hero-poster.jpg');
$faqs = array_map(fn($f) => [$f['q'], $f['a']], $post['faq'] ?? []);

// related: same category first, then latest
$related = array_values(array_filter($posts, fn($p) => $p['slug'] !== $post['slug']));
usort($related, function ($a, $b) use ($post) {
    $sa = $a['category'] === $post['category'] ? 1 : 0; $sb = $b['category'] === $post['category'] ? 1 : 0;
    return [$sb, $b['date']] <=> [$sa, $a['date']];
});
$related = array_slice($related, 0, 3);

$page = [
    'title' => $post['title'] . ' | Connekt India',
    'description' => $post['metaDescription'],
    'keywords' => $post['keywords'],
    'canonical' => $url,
    'og_image' => $imgAbs,
    'og_type' => 'article',
    'nav' => 'blog',
    'jsonld' => [
        [
            '@context' => 'https://schema.org', '@type' => 'Article',
            'headline' => $post['title'], 'description' => $post['metaDescription'], 'image' => [$imgAbs],
            'datePublished' => $post['date'], 'dateModified' => $post['date'],
            'author' => ['@type' => 'Organization', 'name' => SITE_NAME, 'url' => abs_url()],
            'publisher' => ['@type' => 'Organization', 'name' => SITE_NAME, 'logo' => ['@type' => 'ImageObject', 'url' => abs_url('assets/logo/logo.png')]],
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
            'keywords' => $post['keywords'], 'articleSection' => $post['category'],
        ],
        faq_jsonld($faqs),
        breadcrumb_jsonld([['Home', ''], ['Blog', 'blog'], [$post['title'], 'blog/' . $post['slug']]]),
    ],
    'scripts' => ['blog.js'],
];
require __DIR__ . '/includes/header.php';
$share = rawurlencode($url);
$shareText = rawurlencode($post['title']);
?>
<div class="progress-bar" id="readProgress" aria-hidden="true"></div>

<section class="page-hero grid-bg" style="padding-bottom:clamp(40px,6vw,64px)">
  <div class="glow-blob" aria-hidden="true"></div>
  <div class="wrap">
    <nav class="crumbs" aria-label="Breadcrumb" data-reveal>
      <a href="<?= url('') ?>">Home</a><span aria-hidden="true">/</span><a href="<?= url('blog') ?>">Blog</a><span aria-hidden="true">/</span><span aria-current="page"><?= e($post['category']) ?></span>
    </nav>
    <p class="eyebrow" data-reveal><?= e($post['category']) ?></p>
    <h1 class="h2" style="max-width:24ch;text-transform:none;letter-spacing:-.02em" data-reveal><?= e($post['title']) ?></h1>
    <div class="post-meta" style="margin-top:20px;color:#a4adbf" data-reveal>
      <span><?= icon('calendar', '', 16) ?></span><time datetime="<?= e($post['date']) ?>"><?= e(date('j F Y', strtotime($post['date']))) ?></time>
      <span>·</span><span><?= e($post['readTime']) ?></span><span>·</span><span><?= e($post['author']) ?></span>
    </div>
    <?php if (asset_exists($imgPath)): ?>
    <figure class="post-hero-img" data-reveal="zoom">
      <img src="<?= e(asset($imgPath)) ?>" alt="<?= e($post['imageAlt']) ?>" width="1200" height="675" fetchpriority="high">
    </figure>
    <?php endif; ?>
  </div>
</section>

<section class="sec sec-white" style="padding-top:clamp(40px,6vw,72px)">
  <div class="wrap post-layout">
    <article class="prose" id="article">
      <?= $content ?>

      <?php if ($faqs): ?>
      <section class="post-faq" aria-labelledby="faq-title">
        <h2 id="faq-title">Frequently asked questions</h2>
        <?php foreach ($faqs as [$q, $a]): ?>
          <h3><?= e($q) ?></h3>
          <p><?= e($a) ?></p>
        <?php endforeach; ?>
      </section>
      <?php endif; ?>

      <div class="post-cta">
        <p class="eyebrow" style="margin-bottom:10px">Work with Connekt India</p>
        <h2 style="color:#fff;margin-top:0">Want results like these for your brand?</h2>
        <p>Our Bhubaneswar team plans, creates and runs the marketing, content and technology that grow businesses. Get a free strategy call and a clear plan.</p>
        <div class="btn-row">
          <a class="btn btn-primary" href="<?= url('contact') ?>">Start a Conversation <span class="arr">↗</span></a>
          <a class="btn btn-wa" href="<?= e(wa_link()) ?>" target="_blank" rel="noopener" style="color:#06200f">WhatsApp us</a>
        </div>
      </div>
    </article>

    <aside class="aside-sticky" aria-label="Article tools">
      <div class="aside-card">
        <h3>Share</h3>
        <div class="share">
          <a href="https://wa.me/?text=<?= $shareText ?>%20<?= $share ?>" target="_blank" rel="noopener" aria-label="Share on WhatsApp"><?= icon('whatsapp', '', 18) ?></a>
          <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $share ?>" target="_blank" rel="noopener" aria-label="Share on LinkedIn"><?= icon('linkedin', '', 18) ?></a>
          <a href="https://x.com/intent/post?url=<?= $share ?>&amp;text=<?= $shareText ?>" target="_blank" rel="noopener" aria-label="Share on X"><?= icon('x-twitter', '', 18) ?></a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $share ?>" target="_blank" rel="noopener" aria-label="Share on Facebook"><?= icon('facebook', '', 18) ?></a>
          <button type="button" data-copy="<?= e($url) ?>" aria-label="Copy link"><?= icon('link', '', 18) ?></button>
        </div>
      </div>
      <?php if ($toc): ?>
      <div class="aside-card">
        <h3>In this article</h3>
        <nav class="toc">
          <?php foreach ($toc as [$id, $text]): ?><a href="#<?= e($id) ?>"><?= e($text) ?></a><?php endforeach; ?>
        </nav>
      </div>
      <?php endif; ?>
      <div class="aside-card dark">
        <h3 style="color:#80a6ff">Free consultation</h3>
        <p>Tell us about your business and goals. We'll reply within 24 hours with ideas you can use.</p>
        <a class="btn btn-primary btn-block" href="<?= url('contact') ?>">Talk to us <span class="arr">↗</span></a>
      </div>
    </aside>
  </div>
</section>

<section class="sec sec-light">
  <div class="wrap">
    <div class="sec-head-row">
      <div class="sec-head" data-reveal>
        <p class="eyebrow">Keep reading</p>
        <h2 class="h2">RELATED <span class="blue">ARTICLES.</span></h2>
      </div>
      <a class="link-arrow" href="<?= url('blog') ?>" data-reveal>All articles <span class="arr">↗</span></a>
    </div>
    <div class="blog-grid" data-stagger="90">
      <?php foreach ($related as $r): $ri = 'assets/blog/' . $r['image']; ?>
        <a class="post-card" href="<?= url('blog/' . $r['slug']) ?>" data-reveal>
          <div class="post-thumb"><?php if (asset_exists($ri)): ?><img src="<?= e(asset($ri)) ?>" alt="<?= e($r['imageAlt']) ?>" width="1200" height="675" loading="lazy" decoding="async"><?php endif; ?></div>
          <div class="post-body">
            <div class="post-meta"><span class="post-cat"><?= e($r['category']) ?></span><span><?= e($r['readTime']) ?></span></div>
            <h3><?= e($r['title']) ?></h3>
            <span class="link-arrow">Read article <span class="arr">↗</span></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php';
