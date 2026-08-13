<?php
// ============================================
// АВТОРИЗАЦИЯ В АДМИН-ПАНЕЛИ
// ============================================

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function login($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header('Location: /admin/login.php');
    exit;
}
?>