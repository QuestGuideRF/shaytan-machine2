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
$studentId = intval($_POST['student_id'] ?? 0);
$number = intval($_POST['number'] ?? 0);
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$priority = intval($_POST['priority'] ?? 1);
if (!$studentId || !$number || empty($firstName) || empty($lastName)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit;
}
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT class_id FROM students WHERE id = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();
    if (!$student) {
        echo json_encode(['success' => false, 'message' => 'Ученик не найден']);
        exit;
    }
    requireClassAccess($student['class_id']);
    $stmt = $db->prepare("SELECT id FROM students WHERE class_id = ? AND number = ? AND id != ?");
    $stmt->execute([$student['class_id'], $number, $studentId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Ученик с таким номером уже существует']);
        exit;
    }
    $stmt = $db->prepare("UPDATE students SET number = ?, first_name = ?, last_name = ?, priority = ? WHERE id = ?");
    $stmt->execute([$number, $firstName, $lastName, $priority, $studentId]);
    echo json_encode(['success' => true, 'message' => 'Ученик обновлен']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}