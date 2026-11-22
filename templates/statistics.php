<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика - <?= h($class['name']) ?></title>
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
                <span>Статистика</span>
            </div>
            <div class="header-actions">
                <button class="btn btn-icon" id="theme-toggle" title="Переключить тему">🌓</button>
                <a href="/lesson.php?class_id=<?= $classId ?>" class="btn btn-secondary">Назад</a>
            </div>
        </header>
        <div class="page-header">
            <h2>Статистика класса: <?= h($class['name']) ?></h2>
        </div>
        <div class="filters">
            <form method="GET" class="filter-form">
                <input type="hidden" name="class_id" value="<?= $classId ?>">
                <div class="form-group">
                    <label for="date-from">С</label>
                    <input type="date" id="date-from" name="date_from" value="<?= h($dateFrom) ?>">
                </div>
                <div class="form-group">
                    <label for="date-to">По</label>
                    <input type="date" id="date-to" name="date_to" value="<?= h($dateTo) ?>">
                </div>
                <button type="submit" class="btn btn-primary">Применить</button>
                <a href="/statistics.php?class_id=<?= $classId ?>" class="btn btn-secondary">Сбросить</a>
            </form>
        </div>
        <div class="statistics-grid">
            <div class="statistics-card">
                <h3>Общая статистика</h3>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>ФИО</th>
                            <th>Выходов</th>
                            <th>Последний выход</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statistics as $stat): ?>
                            <tr>
                                <td><?= $stat['number'] ?></td>
                                <td><?= h($stat['first_name'] . ' ' . $stat['last_name']) ?></td>
                                <td><strong><?= $stat['total_selections'] ?></strong></td>
                                <td><?= $stat['last_selection'] ? date('d.m.Y H:i', strtotime($stat['last_selection'])) : '—' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="statistics-card">
                <h3>История выходов</h3>
                <div class="history-list">
                    <?php if (empty($history)): ?>
                        <p class="empty-text">Нет данных</p>
                    <?php else: ?>
                        <?php foreach ($history as $item): ?>
                            <div class="history-item">
                                <div class="history-student">
                                    <strong><?= h($item['first_name'] . ' ' . $item['last_name']) ?></strong>
                                    <span class="history-number">№<?= $item['number'] ?></span>
                                </div>
                                <div class="history-date">
                                    <?= date('d.m.Y H:i', strtotime($item['selected_at'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/main.js"></script>
</body>
</html>