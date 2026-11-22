<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    exit;
}
$classId = intval($_POST['class_id'] ?? 0);
$studentIds = $_POST['student_ids'] ?? [];
$duration = intval($_POST['duration'] ?? 0);
if (!$classId || empty($studentIds) || !is_array($studentIds)) {
    echo json_encode(['success' => false, 'message' => 'Неверные параметры']);
    exit;
}
if (!in_array($duration, SPECIAL_MODE_DURATIONS)) {
    echo json_encode(['success' => false, 'message' => 'Неверная длительность']);
    exit;
}
try {
    $db = getDB();
    $db->beginTransaction();
    $stmt = $db->prepare("UPDATE special_mode_settings SET is_active = FALSE WHERE class_id = ?");
    $stmt->execute([$classId]);
    $expiresAt = date('Y-m-d H:i:s', time() + ($duration * 60));
    $stmt = $db->prepare("
        INSERT INTO special_mode_settings (class_id, student_id, is_active, expires_at, created_by)
        VALUES (?, ?, TRUE, ?, ?)
        ON DUPLICATE KEY UPDATE is_active = TRUE, expires_at = ?, created_by = ?
    ");
    foreach ($studentIds as $studentId) {
        $studentId = intval($studentId);
        if ($studentId) {
            $stmt->execute([$classId, $studentId, $expiresAt, $_SESSION['user_id'], $expiresAt, $_SESSION['user_id']]);
        }
    }
    $db->commit();
    echo json_encode([
        'success' => true,
        'message' => "Режим 'свои' активирован на $duration минут",
        'expires_at' => $expiresAt
    ]);
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}