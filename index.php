<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM classes WHERE teacher_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $classes = $stmt->fetchAll();
    $phpDayOfWeek = date('w');
    $mysqlDayOfWeek = $phpDayOfWeek == 0 ? 7 : $phpDayOfWeek;
    $currentTime = date('H:i:s');
    $scheduleStmt = $db->prepare("
        SELECT c.id, c.name, c.color, s.start_time, s.end_time, s.subject
        FROM schedule s
        JOIN classes c ON s.class_id = c.id
        WHERE s.teacher_id = ? AND s.day_of_week = ?
        AND ? BETWEEN s.start_time AND s.end_time
        ORDER BY s.start_time
    ");
    $scheduleStmt->execute([$_SESSION['user_id'], $mysqlDayOfWeek, $currentTime]);
    $currentSchedule = $scheduleStmt->fetch();
    include __DIR__ . '/templates/classes.php';
} else {
    include __DIR__ . '/templates/login.php';
}