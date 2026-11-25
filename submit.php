<?php
// RK Avenue enquiry form handler
// Emails each submission and sends the visitor to thank-you.html,
// where the Google Ads conversion fires.

date_default_timezone_set('Asia/Kolkata');

$to      = 'rkdeveloper12345@gmail.com';
$subject = 'New RK Avenue enquiry';
$form    = 'rk-avenue-offer.html';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $form);
    exit;
}

// Honeypot: real people leave this hidden field empty.
if (!empty($_POST['company'])) {
    header('Location: thank-you.html');
    exit;
}

function clean($key) {
    $v = isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
    $v = str_replace(array("\r", "\n", "%0a", "%0d", "%0A", "%0D"), ' ', $v);
    return mb_substr($v, 0, 500, 'UTF-8');
}

$name    = clean('name');
$phone   = clean('phone');
$email   = clean('email');
$budget  = clean('budget');
$message = clean('message');

// Need a name and a phone number with at least 10 digits.
$digits = preg_replace('/\D/', '', $phone);
if ($name === '' || strlen($digits) < 10 || strlen($digits) > 15) {
    header('Location: ' . $form . '?error=1#enquire');
    exit;
}

$body  = "New enquiry from the RK Avenue landing page\n\n";
$body .= "Name:    $name\n";
$body .= "Phone:   $phone\n";
$body .= "Email:   " . ($email !== '' ? $email : '-') . "\n";
$body .= "Budget:  " . ($budget !== '' ? $budget : '-') . "\n";
$body .= "Message: " . ($message !== '' ? $message : '-') . "\n\n";
$body .= "Submitted: " . date('d M Y, H:i') . " IST\n";
$body .= "Page:      " . (isset($_SERVER['HTTP_REFERER']) ? substr($_SERVER['HTTP_REFERER'], 0, 300) : '-') . "\n";

$headers  = "From: RK Avenue Website <noreply@therkdevelopers.com>\r\n";
if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $headers .= "Reply-To: $email\r\n";
}
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

@mail($to, $subject, $body, $headers);

// Backup copy in case an email does not arrive. It is saved OUTSIDE
// public_html (one folder up), so nobody can open it in a browser.
// Read it in Hostinger File Manager: rk-enquiries.log, next to public_html.
$logDir = dirname(__DIR__);
if (!is_writable($logDir)) {
    $logDir = __DIR__ . '/private';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0750);
        @file_put_contents($logDir . '/.htaccess', "Require all denied\n");
    }
}
@file_put_contents($logDir . '/rk-enquiries.log', $body . "\n----\n", FILE_APPEND | LOCK_EX);

header('Location: thank-you.html?sent=1');
exit;
