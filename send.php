<?php
/**
 * Contact Form Handler — Lahiru Mahakumburage Portfolio
 * Uses PHPMailer + Hostinger SMTP
 * Place this file in: public_html/send.php
 */

// ── CORS & JSON headers ──────────────────────────────────────────
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://lahirumahakumburage.com');
header('Access-Control-Allow-Methods: POST');
header('X-Content-Type-Options: nosniff');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Method not allowed']);
    exit;
}

// ── LOAD PHPMailer ───────────────────────────────────────────────
require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ── YOUR SMTP CONFIGURATION ──────────────────────────────────────
// Get these from Hostinger hPanel → Emails → your account → Configuration
define('SMTP_HOST',     'smtp.hostinger.com');
define('SMTP_PORT',     465);                          // 465 = SSL, 587 = TLS
define('SMTP_SECURE',   PHPMailer::ENCRYPTION_SMTPS);  // SMTPS for port 465
define('SMTP_USER',     'noreply@lahirumahakumburage.com');
define('SMTP_PASS',     'YOUR_EMAIL_PASSWORD_HERE');   // ← change this
define('MAIL_FROM',     'noreply@lahirumahakumburage.com');
define('MAIL_FROM_NAME','Lahiru Portfolio');
define('MAIL_TO',       'info@lahirumahakumburage.com');
define('MAIL_TO_NAME',  'Lahiru Mahakumburage');

// ── SANITIZE & VALIDATE INPUT ────────────────────────────────────
function clean(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

$fname   = clean($_POST['fname']   ?? '');
$lname   = clean($_POST['lname']   ?? '');
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$subject = clean($_POST['subject'] ?? 'Portfolio Inquiry');
$message = clean($_POST['message'] ?? '');

// Required field check
if (empty($fname) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Please fill in all required fields.']);
    exit;
}

// Email format check
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Please enter a valid email address.']);
    exit;
}

// Basic spam guard — block suspicious content
$spamWords = ['http://', 'https://', 'click here', 'buy now', 'casino', 'viagra'];
$combined  = strtolower($fname . $lname . $subject . $message);
foreach ($spamWords as $word) {
    if (str_contains($combined, $word)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'msg' => 'Message blocked as spam.']);
        exit;
    }
}

// ── SEND EMAIL ───────────────────────────────────────────────────
$fullName = $fname . ' ' . $lname;

$mail = new PHPMailer(true);

try {
    // SMTP Settings
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';

    // From / To
    $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
    $mail->addAddress(MAIL_TO, MAIL_TO_NAME);
    $mail->addReplyTo($email, $fullName);  // Reply goes directly to the sender

    // Subject
    $mail->Subject = "[Portfolio] $subject — from $fullName";

    // ── HTML Email Body ──
    $mail->isHTML(true);
    $mail->Body = '
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<style>
  body{margin:0;padding:0;background:#f0f0f5;font-family:\'Outfit\',\'Segoe UI\',Arial,sans-serif}
  .wrap{max-width:600px;margin:40px auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.1)}
  .header{background:linear-gradient(135deg,#6c63ff,#8b5cf6);padding:32px 36px}
  .header h1{color:#fff;margin:0;font-size:22px;font-weight:700;letter-spacing:-.5px}
  .header p{color:rgba(255,255,255,.75);margin:6px 0 0;font-size:14px}
  .body{padding:32px 36px}
  .field{margin-bottom:20px}
  .label{font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:#8888aa;font-weight:600;margin-bottom:5px}
  .value{font-size:15px;color:#1a1a2e;font-weight:400;line-height:1.5}
  .value a{color:#6c63ff;text-decoration:none}
  .divider{height:1px;background:#f0f0f5;margin:20px 0}
  .message-box{background:#f7f7fc;border-left:3px solid #6c63ff;border-radius:0 8px 8px 0;padding:16px 18px;font-size:15px;color:#333;line-height:1.7}
  .footer{background:#f7f7fc;padding:20px 36px;text-align:center;font-size:12px;color:#aaa}
  .footer a{color:#6c63ff;text-decoration:none}
  .badge{display:inline-block;background:rgba(108,99,255,.1);color:#6c63ff;font-size:12px;font-weight:600;padding:4px 12px;border-radius:99px;margin-bottom:12px}
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>📩 New Contact Message</h1>
    <p>Received from your portfolio website</p>
  </div>
  <div class="body">
    <div class="badge">New Message</div>
    <div class="field">
      <div class="label">From</div>
      <div class="value">' . $fullName . '</div>
    </div>
    <div class="field">
      <div class="label">Email</div>
      <div class="value"><a href="mailto:' . $email . '">' . $email . '</a></div>
    </div>
    <div class="field">
      <div class="label">Subject</div>
      <div class="value">' . $subject . '</div>
    </div>
    <div class="divider"></div>
    <div class="field">
      <div class="label">Message</div>
      <div class="message-box">' . nl2br($message) . '</div>
    </div>
    <div class="divider"></div>
    <p style="font-size:13px;color:#888;text-align:center;margin:0">
      Hit reply to respond directly to ' . $fname . ' at <a href="mailto:' . $email . '" style="color:#6c63ff">' . $email . '</a>
    </p>
  </div>
  <div class="footer">
    Sent from <a href="https://lahirumahakumburage.com">lahirumahakumburage.com</a> · ' . date('d M Y, H:i') . ' UTC
  </div>
</div>
</body>
</html>';

    // Plain text fallback
    $mail->AltBody = "New Portfolio Contact\n"
        . "========================\n"
        . "From:    $fullName\n"
        . "Email:   $email\n"
        . "Subject: $subject\n\n"
        . "Message:\n$message\n\n"
        . "Sent: " . date('d M Y, H:i') . " UTC\n"
        . "Site: lahirumahakumburage.com";

    $mail->send();

    echo json_encode(['ok' => true, 'msg' => 'Message sent successfully!']);

} catch (Exception $e) {
    // Log error server-side (not exposed to user)
    error_log('[Portfolio Mailer] PHPMailer Error: ' . $mail->ErrorInfo);

    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Failed to send. Please email info@lahirumahakumburage.com directly.']);
}