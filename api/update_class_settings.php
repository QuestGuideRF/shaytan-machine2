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
$color = trim($_POST['color'] ?? '');
$smartSelection = isset($_POST['smart_selection']) ? (bool)$_POST['smart_selection'] : false;
if (!$classId) {
    echo json_encode(['success' => false, 'message' => 'Неверные параметры']);
    exit;
}
requireClassAccess($classId);
if (!empty($color) && !preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
    echo json_encode(['success' => false, 'message' => 'Неверный формат цвета']);
    exit;
}
try {
    $db = getDB();
    $updates = [];
    $params = [];
    if (!empty($color)) {
        $updates[] = "color = ?";
        $params[] = $color;
    }
    $updates[] = "smart_selection = ?";
    $params[] = $smartSelection ? 1 : 0;
    $params[] = $classId;
    $query = "UPDATE classes SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    echo json_encode(['success' => true, 'message' => 'Настройки обновлены']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}