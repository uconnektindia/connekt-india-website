<?php
/** CSV export of any admin table (respects the current search/filters) */
require_once __DIR__ . '/_init.php';
require_admin();

$tables = admin_tables();
$t = (string)($_GET['t'] ?? '');
if (!isset($tables[$t])) { http_response_code(404); exit('Unknown table'); }
$def = $tables[$t];
[$where, $params] = admin_where($t, $def);
$st = db()->prepare("SELECT * FROM $t $where ORDER BY id DESC");
$st->execute($params);

$cols = array_merge(['id' => 'ID'], $def['cols'], ['status' => 'Status']);
if ($t === 'careers') { $cols['resume_path'] = 'Resume file'; }

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="connekt-' . $t . '-' . date('Y-m-d') . '.csv"');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
fputcsv($out, array_values($cols));
while ($r = $st->fetch()) {
    $line = [];
    foreach (array_keys($cols) as $c) {
        $v = (string)($r[$c] ?? '');
        if ($c === 'resume_path' && $v !== '') $v = abs_url('admin/download.php?id=' . $r['id']);
        if (preg_match('/^[=+\-@]/', $v)) $v = "'" . $v; // CSV injection guard
        $line[] = $v;
    }
    fputcsv($out, $line);
}
fclose($out);
