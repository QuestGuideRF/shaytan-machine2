<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    exit;
}
$name = trim($_POST['name'] ?? '');
$color = trim($_POST['color'] ?? '#2980b9');
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Введите название класса']);
    exit;
}
if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
    $color = '#2980b9';
}
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM classes WHERE teacher_id = ? AND name = ?");
    $stmt->execute([$_SESSION['user_id'], $name]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Класс с таким названием уже существует']);
        exit;
    }
    $stmt = $db->prepare("INSERT INTO classes (name, teacher_id, color) VALUES (?, ?, ?)");
    $stmt->execute([$name, $_SESSION['user_id'], $color]);
    $classId = $db->lastInsertId();
    echo json_encode([
        'success' => true,
        'message' => 'Класс создан успешно',
        'class_id' => $classId
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}