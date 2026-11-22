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
$lessonId = intval($_POST['lesson_id'] ?? 0);
$studentId = intval($_POST['student_id'] ?? 0);
$action = $_POST['action'] ?? 'add';
if (!$lessonId || !$studentId) {
    echo json_encode(['success' => false, 'message' => 'Неверные параметры']);
    exit;
}
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT class_id FROM lessons WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$lessonId, $_SESSION['user_id']]);
    $lesson = $stmt->fetch();
    if (!$lesson) {
        echo json_encode(['success' => false, 'message' => 'Урок не найден']);
        exit;
    }
    if ($action === 'add') {
        $stmt = $db->prepare("INSERT IGNORE INTO lesson_absent_students (lesson_id, student_id) VALUES (?, ?)");
        $stmt->execute([$lessonId, $studentId]);
    } else {
        $stmt = $db->prepare("DELETE FROM lesson_absent_students WHERE lesson_id = ? AND student_id = ?");
        $stmt->execute([$lessonId, $studentId]);
    }
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}