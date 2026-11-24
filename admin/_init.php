<?php
/** Admin bootstrap: auth guard, table definitions, layout */
require_once __DIR__ . '/../includes/bootstrap.php';

header('X-Robots-Tag: noindex, nofollow');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');

const ADMIN_IDLE_TIMEOUT = 7200; // 2 hours

function admin_logged_in(): bool
{
    if (empty($_SESSION['admin_id'])) return false;
    if (time() - ($_SESSION['admin_seen'] ?? 0) > ADMIN_IDLE_TIMEOUT) {
        unset($_SESSION['admin_id'], $_SESSION['admin_name']);
        return false;
    }
    $_SESSION['admin_seen'] = time();
    return true;
}

function require_admin(): void
{
    if (!admin_logged_in()) {
        header('Location: ' . url('admin/login.php'));
        exit;
    }
}

function flash(?string $msg = null, string $type = 'ok'): ?array
{
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
    return $f;
}

/** Table definitions used by the list pages, export and dashboard */
function admin_tables(): array
{
    return [
        'enquiries' => [
            'title' => 'Enquiries', 'icon' => 'inbox', 'page' => 'enquiries.php',
            'cols' => ['created_at' => 'Date', 'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'company' => 'Company', 'service' => 'Service', 'budget' => 'Budget', 'message' => 'Message'],
            'search' => ['name', 'email', 'phone', 'company', 'message'],
            'filter' => ['service', 'Service'],
        ],
        'careers' => [
            'title' => 'Careers', 'icon' => 'briefcase', 'page' => 'careers.php',
            'cols' => ['created_at' => 'Date', 'name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'position' => 'Position', 'experience' => 'Experience', 'portfolio' => 'Portfolio', 'resume_original' => 'Resume', 'message' => 'Message'],
            'search' => ['name', 'email', 'phone', 'position', 'message'],
            'filter' => ['position', 'Position'],
        ],
        'newsletter' => [
            'title' => 'Newsletter', 'icon' => 'mail', 'page' => 'newsletter.php',
            'cols' => ['created_at' => 'Date', 'email' => 'Email'],
            'search' => ['email'],
            'filter' => null,
        ],
        'chatbot_leads' => [
            'title' => 'Chatbot leads', 'icon' => 'bot', 'page' => 'leads.php',
            'cols' => ['created_at' => 'Date', 'name' => 'Name', 'phone' => 'Phone', 'question' => 'Last question', 'page_url' => 'Page'],
            'search' => ['name', 'phone', 'question'],
            'filter' => null,
        ],
    ];
}

const STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'];

/** Build WHERE clause + params from GET filters (shared by list + export) */
function admin_where(string $table, array $def): array
{
    $where = []; $params = [];
    $q = trim((string)($_GET['q'] ?? ''));
    if ($q !== '') {
        $or = [];
        foreach ($def['search'] as $c) { $or[] = "$c LIKE ?"; $params[] = '%' . $q . '%'; }
        $where[] = '(' . implode(' OR ', $or) . ')';
    }
    $status = (string)($_GET['status'] ?? '');
    if (isset(STATUSES[$status])) { $where[] = 'status = ?'; $params[] = $status; }
    if ($def['filter']) {
        $fv = (string)($_GET['f'] ?? '');
        if ($fv !== '') { $where[] = $def['filter'][0] . ' = ?'; $params[] = $fv; }
    }
    $from = (string)($_GET['from'] ?? '');
    $to = (string)($_GET['to'] ?? '');
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) { $where[] = 'created_at >= ?'; $params[] = $from . ' 00:00:00'; }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) { $where[] = 'created_at <= ?'; $params[] = $to . ' 23:59:59'; }
    return [$where ? 'WHERE ' . implode(' AND ', $where) : '', $params];
}

function admin_header(string $title, string $active = ''): void
{
    $tables = admin_tables();
    $f = flash();
    ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> · Connekt Admin</title>
<link rel="icon" type="image/png" href="<?= asset('assets/logo/favicon-32.png') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body>
<?php if (admin_logged_in()): ?>
<header class="a-top">
  <a class="a-brand" href="<?= url('admin/dashboard.php') ?>"><?= logo_svg('a-logo') ?><span>Connekt <b>India</b><small>Admin</small></span></a>
  <input type="checkbox" id="navToggle" class="a-nav-toggle" aria-label="Toggle menu">
  <label for="navToggle" class="a-burger" aria-hidden="true"><span></span><span></span><span></span></label>
  <nav class="a-nav">
    <a href="<?= url('admin/dashboard.php') ?>" class="<?= $active === 'dashboard' ? 'on' : '' ?>"><?= icon('dashboard', '', 18) ?> Dashboard</a>
    <?php foreach ($tables as $t => $d): ?>
      <a href="<?= url('admin/' . $d['page']) ?>" class="<?= $active === $t ? 'on' : '' ?>"><?= icon($d['icon'], '', 18) ?> <?= e($d['title']) ?></a>
    <?php endforeach; ?>
    <a href="<?= url('') ?>" target="_blank" rel="noopener"><?= icon('external', '', 18) ?> View site</a>
    <form method="post" action="<?= url('admin/logout.php') ?>" class="a-logout"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><button type="submit"><?= icon('logout', '', 18) ?> Logout</button></form>
  </nav>
</header>
<?php endif; ?>
<main class="a-main">
<?php if ($f): ?><div class="a-flash <?= e($f[1]) ?>" role="status"><?= e($f[0]) ?></div><?php endif; ?>
<?php }

function admin_footer(): void
{ ?>
</main>
<script>
document.addEventListener('click', function (e) {
  var b = e.target.closest('[data-confirm]');
  if (b && !confirm(b.getAttribute('data-confirm'))) e.preventDefault();
  var m = e.target.closest('.a-more');
  if (m) { m.parentNode.classList.toggle('open'); }
});
document.addEventListener('change', function (e) { if (e.target.matches('.a-status select')) e.target.form.submit(); });
</script>
</body>
</html>
<?php }
