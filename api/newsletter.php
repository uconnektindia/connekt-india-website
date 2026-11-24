<?php
/** POST /api/newsletter.php: save subscriber */
require __DIR__ . '/../includes/api.php';

$in = api_init();
$email = mb_strtolower(clean($in['email'] ?? '', 150));
if (!valid_email($email)) api_json(false, 'Please enter a valid email address.', [], 422);

rate_limit('newsletter', 5, 600);

try {
    $pdo = db();
    $st = $pdo->prepare('SELECT id FROM newsletter WHERE email = ?');
    $st->execute([$email]);
    if ($st->fetchColumn()) api_json(true, "You're already subscribed. Thank you! 🙌");
    $pdo->prepare('INSERT INTO newsletter (email, status, ip, created_at, updated_at) VALUES (?, ?, ?, ?, ?)')
        ->execute([$email, 'new', client_ip(), now(), now()]);
} catch (Throwable $e) {
    log_error('newsletter: ' . $e->getMessage());
    api_json(false, 'Could not subscribe right now. Please try again later.', [], 500);
}
api_json(true, "You're subscribed! Watch your inbox for growth ideas. 🚀");
