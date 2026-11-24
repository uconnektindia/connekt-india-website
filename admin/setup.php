<?php
/**
 * ONE-TIME SETUP: creates the first admin account.
 * After creating your admin, DELETE THIS FILE from the server (File Manager → admin/setup.php → Delete).
 * It also refuses to run once any admin exists.
 */
require_once __DIR__ . '/_init.php';

$msg = ''; $ok = false; $exists = false; $dbError = '';
try {
    $exists = (int)db()->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0;
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

if (!$dbError && !$exists && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim((string)($_POST['username'] ?? ''));
    $em = trim((string)($_POST['email'] ?? ''));
    $p = (string)($_POST['password'] ?? '');
    $p2 = (string)($_POST['password2'] ?? '');
    if (!csrf_check($_POST['csrf'] ?? null)) $msg = 'Session expired. Please try again.';
    elseif (!preg_match('/^[A-Za-z0-9_.-]{3,60}$/', $u)) $msg = 'Username: 3–60 letters, numbers, dots, dashes or underscores.';
    elseif ($em !== '' && !filter_var($em, FILTER_VALIDATE_EMAIL)) $msg = 'Please enter a valid email.';
    elseif (strlen($p) < 10) $msg = 'Password must be at least 10 characters.';
    elseif (!preg_match('/[A-Za-z]/', $p) || !preg_match('/\d/', $p)) $msg = 'Password must contain letters and numbers.';
    elseif ($p !== $p2) $msg = 'Passwords do not match.';
    else {
        db()->prepare('INSERT INTO admins (username, email, password_hash, created_at) VALUES (?, ?, ?, ?)')
            ->execute([$u, $em ?: null, password_hash($p, PASSWORD_DEFAULT), now()]);
        $ok = true; $exists = true;
    }
}

admin_header('Setup');
?>
<div class="a-auth">
  <div class="a-card a-login">
    <div class="a-auth-logo"><?= logo_svg('a-logo-lg') ?></div>
    <h1>First-time setup</h1>
    <?php if ($dbError): ?>
      <div class="a-flash bad">Cannot connect to the database: <?= e($dbError) ?><br><br>1) Create the MySQL database in hPanel. 2) Import <b>database.sql</b> in phpMyAdmin. 3) Put the details in <b>config/config.php</b>. Then reload this page.</div>
    <?php elseif ($ok): ?>
      <div class="a-flash ok">✅ Admin account created.</div>
      <div class="a-flash bad"><b>Important:</b> delete <code>admin/setup.php</code> from your server now (hPanel → File Manager → public_html/admin → setup.php → Delete).</div>
      <a class="a-btn a-btn-block" href="<?= url('admin/login.php') ?>">Go to login</a>
    <?php elseif ($exists): ?>
      <div class="a-flash bad">An admin account already exists, so setup is disabled. Please <b>delete admin/setup.php</b> from your server.</div>
      <a class="a-btn a-btn-block" href="<?= url('admin/login.php') ?>">Go to login</a>
    <?php else: ?>
      <p class="a-sub">Create the first administrator for the website dashboard.</p>
      <?php if ($msg): ?><div class="a-flash bad"><?= e($msg) ?></div><?php endif; ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <label>Username<input name="username" required value="<?= e($_POST['username'] ?? '') ?>" autocomplete="username"></label>
        <label>Email (optional, can be used to log in)<input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>"></label>
        <label>Password (min 10 chars, letters + numbers)<input type="password" name="password" required autocomplete="new-password"></label>
        <label>Confirm password<input type="password" name="password2" required autocomplete="new-password"></label>
        <button class="a-btn a-btn-block" type="submit">Create admin</button>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php admin_footer();
