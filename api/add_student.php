<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireAuth();
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    exit;
}
$classId = intval($_POST['class_id'] ?? 0);
$number = intval($_POST['number'] ?? 0);
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
if (!$classId || !$number || empty($firstName) || empty($lastName)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit;
}
requireClassAccess($classId);
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM students WHERE class_id = ? AND number = ?");
    $stmt->execute([$classId, $number]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ученик с таким номером уже существует']);
        exit;
    }
    $stmt = $db->prepare("INSERT INTO students (class_id, number, first_name, last_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$classId, $number, $firstName, $lastName]);
    $studentId = $db->lastInsertId();
    echo json_encode([
        'success' => true,
        'student_id' => $studentId,
        'message' => 'Ученик добавлен'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}