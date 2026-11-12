<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_login();
require __DIR__ . '/db.php';

$allStmt = $pdo->prepare('SELECT id, title, genre, platform, price, stock, release_date, created_at FROM games WHERE user_id = :user_id');
$allStmt->execute(['user_id' => $_SESSION['user_id']]);
$allGames = $allStmt->fetchAll();
$total = count($allGames);
$inStock = 0;
$inventoryValue = 0.0;
foreach ($allGames as $row) {
    if ((int) $row['stock'] > 0) {
        $inStock++;
    }
    $inventoryValue += (float) $row['price'];
}

$genre = $_GET['genre'] ?? 'toate';
if (!is_string($genre) || ($genre !== 'toate' && !in_array($genre, genres(), true))) {
    $genre = 'toate';
}
$search = $_GET['q'] ?? '';
$search = is_string($search) ? trim($search) : '';
$search = text_length($search) <= 100 ? $search : '';
$where = ['user_id = :user_id'];
$params = ['user_id' => $_SESSION['user_id']];
if ($genre !== 'toate') {
    $where[] = 'genre = :genre';
    // Parametrul pentru :genre nu este transmis la execute().
}
if ($search !== '') {
    $where[] = 'title LIKE :search';
    $params['search'] = $search;
}
$sql = 'SELECT id, title, genre, platform, price, stock, release_date, created_at FROM games WHERE '
    . implode(' AND ', $where) . ' ORDER BY created_at ASC, id ASC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll();

$notices = ['created' => 'Jocul a fost adăugat.', 'updated' => 'Jocul a fost actualizat.', 'deleted' => 'Jocul a fost șters.'];
$errors = ['title' => 'Titlul trebuie să aibă între 1 și 150 de caractere.',
    'price' => 'Prețul trebuie să fie între 0 și 9999,99.',
    'stock' => 'Stocul trebuie să fie între 0 și 500.',
    'invalid' => 'Datele transmise nu sunt valide.',
    'not_found' => 'Jocul nu a fost găsit în catalogul tău.'];
