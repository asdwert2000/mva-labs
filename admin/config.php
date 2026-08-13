<?php
// ============================================
// КОНФИГУРАЦИЯ АДМИН-ПАНЕЛИ MVA LABS
// ============================================

// ===== ПОДКЛЮЧЕНИЕ К БАЗЕ ДАННЫХ =====
define('DB_HOST', 'localhost');
define('DB_NAME', 'mva_labs');
define('DB_USER', 'mva_labs');
define('DB_PASS', 'jlxV9RpFwDGsBLSfIUPW');

// ===== НАСТРОЙКИ АДМИНКИ =====
define('ADMIN_PATH', '/admin/');
define('UPLOAD_PATH', __DIR__ . '/assets/uploads/');
define('UPLOAD_URL', '/admin/assets/uploads/');

// ===== ПОДКЛЮЧЕНИЕ К БД =====
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}

// ===== ФУНКЦИЯ ДЛЯ ПОЛУЧЕНИЯ НАСТРОЕК =====
function getSettings($pdo) {
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

// ===== ФУНКЦИЯ ДЛЯ ПОЛУЧЕНИЯ КОНТЕНТА СТРАНИЦЫ =====
function getPageContent($pdo, $key) {
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE page_key = ?");
    $stmt->execute([$key]);
    return $stmt->fetch();
}

// ===== ОБНОВЛЕНИЕ КОНТЕНТА =====
function updatePageContent($pdo, $key, $data) {
    $sql = "UPDATE pages SET 
            title = :title, 
            content = :content, 
            meta_title = :meta_title, 
            meta_description = :meta_description, 
            meta_keywords = :meta_keywords 
            WHERE page_key = :page_key";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($data);
}

// ЗАПУСКАЕМ СЕССИЮ
session_start();
?>