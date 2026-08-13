<?php
// ============================================
// АВТОРИЗАЦИЯ
// ============================================

require_once __DIR__ . '/db.php';

function auth_init(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/admin',
            'httponly' => true,
        ]);
        session_start();
    }
}

function is_logged_in(): bool {
    auth_init();
    return isset($_SESSION['admin_id']);
}

function require_login(): void {
    auth_init();
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function attempt_login(string $login, string $password): bool {
    $stored_login = get_setting('admin_login', 'admin');
    $stored_hash  = get_setting('admin_password');

    if (!hash_equals($stored_login, $login) || empty($stored_hash)) {
        return false;
    }

    if (password_verify($password, $stored_hash)) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = session_id();
        return true;
    }
    return false;
}

function logout(): void {
    auth_init();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

// ===== CSRF-ЗАЩИТА =====
function csrf_token(): string {
    auth_init();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_check(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Ошибка безопасности: неверный CSRF-токен.');
    }
}
