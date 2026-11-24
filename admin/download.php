<?php
/** Secure resume download (resumes are not publicly accessible) */
require_once __DIR__ . '/_init.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare('SELECT name, resume_path, resume_original FROM careers WHERE id = ?');
$st->execute([$id]);
$r = $st->fetch();
if (!$r || !str_starts_with((string)$r['resume_path'], 'uploads/resumes/')) { http_response_code(404); exit('Not found'); }
$file = realpath(ROOT . '/' . $r['resume_path']);
$base = realpath(ROOT . '/uploads/resumes');
if (!$file || !$base || !str_starts_with($file, $base) || !is_file($file)) { http_response_code(404); exit('File missing'); }

$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$types = ['pdf' => 'application/pdf', 'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$name = 'Resume - ' . preg_replace('/[^\w\- ]+/', '', $r['name']) . '.' . $ext;
header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
header('Content-Disposition: attachment; filename="' . $name . '"');
header('Content-Length: ' . filesize($file));
header('X-Content-Type-Options: nosniff');
readfile($file);
