<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Режим подкрутки</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="breadcrumbs">
                <a href="/index.php">Главная</a> / <span>Админ-панель</span>
            </div>
            <div class="header-actions">
                <button class="btn btn-icon" id="theme-toggle" title="Переключить тему">🌓</button>
                <a href="/index.php" class="btn btn-secondary">Назад</a>
            </div>
        </header>
        <div class="page-header">
            <h2>🔐 Режим подкрутки "свои"</h2>
            <p class="subtitle">Выбранные ученики не будут выпадать при случайном выборе до последнего</p>
        </div>
        <div class="admin-container">
            <form id="special-mode-form">
                <div class="form-group">
                    <label for="admin-class-select">Выберите класс</label>
                    <select id="admin-class-select" name="class_id" required>
                        <option value="">-- Выберите класс --</option>
                        <?php foreach ($allClasses as $cls): ?>
                            <option value="<?= $cls['id'] ?>" <?= $selectedClassId == $cls['id'] ? 'selected' : '' ?>>
                                <?= h($cls['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if ($selectedClassId && !empty($students)): ?>
                    <div class="form-group">
                        <label>Выберите учеников</label>
                        <div class="students-checkbox-list">
                            <?php foreach ($students as $student): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="student_ids[]" value="<?= $student['id'] ?>"
                                           <?= isset($activeModes[$student['id']]) ? 'checked' : '' ?>>
                                    <span>
                                        <?= h($student['first_name'] . ' ' . $student['last_name']) ?>
                                        <?php if (isset($activeModes[$student['id']])): ?>
                                            <span class="badge active-badge">
                                                Активен до <?= date('H:i', strtotime($activeModes[$student['id']])) ?>
                                            </span>
                                        <?php endif; ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="duration">Время действия режима</label>
                        <select id="duration" name="duration" required>
                            <option value="15">15 минут</option>
                            <option value="30" selected>30 минут</option>
                            <option value="45">45 минут</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-large">Активировать режим "свои"</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div id="message" class="message"></div>
    <script>
        const classId = <?= $selectedClassId ?: 0 ?>;
    </script>
    <script src="/js/main.js"></script>
    <script src="/js/admin.js"></script>
</body>
</html>