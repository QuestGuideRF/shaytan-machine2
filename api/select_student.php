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
$classId = intval($_POST['class_id'] ?? 0);
if (!$lessonId || !$classId) {
    echo json_encode(['success' => false, 'message' => 'Неверные параметры']);
    exit;
}
requireClassAccess($classId);
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT student_id FROM lesson_absent_students WHERE lesson_id = ?");
    $stmt->execute([$lessonId]);
    $absentIds = array_column($stmt->fetchAll(), 'student_id');
    $stmt = $db->prepare("SELECT student_id FROM lesson_selected_students WHERE lesson_id = ?");
    $stmt->execute([$lessonId]);
    $selectedIds = array_column($stmt->fetchAll(), 'student_id');
    $stmt = $db->prepare("
        SELECT student_id FROM special_mode_settings
        WHERE class_id = ? AND is_active = TRUE
        AND (expires_at IS NULL OR expires_at > NOW())
    ");
    $stmt->execute([$classId]);
    $specialModeIds = array_column($stmt->fetchAll(), 'student_id');
    $stmt = $db->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY number ASC");
    $stmt->execute([$classId]);
    $allStudents = $stmt->fetchAll();
    $availableStudents = array_filter($allStudents, function($student) use ($absentIds, $selectedIds) {
        return !in_array($student['id'], $absentIds) && !in_array($student['id'], $selectedIds);
    });
    $normalStudents = [];
    $specialStudents = [];
    foreach ($availableStudents as $student) {
        if (in_array($student['id'], $specialModeIds)) {
            $specialStudents[] = $student;
        } else {
            $normalStudents[] = $student;
        }
    }
    $candidates = !empty($normalStudents) ? $normalStudents : $specialStudents;
    if (empty($candidates)) {
        $totalStudents = count($allStudents);
        $totalSelected = count($selectedIds);
        if ($totalSelected >= $totalStudents) {
            echo json_encode([
                'success' => false,
                'message' => 'Все ученики ответили',
                'all_answered' => true
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Нет доступных учеников',
                'all_answered' => false
            ]);
        }
        exit;
    }
    $stmt = $db->prepare("SELECT smart_selection FROM classes WHERE id = ?");
    $stmt->execute([$classId]);
    $class = $stmt->fetch();
    if ($class && $class['smart_selection']) {
        $weights = [];
        foreach ($candidates as $student) {
            $stmt = $db->prepare("
                SELECT COUNT(*) as count, MAX(lss.selected_at) as last_selection
                FROM lesson_selected_students lss
                JOIN lessons l ON lss.lesson_id = l.id
                WHERE lss.student_id = ? AND l.class_id = ?
            ");
            $stmt->execute([$student['id'], $classId]);
            $stats = $stmt->fetch();
            $count = intval($stats['count']);
            $lastSelection = $stats['last_selection'];
            $weight = intval($student['priority'] ?? 1);
            if ($lastSelection) {
                $daysSince = (time() - strtotime($lastSelection)) / 86400;
                $weight += $daysSince * 2;
            } else {
                $weight += 100;
            }
            $weight -= $count * 0.5;
            $weights[$student['id']] = max(1, $weight);
        }
        $totalWeight = array_sum($weights);
        $random = mt_rand(1, $totalWeight);
        $currentWeight = 0;
        foreach ($candidates as $student) {
            $currentWeight += $weights[$student['id']];
            if ($random <= $currentWeight) {
                $selectedStudent = $student;
                break;
            }
        }
    } else {
        $selectedStudent = $candidates[array_rand($candidates)];
    }
    $stmt = $db->prepare("INSERT INTO lesson_selected_students (lesson_id, student_id) VALUES (?, ?)");
    $stmt->execute([$lessonId, $selectedStudent['id']]);
    echo json_encode([
        'success' => true,
        'student' => [
            'id' => $selectedStudent['id'],
            'number' => $selectedStudent['number'],
            'first_name' => $selectedStudent['first_name'],
            'last_name' => $selectedStudent['last_name']
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}