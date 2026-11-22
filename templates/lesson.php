<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Урок - <?= h($class['name']) ?></title>
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
                <a href="/index.php">Главная</a> / <span><?= h($class['name']) ?></span>
            </div>
            <div class="header-actions">
                <button class="btn btn-icon" id="theme-toggle" title="Переключить тему">🌓</button>
                <a href="/index.php" class="btn btn-secondary">Назад</a>
            </div>
        </header>
        <div class="lesson-container">
            <div class="lesson-main">
                <div class="lesson-header">
                    <h2 style="color: <?= h($class['color']) ?>"><?= h($class['name']) ?></h2>
                    <button class="new-lesson-button" id="new-lesson-btn">
                        <span class="btn-icon-new">🔄</span>
                        <span class="btn-text-new">Новый урок</span>
                    </button>
                </div>
                <div class="students-list" id="students-list">
                    <?php foreach ($students as $student): ?>
                        <div class="student-item <?= in_array($student['id'], $absentIds) ? 'absent' : '' ?> <?= ($isAdmin && in_array($student['id'], $specialModeIds)) ? 'special-mode' : '' ?>"
                             data-student-id="<?= $student['id'] ?>">
                            <span class="student-number"><?= $student['number'] ?></span>
                            <span class="student-name"><?= h($student['first_name'] . ' ' . $student['last_name']) ?></span>
                            <?php if ($isAdmin && in_array($student['id'], $specialModeIds)): ?>
                                <span class="badge special-badge">⭐</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="action-buttons">
                    <button class="random-button" id="select-student-btn">
                        🎲 Выбрать случайного ученика
                    </button>
                </div>
                <div class="selected-student-container" id="selected-student-container" style="display: none;">
                    <div class="selected-student" id="selected-student">
                        <div class="selected-number" id="selected-number"></div>
                        <div class="selected-name" id="selected-name"></div>
                    </div>
                </div>
            </div>
            <div class="lesson-sidebar">
                <h3>Отсутствующие</h3>
                <div class="absent-list" id="absent-list">
                    <?php if (empty($absentIds)): ?>
                        <p class="empty-text">Нет отсутствующих</p>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <?php if (in_array($student['id'], $absentIds)): ?>
                                <div class="absent-item loaded" data-student-id="<?= $student['id'] ?>">
                                    <?= h($student['first_name'] . ' ' . $student['last_name']) ?>
                                    <button class="btn-remove-absent" data-student-id="<?= $student['id'] ?>">×</button>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <h3>Выбранные в этом уроке</h3>
                <div class="selected-list" id="selected-list">
                    <?php if (empty($selectedIds)): ?>
                        <p class="empty-text">Пока никого не выбрали</p>
                    <?php else: ?>
                        <?php foreach ($students as $student): ?>
                            <?php if (in_array($student['id'], $selectedIds)): ?>
                                <div class="selected-item loaded" data-student-id="<?= $student['id'] ?>">
                                    <?= h($student['first_name'] . ' ' . $student['last_name']) ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        const lessonId = <?= $lessonId ?>;
        const classId = <?= $classId ?>;
    </script>
    <script src="/js/main.js"></script>
    <script src="/js/lesson.js"></script>
</body>
</html>