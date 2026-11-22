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
$studentsJson = $_POST['students'] ?? '';
if (!$classId || empty($studentsJson)) {
    echo json_encode(['success' => false, 'message' => 'Неверные параметры']);
    exit;
}
requireClassAccess($classId);
try {
    $students = json_decode($studentsJson, true);
    if (!is_array($students)) {
        echo json_encode(['success' => false, 'message' => 'Неверный формат данных']);
        exit;
    }
    $db = getDB();
    $db->beginTransaction();
    $added = 0;
    $errors = [];
    foreach ($students as $index => $student) {
        $number = intval($student['number'] ?? 0);
        $firstName = trim($student['first_name'] ?? '');
        $lastName = trim($student['last_name'] ?? '');
        if (!$number || empty($firstName) || empty($lastName)) {
            $errors[] = "Строка " . ($index + 1) . ": не заполнены все поля";
            continue;
        }
        $stmt = $db->prepare("SELECT id FROM students WHERE class_id = ? AND number = ?");
        $stmt->execute([$classId, $number]);
        if ($stmt->fetch()) {
            $errors[] = "Строка " . ($index + 1) . ": ученик с номером $number уже существует";
            continue;
        }
        $stmt = $db->prepare("INSERT INTO students (class_id, number, first_name, last_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$classId, $number, $firstName, $lastName]);
        $added++;
    }
    $db->commit();
    echo json_encode([
        'success' => true,
        'added' => $added,
        'errors' => $errors,
        'message' => "Добавлено учеников: $added"
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}