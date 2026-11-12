<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();
require_post();
verify_csrf();
$id = valid_game_id($_POST['id'] ?? null);
$game = game_data_from_post();
if ($id === 0 || !validate_game($game)) {
    redirect('dashboard.php?error=invalid');
}
require __DIR__ . '/db.php';
$stmt = $pdo->prepare('UPDATE games SET title = :title, genre = :genre, platform = :platform, price = :price, stock = :stock, release_date = :release_date WHERE id = :id AND user_id = :user_id');
$stmt->execute([
    'title' => $game['title'],
    'genre' => $game['genre'],
    'platform' => $game['platform'],
    'price' => $game['price'],
    'stock' => $game['stock'],
    'release_date' => $game['release_date'],
    'id' => $id,
    'user_id' => $_SESSION['user_id'],
]);
redirect('dashboard.php?msg=updated');