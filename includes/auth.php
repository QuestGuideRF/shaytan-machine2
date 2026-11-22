<?php
require_once __DIR__ . '/db.php';
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, is_admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}
function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['is_admin'] == 1;
}
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: /index.php');
        exit;
    }
}
function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        header('Location: /index.php');
        exit;
    }
}
function hasAccessToClass($classId) {
    if (!isLoggedIn()) {
        return false;
    }
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$classId, $_SESSION['user_id']]);
    return $stmt->fetch() !== false;
}
function requireClassAccess($classId) {
    requireAuth();
    if (!hasAccessToClass($classId)) {
        header('Location: /index.php');
        exit;
    }
}
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}