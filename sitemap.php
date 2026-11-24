<?php
/** Dynamic XML sitemap (served at /sitemap.xml via .htaccess). Includes every blog post. */
require __DIR__ . '/includes/bootstrap.php';
header('Content-Type: application/xml; charset=utf-8');

$pages = [
    ['', '1.0', 'weekly', 'index.php'],
    ['about', '0.8', 'monthly', 'about.php'],
    ['services', '0.9', 'monthly', 'services.php'],
    ['team', '0.7', 'monthly', 'team.php'],
    ['work', '0.8', 'monthly', 'work.php'],
    ['blog', '0.8', 'weekly', 'blog.php'],
    ['contact', '0.8', 'yearly', 'contact.php'],
    ['privacy-policy', '0.3', 'yearly', 'privacy-policy.php'],
    ['terms', '0.3', 'yearly', 'terms.php'],
];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
foreach ($pages as [$path, $prio, $freq, $file]) {
    $mod = is_file(__DIR__ . '/' . $file) ? date('Y-m-d', filemtime(__DIR__ . '/' . $file)) : date('Y-m-d');
    echo "  <url><loc>" . e(abs_url($path)) . "</loc><lastmod>$mod</lastmod><changefreq>$freq</changefreq><priority>$prio</priority></url>\n";
}
foreach (load_json('blogs') as $p) {
    $img = 'assets/blog/' . $p['image'];
    echo "  <url><loc>" . e(abs_url('blog/' . $p['slug'])) . "</loc><lastmod>" . e($p['date']) . "</lastmod><changefreq>monthly</changefreq><priority>0.7</priority>";
    if (asset_exists($img)) echo "<image:image><image:loc>" . e(abs_url($img)) . "</image:loc></image:image>";
    echo "</url>\n";
}
echo "</urlset>\n";
