<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
if (!empty($_SESSION['user_id'])) {
    redirect('dashboard.php');
}
$error = '';
$username = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    verify_csrf();
    $username = input_string('username');
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $error = 'Utilizatorul sau parola sunt incorecte.';
    if ($username !== '' && $password !== '') {
        require __DIR__ . '/db.php';
        $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            redirect('dashboard.php');
        }
    }
}
$pageTitle = 'Autentificare';
$themeReturn = 'login.php';
require __DIR__ . '/partials/header.php';
?>
<div class="container auth-layout">
    <section class="auth-aside">
        <span class="eyebrow">LABORATOR · PHP & MYSQL</span>
        <h1>Catalogul tău de <span class="highlight">jocuri electronice.</span></h1>
        <p>Organizează o colecție de jocuri și lucrează cu operații de adăugare, listare, modificare, căutare și ștergere.</p>
        <div class="feature-list"><span>01 / Autentificare</span><span>02 / Gestionare jocuri</span><span>03 / Filtre și statistici</span></div>
    </section>
    <section class="auth-card" aria-labelledby="login-title">
        <span class="eyebrow">ACCES CONT</span>
        <h2 id="login-title">Autentificare</h2>
        <p class="muted">Introdu datele contului pentru a deschide catalogul.</p>
        <?php if (isset($_GET['registered'])): ?><div class="alert success">Contul a fost creat.</div><?php endif; ?>
        <?php if (isset($_GET['logged_out'])): ?><div class="alert success">Ai fost deconectat.</div><?php endif; ?>
        <?php if ($error !== ''): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
        <form action="login.php" method="post" class="form-stack">
            <?= csrf_field() ?>
            <label>Nume de utilizator<input type="text" name="username" value="<?= e($username) ?>" required maxlength="50" autocomplete="username" placeholder="Ex.: demo"></label>
            <label>Parolă<input type="password" name="password" required autocomplete="current-password" placeholder="Introdu parola"></label>
            <button type="submit" class="button button-primary button-full">Intră în catalog</button>
        </form>
        <p class="auth-bottom">Nu ai cont? <a href="register.php">Înregistrează-te</a></p>
    </section>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
