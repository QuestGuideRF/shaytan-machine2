<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Шайтан-машина - Мои классы</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>🎲 Шайтан-машина</h1>
            <div class="header-actions">
                <button class="btn btn-icon" id="theme-toggle" title="Переключить тему">🌓</button>
                <a href="/logout.php" class="btn btn-secondary">Выйти</a>
            </div>
        </header>
        <?php if (isset($currentSchedule) && $currentSchedule): ?>
        <div class="schedule-notification">
            <p>📅 По расписанию у вас сейчас: <strong><?= h($currentSchedule['name']) ?></strong>
            (<?= date('H:i', strtotime($currentSchedule['start_time'])) ?> - <?= date('H:i', strtotime($currentSchedule['end_time'])) ?>)</p>
            <a href="/lesson.php?class_id=<?= $currentSchedule['id'] ?>" class="btn btn-primary btn-sm">Переключиться</a>
        </div>
        <?php endif; ?>
        <div class="page-header">
            <h2>Мои классы</h2>
            <button class="btn btn-primary" id="create-class-btn">+ Создать класс</button>
        </div>
        <div class="classes-grid" id="classes-grid">
            <?php if (empty($classes)): ?>
                <div class="empty-state">
                    <p>У вас пока нет классов</p>
                    <p>Создайте первый класс, чтобы начать работу</p>
                </div>
            <?php else: ?>
                <?php foreach ($classes as $class): ?>
                    <div class="class-card" style="border-left-color: <?= h($class['color']) ?>">
                        <div class="class-header">
                            <h3><?= h($class['name']) ?></h3>
                            <div class="class-actions">
                                <a href="/manage_students.php?class_id=<?= $class['id'] ?>" class="btn-icon" title="Управление учениками">👥</a>
                                <a href="/statistics.php?class_id=<?= $class['id'] ?>" class="btn-icon" title="Статистика">📊</a>
                                <button class="btn-icon class-settings-btn" data-class-id="<?= $class['id'] ?>" title="Настройки">⚙️</button>
                            </div>
                        </div>
                        <a href="/lesson.php?class_id=<?= $class['id'] ?>" class="class-link">
                            <div class="class-info">
                                <span>Начать урок</span>
                                <span class="arrow">→</span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if (isAdmin()): ?>
        <div class="admin-section">
            <a href="/admin.php" class="btn btn-admin">🔐 Админ-панель</a>
        </div>
        <?php endif; ?>
    </div>
    <div id="create-class-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Создать класс</h2>
            <form id="create-class-form">
                <div class="form-group">
                    <label for="class-name">Название класса</label>
                    <input type="text" id="class-name" name="name" required placeholder="Например: 11А">
                </div>
                <div class="form-group">
                    <label for="class-color">Цвет класса</label>
                    <input type="color" id="class-color" name="color" value="#2980b9">
                </div>
                <button type="submit" class="btn btn-primary">Создать</button>
            </form>
        </div>
    </div>
    <div id="class-settings-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Настройки класса</h2>
            <form id="class-settings-form">
                <input type="hidden" id="settings-class-id" name="class_id">
                <div class="form-group">
                    <label for="settings-class-color">Цвет класса</label>
                    <input type="color" id="settings-class-color" name="color">
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="settings-smart-selection" name="smart_selection">
                        <span>Умный выбор (взвешенная вероятность)</span>
                    </label>
                    <small>Ученики, которые давно не выходили, имеют больший шанс</small>
                </div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
    </div>
    <div id="message" class="message"></div>
    <script src="/js/main.js"></script>
</body>
</html>