<?php
/** POST /api/contact.php: project enquiry → DB + admin email + auto-reply */
require __DIR__ . '/../includes/api.php';

$in = api_init();
if (!recaptcha_ok($in['recaptcha_token'] ?? null, 'contact')) {
    api_json(false, 'Spam check failed. Please refresh and try again, or WhatsApp us.', [], 400);
}

$allowedServices = array_merge(array_column(services(), 'name'), ['Other']);
$allowedBudgets = ['', 'Under ₹25,000', '₹25,000 – ₹50,000', '₹50,000 – ₹1,00,000', '₹1,00,000 – ₹3,00,000', 'Above ₹3,00,000', 'Not sure yet'];

$data = [
    'name'    => clean($in['name'] ?? '', 100),
    'email'   => mb_strtolower(clean($in['email'] ?? '', 150)),
    'phone'   => normalize_phone(clean($in['phone'] ?? '', 20)),
    'company' => clean($in['company'] ?? '', 150),
    'service' => clean($in['service'] ?? '', 80),
    'budget'  => clean($in['budget'] ?? '', 60),
    'message' => clean_multiline($in['message'] ?? '', 3000),
];

$errors = [];
if (mb_strlen($data['name']) < 2) $errors['name'] = 'Please enter your full name.';
if (!valid_email($data['email'])) $errors['email'] = 'Please enter a valid email address.';
if (!valid_phone($data['phone'])) $errors['phone'] = 'Enter a valid 10-digit Indian mobile number.';
if (!in_array($data['service'], $allowedServices, true)) $errors['service'] = 'Please choose a service.';
if (!in_array($data['budget'], $allowedBudgets, true)) $data['budget'] = '';
if (mb_strlen($data['message']) < 10) $errors['message'] = 'Please write at least 10 characters.';
if (empty($in['consent'])) $errors['consent'] = 'Please accept to continue.';
if ($errors) api_json(false, 'Please fix the highlighted fields.', ['errors' => $errors], 422);

rate_limit('contact', 5, 600);

try {
    $st = db()->prepare('INSERT INTO enquiries (name, email, phone, company, service, budget, message, status, ip, user_agent, created_at, updated_at)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $st->execute([$data['name'], $data['email'], $data['phone'], $data['company'], $data['service'], $data['budget'], $data['message'], 'new', client_ip(), user_agent(), now(), now()]);
} catch (Throwable $e) {
    log_error('contact insert: ' . $e->getMessage());
    api_json(false, 'We could not save your message right now. Please WhatsApp or email us.', [], 500);
}

$mailer = new Mailer();
[$subj, $html] = mail_admin('enquiry', [
    'Name' => $data['name'], 'Email' => $data['email'], 'Phone' => '+91 ' . $data['phone'],
    'Company' => $data['company'], 'Service' => $data['service'], 'Budget' => $data['budget'], 'Message' => $data['message'],
], $data['email'], $data['phone']);
if (!$mailer->send(MAIL_TO_ADMIN, $subj, $html, '', $data['email'], $data['name'])) log_error('contact admin mail: ' . $mailer->lastError);

[$subj, $html] = mail_autoreply('enquiry', $data['name']);
if (!$mailer->send($data['email'], $subj, $html, '', MAIL_TO_ADMIN, SITE_NAME)) log_error('contact autoreply: ' . $mailer->lastError);

api_json(true, 'Thank you, ' . explode(' ', $data['name'])[0] . '! Your enquiry has been received. We will get back to you within 24 hours.');
