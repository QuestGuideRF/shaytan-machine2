<?php
echo "<h1>Проверка установки Шайтан-машины</h1>";
echo "<h2>1. Проверка PHP</h2>";
echo "Версия PHP: " . PHP_VERSION . "<br>";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "✓ Версия PHP подходит<br>";
} else {
    echo "✗ Требуется PHP 7.4 или выше<br>";
}
echo "<h2>2. Проверка расширений PHP</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'session', 'json'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✓ Расширение $ext установлено<br>";
    } else {
        echo "✗ Расширение $ext не установлено<br>";
    }
}
echo "<h2>3. Проверка подключения к базе данных</h2>";
require_once __DIR__ . '/includes/config.php';
try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "✓ Подключение к базе данных успешно<br>";
    echo "<h2>4. Проверка таблиц</h2>";
    $tables = ['users', 'classes', 'students', 'lessons', 'lesson_absent_students', 'lesson_selected_students', 'special_mode_settings', 'schedule'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Таблица $table существует<br>";
        } else {
            echo "✗ Таблица $table не найдена. Выполните sql/schema.sql<br>";
        }
    }
} catch (PDOException $e) {
    echo "✗ Ошибка подключения: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "Проверьте настройки в includes/config.php<br>";
}
echo "<h2>5. Проверка прав доступа</h2>";
$writable_dirs = [];
if (is_writable(__DIR__)) {
    echo "✓ Корневая директория доступна для записи<br>";
} else {
    echo "✗ Корневая директория недоступна для записи<br>";
}
echo "<h2>6. Проверка файлов</h2>";
$required_files = [
    'index.php',
    'includes/config.php',
    'includes/db.php',
    'includes/auth.php',
    'css/style.css',
    'js/main.js'
];
foreach ($required_files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✓ Файл $file найден<br>";
    } else {
        echo "✗ Файл $file не найден<br>";
    }
}
echo "<hr>";
echo "<p><strong>Если все проверки пройдены успешно, удалите этот файл (check.php) и перейдите на главную страницу.</strong></p>";
echo "<p><a href='index.php'>Перейти на главную страницу</a></p>";