<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();
$classId = intval($_GET['class_id'] ?? 0);
if (!$classId) {
    header('Location: /index.php');
    exit;
}
requireClassAccess($classId);
$db = getDB();
$stmt = $db->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$classId]);
$class = $stmt->fetch();
if (!$class) {
    header('Location: /index.php');
    exit;
}
$stmt = $db->prepare("SELECT id FROM lessons WHERE class_id = ? AND teacher_id = ? AND status = 'active' ORDER BY date DESC LIMIT 1");
$stmt->execute([$classId, $_SESSION['user_id']]);
$lesson = $stmt->fetch();
if (!$lesson) {
    $stmt = $db->prepare("INSERT INTO lessons (class_id, teacher_id, status) VALUES (?, ?, 'active')");
    $stmt->execute([$classId, $_SESSION['user_id']]);
    $lessonId = $db->lastInsertId();
} else {
    $lessonId = $lesson['id'];
}
$stmt = $db->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY number ASC");
$stmt->execute([$classId]);
$students = $stmt->fetchAll();
$stmt = $db->prepare("SELECT student_id FROM lesson_absent_students WHERE lesson_id = ?");
$stmt->execute([$lessonId]);
$absentIds = array_column($stmt->fetchAll(), 'student_id');
$stmt = $db->prepare("SELECT student_id FROM lesson_selected_students WHERE lesson_id = ?");
$stmt->execute([$lessonId]);
$selectedIds = array_column($stmt->fetchAll(), 'student_id');
$isAdmin = isAdmin();
$specialModeIds = [];
if ($isAdmin) {
    $stmt = $db->prepare("
        SELECT student_id FROM special_mode_settings
        WHERE class_id = ? AND is_active = TRUE
        AND (expires_at IS NULL OR expires_at > NOW())
    ");
    $stmt->execute([$classId]);
    $specialModeIds = array_column($stmt->fetchAll(), 'student_id');
}
include __DIR__ . '/templates/lesson.php';