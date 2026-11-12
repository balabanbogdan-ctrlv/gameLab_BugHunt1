<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function text_length(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value, 'UTF-8');
    }
    $count = preg_match_all('/./us', $value);
    return $count === false ? strlen($value) : $count;
}

function redirect(string $path): never
{
    header('Location: ' . $path, true, 303);
    exit;
}

function require_login(): void
{
    if (empty($_SESSION['user_id']) || !is_int($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        http_response_code(405);
        exit('Metodă HTTP nepermisă.');
    }
}

function verify_csrf(): void
{
    $token = $_POST['csrf'] ?? null;
    if (!is_string($token) || !hash_equals((string) ($_SESSION['csrf'] ?? ''), $token)) {
        http_response_code(403);
        exit('Cerere nevalidă.');
    }
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}

function current_theme(): string
{
    $theme = $_COOKIE['tema'] ?? 'light';
    return is_string($theme) && in_array($theme, ['light', 'dark'], true) ? $theme : 'light';
}

function valid_game_id(mixed $value): int
{
    if (!is_string($value) && !is_int($value)) {
        return 0;
    }
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? 0 : (int) $id;
}

function genres(): array
{
    return ['Acțiune', 'Aventură', 'RPG', 'Strategie', 'Sport', 'Simulare', 'Puzzle'];
}

function platforms(): array
{
    return ['PC', 'PlayStation', 'Xbox', 'Nintendo Switch'];
}

function valid_release_date(string $value): bool
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    return $date !== false && $date->format('Y-m-d') === $value;
}

function input_string(string $name): string
{
    $value = $_POST[$name] ?? null;
    return is_string($value) ? trim($value) : '';
}

function game_data_from_post(): array
{
    return [
        'title' => input_string('title'),
        'genre' => input_string('genre'),
        'platform' => input_string('platform'),
        'price' => input_string('price'),
        'stock' => input_string('stock'),
        'release_date' => input_string('release_date'),
    ];
}

function validate_game(array $game): bool
{
    $price = filter_var($game['price'], FILTER_VALIDATE_FLOAT);
    $stock = filter_var($game['stock'], FILTER_VALIDATE_INT);
    return text_length($game['title']) >= 1
        && text_length($game['title']) <= 150
        && in_array($game['genre'], genres(), true)
        && in_array($game['platform'], platforms(), true)
        && $price !== false && $price >= 0 && $price <= 9999.99
        && $stock !== false && $stock >= 0 && $stock <= 500
        && valid_release_date($game['release_date']);
}
