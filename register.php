<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
if (!empty($_SESSION['user_id'])) {
    redirect('dashboard.php');
}
$errors = [];
$username = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    verify_csrf();
    $username = input_string('username');
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    if (text_length($username) < 3 || text_length($username) > 50) {
        $errors[] = 'Utilizatorul trebuie să aibă între 3 și 50 de caractere.';
    }
    if (text_length($password) < 8 || strlen($password) > 72) {
        $errors[] = 'Parola trebuie să aibă minimum 8 caractere și maximum 72 de octeți.';
    }
    if (!$errors) {
        require __DIR__ . '/db.php';
        try {
            $stmt = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (:username, :hash)');
            $stmt->execute(['username' => $username, 'hash' => password_hash($password, PASSWORD_DEFAULT)]);
            redirect('login.php?registered=1');
        } catch (PDOException $exception) {
            $errors[] = (string) $exception->getCode() === '23000'
                ? 'Acest utilizator există deja.' : 'Înregistrarea nu a reușit.';
        }
    }
}
$pageTitle = 'Înregistrare';
$themeReturn = 'register.php';
require __DIR__ . '/partials/header.php';
?>
<div class="container auth-layout">
    <section class="auth-aside">
        <span class="eyebrow">CONT NOU</span>
        <h1>Construiește o <span class="highlight">colecție digitală.</span></h1>
        <p>Fiecare utilizator poate administra doar propriile înregistrări din baza de date.</p>
        <div class="feature-list"><span>PHP procedural</span><span>PDO + MySQL</span><span>Sesiuni și formulare</span></div>
    </section>
    <section class="auth-card" aria-labelledby="register-title">
        <span class="eyebrow">ÎNREGISTRARE</span>
        <h2 id="register-title">Creează un cont</h2>
        <p class="muted">Datele contului sunt utilizate doar în proiectul local.</p>
        <?php if ($errors): ?><div class="alert error"><?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?></div><?php endif; ?>
        <form action="register.php" method="post" class="form-stack">
            <?= csrf_field() ?>
            <label>Nume de utilizator<input type="text" name="username" required minlength="3" maxlength="50" autocomplete="username" value="<?= e($username) ?>" placeholder="Ex.: elev03"></label>
            <label>Parolă<input type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Minimum 8 caractere"></label>
            <button type="submit" class="button button-primary button-full">Creează contul</button>
        </form>
        <p class="auth-bottom">Ai deja cont? <a href="login.php">Autentifică-te</a></p>
    </section>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
