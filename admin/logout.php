<?php
session_start();

// Hapus session
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Hapus cookie Remember Me
if (isset($_COOKIE['admin_remember_token'])) {
    setcookie('admin_remember_token', '', time() - 3600, '/');
}

header('Location: login.php');
exit;