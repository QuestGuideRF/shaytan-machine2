<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u3343221_shaytan-machine');
define('DB_USER', 'u3343221_shaytan-machine');
define('DB_PASS', 'u3343221_shaytan-machine');
define('SESSION_LIFETIME', 31536000);
define('SITE_URL', 'https://kairomaestro.online');
define('ADMIN_USERNAME', 'Slavik10');
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('CSS_PATH', '/css');
define('JS_PATH', '/js');
define('SPECIAL_MODE_DURATIONS', [15, 30, 45]);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_set_cookie_params(SESSION_LIFETIME);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Europe/Moscow');