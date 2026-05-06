<?php
/**
 * SMTP Test Script — Lahiru Mahakumburage Portfolio
 * ─────────────────────────────────────────────────
 * INSTRUCTIONS:
 *   1. Upload to: public_html/test-mail.php
 *   2. Visit: https://lahirumahakumburage.com/test-mail.php
 *   3. Check if a test email arrives at info@lahirumahakumburage.com
 *   4. DELETE this file immediately after testing!
 * ─────────────────────────────────────────────────
 */

require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ── Must match your send.php config ──────────────
$smtpHost = 'smtp.hostinger.com';
$smtpPort = 465;
$smtpUser = 'noreply@lahirumahakumburage.com';
$smtpPass = 'YOUR_EMAIL_PASSWORD_HERE';  // ← same as send.php
$mailTo   = 'info@lahirumahakumburage.com';
// ─────────────────────────────────────────────────

$result  = '';
$success = false;
$debug   = '';

$mail = new PHPMailer(true);

try {
    // Capture SMTP debug output
    $mail->SMTPDebug  = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = function($str, $level) use (&$debug) {
        $debug .= htmlspecialchars($str) . "\n";
    };

    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUser;
    $mail->Password   = $smtpPass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $smtpPort;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($smtpUser, 'Portfolio Test');
    $mail->addAddress($mailTo, 'Lahiru Mahakumburage');

    $mail->Subject = '✓ SMTP Test — Portfolio Contact Form is Working';
    $mail->isHTML(true);
    $mail->Body    = '<h2 style="color:#6c63ff">SMTP Test Successful!</h2>
        <p>Your contact form SMTP configuration is working correctly.</p>
        <p><strong>Server:</strong> ' . $smtpHost . ':' . $smtpPort . '<br/>
        <strong>From:</strong> ' . $smtpUser . '<br/>
        <strong>Sent:</strong> ' . date('d M Y, H:i:s') . ' UTC</p>
        <p style="color:#888;font-size:13px">Delete test-mail.php from your server now.</p>';
    $mail->AltBody = 'SMTP Test Successful! Your contact form is working. Sent: ' . date('d M Y H:i:s') . ' UTC';

    $mail->send();
    $success = true;
    $result  = 'Email sent successfully to ' . $mailTo;

} catch (Exception $e) {
    $result = 'Error: ' . $mail->ErrorInfo;
}

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>SMTP Test — Lahiru Portfolio</title>
<style>
  *{box-sizing:border-box}
  body{font-family:'Segoe UI',Arial,sans-serif;background:#0b0c1a;color:#e0e0ff;margin:0;padding:40px 20px}
  .box{max-width:680px;margin:0 auto;background:#141528;border:1px solid rgba(108,99,255,.25);border-radius:16px;padding:32px}
  h1{font-size:20px;margin:0 0 20px;color:#fff}
  .result{padding:16px 20px;border-radius:10px;font-size:15px;font-weight:600;margin-bottom:20px}
  .ok {background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.3);color:#4ade80}
  .err{background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); color:#f87171}
  .debug{background:#0b0c1a;border-radius:10px;padding:16px;font-family:monospace;font-size:12px;color:#818cf8;white-space:pre-wrap;word-break:break-all;max-height:400px;overflow-y:auto;line-height:1.6}
  .label{font-size:11px;letter-spacing:.1em;text-transform:uppercase;color:#7b7fa0;margin:16px 0 6px;font-weight:600}
  .config-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:13px}
  .config-row:last-child{border-bottom:none}
  .ck{color:#9ba3c0}.cv{color:#c8cce8;font-family:monospace}
  .warn{background:rgba(250,204,21,.08);border:1px solid rgba(250,204,21,.3);border-radius:8px;padding:12px 16px;margin-top:20px;font-size:13px;color:#fde68a}
</style>
</head>
<body>
<div class="box">
  <h1>🧪 SMTP Test — Lahiru Portfolio</h1>

  <div class="result <?= $success ? 'ok' : 'err' ?>">
    <?= $success ? '✓ ' : '✗ ' ?><?= htmlspecialchars($result) ?>
  </div>

  <div class="label">Config used</div>
  <div style="background:#0b0c1a;border-radius:10px;padding:12px 16px;margin-bottom:16px">
    <div class="config-row"><span class="ck">Host</span><span class="cv"><?= $smtpHost ?>:<?= $smtpPort ?></span></div>
    <div class="config-row"><span class="ck">Username</span><span class="cv"><?= $smtpUser ?></span></div>
    <div class="config-row"><span class="ck">Encryption</span><span class="cv">SMTPS (SSL)</span></div>
    <div class="config-row"><span class="ck">To</span><span class="cv"><?= $mailTo ?></span></div>
    <div class="config-row"><span class="ck">PHP version</span><span class="cv"><?= phpversion() ?></span></div>
  </div>

  <?php if (!$success): ?>
  <div class="label">Common fixes</div>
  <div style="font-size:13px;color:#9ba3c0;line-height:1.7;margin-bottom:16px">
    <p>1. <strong>Wrong password</strong> — double-check <code>SMTP_PASS</code> in this file and <code>send.php</code></p>
    <p>2. <strong>Email not created</strong> — go to Hostinger hPanel → Emails → create <code>noreply@lahirumahakumburage.com</code> first</p>
    <p>3. <strong>Port blocked</strong> — try changing port to <code>587</code> and <code>ENCRYPTION_STARTTLS</code> in both files</p>
    <p>4. <strong>PHP allow_url_fopen off</strong> — contact Hostinger support</p>
  </div>
  <?php endif; ?>

  <div class="label">SMTP debug log</div>
  <div class="debug"><?= $debug ?: 'No debug output.' ?></div>

  <div class="warn">
    ⚠ <strong>Delete this file now!</strong>
    It exposes your SMTP credentials in the browser.
    In Hostinger File Manager → select <code>test-mail.php</code> → Delete.
  </div>
</div>
</body>
</html>