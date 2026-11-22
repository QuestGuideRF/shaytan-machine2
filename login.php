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
if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Введите имя пользователя']);
    exit;
}
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Пользователь с таким именем не найден']);
        exit;
    }
    $password = $_POST['password'] ?? '';
    if (!empty($password)) {
        if (!password_verify($password, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Неверный пароль']);
            exit;
        }
    }
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = $user['is_admin'];
    echo json_encode([
        'success' => true,
        'message' => 'Вход выполнен успешно',
        'is_admin' => $user['is_admin']
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}