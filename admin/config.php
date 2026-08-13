<?php
// ============================================
// КОНФИГУРАЦИЯ MVA LABS ADMIN
// ============================================

// Путь к SQLite-базе (файл создастся автоматически)
define('DB_PATH', __DIR__ . '/../data/admin.sqlite');

// Адрес сайта (без слэша в конце)
define('SITE_URL', 'http://103.249.134.60');

// Параметры сессии
define('SESSION_NAME', 'mva_admin_session');
define('SESSION_LIFETIME', 86400); // 24 часа

// Лимит загрузки изображений (байт) — 5 МБ
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

// Разрешённые типы файлов для загрузки
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif']);

// Каталог для загружаемых изображений
define('UPLOAD_DIR', __DIR__ . '/assets/uploads');
