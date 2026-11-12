<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
redirect(!empty($_SESSION['user_id']) ? 'dashboard.php' : 'login.php');
