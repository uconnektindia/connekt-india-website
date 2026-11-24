<?php
/** Shared page header. Set $page before including. */
// HTML carries a per-session CSRF token, so it must never be shared-cached (e.g. LiteSpeed Cache)
if (!headers_sent()) header('Cache-Control: private, no-cache, must-revalidate');
$active = $page['nav'] ?? '';
$nav = [
    ['home',     'Home',     ''],
    ['about',    'About',    'about'],
    ['services', 'Services', 'services'],
    ['team',     'Our Team', 'team'],
    ['work',     'Work',     'work'],
    ['blog',     'Blog',     'blog'],
    ['contact',  'Contact',  'contact'],
];
?><!DOCTYPE html>
<html lang="en-IN" class="no-js">
<head>
<?php require __DIR__ . '/head-meta.php'; ?>
</head>
<body class="<?= e($page['body_class'] ?? '') ?>">
<a class="skip-link" href="#main">Skip to content</a>
<?php require __DIR__ . '/preloader.php'; ?>
<?php require __DIR__ . '/whatsapp.php'; ?>

<header class="site-header" id="siteHeader">
  <div class="wrap header-inner">
    <a class="brand" href="<?= url('') ?>" aria-label="Connekt India home">
      <?= logo_svg('brand-mark') ?>
      <span class="brand-text"><span class="brand-name">Connekt <b>India</b></span><span class="brand-tag">Ideas · Content · Growth</span></span>
    </a>

    <nav class="main-nav" aria-label="Main navigation">
      <ul>
        <?php foreach ($nav as $i => [$key, $label, $href]): ?>
          <li style="--i:<?= $i ?>"><a href="<?= url($href) ?>" class="<?= $active === $key ? 'is-active' : '' ?>" <?= $active === $key ? 'aria-current="page"' : '' ?>><?= e($label) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>

    <a class="btn btn-primary btn-sm header-cta" href="<?= url('contact') ?>">Let's Talk <span class="arr">↗</span></a>

    <button class="burger" id="burger" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<div class="mobile-menu" id="mobileMenu" aria-hidden="true">
  <div class="mm-bg"></div>
  <nav class="mm-nav" aria-label="Mobile navigation">
    <?php foreach ($nav as $i => [$key, $label, $href]): ?>
      <a href="<?= url($href) ?>" style="--i:<?= $i ?>" class="<?= $active === $key ? 'is-active' : '' ?>"><span class="mm-num">0<?= $i + 1 ?></span><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="mm-foot">
    <a class="btn btn-primary" href="<?= url('contact') ?>">Let's Talk <span class="arr">↗</span></a>
    <a class="mm-mail" href="mailto:<?= e(BUSINESS_EMAIL) ?>"><?= e(BUSINESS_EMAIL) ?></a>
  </div>
</div>

<main id="main">