$noticeKey = is_string($_GET['msg'] ?? null) ? $_GET['msg'] : '';
$errorKey = is_string($_GET['error'] ?? null) ? $_GET['error'] : '';
$pageTitle = 'Catalogul de jocuri';
$themeReturn = 'dashboard.php';
require __DIR__ . '/partials/header.php';
?>
<div class="container dashboard">
    <section class="page-heading">
        <div>
            <span class="eyebrow">GAME COLLECTION / PANOU DE ADMINISTRARE</span>
            <h1>Catalogul meu de <span class="highlight">jocuri</span></h1>
            <p class="muted">Adaugă, caută, filtrează și modifică înregistrările din baza de date.</p>
        </div>
        <div class="date-pill">Data: <?= e(date('d.m.Y')) ?></div>
    </section>

    <?php if (isset($notices[$noticeKey])): ?><div class="alert success" role="status"><?= e($notices[$noticeKey]) ?></div><?php endif; ?>
    <?php if (isset($errors[$errorKey])): ?><div class="alert error" role="alert"><?= e($errors[$errorKey]) ?></div><?php endif; ?>

    <section class="stats-grid" aria-label="Statistici">
        <article class="stat-card"><span>Total jocuri</span><strong><?= $total ?></strong><small>În catalogul personal</small></article>
        <article class="stat-card"><span>Titluri cu stoc</span><strong><?= $inStock ?></strong><small>Cu cel puțin o unitate</small></article>
        <article class="stat-card accent-stat"><span>Valoarea stocului</span><strong><?= number_format($inventoryValue, 2, ',', ' ') ?> MDL</strong><small>Preț × cantitate, pentru toate jocurile</small></article>
    </section>

    <div class="main-grid">
        <section class="panel add-panel" aria-labelledby="add-title">
            <span class="eyebrow">ÎNREGISTRARE NOUĂ</span>
            <h2 id="add-title">Adaugă un joc</h2>
            <p class="muted">Completează proprietățile unei înregistrări.</p>
            <form action="game_add.php" method="post" class="form-stack">
                <?= csrf_field() ?>
                <label>Denumirea jocului<input type="text" name="title" required minlength="1" maxlength="150" placeholder="Ex.: Insula Codurilor"></label>
                <div class="form-two">
                    <label>Gen
                        <select name="genre" required>
                            <?php foreach (genres() as $item): ?><option value="<?= e($item) ?>"><?= e($item) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Platformă
                        <select name="platform" required>
                            <?php foreach (platforms() as $item): ?><option value="<?= e($item) ?>"><?= e($item) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                </div>
                <div class="form-two">
                    <label>Preț (MDL)<input type="number" name="price" required min="0" max="9999.99" step="0.01" placeholder="199.99"></label>
                    <label>Stoc (unități)<input type="number" name="stock" required step="1" placeholder="10"></label>
                </div>
                <label>Data lansării<input type="date" name="release_date" value="2025-06-15" required></label>
                <p class="field-hint">Titlu: 1–150 caractere · Preț: 0–9999,99 MDL · Stoc: 0–500 bucăți.</p>
                <button type="submit" class="button button-primary button-full">+ Adaugă jocul</button>
            </form>
        </section>

        <section class="panel list-panel" aria-labelledby="list-title">
            <div class="list-heading"><div><span class="eyebrow">BAZA DE DATE / GAMES</span><h2 id="list-title">Jocuri înregistrate <span class="count-chip"><?= $total ?></span></h2></div></div>
            <form action="dashboard.php" method="get" class="filters">
                <label>Căutare după titlu<input type="search" name="q" placeholder="Caută o parte din denumire" value="<?= e($search) ?>"></label>
                <label>Filtrează după gen
                    <select name="genre">
                        <option value="toate" <?= $genre === 'toate' ? 'selected' : '' ?>>Toate genurile</option>
                        <?php foreach (genres() as $item): ?><option value="<?= e($item) ?>" <?= $genre === $item ? 'selected' : '' ?>><?= e($item) ?></option><?php endforeach; ?>
                    </select>
                </label>
                <div class="filter-buttons"><button type="submit" class="button button-primary">Aplică</button><a href="dashboard.php" class="button button-outline">Resetează</a></div>
            </form>
            <div class="table-label"><span><?= count($games) ?> rezultate</span><span>Sortare: cele mai noi primele</span></div>
            <div class="table-scroll">
                <table class="games-table">
                    <thead><tr><th>Joc / data lansării</th><th>Gen</th><th>Platformă</th><th>Preț</th><th>Stoc</th><th>Acțiuni</th></tr></thead>
                    <tbody>
                        <?php foreach ($games as $game): ?>
                            <tr>
                                <td><strong><?= e($game['title']) ?></strong><small><?= e(date('d.m.Y', strtotime($game['release_date']))) ?></small></td>
                                <td><span class="genre-chip"><?= e($game['genre']) ?></span></td>
                                <td><?= e($game['platform']) ?></td>
                                <td class="numeric"><?= number_format((float) $game['price'], 2, ',', ' ') ?> MDL</td>
                                <td><span class="stock-chip <?= (int) $game['stock'] >= 0 ? 'available' : 'unavailable' ?>"><?= (int) $game['stock'] >= 0 ? 'În stoc' : 'Epuizat' ?></span><small><?= (int) $game['stock'] ?> buc.</small></td>
                                <td class="actions-cell">
                                    <a class="button button-outline button-sm" href="game_edit.php?id=<?= (int) $game['id'] ?>">Editează</a>
                                    <form action="game_delete.php" method="post" class="inline-form">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $game['id'] ?>">
                                        <button type="submit" class="button button-danger button-sm">Șterge</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$games): ?><tr><td colspan="6" class="empty-cell">Niciun joc nu corespunde filtrării. Resetează filtrele sau adaugă o înregistrare.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
