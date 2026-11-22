<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    exit;
}
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Введите имя пользователя']);
    exit;
}
if (strlen($username) < 3 || strlen($username) > 100) {
    echo json_encode(['success' => false, 'message' => 'Имя пользователя должно быть от 3 до 100 символов']);
    exit;
}
if (empty($password)) {
    $hashedPassword = password_hash('', PASSWORD_DEFAULT);
} else {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
}
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким именем уже существует']);
        exit;
    }
    $isAdmin = ($username === ADMIN_USERNAME) ? 1 : 0;
    $stmt = $db->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, ?)");
    $stmt->execute([$username, $hashedPassword, $isAdmin]);
    $_SESSION['user_id'] = $db->lastInsertId();
    $_SESSION['username'] = $username;
    $_SESSION['is_admin'] = $isAdmin;
    echo json_encode([
        'success' => true,
        'message' => 'Регистрация успешна',
        'is_admin' => $isAdmin
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}