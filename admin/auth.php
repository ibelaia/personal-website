<?php
if (session_status() === PHP_SESSION_NONE) {
    // Pengaturan Cookie Session yang aman
    session_set_cookie_params([
        'lifetime' => 86400 * 7, // 7 hari default
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Cek auto-login lewat Remember Me Cookie jika session kosong
if (empty($_SESSION['admin_logged_in']) && !empty($_COOKIE['admin_remember_token'])) {
    $token = $_COOKIE['admin_remember_token'];
    $expected_hash = hash('sha256', 'ibezlaia_admin_secure_secret_key_2026');
    
    if (hash_equals($expected_hash, $token)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = 'ibezlaia@gmail.com';
        $_SESSION['admin_role'] = 'Owner / Admin';
    }
}

// Fungsi proteksi: panggil di setiap file admin yang wajib login
function require_admin_login() {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }
}