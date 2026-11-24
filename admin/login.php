<?php
require_once __DIR__ . '/_init.php';

if (admin_logged_in()) { header('Location: ' . url('admin/dashboard.php')); exit; }

const MAX_TRIES = 5;
const LOCK_MINUTES = 15;
$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if (!csrf_check($_POST['csrf'] ?? null)) {
        $error = 'Session expired. Please try again.';
    } else {
        try {
            $pdo = db();
            // IP throttle: 10 failed attempts per 15 minutes from one IP
            $st = $pdo->prepare('SELECT COUNT(*) FROM rate_limits WHERE ip = ? AND action = ? AND created_at > ?');
            $st->execute([client_ip(), 'login_fail', date('Y-m-d H:i:s', time() - 900)]);
            if ((int)$st->fetchColumn() >= 10) {
                $error = 'Too many failed attempts from your network. Please wait 15 minutes.';
            } else {
                $st = $pdo->prepare('SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1');
                $st->execute([$username, $username]);
                $admin = $st->fetch();
                if ($admin && $admin['locked_until'] && strtotime($admin['locked_until']) > time()) {
                    $mins = (int)ceil((strtotime($admin['locked_until']) - time()) / 60);
                    $error = "This account is locked after too many failed attempts. Try again in $mins minute(s).";
                } elseif ($admin && password_verify($password, $admin['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_id'] = (int)$admin['id'];
                    $_SESSION['admin_name'] = $admin['username'];
                    $_SESSION['admin_seen'] = time();
                    unset($_SESSION['csrf']);
                    $pdo->prepare('UPDATE admins SET failed_attempts = 0, locked_until = NULL, last_login = ? WHERE id = ?')->execute([now(), $admin['id']]);
                    if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
                        $pdo->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')->execute([password_hash($password, PASSWORD_DEFAULT), $admin['id']]);
                    }
                    header('Location: ' . url('admin/dashboard.php'));
                    exit;
                } else {
                    $pdo->prepare('INSERT INTO rate_limits (ip, action, created_at) VALUES (?, ?, ?)')->execute([client_ip(), 'login_fail', now()]);
                    if ($admin) {
                        $tries = (int)$admin['failed_attempts'] + 1;
                        $lock = $tries >= MAX_TRIES ? date('Y-m-d H:i:s', time() + LOCK_MINUTES * 60) : null;
                        $pdo->prepare('UPDATE admins SET failed_attempts = ?, locked_until = ? WHERE id = ?')->execute([$lock ? 0 : $tries, $lock, $admin['id']]);
                        $error = $lock ? 'Too many failed attempts. This account is locked for ' . LOCK_MINUTES . ' minutes.' : 'Incorrect username or password. ' . (MAX_TRIES - $tries) . ' attempt(s) left.';
                    } else {
                        $error = 'Incorrect username or password.';
                    }
                    usleep(400000);
                }
            }
        } catch (Throwable $e) {
            $error = 'Database connection failed. Check config/config.php and import database.sql.';
        }
    }
}

admin_header('Login');
?>
<div class="a-auth">
  <form class="a-card a-login" method="post" autocomplete="on">
    <div class="a-auth-logo"><?= logo_svg('a-logo-lg') ?></div>
    <h1>Admin login</h1>
    <p class="a-sub">Connekt India website dashboard</p>
    <?php if ($error): ?><div class="a-flash bad" role="alert"><?= e($error) ?></div><?php endif; ?>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <label>Username or email<input type="text" name="username" value="<?= e($username) ?>" required autocomplete="username" autofocus></label>
    <label>Password<input type="password" name="password" required autocomplete="current-password"></label>
    <button class="a-btn a-btn-block" type="submit"><?= icon('lock', '', 18) ?> Log in</button>
  </form>
</div>
<?php admin_footer();
