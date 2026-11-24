<?php
/** <head> contents: SEO, Open Graph, Twitter, JSON-LD, fonts, CSS. Expects $page array. */
$title       = $page['title'] ?? 'Connekt India | Digital Marketing Agency in Bhubaneswar';
$description = $page['description'] ?? 'Connekt India is a digital marketing, creative and technology agency in Bhubaneswar, Odisha. Social media, performance ads, branding, websites and custom software.';
$canonical   = $page['canonical'] ?? abs_url($page['path'] ?? '');
$ogImage     = $page['og_image'] ?? abs_url('assets/video/hero-poster.jpg');
$ogType      = $page['og_type'] ?? 'website';
$robots      = $page['robots'] ?? 'index, follow, max-image-preview:large';

$sameAs = array_values(array_filter(array_map(fn($s) => $s[3], social_links())));
$localBusiness = [
    '@context' => 'https://schema.org',
    '@type' => 'ProfessionalService',
    '@id' => abs_url('#business'),
    'name' => SITE_NAME,
    'description' => 'Digital marketing, creative and technology agency in Bhubaneswar, Odisha.',
    'url' => abs_url(),
    'logo' => abs_url('assets/logo/logo.png'),
    'image' => abs_url('assets/logo/logo.png'),
    'email' => BUSINESS_EMAIL,
    'telephone' => '+' . wa_digits(),
    'priceRange' => '₹₹',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => '446, Saheed Nagar',
        'addressLocality' => 'Bhubaneswar',
        'addressRegion' => 'Odisha',
        'postalCode' => '751007',
        'addressCountry' => 'IN',
    ],
    'geo' => ['@type' => 'GeoCoordinates', 'latitude' => GEO_LAT, 'longitude' => GEO_LNG],
    'areaServed' => ['Bhubaneswar', 'Odisha', 'India'],
    'sameAs' => $sameAs,
];
$jsonld = array_merge([$localBusiness], $page['jsonld'] ?? []);
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<?php if (!empty($page['keywords'])): ?><meta name="keywords" content="<?= e($page['keywords']) ?>"><?php endif; ?>
<meta name="robots" content="<?= e($robots) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta name="theme-color" content="#05070b">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">

<meta property="og:type" content="<?= e($ogType) ?>">
<meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta property="og:locale" content="en_IN">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($title) ?>">
<meta name="twitter:description" content="<?= e($description) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">

<link rel="icon" type="image/png" sizes="32x32" href="<?= asset('assets/logo/favicon-32.png') ?>">
<link rel="icon" type="image/png" sizes="512x512" href="<?= asset('assets/logo/favicon.png') ?>">
<link rel="apple-touch-icon" href="<?= asset('assets/logo/apple-touch-icon.png') ?>">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<?php if (!empty($page['preload_image'])): ?><link rel="preload" as="image" href="<?= e($page['preload_image']) ?>"><?php endif; ?>

<script>
/* Runs before paint: decide preloader mode + flag JS + reduced motion */
(function(d){var h=d.documentElement;h.classList.remove('no-js');h.classList.add('js');
try{var seen=sessionStorage.getItem('ck_seen');h.classList.add(seen?'pl-quick':'pl-full');sessionStorage.setItem('ck_seen','1');}catch(e){h.classList.add('pl-quick');}
if(window.matchMedia&&matchMedia('(prefers-reduced-motion: reduce)').matches)h.classList.add('reduce-motion');
})(document);
window.CK = {
  base: <?= json_encode(base_path()) ?>,
  wa: <?= json_encode(wa_link()) ?>,
  waNumber: <?= json_encode(wa_digits()) ?>,
  hours: <?= json_encode(WORKING_HOURS) ?>,
  email: <?= json_encode(BUSINESS_EMAIL) ?>,
  phone: <?= json_encode(PHONE_DISPLAY) ?>,
  recaptcha: <?= json_encode(RECAPTCHA_SITE_KEY) ?>,
  dev: <?= DEV_MODE ? 'true' : 'false' ?>
};
</script>
<?php foreach ($jsonld as $ld): ?>
<script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
<?php endforeach; ?>
