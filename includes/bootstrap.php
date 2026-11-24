<?php
/**
 * Connekt India: core bootstrap (loaded by every page, API and admin file)
 */
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

$__cfg = ROOT . '/config/config.php';
if (!is_file($__cfg)) { $__cfg = ROOT . '/config/config.sample.php'; }
require_once $__cfg;

if (defined('DEV_MODE') && DEV_MODE) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}
date_default_timezone_set('Asia/Kolkata');

if (session_status() === PHP_SESSION_NONE) {
    session_name('ck_sess');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/* ---------------------------------------------------------------
 * Paths & URLs
 * Works in the domain root (Hostinger) and in a sub-folder (XAMPP).
 * ------------------------------------------------------------- */
function base_path(): string
{
    static $base = null;
    if ($base !== null) return $base;
    $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
    $root    = realpath(ROOT) ?: ROOT;
    $base = '';
    if ($docRoot !== '' && str_starts_with($root, $docRoot)) {
        $base = str_replace('\\', '/', substr($root, strlen($docRoot)));
    }
    $base = trim($base, '/');
    $base = $base === '' ? '' : '/' . $base;
    return $base;
}

/** Internal page link: url('about') → /about  ·  url('') → / */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return base_path() . '/' . $path;
}

/** Static asset with cache-busting version */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = ROOT . '/' . $path;
    $v = is_file($file) ? '?v=' . filemtime($file) : '';
    return base_path() . '/' . $path . $v;
}

/** Absolute public URL (for canonical, OG, sitemap) */
function abs_url(string $path = ''): string
{
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}

function asset_exists(string $path): bool
{
    return is_file(ROOT . '/' . ltrim($path, '/'));
}

/* ---------------------------------------------------------------
 * Output helpers
 * ------------------------------------------------------------- */
function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function load_json(string $name): array
{
    static $cache = [];
    if (isset($cache[$name])) return $cache[$name];
    $file = ROOT . '/data/' . $name . '.json';
    if (!is_file($file)) return $cache[$name] = [];
    $data = json_decode((string)file_get_contents($file), true);
    return $cache[$name] = is_array($data) ? $data : [];
}

function wa_digits(): string
{
    return preg_replace('/\D+/', '', WHATSAPP_NUMBER);
}

function wa_link(string $text = "Hi Connekt India, I'd like to discuss a project."): string
{
    return 'https://wa.me/' . wa_digits() . '?text=' . rawurlencode($text);
}

function tel_link(): string
{
    return 'tel:+' . wa_digits();
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    $first = mb_substr($parts[0] ?? '', 0, 1);
    $last  = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
    return mb_strtoupper($first . $last);
}

function slugify(string $s): string
{
    $s = strtolower(trim($s));
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim((string)$s, '-');
}

/** Inline SVG icon. $cls is added to the <svg>. */
function icon(string $name, string $cls = '', int $size = 24): string
{
    static $set = null;
    if ($set === null) $set = require __DIR__ . '/icon-data.php';
    if (!isset($set[$name])) return '';
    [$vb, $stroke, $inner] = $set[$name];
    $attrs = $stroke
        ? 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"'
        : 'fill="currentColor"';
    return '<svg class="ico ' . e($cls) . '" width="' . $size . '" height="' . $size . '" viewBox="' . $vb . '" ' . $attrs . ' aria-hidden="true" focusable="false">' . $inner . '</svg>';
}

/** Logo mark as inline SVG (crisp at any size, animatable) */
function logo_svg(string $cls = '', bool $white = false): string
{
    $a = $white ? '#ffffff' : '#4074fe';
    $b = $white ? '#ffffff' : '#1f5dfc';
    return '<svg class="' . e($cls) . '" viewBox="60 30 505 575" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Connekt India">'
        . '<polygon class="lg-a" fill="' . $a . '" points="233,45 389,45 313,181 156,181"/>'
        . '<path class="lg-b" fill="' . $b . '" fill-rule="evenodd" d="M155,183 L470,183 L550,318 L470,453 L155,453 L75,318 Z M155,183 L312,183 L233,318 Z M392,318 L470,453 L314,453 Z"/>'
        . '<polygon class="lg-c" fill="' . $a . '" points="313,455 470,455 392,590 236,590"/></svg>';
}

/** Social links from config: [key, label, icon, url] in the required order */
function social_links(): array
{
    return [
        ['x',         'X (Twitter)', 'x-twitter', SOCIAL_X],
        ['youtube',   'YouTube',     'youtube',   SOCIAL_YOUTUBE],
        ['linkedin',  'LinkedIn',    'linkedin',  SOCIAL_LINKEDIN],
        ['instagram', 'Instagram',   'instagram', SOCIAL_INSTAGRAM],
        ['facebook',  'Facebook',    'facebook',  SOCIAL_FACEBOOK],
    ];
}

