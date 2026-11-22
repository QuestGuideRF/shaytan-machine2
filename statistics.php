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
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$query = "
    SELECT
        s.id,
        s.number,
        s.first_name,
        s.last_name,
        COUNT(lss.id) as total_selections,
        MAX(lss.selected_at) as last_selection
    FROM students s
    LEFT JOIN lesson_selected_students lss ON s.id = lss.student_id
    LEFT JOIN lessons l ON lss.lesson_id = l.id
    WHERE s.class_id = ?
";
$params = [$classId];
if ($dateFrom) {
    $query .= " AND l.date >= ?";
    $params[] = $dateFrom;
}
if ($dateTo) {
    $query .= " AND l.date <= ?";
    $params[] = $dateTo . ' 23:59:59';
}
$query .= " GROUP BY s.id ORDER BY total_selections DESC, s.number ASC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$statistics = $stmt->fetchAll();
$historyQuery = "
    SELECT
        s.first_name,
        s.last_name,
        s.number,
        lss.selected_at,
        l.date as lesson_date
    FROM lesson_selected_students lss
    JOIN students s ON lss.student_id = s.id
    JOIN lessons l ON lss.lesson_id = l.id
    WHERE s.class_id = ?
";
$historyParams = [$classId];
if ($dateFrom) {
    $historyQuery .= " AND l.date >= ?";
    $historyParams[] = $dateFrom;
}
if ($dateTo) {
    $historyQuery .= " AND l.date <= ?";
    $historyParams[] = $dateTo . ' 23:59:59';
}
$historyQuery .= " ORDER BY lss.selected_at DESC";
$historyStmt = $db->prepare($historyQuery);
$historyStmt->execute($historyParams);
$history = $historyStmt->fetchAll();
include __DIR__ . '/templates/statistics.php';