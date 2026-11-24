<?php
/** POST /api/chatbot-log.php: call-back lead captured inside the "Connekt" chatbot */
require __DIR__ . '/../includes/api.php';

$in = api_init();
$name = clean($in['name'] ?? '', 100);
$phone = normalize_phone(clean($in['phone'] ?? '', 20));
$question = clean($in['question'] ?? '', 500);
$pageUrl = clean($_SERVER['HTTP_REFERER'] ?? '', 255);

if (mb_strlen($name) < 2) api_json(false, 'Please enter your name.', [], 422);
if (!valid_phone($phone)) api_json(false, 'Please enter a valid 10-digit mobile number.', [], 422);

rate_limit('chatbot', 5, 600);

try {
    db()->prepare('INSERT INTO chatbot_leads (name, phone, question, page_url, status, ip, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')
        ->execute([$name, $phone, $question, $pageUrl, 'new', client_ip(), now(), now()]);
} catch (Throwable $e) {
    log_error('chatbot lead: ' . $e->getMessage());
    api_json(false, 'Could not save your details. Please WhatsApp us instead.', [], 500);
}

$mailer = new Mailer();
[$subj, $html] = mail_admin('lead', ['Name' => $name, 'Phone' => '+91 ' . $phone, 'Last question' => $question, 'Page' => $pageUrl], '', $phone);
if (!$mailer->send(MAIL_TO_ADMIN, $subj, $html)) log_error('chatbot lead mail: ' . $mailer->lastError);

api_json(true, 'Thanks! Our team will call you shortly.');
