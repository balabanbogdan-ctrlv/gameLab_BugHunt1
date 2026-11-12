<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();
require_post();
verify_csrf();
$id = valid_game_id($_POST['id'] ?? null);
if ($id === 0) {
    redirect('dashboard.php?error=invalid');
}
require __DIR__ . '/db.php';
$stmt = $pdo->prepare('DELETE FROM games WHERE id = :id AND user_id = :user_id')
$stmt->execute(['id' => $id, 'user_id' => $_SESSION['user_id']]);
redirect($stmt->rowCount() > 0 ? 'dashboard.php?msg=deleted' : 'dashboard.php?error=not_found');
