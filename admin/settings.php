<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once __DIR__ . '/../cache.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $settingKey = substr($key, 8);
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $settingKey]);
        }
    }
    clear_page_cache();
    $success = '✅ Настройки сохранены!';
}

$settings = getSettings($pdo);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки — MVA Labs Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar__logo">
                <span class="logo__multiply">×</span>
                <span class="logo__text">MVA <span class="logo__labs">Labs</span></span>
            </div>
            <nav class="sidebar__nav">
                <a href="/admin/index.php">📊 Дашборд</a>
                <a href="/admin/editor.php?page=hero">✏️ Редактор страниц</a>
                <a href="/admin/portfolio.php">🖼️ Портфолио</a>
                <a href="/admin/prices.php">💰 Цены</a>
                <a href="/admin/leads.php">📩 Заявки</a>
                <a href="/admin/settings.php" class="active">⚙️ Настройки</a>
                <a href="/admin/logout.php" style="margin-top: 40px; color: #F87171;">🚪 Выход</a>
            </nav>
            <div class="sidebar__user">
                👤 <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
            </div>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1>⚙️ Настройки сайта</h1>
                <a href="/" target="_blank" class="btn-view-site">🌐 Смотреть сайт</a>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="POST" class="settings-form">
                <div class="form-card">
                    <h2>Основные настройки</h2>
                    
                    <div class="form-group">
                        <label>Название сайта</label>
                        <input type="text" name="setting_site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>URL сайта</label>
                        <input type="text" name="setting_site_url" value="<?= htmlspecialchars($settings['site_url'] ?? '') ?>" class="form-control">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="setting_email" value="<?= htmlspecialchars($settings['email'] ?? '') ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Телефон</label>
                            <input type="text" name="setting_phone" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" class="form-control">
                        </div>
                    </div>
                    
                    <h2 style="margin-top: 24px;">Telegram бот</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Bot Username</label>
                            <input type="text" name="setting_telegram_bot" value="<?= htmlspecialchars($settings['telegram_bot'] ?? '') ?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Chat ID</label>
                            <input type="text" name="setting_telegram_chat_id" value="<?= htmlspecialchars($settings['telegram_chat_id'] ?? '') ?>" class="form-control">
                        </div>
                    </div>
                    
                    <h2 style="margin-top: 24px;">SEO</h2>
                    
                    <div class="form-group">
                        <label>OG Image (для соцсетей)</label>
                        <input type="text" name="setting_og_image" value="<?= htmlspecialchars($settings['og_image'] ?? '') ?>" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label>Копирайт в подвале</label>
                        <input type="text" name="setting_copyright" value="<?= htmlspecialchars($settings['copyright'] ?? '') ?>" class="form-control">
                    </div>
                    
                    <button type="submit" class="btn-save">💾 Сохранить настройки</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>