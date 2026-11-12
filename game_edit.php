<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();
$id = valid_game_id($_GET['id'] ?? null);
if ($id === 0) {
    redirect('dashboard.php?error=invalid');
}
require __DIR__ . '/db.php';
$stmt = $pdo->prepare('SELECT id, title, genre, platform, price, stock, release_date FROM games WHERE id = :id AND user_id = :user_id');
$stmt->execute(['id' => $id, 'user_id' => $_SESSION['user_id']]);
$game = $stmt->fetch();
if (!$game) {
    redirect('dashboard.php?error=not_found');
}
$pageTitle = 'Editare joc';
$themeReturn = 'dashboard.php';
require __DIR__ . '/partials/header.php';
?>
<div class="container edit-wrapper">
    <a href="dashboard.php" class="back-link">← Înapoi la catalog</a>
    <section class="panel edit-panel" aria-labelledby="edit-title">
        <span class="eyebrow">EDITARE / ID #<?= (int) $game['id'] ?></span>
        <h1 id="edit-title">Modifică jocul</h1>
        <p class="muted">Actualizează proprietățile jocului selectat.</p>
        <?php if (isset($_GET['error'])): ?><div class="alert error">Verifică datele introduse și încearcă din nou.</div><?php endif; ?>
        <form action="game_update.php" method="post" class="form-stack">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $game['id'] ?>">
            <label>Denumirea jocului<input type="text" name="title" minlength="1" maxlength="150" required value="<?= e($game['title']) ?>"></label>
            <div class="form-two">
                <label>Gen
                    <select name="genre" required><?php foreach (genres() as $item): ?><option value="<?= e($item) ?>" <?= $game['genre'] === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select>
                </label>
                <label>Platformă
                    <select name="platform" required><?php foreach (platforms() as $item): ?><option value="<?= e($item) ?>" <?= $game['platform'] === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?></select>
                </label>
            </div>
            <div class="form-two">
                <label>Preț (MDL)<input type="number" name="price" min="0" max="9999.99" step="0.01" required value="<?= e($game['price']) ?>"></label>
                <label>Stoc (unități)<input type="number" name="stock" min="0" max="500" step="1" required value="<?= (int) $game['stock'] ?>"></label>
            </div>
            <label>Data lansării<input type="date" name="release_date" required value="<?= e($game['release_date']) ?>"></label>
            <div class="form-actions"><button type="submit" class="button button-primary">Salvează modificările</button><a class="button button-outline" href="dashboard.php">Anulează</a></div>
        </form>
    </section>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
