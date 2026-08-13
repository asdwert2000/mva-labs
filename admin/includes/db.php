<?php
// ============================================
// РАБОТА С БАЗОЙ ДАННЫХ (SQLite)
// ============================================

require_once __DIR__ . '/../config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dir = dirname(DB_PATH);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA journal_mode = WAL');
        initSchema($pdo);
    }
    return $pdo;
}

// ===== СТРУКТУРА БАЗЫ =====
function initSchema(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        key_name TEXT PRIMARY KEY,
        key_value TEXT
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS content (
        block_key TEXT PRIMARY KEY,
        block_value TEXT
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS portfolio (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        description TEXT,
        category TEXT NOT NULL DEFAULT 'site',
        image TEXT,
        sort INTEGER DEFAULT 0,
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS leads (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        phone TEXT,
        email TEXT,
        package TEXT,
        site TEXT,
        message TEXT,
        created_at TEXT DEFAULT (datetime('now')),
        readed INTEGER DEFAULT 0
    )");

    // Начальные настройки при первом запуске
    $count = (int)$pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    if ($count === 0) {
        $seed = [
            'admin_login'    => 'admin',
            'admin_password' => password_hash('admin123', PASSWORD_DEFAULT),
            'contact_email'  => 'hello@mvalabs.ru',
            'contact_phone'  => '+7 (XXX) XXX-XX-XX',
            'telegram_link'  => 'https://t.me/mva_labs_bot',
        ];
        $stmt = $pdo->prepare("INSERT INTO settings (key_name, key_value) VALUES (:k, :v)");
        foreach ($seed as $k => $v) {
            $stmt->execute([':k' => $k, ':v' => $v]);
        }
    }
}

// ===== ПРОСТЫЕ ПОМОЩНИКИ =====
function get_setting(string $key, string $default = ''): string {
    $stmt = db()->prepare("SELECT key_value FROM settings WHERE key_name = :k");
    $stmt->execute([':k' => $key]);
    $val = $stmt->fetchColumn();
    return $val === false ? $default : (string)$val;
}

function set_setting(string $key, string $value): void {
    $stmt = db()->prepare("INSERT INTO settings (key_name, key_value) VALUES (:k, :v)
        ON CONFLICT(key_name) DO UPDATE SET key_value = :v");
    $stmt->execute([':k' => $key, ':v' => $value]);
}

function get_content(string $key, string $default = ''): string {
    $stmt = db()->prepare("SELECT block_value FROM content WHERE block_key = :k");
    $stmt->execute([':k' => $key]);
    $val = $stmt->fetchColumn();
    return $val === false ? $default : (string)$val;
}

function set_content(string $key, string $value): void {
    $stmt = db()->prepare("INSERT INTO content (block_key, block_value) VALUES (:k, :v)
        ON CONFLICT(block_key) DO UPDATE SET block_value = :v");
    $stmt->execute([':k' => $key, ':v' => $value]);
}
