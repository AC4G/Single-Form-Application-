<?php
session_start();
//error_reporting(-1);
//ini_set('display_errors', 'On');

if(empty($_SESSION['csrfToken'])){
    $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
    $_SESSION['csrfTokenExpire'] = time() + 216000;
}

define('CONFIG_DIR', __DIR__ . '/config');
define('FUNCTION_DIR', __DIR__ . '/function');
define('LOG_DIR', __DIR__ . '/logs');
define('ASSETS_DIR', __DIR__ . '/assets');
define('TEMPLATES_DIR', __DIR__ . '/templates');
define('STORAGE_DIR', __DIR__ . '/storage');
define('BIN_DIR', __DIR__ . '/bin');
define('ACTIONS_DIR',__DIR__.'/actions');
define('MAILER_DIR',__DIR__.'/PHPMailer');
define('PHPQRCODE_DIR',__DIR__.'/qr-code-master');

$page = $_GET['page'] ?? 'main';

require_once TEMPLATES_DIR . '/' . $page .'.php';

require __DIR__ . '/includes.php';
