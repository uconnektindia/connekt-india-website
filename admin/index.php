<?php
require_once __DIR__ . '/_init.php';
header('Location: ' . url(admin_logged_in() ? 'admin/dashboard.php' : 'admin/login.php'));
exit;
