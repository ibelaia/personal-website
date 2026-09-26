<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../auth.php';

$current_page = basename($_SERVER['PHP_SELF']);
$adminEmail = $_SESSION['admin_user'] ?? 'ibezlaia@gmail.com';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard Admin') ?> — IbeLaia.Dev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #0a0d14;
            --bg-sidebar: #0f141f;
            --bg-card: #131926;
            --bg-card-hover: #182030;
            --border-color: rgba(255, 255, 255, 0.07);
            --border-accent: rgba(124, 58, 237, 0.3);
            --primary: #8b5cf6;
            --primary-glow: rgba(139, 92, 246, 0.15);
            --text-main: #f3f4f6;
            --text-muted: #8492a6;
            --text-sub: #627289;
            --success: #10b981;
            --danger: #ef4444;
            --font: 'Plus Jakarta Sans', -apple-system, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font);
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* SIDEBAR DESKTOP */
        aside.sidebar {
            width: 260px;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 24px 16px;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-header-mobile {
            display: none;
            justify-content: space-between;
            align-items: center;
            padding: 0 8px 16px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 12px;
        }
        .sidebar-header-mobile h4 {
            color: #fff;
            font-size: 16px;
            font-weight: 700;
        }
        .btn-close-drawer {
            background: none;
            border: none;
            color: #8492a6;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 10px 24px 10px;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.5px;
        }
        .brand span {
            color: var(--primary);
        }

        .nav-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-sub);
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 16px 10px 8px;
        }

        .nav-group {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            border-radius: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .nav-item:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.03);
        }
        .nav-item.active {
            color: #fff;
            background: #2e1065;
            border: 1px solid var(--border-accent);
            box-shadow: 0 0 16px var(--primary-glow);
        }
        .nav-item .icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sidebar-bottom {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding-top: 16px;
            border-top: 1px solid var(--border-color);
            margin-top: 20px;
        }

        .btn-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            background: rgba(139, 92, 246, 0.08);
            border: 1px solid rgba(139, 92, 246, 0.3);
            border-radius: 8px;
            color: #c4b5fd;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        .btn-preview:hover {
            background: rgba(139, 92, 246, 0.16);
            color: #fff;
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            margin-bottom: 6px;
        }
        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #4c1d95;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            color: #fff;
        }
        .user-info {
            flex: 1;
            overflow: hidden;
        }
        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* MAIN WRAPPER */
        main.main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            width: 100%;
        }

        header.topbar {
            height: 64px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            background: rgba(10, 13, 20, 0.8);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .page-heading {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .mobile-header-bar {
            display: none;
            height: 60px;
            padding: 0 18px;
            align-items: center;
            justify-content: space-between;
            background: #0f141f;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 990;
        }
        .mobile-brand {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
        }
        .mobile-brand span {
            color: var(--primary);
        }
        .btn-menu-pill {
            background: #1b2234;
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #c9d1d9;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .content-area {
            padding: 32px;
            flex: 1;
        }

        .drawer-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: 999;
        }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            header.topbar { display: none; }
            .mobile-header-bar { display: flex; }
            aside.sidebar {
                position: fixed;
                top: 0; right: 0; bottom: 0;
                width: 270px; height: 100vh;
                transform: translateX(100%);
                box-shadow: -10px 0 30px rgba(0, 0, 0, 0.8);
                border-left: 1px solid var(--border-color);
            }
            aside.sidebar.open { transform: translateX(0); }
            .sidebar-header-mobile { display: flex; }
            .brand { display: none; }
            .drawer-backdrop.active { display: block; }
            .content-area { padding: 16px 12px; }
        }
    </style>
</head>
<body>

    <div class="drawer-backdrop" id="drawerBackdrop"></div>

    <div class="mobile-header-bar">
        <div class="mobile-brand">IbeLaia<span>.Dev</span></div>
        <button class="btn-menu-pill" id="mobileMenuBtn">
            MENU <span>☰</span>
        </button>
    </div>

    <aside class="sidebar" id="adminSidebar">
        <div class="sidebar-top">
            <div class="sidebar-header-mobile">
                <h4>Menu</h4>
                <button class="btn-close-drawer" id="closeDrawerBtn">✕</button>
            </div>

            <div class="brand">
                IbeLaia<span>.Dev</span>
            </div>

            <div class="nav-label">Overview</div>
            <div class="nav-group">
                <a href="index.php" class="nav-item <?= $current_page === 'index.php' ? 'active' : '' ?>">
                    <span class="icon">⊞</span> Dashboard
                </a>
            </div>

            <div class="nav-label">Website Content</div>
            <div class="nav-group">
                <a href="edit-home.php" class="nav-item <?= $current_page === 'edit-home.php' ? 'active' : '' ?>">
                    <span class="icon">🏠</span> Home
                </a>
                <a href="edit-about.php" class="nav-item <?= $current_page === 'edit-about.php' ? 'active' : '' ?>">
                    <span class="icon">👤</span> About
                </a>
                <a href="edit-journey.php" class="nav-item <?= $current_page === 'edit-journey.php' ? 'active' : '' ?>">
                    <span class="icon">🚀</span> Journey
                </a>
                <a href="edit-education.php" class="nav-item <?= $current_page === 'edit-education.php' ? 'active' : '' ?>">
                    <span class="icon">🎓</span> Education
                </a>
                <a href="edit-achievements.php" class="nav-item <?= $current_page === 'edit-achievements.php' ? 'active' : '' ?>">
                    <span class="icon">🏆</span> Achievements
                </a>
                <a href="custom-sections.php" class="nav-item <?= ($current_page === 'custom-sections.php') ? 'active' : '' ?>">
                    <span class="icon">🧩</span> Custom Sections
                </a>
                <a href="messages.php" class="nav-item <?= $current_page === 'messages.php' ? 'active' : '' ?>">
                    <span class="icon">✉️</span> Messages
                </a>
            </div>
        </div>

        <div class="sidebar-bottom">
            <a href="../index.php" target="_blank" class="btn-preview">
                Preview Website ↗
            </a>
            
            <div class="user-card">
                <div class="user-avatar">IB</div>
                <div class="user-info">
                    <div class="user-name">Ibe Laia</div>
                    <div class="user-role">Owner / Admin</div>
                </div>
            </div>

            <a href="logout.php" class="nav-item" style="color: #f87171; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2); justify-content: center; font-weight: 600; gap: 8px;">
                <span>🚪</span> Keluar (Logout)
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div class="page-heading"><?= htmlspecialchars($topbarTitle ?? 'Dashboard Overview') ?></div>
            <div style="font-size: 12px; color: var(--text-sub);">
                <?= date('d F Y') ?>
            </div>
        </header>
        <div class="content-area">