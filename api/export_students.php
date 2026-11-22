<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
requireAuth();
$classId = intval($_GET['class_id'] ?? 0);
if (!$classId) {
    http_response_code(400);
    die('Неверные параметры');
}
requireClassAccess($classId);
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT name FROM classes WHERE id = ?");
    $stmt->execute([$classId]);
    $class = $stmt->fetch();
    if (!$class) {
        http_response_code(404);
        die('Класс не найден');
    }
    $stmt = $db->prepare("
        SELECT
            s.id,
            s.number,
            s.first_name,
            s.last_name,
            s.priority,
            COUNT(lss.id) as total_selections,
            MAX(lss.selected_at) as last_selection
        FROM students s
        LEFT JOIN lesson_selected_students lss ON s.id = lss.student_id
        LEFT JOIN lessons l ON lss.lesson_id = l.id AND l.class_id = ?
        WHERE s.class_id = ?
        GROUP BY s.id
        ORDER BY s.number ASC
    ");
    $stmt->execute([$classId, $classId]);
    $students = $stmt->fetchAll();
    $data = [
        'class_name' => $class['name'],
        'export_date' => date('Y-m-d H:i:s'),
        'students' => array_map(function($s) {
            return [
                'number' => intval($s['number']),
                'first_name' => $s['first_name'],
                'last_name' => $s['last_name'],
                'priority' => intval($s['priority']),
                'total_selections' => intval($s['total_selections']),
                'last_selection' => $s['last_selection']
            ];
        }, $students)
    ];
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $class['name'] . '_students.json"');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    die('Ошибка базы данных');
}