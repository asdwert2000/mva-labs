<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once __DIR__ . '/../cache.php';
requireLogin();

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// УДАЛЕНИЕ
if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM price_packages WHERE id = ?");
    $stmt->execute([$id]);
    clear_page_cache();
    header('Location: /admin/prices.php?deleted=1');
    exit;
}

// ДОБАВЛЕНИЕ / РЕДАКТИРОВАНИЕ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $badge = trim($_POST['badge'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $note = trim($_POST['note'] ?? '');
    $popular = isset($_POST['popular']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $active = isset($_POST['active']) ? 1 : 0;

    if ($name === '' || $price === '') {
        $errorMsg = 'Заполните название и цену пакета.';
    } else if ($action === 'edit' && $id) {
        $stmt = $pdo->prepare("UPDATE price_packages SET
            name = ?, badge = ?, price = ?, features = ?, note = ?, popular = ?, sort_order = ?, active = ?
            WHERE id = ?");
        $stmt->execute([$name, $badge, $price, $features, $note, $popular, $sort_order, $active, $id]);
        clear_page_cache();
        header('Location: /admin/prices.php?updated=1');
        exit;
    } else {
        $stmt = $pdo->prepare("INSERT INTO price_packages (name, badge, price, features, note, popular, sort_order, active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $badge, $price, $features, $note, $popular, $sort_order, $active]);
        clear_page_cache();
        header('Location: /admin/prices.php?added=1');
        exit;
    }
}

// ДАННЫЕ ДЛЯ РЕДАКТИРОВАНИЯ
$editItem = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM price_packages WHERE id = ?");
    $stmt->execute([$id]);
    $editItem = $stmt->fetch();
}

$packages = $pdo->query("SELECT * FROM price_packages ORDER BY sort_order, id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Цены — MVA Labs Admin</title>
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
                <a href="/admin/prices.php" class="active">💰 Цены</a>
                <a href="/admin/leads.php">📩 Заявки</a>
                <a href="/admin/settings.php">⚙️ Настройки</a>
                <a href="/admin/logout.php" style="margin-top: 40px; color: #F87171;">🚪 Выход</a>
            </nav>
            <div class="sidebar__user">
                👤 <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
            </div>
        </aside>

        <main class="admin-main">
            <div class="admin-header">
                <h1>💰 Управление ценами</h1>
                <a href="?action=add" class="btn-add">➕ Добавить пакет</a>
            </div>

            <?php if (isset($_GET['added'])): ?>
                <div class="alert alert-success">✅ Пакет добавлен!</div>
            <?php endif; ?>
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">✅ Пакет обновлён!</div>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">🗑️ Пакет удалён!</div>
            <?php endif; ?>

            <?php if ($action === 'add' || $action === 'edit'): ?>
                <div class="form-card">
                    <h2><?= $action === 'edit' ? '✏️ Редактировать пакет' : '➕ Новый пакет' ?></h2>
                    <?php if (!empty($errorMsg)): ?>
                        <div class="alert alert-error">❌ <?= htmlspecialchars($errorMsg) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Название *</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Бейдж (Старт / Хит продаж / …)</label>
                                <input type="text" name="badge" value="<?= htmlspecialchars($editItem['badge'] ?? '') ?>" class="form-control">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Цена *</label>
                                <input type="text" name="price" value="<?= htmlspecialchars($editItem['price'] ?? '') ?>" class="form-control" placeholder="49 900 ₽" required>
                            </div>
                            <div class="form-group">
                                <label>Сортировка</label>
                                <input type="number" name="sort_order" value="<?= $editItem['sort_order'] ?? 0 ?>" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Состав пакета (каждый пункт с новой строки)</label>
                            <textarea name="features" rows="7" class="form-control"><?= htmlspecialchars($editItem['features'] ?? '') ?></textarea>
                            <p class="form-hint">Каждая строка станет пунктом списка</p>
                        </div>

                        <div class="form-group">
                            <label>Примечание внизу списка (например: «🔥 Оптимальный выбор»)</label>
                            <input type="text" name="note" value="<?= htmlspecialchars($editItem['note'] ?? '') ?>" class="form-control">
                        </div>

                        <div class="form-group" style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                            <label style="margin: 0;">
                                <input type="checkbox" name="popular" <?= !isset($editItem) || $editItem['popular'] ? 'checked' : '' ?>>
                                Выделить карточку (хит продаж)
                            </label>
                            <label style="margin: 0;">
                                <input type="checkbox" name="active" <?= !isset($editItem) || $editItem['active'] ? 'checked' : '' ?>>
                                Активен (показывать на сайте)
                            </label>
                        </div>

                        <div style="display: flex; gap: 12px; margin-top: 16px;">
                            <button type="submit" class="btn-save">💾 Сохранить</button>
                            <a href="/admin/prices.php" class="btn-cancel">Отмена</a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="form-card">
                    <?php if (empty($packages)): ?>
                        <p style="color: #6B7280;">Пакетов пока нет. Нажмите «Добавить пакет».</p>
                    <?php else: ?>
                        <?php foreach ($packages as $pkg): ?>
                            <div class="portfolio-item">
                                <div class="portfolio-item__info">
                                    <strong><?= htmlspecialchars($pkg['name']) ?></strong>
                                    <span style="color: #A855F7; font-size: 1rem;"><?= htmlspecialchars($pkg['price']) ?></span>
                                    <span class="badge <?= $pkg['active'] ? 'badge-green' : 'badge-gray' ?>">
                                        <?= $pkg['active'] ? 'Активен' : 'Скрыт' ?>
                                    </span>
                                    <?php if ($pkg['popular']): ?>
                                        <span class="badge badge-green">⭐ Хит</span>
                                    <?php endif; ?>
                                </div>
                                <div class="portfolio-item__actions">
                                    <a href="?action=edit&id=<?= $pkg['id'] ?>" class="btn-edit">✏️</a>
                                    <a href="?action=delete&id=<?= $pkg['id'] ?>" class="btn-delete" onclick="return confirm('Удалить пакет?')">🗑️</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>