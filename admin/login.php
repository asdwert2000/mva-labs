<?php
require_once 'config.php';
require_once 'includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($pdo, $username, $password)) {
        header('Location: /admin/index.php');
        exit;
    } else {
        $error = 'Неверное имя пользователя или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель MVA Labs</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0A0618;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-box {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 48px 40px;
            width: 400px;
            max-width: 95%;
        }
        .login-box h1 {
            color: white;
            font-size: 2rem;
            margin-bottom: 8px;
        }
        .login-box p {
            color: #6B7280;
            margin-bottom: 32px;
        }
        .login-box .highlight { color: #A855F7; }
        .login-box input {
            width: 100%;
            padding: 14px 18px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.04);
            color: white;
            font-size: 1rem;
            margin-bottom: 16px;
            font-family: inherit;
        }
        .login-box input:focus {
            outline: none;
            border-color: #A855F7;
        }
        .login-box button {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #A855F7, #7C3AED);
            color: white;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        .login-box button:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(168,85,247,0.3); }
        .error { color: #F87171; margin-bottom: 16px; font-size: 0.9rem; }
        .logo { text-align: center; margin-bottom: 24px; }
        .logo__multiply { color: #A855F7; font-size: 2rem; font-weight: 900; }
        .logo__text { color: white; font-size: 1.6rem; font-weight: 900; }
        .logo__labs { color: #6B7280; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <span class="logo__multiply">×</span>
            <span class="logo__text">MVA <span class="logo__labs">Labs</span></span>
        </div>
        <h1>Вход в <span class="highlight">админку</span></h1>
        <p>Управляйте контентом вашего лендинга</p>
        
        <?php if ($error): ?>
            <div class="error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Имя пользователя" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
        <p style="margin-top: 16px; color: #4B5563; font-size: 0.8rem;">
            По умолчанию: admin / admin123
        </p>
    </div>
</body>
</html>