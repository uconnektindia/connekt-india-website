<?php
/**
 * Shared helpers for /api endpoints:
 * POST-only, JSON or FormData input, CSRF, honeypot, rate limit, reCAPTCHA v3, sanitising.
 */
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/Mailer.php';
require_once __DIR__ . '/mail-templates.php';

function api_json(bool $success, string $message, array $extra = [], int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

/** Validate method, parse JSON bodies, check CSRF + honeypot. Returns the input array. */
function api_init(): array
{
    header('X-Content-Type-Options: nosniff');
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        api_json(false, 'Method not allowed.', [], 405);
    }
    $input = $_POST;
    $ctype = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($ctype, 'application/json') !== false) {
        $raw = file_get_contents('php://input', false, null, 0, 100000);
        $json = json_decode((string)$raw, true);
        if (is_array($json)) $input = $json;
    }
    // honeypot: bots fill hidden fields → pretend success, store nothing
    if (!empty($input['website'])) {
        api_json(true, 'Thank you!');
    }
    if (!csrf_check($input['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null))) {
        api_json(false, 'Your session expired. Please refresh the page and try again.', [], 419);
    }
    return $input;
}

function clean(mixed $v, int $max = 255): string
{
    $v = is_string($v) ? $v : '';
    $v = strip_tags($v);
    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $v) ?? '';
    $v = trim(preg_replace('/[ \t]+/u', ' ', $v) ?? '');
    return mb_substr($v, 0, $max);
}

function clean_multiline(mixed $v, int $max = 3000): string
{
    $v = is_string($v) ? $v : '';
    $v = strip_tags($v);
    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $v) ?? '';
    $v = preg_replace("/\r\n?/", "\n", $v) ?? '';
    $v = preg_replace("/\n{3,}/", "\n\n", $v) ?? '';
    return mb_substr(trim($v), 0, $max);
}

function valid_email(string $e): bool
{
    return (bool)filter_var($e, FILTER_VALIDATE_EMAIL) && mb_strlen($e) <= 150;
}

/** Normalise to 10 digits; accepts +91 / 0 prefixes */
function normalize_phone(string $p): string
{
    $d = preg_replace('/\D+/', '', $p) ?? '';
    if (strlen($d) === 12 && str_starts_with($d, '91')) $d = substr($d, 2);
    if (strlen($d) === 11 && str_starts_with($d, '0')) $d = substr($d, 1);
    return $d;
}

function valid_phone(string $p): bool
{
    return (bool)preg_match('/^[6-9]\d{9}$/', $p);
}

/** Max $limit submissions per IP per $window seconds for an action */
function rate_limit(string $action, int $limit = 5, int $window = 600): void
{
    try {
        $pdo = db();
        $ip = client_ip();
        $since = date('Y-m-d H:i:s', time() - $window);
        $st = $pdo->prepare('SELECT COUNT(*) FROM rate_limits WHERE ip = ? AND action = ? AND created_at > ?');
        $st->execute([$ip, $action, $since]);
        if ((int)$st->fetchColumn() >= $limit) {
            api_json(false, 'Too many submissions. Please wait a few minutes or message us on WhatsApp.', [], 429);
        }
        $pdo->prepare('INSERT INTO rate_limits (ip, action, created_at) VALUES (?, ?, ?)')->execute([$ip, $action, now()]);
        // housekeeping (1% of requests)
        if (random_int(1, 100) === 1) {
            $pdo->prepare('DELETE FROM rate_limits WHERE created_at < ?')->execute([date('Y-m-d H:i:s', time() - 86400)]);
        }
    } catch (Throwable $e) {
        log_error('rate_limit: ' . $e->getMessage());
        api_json(false, 'Our server is temporarily unavailable. Please WhatsApp us instead.', [], 503);
    }
}

/** Optional Google reCAPTCHA v3 check (only when keys are configured) */
function recaptcha_ok(?string $token, string $action): bool
{
    if (RECAPTCHA_SECRET === '' || RECAPTCHA_SITE_KEY === '') return true;
    if (!$token) return false;
    $post = http_build_query(['secret' => RECAPTCHA_SECRET, 'response' => $token, 'remoteip' => client_ip()]);
    $res = false;
    if (function_exists('curl_init')) {
        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $post, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8]);
        $res = curl_exec($ch);
        curl_close($ch);
    } else {
        $ctx = stream_context_create(['http' => ['method' => 'POST', 'header' => 'Content-Type: application/x-www-form-urlencoded', 'content' => $post, 'timeout' => 8]]);
        $res = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $ctx);
    }
    $j = json_decode((string)$res, true);
    return is_array($j) && !empty($j['success']) && ($j['score'] ?? 0) >= 0.5 && (($j['action'] ?? $action) === $action);
}

function log_error(string $msg): void
{
    $dir = ROOT . '/logs';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    @file_put_contents($dir . '/error.log', '[' . date('c') . '] ' . $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function user_agent(): string
{
    return mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
}
