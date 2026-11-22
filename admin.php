<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
requireAdmin();
$db = getDB();
$stmt = $db->prepare("SELECT * FROM classes ORDER BY name ASC");
$stmt->execute();
$allClasses = $stmt->fetchAll();
$selectedClassId = intval($_GET['class_id'] ?? 0);
$students = [];
if ($selectedClassId) {
    $stmt = $db->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY number ASC");
    $stmt->execute([$selectedClassId]);
    $students = $stmt->fetchAll();
    $stmt = $db->prepare("
        SELECT student_id, expires_at FROM special_mode_settings
        WHERE class_id = ? AND is_active = TRUE
        AND (expires_at IS NULL OR expires_at > NOW())
    ");
    $stmt->execute([$selectedClassId]);
    $activeModes = [];
    while ($row = $stmt->fetch()) {
        $activeModes[$row['student_id']] = $row['expires_at'];
    }
}
include __DIR__ . '/templates/admin.php';