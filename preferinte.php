<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_post();
verify_csrf();
$theme = $_POST['tema'] ?? null;
if (!is_string($theme) || !in_array($theme, ['light', 'dark'], true)) {
    http_response_code(400);
    exit('Tema nu este validă.');
}
$https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
setcookie('tema', $theme, [
    'expires' => time() + 7 * 24 * 60 * 60, 'path' => '/', 'secure' => $https,
    'httponly' => true, 'samesite' => 'Lax',
]);
$returnTo = $_POST['return_to'] ?? null;
$allowed = ['login.php', 'register.php', 'dashboard.php', 'game_edit.php'];
$destination = is_string($returnTo) && in_array($returnTo, $allowed, true) ? $returnTo : 'index.php';
redirect($destination);
