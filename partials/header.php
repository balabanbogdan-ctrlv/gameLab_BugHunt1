<?php
$theme = current_theme();
$isAuthenticated = !empty($_SESSION['user_id']) && is_int($_SESSION['user_id']);
$nextTheme = $theme === 'light' ? 'dark' : 'light';
?>
<!doctype html>
<html lang="ro" data-theme="<?= e($theme) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="GameLab - catalog personal de jocuri electronice">
    <title><?= e($pageTitle ?? 'Catalog') ?> | GameLab</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="site-shell">
    <header class="site-header">
        <div class="container nav-inner">
            <a href="index.php" class="brand"><span class="brand-icon">G</span><span>Game<span class="brand-accent">Lab</span><small>CATALOG DE JOCURI</small></span></a>
            <div class="nav-actions">
                <?php if ($isAuthenticated): ?>
                    <a href="dashboard.php" class="nav-link">Catalog</a>
                    <span class="account-name"><?= e($_SESSION['username']) ?></span>
                <?php endif; ?>
                <form action="preferinte.php" method="post" class="inline-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="tema" value="<?= e($nextTheme) ?>">
                    <input type="hidden" name="return_to" value="<?= e($themeReturn ?? 'index.php') ?>">
                    <button type="submit" class="button button-quiet button-sm" title="Schimbă tema"><?= $theme === 'light' ? 'Temă întunecată' : 'Temă luminoasă' ?></button>
                </form>
                <?php if ($isAuthenticated): ?>
                    <form action="logout.php" method="post" class="inline-form">
                        <?= csrf_field() ?>
                        <button type="submit" class="button button-outline button-sm">Ieșire</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="site-main">
