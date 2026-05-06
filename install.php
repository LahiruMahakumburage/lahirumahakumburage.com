<?php
/**
 * PHPMailer Auto-Installer
 * ─────────────────────────────────────────────────────────────
 * INSTRUCTIONS:
 *   1. Upload this file to: public_html/install.php
 *   2. Visit: https://lahirumahakumburage.com/install.php
 *   3. It downloads and installs PHPMailer automatically
 *   4. DELETE this file immediately after — it's a security risk!
 * ─────────────────────────────────────────────────────────────
 */

// ── Basic security: only run from browser, never from CLI ──
if (php_sapi_name() === 'cli') {
    die('Run this from your browser only.');
}

$log    = [];
$errors = [];

function log_step(string $msg): void {
    global $log;
    $log[] = $msg;
    echo '<p style="margin:4px 0;font-size:14px">' . htmlspecialchars($msg) . '</p>';
    ob_flush(); flush();
}

function fail(string $msg): void {
    global $errors;
    $errors[] = $msg;
    echo '<p style="color:#ef4444;margin:4px 0;font-size:14px">✗ ' . htmlspecialchars($msg) . '</p>';
    ob_flush(); flush();
}

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>PHPMailer Installer</title>
<style>
  *{box-sizing:border-box}
  body{font-family:'Segoe UI',Arial,sans-serif;background:#0b0c1a;color:#e0e0ff;margin:0;padding:40px 20px}
  .box{max-width:640px;margin:0 auto;background:#141528;border:1px solid rgba(108,99,255,.25);border-radius:16px;padding:32px;box-shadow:0 20px 60px rgba(0,0,0,.5)}
  h1{font-size:20px;margin:0 0 6px;color:#fff}
  .sub{font-size:13px;color:#7b7fa0;margin:0 0 24px}
  .log{background:#0b0c1a;border-radius:10px;padding:16px;margin:16px 0;min-height:60px;font-family:monospace}
  .ok{color:#4ade80}.warn{color:#facc15}.info{color:#818cf8}
  .done{background:rgba(74,222,128,.08);border:1px solid rgba(74,222,128,.2);border-radius:10px;padding:16px;margin-top:20px}
  .done h2{color:#4ade80;margin:0 0 8px;font-size:16px}
  .done p{color:#9ba3c0;font-size:13px;margin:4px 0;line-height:1.6}
  .err{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:10px;padding:16px;margin-top:20px}
  .err h2{color:#f87171;margin:0 0 8px;font-size:16px}
  a{color:#6c63ff}
  .warn-box{background:rgba(250,204,21,.08);border:1px solid rgba(250,204,21,.3);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#fde68a}
</style>
</head>
<body>
<div class="box">
  <h1>📦 PHPMailer Installer</h1>
  <p class="sub">For Lahiru Mahakumburage Portfolio — lahirumahakumburage.com</p>

  <div class="warn-box">
    ⚠ <strong>Delete this file immediately after installation!</strong>
    Leaving it online is a security risk.
  </div>

  <div class="log">
<?php
ob_start();

// ── Step 1: Check PHP version ──
$phpVer = phpversion();
if (version_compare($phpVer, '7.4', '>=')) {
    log_step('<span class="ok">✓</span> PHP ' . $phpVer . ' — compatible');
} else {
    fail('PHP 7.4+ required. Your version: ' . $phpVer);
    goto done;
}

// ── Step 2: Check write permission ──
$dir = __DIR__ . '/phpmailer';
if (!is_dir($dir)) {
    if (!mkdir($dir, 0755, true)) {
        fail('Cannot create phpmailer/ directory. Check folder permissions.');
        goto done;
    }
}
log_step('<span class="ok">✓</span> phpmailer/ directory ready');

// ── Step 3: Download PHPMailer from GitHub ──
log_step('<span class="info">↓</span> Downloading PHPMailer from GitHub…');

// We only need the 3 core files — no need for the full repo
$files = [
    'PHPMailer.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php',
    'SMTP.php'      => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php',
    'Exception.php' => 'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php',
];

$srcDir = $dir . '/src';
if (!is_dir($srcDir)) mkdir($srcDir, 0755, true);

$allOk = true;
foreach ($files as $filename => $url) {
    $dest    = $srcDir . '/' . $filename;
    $content = @file_get_contents($url);

    if ($content === false) {
        // Try with curl as fallback
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_USERAGENT      => 'Mozilla/5.0',
            ]);
            $content = curl_exec($ch);
            curl_close($ch);
        }
    }

    if ($content && strlen($content) > 100) {
        file_put_contents($dest, $content);
        log_step('<span class="ok">✓</span> Downloaded: src/' . $filename . ' (' . number_format(strlen($content)/1024, 1) . ' KB)');
    } else {
        fail('Failed to download: ' . $filename . '. Check internet access from server.');
        $allOk = false;
    }
}

if (!$allOk) goto done;

// ── Step 4: Verify files ──
log_step('<span class="info">→</span> Verifying installation…');
$ok = true;
foreach (array_keys($files) as $fn) {
    $path = $srcDir . '/' . $fn;
    if (file_exists($path) && filesize($path) > 1000) {
        log_step('<span class="ok">✓</span> Verified: phpmailer/src/' . $fn);
    } else {
        fail('File missing or empty: phpmailer/src/' . $fn);
        $ok = false;
    }
}

if (!$ok) goto done;

// ── Step 5: Quick smoke test ──
log_step('<span class="info">→</span> Running smoke test…');
try {
    require_once $srcDir . '/Exception.php';
    require_once $srcDir . '/PHPMailer.php';
    require_once $srcDir . '/SMTP.php';
    $m = new PHPMailer\PHPMailer\PHPMailer();
    log_step('<span class="ok">✓</span> PHPMailer loaded successfully — smoke test passed');
} catch (\Throwable $t) {
    fail('Load error: ' . $t->getMessage());
    goto done;
}

log_step('<span class="ok">✓</span> Installation complete!');

done:
ob_end_flush();
?>
  </div>

<?php if (empty($errors)): ?>
  <div class="done">
    <h2>✓ PHPMailer is installed and ready</h2>
    <p>The following files are now on your server:</p>
    <p>
      <code>public_html/phpmailer/src/PHPMailer.php</code><br/>
      <code>public_html/phpmailer/src/SMTP.php</code><br/>
      <code>public_html/phpmailer/src/Exception.php</code>
    </p>
    <p style="margin-top:12px;color:#fde68a">
      ⚠ <strong>Next step:</strong> Delete this file from your server now.<br/>
      In Hostinger File Manager → select <code>install.php</code> → Delete.
    </p>
    <p>Then open <a href="send.php">send.php</a> and update <code>SMTP_PASS</code> with your email password.</p>
  </div>
<?php else: ?>
  <div class="err">
    <h2>✗ Installation had errors</h2>
    <p>Please upload PHPMailer manually:</p>
    <p>1. Download from <a href="https://github.com/PHPMailer/PHPMailer/archive/refs/heads/master.zip" target="_blank">github.com/PHPMailer/PHPMailer</a></p>
    <p>2. Extract the ZIP → go into the <code>src/</code> folder</p>
    <p>3. Upload those 3 PHP files to <code>public_html/phpmailer/src/</code></p>
  </div>
<?php endif; ?>

</div>
</body>
</html>