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
if (!$classId) {
    echo json_encode(['success' => false, 'message' => 'Неверные параметры']);
    exit;
}
requireClassAccess($classId);
try {
    $db = getDB();
    $stmt = $db->prepare("UPDATE lessons SET status = 'completed' WHERE class_id = ? AND teacher_id = ? AND status = 'active'");
    $stmt->execute([$classId, $_SESSION['user_id']]);
    $stmt = $db->prepare("INSERT INTO lessons (class_id, teacher_id, status) VALUES (?, ?, 'active')");
    $stmt->execute([$classId, $_SESSION['user_id']]);
    $lessonId = $db->lastInsertId();
    echo json_encode([
        'success' => true,
        'lesson_id' => $lessonId,
        'message' => 'Новый урок создан'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}