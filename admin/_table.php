<?php
/**
 * Generic admin list page: search, filters, status update, delete, pagination, CSV export.
 * Usage (in enquiries.php etc.): $TABLE = 'enquiries'; require __DIR__ . '/_table.php';
 */
require_once __DIR__ . '/_init.php';
require_admin();

$tables = admin_tables();
if (!isset($TABLE, $tables[$TABLE])) { http_response_code(404); exit('Unknown table'); }
$def = $tables[$TABLE];
$self = url('admin/' . $def['page']);

/* ---------- POST actions: status update / delete ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? null)) { flash('Session expired. Please try again.', 'bad'); header('Location: ' . $self); exit; }
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $back = $self . (!empty($_POST['qs']) ? '?' . preg_replace('/[^\w=&%.\-+]/', '', (string)$_POST['qs']) : '');
    try {
        if ($action === 'status' && isset(STATUSES[$_POST['status'] ?? ''])) {
            db()->prepare("UPDATE $TABLE SET status = ?, updated_at = ? WHERE id = ?")->execute([$_POST['status'], now(), $id]);
            flash('Status updated.');
        } elseif ($action === 'delete') {
            if ($TABLE === 'careers') {
                $st = db()->prepare('SELECT resume_path FROM careers WHERE id = ?'); $st->execute([$id]);
                $p = (string)$st->fetchColumn();
                if ($p && str_starts_with($p, 'uploads/resumes/')) { $abs = ROOT . '/' . $p; if (is_file($abs)) @unlink($abs); }
            }
            db()->prepare("DELETE FROM $TABLE WHERE id = ?")->execute([$id]);
            flash('Entry deleted.');
        }
    } catch (Throwable $e) {
        flash('Database error: ' . $e->getMessage(), 'bad');
    }
    header('Location: ' . $back);
    exit;
}

/* ---------- Query ---------- */
[$where, $params] = admin_where($TABLE, $def);
$per = 25;
$pageNo = max(1, (int)($_GET['p'] ?? 1));
$total = 0; $rows = []; $filterValues = []; $err = '';
try {
    $st = db()->prepare("SELECT COUNT(*) FROM $TABLE $where");
    $st->execute($params);
    $total = (int)$st->fetchColumn();
    $pages = max(1, (int)ceil($total / $per));
    $pageNo = min($pageNo, $pages);
    $offset = ($pageNo - 1) * $per;
    $st = db()->prepare("SELECT * FROM $TABLE $where ORDER BY id DESC LIMIT $per OFFSET $offset");
    $st->execute($params);
    $rows = $st->fetchAll();
    if ($def['filter']) {
        $col = $def['filter'][0];
        $filterValues = db()->query("SELECT DISTINCT $col FROM $TABLE WHERE $col IS NOT NULL AND $col <> '' ORDER BY $col")->fetchAll(PDO::FETCH_COLUMN);
    }
} catch (Throwable $e) {
    $err = $e->getMessage();
    $pages = 1;
}

$qs = $_GET; unset($qs['p']);
$qsStr = http_build_query($qs);
admin_header($def['title'], $TABLE);
?>
<div class="a-head">
  <div>
    <h1><?= e($def['title']) ?></h1>
    <p class="a-sub"><?= number_format($total) ?> <?= $total === 1 ? 'entry' : 'entries' ?><?= $where ? ' match your filters' : '' ?></p>
  </div>
  <a class="a-btn" href="<?= url('admin/export.php') ?>?t=<?= e($TABLE) ?>&amp;<?= e($qsStr) ?>"><?= icon('download', '', 18) ?> Export CSV</a>
</div>

<?php if ($err): ?><div class="a-flash bad">Database error: <?= e($err) ?>. Check config/config.php and that database.sql was imported.</div><?php endif; ?>

