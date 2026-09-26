<?php

$protocol = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || ($_SERVER['SERVER_PORT'] ?? '') == 443
    || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
) ? 'https' : 'http';

$host = $_SERVER['HTTP_HOST'];

define('BASE_URL', $protocol . '://' . $host);

// Portofolio Next.js di lokal selalu berjalan di localhost:3000 (termasuk saat kamu tes pakai ngrok)
define('PORTFOLIO_URL', 'http://localhost:3000');