<?php
require_once __DIR__ . '/auth.php';

// Jika sudah ada session/cookie aktif, langsung arahkan ke dashboard
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = !empty($_POST['remember']);

    // Kredensial default admin
    $valid_user = 'ibezlaia@gmail.com';
    $valid_pass = 'Keluargaq_02'; // Sesuaikan password admin Anda

    if ($username === $valid_user && $password === $valid_pass) {
        // Simpan ke Session
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        $_SESSION['admin_role'] = 'Owner / Admin';

        // Simpan ke Cookie jika dicentang "Ingat Saya" (aktif 30 hari)
        if ($remember) {
            $token = hash('sha256', 'ibezlaia_admin_secure_secret_key_2026');
            setcookie('admin_remember_token', $token, [
                'expires'  => time() + (86400 * 30),
                'path'     => '/',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }

        header('Location: index.php');
        exit;
    } else {
        $error = 'Email atau password yang Anda masukkan salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk Admin Panel — IbeLaia.Dev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #080c14;
            --bg-card: #0e1422;
            --border-card: rgba(255, 255, 255, 0.08);
            --primary: #8b5cf6;
            --primary-glow: rgba(139, 92, 246, 0.25);
            --accent-cyan: #2dd4bf;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --font: 'Plus Jakarta Sans', -apple-system, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font);
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 16px;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient Glow Background */
        .ambient-glow {
            position: absolute;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.18) 0%, rgba(45, 212, 191, 0.05) 70%, transparent 100%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            pointer-events: none;
            filter: blur(40px);
        }

        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            background: rgba(14, 20, 34, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-card);
            border-radius: 20px;
            padding: 36px 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-logo {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .brand-logo span {
            color: var(--primary);
        }

        .brand-desc {
            font-size: 13px;
            color: var(--text-muted);
        }

        .error-badge {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .input-group {
            margin-bottom: 18px;
        }

        .input-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 6px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            font-size: 15px;
            color: #64748b;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            background: #090e18;
            border: 1px solid var(--border-card);
            border-radius: 10px;
            color: #fff;
            font-size: 13.5px;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 14px var(--primary-glow);
            background: #0a101c;
        }

        .toggle-pwd {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #64748b;
            font-size: 14px;
            cursor: pointer;
            padding: 4px;
        }

        .remember-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            font-size: 12.5px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
        }

        .checkbox-label input {
            accent-color: var(--primary);
            width: 15px;
            height: 15px;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
            color: #fff;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.4);
        }

        .btn-submit:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--text-muted);
            font-size: 12.5px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #fff;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 28px 20px;
                border-radius: 16px;
            }
        }
    </style>
</head>
<body>

    <div class="ambient-glow"></div>

    <div class="login-card">
        <div class="brand-header">
            <div class="brand-logo">IbeLaia<span>.Dev</span></div>
            <p class="brand-desc">Masuk untuk mengelola konten portofolio</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-badge">
                <span>⚠️</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="input-group">
                <label for="username">Email Administrator</label>
                <div class="input-wrapper">
                    <span class="input-icon">✉️</span>
                    <input type="email" id="username" name="username" class="form-input" placeholder="nama@email.com" value="ibezlaia@gmail.com" required autocomplete="username">
                </div>
            </div>

            <div class="input-group">
                <label for="password">Kata Sandi</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                    <button type="button" class="toggle-pwd" onclick="togglePass()" title="Tampilkan/Sembunyikan Kata Sandi">👁️</button>
                </div>
            </div>

            <div class="remember-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" value="1" checked>
                    <span>Ingat Saya (30 Hari)</span>
                </label>
            </div>

            <button type="submit" class="btn-submit">Masuk ke Dashboard →</button>
        </form>

        <a href="../index.php" class="back-link">← Kembali ke Website Utama</a>
    </div>

    <script>
    function togglePass() {
        const passInput = document.getElementById('password');
        if (passInput.type === 'password') {
            passInput.type = 'text';
        } else {
            passInput.type = 'password';
        }
    }
    </script>
</body>
</html>