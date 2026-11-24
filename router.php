<?php
/**
 * DEV ONLY: router for PHP's built-in server (mimics .htaccess clean URLs).
 *   php -S localhost:8000 router.php
 * Not used on Hostinger (Apache/LiteSpeed uses .htaccess). Blocked from the web by .htaccess.
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$root = __DIR__;
if (preg_match('#^/(config|logs|vendor|includes|uploads)(/|$)#', $uri) || preg_match('#\.(sql|md|log|ini)$#', $uri) || $uri === '/router.php') { http_response_code(403); require $root . '/404.php'; return true; }
if ($uri === '/sitemap.xml') { require $root . '/sitemap.php'; return true; }
if (preg_match('#^/blog/([a-z0-9-]+)/?$#', $uri, $m)) { $_GET['slug'] = $m[1]; require $root . '/blog-post.php'; return true; }
if ($uri !== '/' && is_file($root . $uri)) {
    if (str_ends_with($uri, '.php')) { chdir(dirname($root . $uri)); require $root . $uri; return true; }
    return false; // static file
}
if (is_dir($root . $uri) && is_file(rtrim($root . $uri, '/') . '/index.php')) { require rtrim($root . $uri, '/') . '/index.php'; return true; }
$php = $root . rtrim($uri, '/') . '.php';
if ($uri === '/') { require $root . '/index.php'; return true; }
if (is_file($php)) { require $php; return true; }
http_response_code(404);
require $root . '/404.php';
return true;
