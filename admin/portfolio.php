<?php
require_once 'config.php';
require_once 'includes/auth.php';
requireLogin();

// ===== ОБРАБОТКА ДЕЙСТВИЙ =====
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// УДАЛЕНИЕ
if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM portfolio WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: /admin/portfolio.php?deleted=1');
    exit;
}

// ДОБАВЛЕНИЕ / РЕДАКТИРОВАНИЕ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $result = $_POST['result'] ?? '';
    $gradient = $_POST['gradient'] ?? '';
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $active = isset($_POST['active']) ? 1 : 0;
    
    if ($action === 'edit' && $id) {
        $stmt = $pdo->prepare("UPDATE portfolio SET 
            category = ?, title = ?, description = ?, result = ?, gradient = ?, sort_order = ?, active = ? 
            WHERE id = ?");
        $stmt->execute([$category, $title, $description, $result, $gradient, $sort_order, $active, $id]);
        header('Location: /admin/portfolio.php?updated=1');
        exit;
    } else {
        $stmt = $pdo->prepare("INSERT INTO portfolio (category, title, description, result, gradient, sort_order, active) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category, $title, $description, $result, $gradient, $sort_order, $active]);
        header('Location: /admin/portfolio.php?added=1');
        exit;
    }
}

// ПОЛУЧЕНИЕ ДАННЫХ ДЛЯ РЕДАКТИРОВАНИЯ
$editItem = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM portfolio WHERE id = ?");
    $stmt->execute([$id]);
    $editItem = $stmt->fetch();
}

// ПОЛУЧЕНИЕ СПИСКА ПОРТФОЛИО
$portfolio = $pdo->query("SELECT * FROM portfolio ORDER BY category, sort_order")->fetchAll();
$categories = ['sites' => 'Сайты', 'apps' => 'Приложения', 'strategies' => 'Стратегии'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Портфолио — MVA Labs Admin</title>
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
                <a href="/admin/index.php">📊 Дашборд</a>
                <a href="/admin/editor.php?page=hero">✏️ Редактор страниц</a>
                <a href="/admin/portfolio.php" class="active">🖼️ Портфолио</a>
                <a href="/admin/leads.php">📩 Заявки</a>
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
                <h1>🖼️ Управление портфолио</h1>
                <a href="?action=add" class="btn-add">➕ Добавить работу</a>
            </div>
            
            <!-- ===== СООБЩЕНИЯ ===== -->
            <?php if (isset($_GET['added'])): ?>
                <div class="alert alert-success">✅ Работа добавлена!</div>
            <?php endif; ?>
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">✅ Работа обновлена!</div>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">🗑️ Работа удалена!</div>
            <?php endif; ?>
            
            <!-- ===== ФОРМА ДОБАВЛЕНИЯ/РЕДАКТИРОВАНИЯ ===== -->
            <?php if ($action === 'add' || $action === 'edit'): ?>
                <div class="form-card">
                    <h2><?= $action === 'edit' ? '✏️ Редактировать работу' : '➕ Новая работа в портфолио' ?></h2>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Категория</label>
                                <select name="category" class="form-control">
                                    <?php foreach ($categories as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= isset($editItem) && $editItem['category'] === $key ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Сортировка</label>
                                <input type="number" name="sort_order" value="<?= $editItem['sort_order'] ?? 0 ?>" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Название проекта *</label>
                            <input type="text" name="title" value="<?= htmlspecialchars($editItem['title'] ?? '') ?>" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Описание</label>
                            <textarea name="description" rows="3" class="form-control"><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Результат (например: +27% конверсии)</label>
                                <input type="text" name="result" value="<?= htmlspecialchars($editItem['result'] ?? '') ?>" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Градиент (например: #2D1B69, #120A2B)</label>
                                <input type="text" name="gradient" value="<?= htmlspecialchars($editItem['gradient'] ?? '#2D1B69, #120A2B') ?>" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-group" style="display: flex; gap: 12px; align-items: center;">
                            <label style="margin: 0;">
                                <input type="checkbox" name="active" <?= !isset($editItem) || $editItem['active'] ? 'checked' : '' ?>>
                                Активно (показывать на сайте)
                            </label>
                        </div>
                        
                        <div style="display: flex; gap: 12px; margin-top: 16px;">
                            <button type="submit" class="btn-save">💾 Сохранить</button>
                            <a href="/admin/portfolio.php" class="btn-cancel">Отмена</a>
                        </div>
                    </form>
                </div>
            
            <!-- ===== СПИСОК ПОРТФОЛИО ===== -->
            <?php else: ?>
                <?php foreach ($categories as $key => $label): ?>
                    <h3 style="color: #D1D5DB; margin: 24px 0 12px;"><?= $label ?></h3>
                    <div class="portfolio-list">
                        <?php 
                        $items = array_filter($portfolio, fn($item) => $item['category'] === $key);
                        if (empty($items)): ?>
                            <p style="color: #6B7280;">Нет работ в этой категории</p>
                        <?php else: ?>
                            <?php foreach ($items as $item): ?>
                                <div class="portfolio-item">
                                    <div class="portfolio-item__info">
                                        <strong><?= htmlspecialchars($item['title']) ?></strong>
                                        <span style="color: #9CA3AF; font-size: 0.85rem;"><?= htmlspecialchars($item['result']) ?></span>
                                        <span class="badge <?= $item['active'] ? 'badge-green' : 'badge-gray' ?>">
                                            <?= $item['active'] ? 'Активно' : 'Скрыто' ?>
                                        </span>
                                    </div>
                                    <div class="portfolio-item__actions">
                                        <a href="?action=edit&id=<?= $item['id'] ?>" class="btn-edit">✏️</a>
                                        <a href="?action=delete&id=<?= $item['id'] ?>" class="btn-delete" onclick="return confirm('Удалить эту работу?')">🗑️</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>