<form class="a-filters" method="get">
  <input type="search" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Search…" aria-label="Search">
  <select name="status" aria-label="Status">
    <option value="">All statuses</option>
    <?php foreach (STATUSES as $k => $v): ?><option value="<?= $k ?>" <?= ($_GET['status'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
  </select>
  <?php if ($def['filter']): ?>
  <select name="f" aria-label="<?= e($def['filter'][1]) ?>">
    <option value="">All <?= e(strtolower($def['filter'][1])) ?>s</option>
    <?php foreach ($filterValues as $fv): ?><option <?= ($_GET['f'] ?? '') === $fv ? 'selected' : '' ?>><?= e($fv) ?></option><?php endforeach; ?>
  </select>
  <?php endif; ?>
  <label>From <input type="date" name="from" value="<?= e($_GET['from'] ?? '') ?>"></label>
  <label>To <input type="date" name="to" value="<?= e($_GET['to'] ?? '') ?>"></label>
  <button class="a-btn" type="submit"><?= icon('filter', '', 16) ?> Apply</button>
  <?php if ($qs): ?><a class="a-link" href="<?= $self ?>">Reset</a><?php endif; ?>
</form>

<div class="a-table-wrap">
<table class="a-table">
  <thead><tr>
    <th>#</th>
    <?php foreach ($def['cols'] as $c => $label): ?><th><?= e($label) ?></th><?php endforeach; ?>
    <th>Status</th><th></th>
  </tr></thead>
  <tbody>
  <?php if (!$rows): ?>
    <tr><td colspan="<?= count($def['cols']) + 3 ?>" class="a-empty">No entries yet.</td></tr>
  <?php endif; ?>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td data-l="#"><?= (int)$r['id'] ?></td>
      <?php foreach ($def['cols'] as $c => $label):
        $v = (string)($r[$c] ?? ''); ?>
        <td data-l="<?= e($label) ?>">
          <?php if ($c === 'created_at'): ?>
            <span class="a-date"><?= e(date('d M Y', strtotime($v))) ?><small><?= e(date('g:i A', strtotime($v))) ?></small></span>
          <?php elseif ($c === 'email'): ?>
            <a href="mailto:<?= e($v) ?>"><?= e($v) ?></a>
          <?php elseif ($c === 'phone'): ?>
            <a href="tel:+91<?= e($v) ?>">+91 <?= e($v) ?></a> <a class="a-wa" href="https://wa.me/91<?= e($v) ?>" target="_blank" rel="noopener" title="WhatsApp"><?= icon('whatsapp', '', 14) ?></a>
          <?php elseif ($c === 'portfolio' || $c === 'page_url'): ?>
            <?php if ($v): ?><a href="<?= e($v) ?>" target="_blank" rel="noopener noreferrer" class="a-trunc"><?= e(preg_replace('~^https?://~', '', $v)) ?></a><?php endif; ?>
          <?php elseif ($c === 'resume_original'): ?>
            <a class="a-btn a-btn-sm" href="<?= url('admin/download.php') ?>?id=<?= (int)$r['id'] ?>"><?= icon('download', '', 14) ?> Download</a>
          <?php elseif ($c === 'message' || $c === 'question'): ?>
            <div class="a-msg"><span><?= nl2br(e($v)) ?></span><?php if (mb_strlen($v) > 120): ?><button type="button" class="a-more">more</button><?php endif; ?></div>
          <?php else: ?>
            <?= e($v) ?>
          <?php endif; ?>
        </td>
      <?php endforeach; ?>
      <td data-l="Status">
        <form method="post" class="a-status">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="qs" value="<?= e($_SERVER['QUERY_STRING'] ?? '') ?>">
          <select name="status" class="st-<?= e($r['status']) ?>" aria-label="Status">
            <?php foreach (STATUSES as $k => $lbl): ?><option value="<?= $k ?>" <?= $r['status'] === $k ? 'selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?>
          </select>
          <noscript><button type="submit">Save</button></noscript>
        </form>
      </td>
      <td>
        <form method="post">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><input type="hidden" name="qs" value="<?= e($_SERVER['QUERY_STRING'] ?? '') ?>">
          <button type="submit" class="a-del" data-confirm="Delete this entry permanently?" aria-label="Delete"><?= icon('trash', '', 16) ?></button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>

<?php if ($pages > 1): ?>
<nav class="a-pager" aria-label="Pages">
  <?php for ($i = 1; $i <= $pages; $i++): ?>
    <a href="?<?= e(http_build_query($qs + ['p' => $i])) ?>" class="<?= $i === $pageNo ? 'on' : '' ?>"><?= $i ?></a>
  <?php endfor; ?>
</nav>
<?php endif; ?>
<?php admin_footer();
