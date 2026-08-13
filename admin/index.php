<?php
require_once 'config.php';
require_once 'includes/auth.php';
requireLogin();

$settings = getSettings($pdo);

// Статистика
$leadsCount = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$leadsNew = $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'new'")->fetchColumn();
$portfolioCount = $pdo->query("SELECT COUNT(*) FROM portfolio")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель MVA Labs</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- ===== САЙДБАР ===== -->
        <aside class="sidebar">
            <div class="sidebar__logo">
                <span class="logo__multiply">×</span>
                <span class="logo__text">MVA <span class="logo__labs">Labs</span></span>
            </div>
            <nav class="sidebar__nav">
                <a href="/admin/index.php" class="active">📊 Дашборд</a>
                <a href="/admin/editor.php?page=hero">✏️ Редактор страниц</a>
                <a href="/admin/portfolio.php">🖼️ Портфолио</a>
                <a href="/admin/leads.php">📩 Заявки <span class="badge"><?= $leadsNew ?></span></a>
                <a href="/admin/settings.php">⚙️ Настройки</a>
                <a href="/admin/logout.php" style="margin-top: 40px; color: #F87171;">🚪 Выход</a>
            </nav>
            <div class="sidebar__user">
                👤 <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
            </div>
        </aside>
        
        <!-- ===== ОСНОВНОЙ КОНТЕНТ ===== -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>📊 Дашборд</h1>
                <a href="/" target="_blank" class="btn-view-site">🌐 Смотреть сайт</a>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card__icon">📩</div>
                    <div class="stat-card__value"><?= $leadsCount ?></div>
                    <div class="stat-card__label">Всего заявок</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card__icon">🆕</div>
                    <div class="stat-card__value" style="color: #A855F7;"><?= $leadsNew ?></div>
                    <div class="stat-card__label">Новых заявок</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card__icon">🖼️</div>
                    <div class="stat-card__value"><?= $portfolioCount ?></div>
                    <div class="stat-card__label">Работ в портфолио</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card__icon">📄</div>
                    <div class="stat-card__value">5</div>
                    <div class="stat-card__label">Страниц контента</div>
                </div>
            </div>
            
            <div class="quick-actions">
                <h2>Быстрые действия</h2>
                <div class="quick-actions__grid">
                    <a href="/admin/editor.php?page=hero" class="quick-action">
                        ✏️ Редактировать главный экран
                    </a>
                    <a href="/admin/portfolio.php" class="quick-action">
                        🖼️ Добавить работу в портфолио
                    </a>
                    <a href="/admin/leads.php" class="quick-action">
                        📩 Просмотреть заявки
                    </a>
                    <a href="/admin/settings.php" class="quick-action">
                        ⚙️ Настройки сайта
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>