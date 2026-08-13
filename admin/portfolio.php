<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db.php';

require_login();
$current = 'portfolio';

$categories = [
    'site'      => '📱 Сайты',
    'app'       => '📲 Приложения',
    'strategy'  => '📊 Стратегии',
];

// ===== ДЕЙСТВИЯ =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = db()->prepare("SELECT image FROM portfolio WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $old = $stmt->fetchColumn();
            if ($old && file_exists(UPLOAD_DIR . '/' . $old)) {
                @unlink(UPLOAD_DIR . '/' . $old);
            }
            db()->prepare("DELETE FROM portfolio WHERE id = :id")->execute([':id' => $id]);
            flash('Запись удалена.');
            redirect('portfolio.php');
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category = $_POST['category'] ?? 'site';
        if (!isset($categories[$category])) {
            $category = 'site';
        }

        if ($title === '') {
            throw new RuntimeException('Заполните название.');
        }

        $image = upload_image($_FILES['image'] ?? [], 'portf');
        $sort = (int)($_POST['sort'] ?? 0);

        if ($action === 'edit') {
            $id = (int)($_POST['id'] ?? 0);
            $sql = "UPDATE portfolio SET title = :title, description = :description,
                    category = :category, sort = :sort WHERE id = :id";
            $params = [
                ':title' => $title, ':description' => $description,
                ':category' => $category, ':sort' => $sort, ':id' => $id,
            ];
            if ($image !== null) {
                $stmt = db()->prepare("SELECT image FROM portfolio WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $old = $stmt->fetchColumn();
                if ($old && file_exists(UPLOAD_DIR . '/' . $old)) {
                    @unlink(UPLOAD_DIR . '/' . $old);
                }
                $sql = "UPDATE portfolio SET title = :title, description = :description,
                        category = :category, image = :image, sort = :sort WHERE id = :id";
                $params[':image'] = $image;
            }
            db()->prepare($sql)->execute($params);
            flash('Запись обновлена.');
        } else {
            $stmt = db()->prepare("INSERT INTO portfolio (title, description, category, image, sort)
                                   VALUES (:title, :description, :category, :image, :sort)");
            $stmt->execute([
                ':title' => $title, ':description' => $description,
                ':category' => $category, ':image' => $image, ':sort' => $sort,
            ]);
            flash('Запись добавлена.');
        }
    } catch (RuntimeException $ex) {
        flash($ex->getMessage(), 'error');
    }
    redirect('portfolio.php');
}

// ===== ДАННЫЕ =====
$items = db()->query("SELECT * FROM portfolio ORDER BY sort, id DESC")->fetchAll();
$editItem = null;
if (isset($_GET['edit'])) {
    $stmt = db()->prepare("SELECT * FROM portfolio WHERE id = :id");
    $stmt->execute([':id' => (int)$_GET['edit']]);
    $editItem = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Портфолио — MVA Labs Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="main">
        <header class="topbar">
            <h1>Портфолио</h1>
        </header>

        <?= flash_alert() ?>

        <div class="card">
            <h2><?= $editItem ? 'Редактировать' : 'Добавить работу' ?></h2>
            <form method="POST" action="portfolio.php" enctype="multipart/form-data" class="form-grid">
                <?= csrf_field() ?>
                <?php if ($editItem): ?>
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>">
                <?php else: ?>
                    <input type="hidden" name="action" value="add">
                <?php endif; ?>

                <label class="field">
                    <span class="field__label">Название *</span>
                    <input type="text" name="title" required value="<?= e($editItem['title'] ?? '') ?>">
                </label>

                <label class="field">
                    <span class="field__label">Категория</span>
                    <select name="category">
                        <?php foreach ($categories as $val => $label): ?>
                            <option value="<?= e($val) ?>" <?= ($editItem['category'] ?? '') === $val ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label class="field field--full">
                    <span class="field__label">Описание</span>
                    <textarea name="description" rows="3"><?= e($editItem['description'] ?? '') ?></textarea>
                </label>

                <label class="field">
                    <span class="field__label">Изображение <?= $editItem ? '(оставьте пустым, чтобы не менять)' : '' ?></span>
                    <input type="file" name="image" accept="image/*">
                    <?php if (!empty($editItem['image'])): ?>
                        <img src="assets/uploads/<?= e($editItem['image']) ?>" alt="" class="thumb">
                    <?php endif; ?>
                </label>

                <label class="field">
                    <span class="field__label">Порядок (0 — первый)</span>
                    <input type="number" name="sort" value="<?= (int)($editItem['sort'] ?? 0) ?>">
                </label>

                <div class="field--full">
                    <button type="submit" class="btn btn--primary"><?= $editItem ? 'Сохранить' : 'Добавить' ?></button>
                    <?php if ($editItem): ?>
                        <a href="portfolio.php" class="btn btn--ghost">Отмена</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <section class="card">
            <h2>Список работ (<?= count($items) ?>)</h2>
            <?php if (!$items): ?>
                <p class="muted">Пока нет записей.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Изображение</th>
                            <th>Название</th>
                            <th>Категория</th>
                            <th>Порядок</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <?php if ($item['image']): ?>
                                        <img src="assets/uploads/<?= e($item['image']) ?>" alt="" class="thumb thumb--sm">
                                    <?php else: ?>
                                        <span class="muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= e($item['title']) ?></td>
                                <td><?= e($categories[$item['category']] ?? $item['category']) ?></td>
                                <td><?= (int)$item['sort'] ?></td>
                                <td class="table__actions">
                                    <a href="portfolio.php?edit=<?= (int)$item['id'] ?>" class="btn btn--small">✏️</a>
                                    <form method="POST" action="portfolio.php" onsubmit="return confirm('Удалить запись?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                        <button type="submit" class="btn btn--small btn--danger">🗑</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>