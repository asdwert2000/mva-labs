<?php
// ============================================
// КЕШИРОВАНИЕ ГЛАВНОЙ СТРАНИЦЫ
// ============================================

define('CACHE_DIR', __DIR__ . '/cache');
define('CACHE_TTL', 300); // 5 минут

// Если кеш свежий — отдаём готовый HTML и завершаем работу
function page_cache_start(string $key = 'home'): void {
    $file = CACHE_DIR . '/' . $key . '.html';
    if (is_file($file) && (time() - filemtime($file)) < CACHE_TTL) {
        readfile($file);
        exit;
    }
    ob_start();
}

// Сохраняем отрендеренную страницу в кеш
function page_cache_end(string $key = 'home'): void {
    $html = ob_get_clean();
    if (!is_dir(CACHE_DIR)) {
        @mkdir(CACHE_DIR, 0755, true);
    }
    @file_put_contents(CACHE_DIR . '/' . $key . '.html', $html, LOCK_EX);
    echo $html;
}

// Сброс кеша после правок контента
function clear_page_cache(): void {
    if (!is_dir(CACHE_DIR)) {
        return;
    }
    foreach (glob(CACHE_DIR . '/*.html') as $file) {
        @unlink($file);
    }
}
