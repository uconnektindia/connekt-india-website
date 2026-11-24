<?php
/** POST /api/career.php: job application with resume upload → DB + admin email (resume attached) + auto-reply */
require __DIR__ . '/../includes/api.php';

$in = api_init();
if (!recaptcha_ok($in['recaptcha_token'] ?? null, 'career')) {
    api_json(false, 'Spam check failed. Please refresh and try again.', [], 400);
}

$positions = ['Digital Marketing Executive', 'Social Media Manager', 'Graphic Designer', 'Video Editor', 'Web Developer', 'Software Developer', 'Intern', 'Other'];
$experiences = ['', 'Fresher', 'Less than 1 year', '1–2 years', '3–5 years', '5+ years'];

$data = [
    'name'       => clean($in['name'] ?? '', 100),
    'email'      => mb_strtolower(clean($in['email'] ?? '', 150)),
    'phone'      => normalize_phone(clean($in['phone'] ?? '', 20)),
    'position'   => clean($in['position'] ?? '', 80),
    'experience' => clean($in['experience'] ?? '', 40),
    'portfolio'  => clean($in['portfolio'] ?? '', 255),
    'message'    => clean_multiline($in['message'] ?? '', 3000),
];

$errors = [];
if (mb_strlen($data['name']) < 2) $errors['name'] = 'Please enter your full name.';
if (!valid_email($data['email'])) $errors['email'] = 'Please enter a valid email address.';
if (!valid_phone($data['phone'])) $errors['phone'] = 'Enter a valid 10-digit Indian mobile number.';
if (!in_array($data['position'], $positions, true)) $errors['position'] = 'Please choose a position.';
if (!in_array($data['experience'], $experiences, true)) $data['experience'] = '';
if ($data['portfolio'] !== '') {
    if (!preg_match('~^https?://~i', $data['portfolio'])) $data['portfolio'] = 'https://' . $data['portfolio'];
    if (!filter_var($data['portfolio'], FILTER_VALIDATE_URL)) $errors['portfolio'] = 'Please enter a valid URL.';
}
if (empty($in['consent'])) $errors['consent'] = 'Please accept to continue.';

/* ---------- Resume upload: extension + MIME check, random name, protected folder ---------- */
$file = $_FILES['resume'] ?? null;
$allowed = [
    'pdf'  => ['application/pdf'],
    'doc'  => ['application/msword', 'application/vnd.ms-office', 'application/octet-stream', 'application/x-ole-storage', 'application/CDFV2'],
    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
];
$ext = '';
if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    $errors['resume'] = 'Please upload your resume.';
} elseif ($file['error'] !== UPLOAD_ERR_OK) {
    $errors['resume'] = in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) ? 'File is larger than 5 MB.' : 'Upload failed. Please try again.';
} else {
    $ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
    $mime = '';
    if (class_exists('finfo')) { $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) ?: ''; }
    if ($file['size'] > 5 * 1024 * 1024) $errors['resume'] = 'File is larger than 5 MB.';
    elseif (!isset($allowed[$ext])) $errors['resume'] = 'Upload a PDF, DOC or DOCX file.';
    elseif ($mime !== '' && !in_array($mime, $allowed[$ext], true)) $errors['resume'] = 'This file type is not allowed.';
    elseif ($ext === 'pdf' && strncmp((string)file_get_contents($file['tmp_name'], false, null, 0, 5), '%PDF-', 5) !== 0) $errors['resume'] = 'This does not look like a valid PDF.';
}
if ($errors) api_json(false, 'Please fix the highlighted fields.', ['errors' => $errors], 422);

rate_limit('career', 5, 600);

$dir = ROOT . '/uploads/resumes';
if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
    log_error('career: cannot create uploads/resumes');
    api_json(false, 'Upload folder is not writable. Please email your resume to ' . BUSINESS_EMAIL . '.', [], 500);
}
$stored = date('Ymd') . '-' . bin2hex(random_bytes(12)) . '.' . $ext;
$dest = $dir . '/' . $stored;
if (!move_uploaded_file($file['tmp_name'], $dest)) {
    log_error('career: move_uploaded_file failed');
    api_json(false, 'We could not save your resume. Please try again or email it to ' . BUSINESS_EMAIL . '.', [], 500);
}
@chmod($dest, 0644);
$original = mb_substr(preg_replace('/[^\w.\- ]+/u', '_', (string)$file['name']) ?? 'resume', 0, 150);

try {
    $st = db()->prepare('INSERT INTO careers (name, email, phone, position, experience, portfolio, resume_path, resume_original, message, status, ip, user_agent, created_at, updated_at)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $st->execute([$data['name'], $data['email'], $data['phone'], $data['position'], $data['experience'], $data['portfolio'], 'uploads/resumes/' . $stored, $original, $data['message'], 'new', client_ip(), user_agent(), now(), now()]);
} catch (Throwable $e) {
    @unlink($dest);
    log_error('career insert: ' . $e->getMessage());
    api_json(false, 'We could not save your application right now. Please email us your resume.', [], 500);
}

$mailer = new Mailer();
[$subj, $html] = mail_admin('career', [
    'Name' => $data['name'], 'Email' => $data['email'], 'Phone' => '+91 ' . $data['phone'], 'Position' => $data['position'],
    'Experience' => $data['experience'], 'Portfolio' => $data['portfolio'], 'Resume' => $original . ' (attached)', 'Message' => $data['message'],
], $data['email'], $data['phone']);
$attach = [['path' => $dest, 'name' => 'Resume - ' . $data['name'] . '.' . $ext]];
if (!$mailer->send(MAIL_TO_ADMIN, $subj, $html, '', $data['email'], $data['name'], $attach)) log_error('career admin mail: ' . $mailer->lastError);

[$subj, $html] = mail_autoreply('career', $data['name']);
if (!$mailer->send($data['email'], $subj, $html, '', MAIL_TO_ADMIN, SITE_NAME)) log_error('career autoreply: ' . $mailer->lastError);

api_json(true, 'Thank you, ' . explode(' ', $data['name'])[0] . '! Your application for ' . $data['position'] . ' has been received. We will be in touch if your profile matches an opening.');
