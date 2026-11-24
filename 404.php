<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/components.php';
http_response_code(404);
$page = [
    'title' => 'Page not found | Connekt India',
    'description' => 'The page you are looking for has drifted into space. Head back to the Connekt India home page.',
    'path' => '404',
    'robots' => 'noindex, follow',
    'nav' => '',
];
require __DIR__ . '/includes/header.php';
?>
<section class="lost sec-dark">
  <div class="stars-bg" aria-hidden="true"></div>
  <div class="glow-blob" style="left:30%;top:20%" aria-hidden="true"></div>
  <div class="wrap">
    <svg class="astro" viewBox="0 0 160 160" aria-hidden="true">
      <defs><linearGradient id="vis" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#80a6ff"/><stop offset="1" stop-color="#0b3dce"/></linearGradient></defs>
      <rect x="52" y="78" width="56" height="54" rx="18" fill="#e8ecf4"/>
      <rect x="36" y="86" width="20" height="34" rx="10" fill="#d4dbe8" transform="rotate(18 46 103)"/>
      <rect x="104" y="86" width="20" height="34" rx="10" fill="#d4dbe8" transform="rotate(-24 114 103)"/>
      <rect x="58" y="126" width="18" height="24" rx="8" fill="#d4dbe8"/>
      <rect x="84" y="126" width="18" height="24" rx="8" fill="#d4dbe8"/>
      <circle cx="80" cy="56" r="34" fill="#f4f6fb"/>
      <rect x="58" y="40" width="44" height="32" rx="14" fill="url(#vis)"/>
      <rect x="64" y="45" width="12" height="6" rx="3" fill="#ffffff88"/>
      <rect x="68" y="94" width="24" height="14" rx="4" fill="#155dfc"/>
      <circle cx="80" cy="101" r="3" fill="#fff"/>
    </svg>
    <p class="eyebrow" style="justify-content:center;display:flex">Error 404</p>
    <h1 class="lost-num">404</h1>
    <h2 class="h3" style="margin:12px 0 14px">LOST IN <span class="blue">SPACE.</span></h2>
    <p class="lead" style="margin-inline:auto;color:#a4adbf">The page you're looking for has drifted out of orbit. Let's get you back to familiar ground.</p>
    <div class="btn-row" style="justify-content:center;margin-top:30px">
      <a class="btn btn-primary" href="<?= url('') ?>">Back to Home <span class="arr">↗</span></a>
      <a class="btn btn-ghost" href="<?= url('contact') ?>">Contact us</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php';
