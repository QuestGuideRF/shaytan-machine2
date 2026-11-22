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
if (!$studentId) {
    echo json_encode(['success' => false, 'message' => 'Неверные параметры']);
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
    $stmt = $db->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$studentId]);
    echo json_encode(['success' => true, 'message' => 'Ученик удален']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}