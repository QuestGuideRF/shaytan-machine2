<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireAuth();
header('Content-Type: application/json');
$classId = intval($_GET['class_id'] ?? 0);
if (!$classId) {
    echo json_encode(['success' => false, 'message' => 'Неверные параметры']);
    exit;
}
requireClassAccess($classId);
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$classId]);
    $class = $stmt->fetch();
    if (!$class) {
        echo json_encode(['success' => false, 'message' => 'Класс не найден']);
        exit;
    }
    echo json_encode([
        'success' => true,
        'class' => $class
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}