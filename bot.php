<?php
// ============================================
// TELEGRAM БОТ ДЛЯ MVA LABS
// Автоматический приём заявок с лендинга
// ============================================

// ===== НАСТРОЙКИ (ЗАМЕНИТЕ НА СВОИ) =====
define('BOT_TOKEN', '8977608669:AAFpm5WxuRmAvsBVIIaGzfz0GsjE6DtkU7o'); // ВАШ ТОКЕН ОТ BOTFATHER
define('CHAT_ID', '351341132'); // ВАШ ID В TELEGRAM (узнать у @userinfobot)

// ===== ФУНКЦИЯ ОТПРАВКИ В TELEGRAM =====
function sendToTelegram($message) {
    $url = "https://api.telegram.org/bot" . BOT_TOKEN . "/sendMessage";
    $data = [
        'chat_id' => CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}

// ===== ОБРАБОТКА ЗАЯВКИ =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Получаем данные из формы
    $name = htmlspecialchars($_POST['name'] ?? 'Не указано');
    $phone = htmlspecialchars($_POST['phone'] ?? 'Не указано');
    $email = htmlspecialchars($_POST['email'] ?? 'Не указано');
    $package = htmlspecialchars($_POST['package'] ?? 'Не выбран');
    
    // Дополнительные поля (если есть)
    $message = htmlspecialchars($_POST['message'] ?? '');
    $site = htmlspecialchars($_POST['site'] ?? '');
    
    // ===== ФОРМИРУЕМ КРАСИВОЕ СООБЩЕНИЕ =====
    $telegramMessage = "🚀 <b>НОВАЯ ЗАЯВКА В MVA LABS</b> 🚀\n\n";
    $telegramMessage .= "━━━━━━━━━━━━━━━━━━━━\n";
    $telegramMessage .= "👤 <b>Имя:</b> {$name}\n";
    $telegramMessage .= "📞 <b>Телефон:</b> {$phone}\n";
    $telegramMessage .= "📧 <b>Email:</b> {$email}\n";
    $telegramMessage .= "📦 <b>Пакет:</b> {$package}\n";
    
    if (!empty($site)) {
        $telegramMessage .= "🔗 <b>Сайт:</b> {$site}\n";
    }
    
    if (!empty($message)) {
        $telegramMessage .= "💬 <b>Сообщение:</b> {$message}\n";
    }
    
    $telegramMessage .= "━━━━━━━━━━━━━━━━━━━━\n";
    $telegramMessage .= "🕐 <b>Время:</b> " . date('d.m.Y H:i:s') . "\n";
    $telegramMessage .= "🌐 <b>IP:</b> " . $_SERVER['REMOTE_ADDR'] . "\n";
    $telegramMessage .= "━━━━━━━━━━━━━━━━━━━━\n";
    $telegramMessage .= "📌 <i>Свяжитесь с клиентом в ближайшее время!</i>";
    
    // Отправляем в Telegram
    sendToTelegram($telegramMessage);
    
    // ===== ОТВЕТ ДЛЯ ФРОНТЕНДА (JSON) =====
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Заявка успешно отправлена!'
    ]);
    exit;
}

// Если обратились не через POST
http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
?>