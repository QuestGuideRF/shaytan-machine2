<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление учениками - <?= h($class['name']) ?></title>
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
                <a href="/index.php">Главная</a> /
                <a href="/lesson.php?class_id=<?= $classId ?>"><?= h($class['name']) ?></a> /
                <span>Ученики</span>
            </div>
            <div class="header-actions">
                <button class="btn btn-icon" id="theme-toggle" title="Переключить тему">🌓</button>
                <a href="/lesson.php?class_id=<?= $classId ?>" class="btn btn-secondary">Назад</a>
            </div>
        </header>
        <div class="page-header">
            <h2>Управление учениками: <?= h($class['name']) ?></h2>
            <div class="header-buttons">
                <button class="btn btn-primary" id="add-student-btn">+ Добавить ученика</button>
                <button class="btn btn-secondary" id="import-btn">Импорт</button>
                <a href="/api/export_students.php?class_id=<?= $classId ?>" class="btn btn-secondary" download>Экспорт JSON</a>
            </div>
        </div>
        <div class="students-table-container">
            <table class="students-table">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>Имя</th>
                        <th>Фамилия</th>
                        <th>Приоритет</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody id="students-table-body">
                    <?php foreach ($students as $student): ?>
                        <tr data-student-id="<?= $student['id'] ?>">
                            <td><?= $student['number'] ?></td>
                            <td><?= h($student['first_name']) ?></td>
                            <td><?= h($student['last_name']) ?></td>
                            <td><?= $student['priority'] ?></td>
                            <td>
                                <button class="btn-icon edit-student-btn" data-student-id="<?= $student['id'] ?>" title="Редактировать">✏️</button>
                                <button class="btn-icon delete-student-btn" data-student-id="<?= $student['id'] ?>" title="Удалить">🗑️</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="student-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modal-title">Добавить ученика</h2>
            <form id="student-form">
                <input type="hidden" id="student-id" name="student_id">
                <input type="hidden" name="class_id" value="<?= $classId ?>">
                <div class="form-group">
                    <label for="student-number">Номер в списке</label>
                    <input type="number" id="student-number" name="number" required min="1">
                </div>
                <div class="form-group">
                    <label for="student-first-name">Имя</label>
                    <input type="text" id="student-first-name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="student-last-name">Фамилия</label>
                    <input type="text" id="student-last-name" name="last_name" required>
                </div>
                <div class="form-group">
                    <label for="student-priority">Приоритет (для умного выбора)</label>
                    <input type="number" id="student-priority" name="priority" value="1" min="1" max="10">
                    <small>Чем выше число, тем больше шанс быть выбранным</small>
                </div>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
    </div>
    <div id="import-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Импорт учеников</h2>
            <form id="import-form">
                <input type="hidden" name="class_id" value="<?= $classId ?>">
                <div class="form-group">
                    <label for="import-data">Данные (JSON или по одному на строку: номер, имя, фамилия)</label>
                    <textarea id="import-data" name="students" rows="10" placeholder='[{"number": 1, "first_name": "Иван", "last_name": "Иванов"}, ...] или&#10;1, Иван, Иванов&#10;2, Петр, Петров'></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Импортировать</button>
            </form>
        </div>
    </div>
    <div id="message" class="message"></div>
    <script>
        const classId = <?= $classId ?>;
    </script>
    <script src="/js/main.js"></script>
    <script src="/js/manage_students.js"></script>
</body>
</html>