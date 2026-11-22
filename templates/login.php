<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Шайтан-машина - Вход</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h1>🎲 Шайтан-машина</h1>
            <p class="subtitle">Система случайного выбора учеников</p>
            <div class="tabs">
                <button class="tab-btn active" data-tab="login">Вход</button>
                <button class="tab-btn" data-tab="register">Регистрация</button>
            </div>
            <div class="tab-content active" id="login-tab">
                <form id="login-form" class="auth-form" novalidate>
                    <div class="form-group">
                        <label for="login-username">Имя пользователя</label>
                        <input type="text" id="login-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Пароль (необязательно)</label>
                        <input type="password" id="login-password" name="password">
                        <small>Если пароль не был установлен при регистрации, оставьте поле пустым</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Войти</button>
                </form>
            </div>
            <div class="tab-content" id="register-tab">
                <form id="register-form" class="auth-form" novalidate>
                    <div class="form-group">
                        <label for="register-username">Имя пользователя</label>
                        <input type="text" id="register-username" name="username" required minlength="3" maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="register-password">Пароль (необязательно)</label>
                        <input type="password" id="register-password" name="password">
                        <small>Если не укажете пароль, для входа будет достаточно имени пользователя</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                </form>
            </div>
            <div id="message" class="message"></div>
        </div>
    </div>
    <script src="/js/main.js"></script>
</body>
</html>