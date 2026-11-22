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
$stmt = $db->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY number ASC");
$stmt->execute([$classId]);
$students = $stmt->fetchAll();
include __DIR__ . '/templates/manage_students.php';