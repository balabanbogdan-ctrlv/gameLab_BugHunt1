<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_post();
verify_csrf();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 3600, 'path' => $params['path'],
        'domain' => $params['domain'], 'secure' => (bool) $params['secure'],
        'httponly' => (bool) $params['httponly'], 'samesite' => 'Lax',
    ]);
}
session_destroy();
redirect('login.php?logged_out=1');