/* ---------------------------------------------------------------
 * Services (single source of truth)
 * ------------------------------------------------------------- */
function services(): array
{
    return [
        [
            'id' => 'digital-marketing', 'name' => 'Digital Marketing', 'icon' => 'megaphone',
            'short' => 'Full-funnel strategy, SEO and content that turns attention into enquiries.',
            'desc' => 'We build a clear digital roadmap for your brand: who to reach, what to say and where to say it. Then we execute with search engine optimisation, helpful content and honest analytics, so every rupee you spend has a job to do.',
            'items' => ['Digital marketing strategy & roadmap', 'On-page, technical & local SEO', 'Google Business Profile optimisation', 'Content marketing & blogging', 'Email & WhatsApp marketing', 'Analytics setup & monthly reporting'],
        ],
        [
            'id' => 'social-media', 'name' => 'Social Media Management', 'icon' => 'social',
            'short' => 'Calendars, posts, reels and community management that build a loyal following.',
            'desc' => 'We plan, create and manage content that people actually stop scrolling for. From monthly content calendars to reels, captions and community replies, we keep your brand active, consistent and on-trend across Instagram, Facebook, LinkedIn and YouTube.',
            'items' => ['Monthly content calendars', 'Post & carousel design', 'Reels concept, shoot & edit', 'Captions, hashtags & scheduling', 'Community & DM management', 'Influencer collaborations'],
        ],
        [
            'id' => 'performance-marketing', 'name' => 'Performance Marketing', 'icon' => 'target',
            'short' => 'Meta Ads and Google Ads built around leads, sales and measurable ROI.',
            'desc' => 'We run paid campaigns that are measured on results, not impressions. Structured testing, sharp creatives, proper conversion tracking and weekly optimisation help you get more leads and sales from the same budget.',
            'items' => ['Meta (Facebook & Instagram) Ads', 'Google Search, Display & YouTube Ads', 'Retargeting & lookalike audiences', 'Pixel, Conversions API & GA4 tracking', 'Landing page & funnel optimisation', 'Weekly performance reports'],
        ],
        [
            'id' => 'creative-branding', 'name' => 'Creative & Branding', 'icon' => 'palette',
            'short' => 'Logos, identities, graphics and motion that make your brand unforgettable.',
            'desc' => 'Your brand is the feeling people get when they see you. We craft identities, visual systems and content that look premium and stay consistent everywhere, from packaging and print to motion graphics and video.',
            'items' => ['Logo & brand identity', 'Brand guidelines & colour systems', 'Social media & print graphics', 'Packaging & collateral design', 'Video editing', 'Motion graphics & animation'],
        ],
        [
            'id' => 'web-development', 'name' => 'Web Development', 'icon' => 'monitor',
            'short' => 'Fast, SEO-ready websites, e-commerce stores and landing pages.',
            'desc' => 'We design and build websites that load fast, rank well and convert visitors into customers. Every site is mobile-first, secure and easy for you to update, whether it is a business website, an online store or a campaign landing page.',
            'items' => ['Business & corporate websites', 'E-commerce stores (Razorpay & more)', 'High-converting landing pages', 'UI/UX design & prototyping', 'Speed, SEO & security setup', 'Hosting, domain & maintenance'],
        ],
        [
            'id' => 'software-development', 'name' => 'Software Development', 'icon' => 'cpu',
            'short' => 'Custom software, web apps, mobile apps, CRMs and automation.',
            'desc' => 'When off-the-shelf tools do not fit, we build software that does. Custom web apps, CRMs, dashboards, mobile apps and automations that remove repetitive work and give you real-time visibility of your business.',
            'items' => ['Custom web applications', 'Android & iOS mobile apps', 'CRM & lead management systems', 'Dashboards & reporting tools', 'Workflow & WhatsApp automation', 'API integrations & AI tools'],
        ],
    ];
}

/* ---------------------------------------------------------------
 * CSRF
 * ------------------------------------------------------------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(?string $token): bool
{
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function client_ip(): string
{
    return substr((string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

/* ---------------------------------------------------------------
 * Database (PDO, prepared statements only)
 * ------------------------------------------------------------- */
function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;
    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    if (defined('DB_DSN')) {                       // optional override (local testing)
        $pdo = new PDO(DB_DSN, null, null, $opts);
    } else {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opts);
    }
    return $pdo;
}

function now(): string
{
    return date('Y-m-d H:i:s');
}
