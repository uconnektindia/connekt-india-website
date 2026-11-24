<?php
require_once __DIR__ . '/_init.php';
require_admin();

$counts = []; $latest = []; $err = '';
try {
    foreach (admin_tables() as $t => $d) {
        $row = db()->query("SELECT COUNT(*) AS total, SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) AS fresh FROM $t")->fetch();
        $st = db()->prepare("SELECT COUNT(*) FROM $t WHERE created_at >= ?");
        $st->execute([date('Y-m-d H:i:s', time() - 7 * 86400)]);
        $counts[$t] = ['total' => (int)$row['total'], 'new' => (int)$row['fresh'], 'week' => (int)$st->fetchColumn()];
    }
    $latest = db()->query('SELECT id, name, service, phone, status, created_at FROM enquiries ORDER BY id DESC LIMIT 6')->fetchAll();
} catch (Throwable $e) {
    $err = $e->getMessage();
}

admin_header('Dashboard', 'dashboard');
?>
<div class="a-head">
  <div>
    <h1>Hello, <?= e($_SESSION['admin_name'] ?? 'Admin') ?> 👋</h1>
    <p class="a-sub"><?= e(date('l, j F Y')) ?></p>
  </div>
</div>
<?php if ($err): ?><div class="a-flash bad">Database error: <?= e($err) ?></div><?php endif; ?>

<div class="a-stats">
  <?php foreach (admin_tables() as $t => $d): $c = $counts[$t] ?? ['total' => 0, 'new' => 0, 'week' => 0]; ?>
    <a class="a-card a-stat" href="<?= url('admin/' . $d['page']) ?>">
      <span class="a-ico"><?= icon($d['icon'], '', 22) ?></span>
      <span class="a-stat-label"><?= e($d['title']) ?></span>
      <strong><?= number_format($c['total']) ?></strong>
      <span class="a-stat-meta"><b><?= $c['new'] ?></b> new · <?= $c['week'] ?> this week</span>
    </a>
  <?php endforeach; ?>
</div>

<div class="a-card" style="margin-top:22px">
  <div class="a-head" style="margin-bottom:12px"><h2>Latest enquiries</h2><a class="a-link" href="<?= url('admin/enquiries.php') ?>">View all →</a></div>
  <div class="a-table-wrap">
    <table class="a-table">
      <thead><tr><th>Date</th><th>Name</th><th>Service</th><th>Phone</th><th>Status</th></tr></thead>
      <tbody>
        <?php if (!$latest): ?><tr><td colspan="5" class="a-empty">No enquiries yet. Test the contact form on your website!</td></tr><?php endif; ?>
        <?php foreach ($latest as $r): ?>
          <tr>
            <td data-l="Date"><?= e(date('d M, g:i A', strtotime($r['created_at']))) ?></td>
            <td data-l="Name"><?= e($r['name']) ?></td>
            <td data-l="Service"><?= e($r['service']) ?></td>
            <td data-l="Phone"><a href="tel:+91<?= e($r['phone']) ?>">+91 <?= e($r['phone']) ?></a></td>
            <td data-l="Status"><span class="a-pill st-<?= e($r['status']) ?>"><?= e(STATUSES[$r['status']] ?? $r['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if (is_file(__DIR__ . '/setup.php')): ?>
  <div class="a-flash bad" style="margin-top:22px"><b>Security reminder:</b> delete <code>admin/setup.php</code> from your server.</div>
<?php endif; ?>
<?php admin_footer();
