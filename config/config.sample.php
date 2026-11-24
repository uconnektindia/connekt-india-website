<?php
/**
 * Connekt India: site configuration (SAMPLE)
 * ------------------------------------------------------------------
 * Copy this file to config.php and fill in your real values.
 * This folder is blocked from the web by config/.htaccess.
 * Never share config.php publicly: it holds your passwords.
 */

// ---------- Database (hPanel → Databases → MySQL Databases) ----------
const DB_HOST = 'localhost';
const DB_NAME = 'u000000000_connekt';
const DB_USER = 'u000000000_connekt';
const DB_PASS = 'CHANGE_ME';

// ---------- Email / SMTP (hPanel → Emails) ----------
const SMTP_HOST   = 'smtp.hostinger.com';
const SMTP_PORT   = 465;
const SMTP_SECURE = 'ssl';                    // 'ssl' for 465, 'tls' for 587
const SMTP_USER   = 'info@uconnekt.in';       // the mailbox you created in hPanel
const SMTP_PASS   = 'CHANGE_ME';
const MAIL_FROM   = 'info@uconnekt.in';       // must be the same mailbox as SMTP_USER on Hostinger
const MAIL_FROM_NAME = 'Connekt India';
const MAIL_TO_ADMIN  = 'uconnekt.india@gmail.com';

// ---------- Business details ----------
const WHATSAPP_NUMBER = '+91 9556610339';     // any format; non-digits are stripped automatically
const PHONE_DISPLAY   = '+91 95566 10339';
const BUSINESS_EMAIL  = 'uconnekt.india@gmail.com';
const BUSINESS_ADDRESS = '446, Saheed Nagar, Bhubaneswar, Odisha, India – 751007';
const WORKING_HOURS   = 'Monday – Saturday, 10:00 AM – 7:00 PM';   // ← confirm your real hours
const GEO_LAT = 20.2893;                      // Saheed Nagar, Bhubaneswar (approx.)
const GEO_LNG = 85.8449;

// ---------- Social links (leave '' until ready; icons still show) ----------
const SOCIAL_X         = '';
const SOCIAL_YOUTUBE   = '';
const SOCIAL_LINKEDIN  = '';
const SOCIAL_INSTAGRAM = '';
const SOCIAL_FACEBOOK  = '';

// ---------- Google reCAPTCHA v3 (optional; leave '' to disable) ----------
const RECAPTCHA_SITE_KEY = '';
const RECAPTCHA_SECRET   = '';

// ---------- Site ----------
const SITE_URL  = 'https://uconnekt.in/';     // with trailing slash
const SITE_NAME = 'Connekt India';
const DEV_MODE  = false;                      // true = show PHP errors + side-gap console check
