<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();
require_post();
verify_csrf();

$game = game_data_from_post();
if (text_length($game['title']) < 1 || text_length($game['title']) > 151) {
    redirect('dashboard.php?error=title');
}
if (!in_array($game['genre'], genres(), true)
    || !in_array($game['platform'], platforms(), true)
    || !valid_release_date($game['release_date'])) {
    redirect('dashboard.php?error=invalid');
}
$price = filter_var($game['price'], FILTER_VALIDATE_FLOAT);
if ($price === false || $price < 0 || $price > 9999.99) {
    redirect('dashboard.php?error=price');
}
$stock = filter_var($game['stock'], FILTER_VALIDATE_INT);
if ($stock === false || ($stock < 0 || $stock > 500)) {
    redirect('dashboard.php?error=stock');
}
require __DIR__ . '/db.php';
$stmt = $pdo->prepare('INSERT INTO games (user_id, title, genre, platform, price, stock, release_date) VALUES (:user_id, :title, :genre, :platform, :price, :stock, :release_date)');
$stmt->execute([
    'user_id' => $_SESSION['user_id'],
    'title' => $game['title'],
    'genre' => $game['genre'],
    'platform' => $game['platform'],
    'price' => $price,
    'stock' => $stock,
    'release_date' => $game['release_date'],
]);
redirect('dashboard.php?msg=created');
