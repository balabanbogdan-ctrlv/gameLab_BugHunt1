<?php
declare(strict_types=1);

$settings = [
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_NAME') ?: 'gamelab_bug_hunt',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '',
];
$localFile = __DIR__ . '/db.local.php';
if (is_file($localFile)) {
    $local = require $localFile;
    if (is_array($local)) {
        $settings = array_replace($settings, $local);
    }
}
try {
    $pdo = new PDO(
        'mysql:host=' . $settings['host'] . ';port=' . $settings['port']
        . ';dbname=' . $settings['database'] . ';charset=utf8mb4',
        $settings['username'], $settings['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    http_response_code(500);
    exit('Nu se poate realiza conexiunea MySQL. Verificați setările din db.local.php.');
}
