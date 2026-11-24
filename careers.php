<?php
/** Careers: the application form lives inside the contact page (Career tab). */
require __DIR__ . '/includes/bootstrap.php';
header('Location: ' . url('contact?type=career'), true, 301);
exit;
