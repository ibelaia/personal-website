<?php
$pageTitle = 'Dashboard Overview';
$topbarTitle = 'Overview / Dashboard';

require_once __DIR__ . '/../supabase.php';
require_once __DIR__ . '/partials/header.php';

// Hitung total Custom Sections secara otomatis dari file JSON / data
$sectionCount = 0;
$sectionsPath = __DIR__ . '/../data/custom_sections.json'; // sesuaikan jika path file JSON custom sections Anda berbeda, atau fallback ke 0
if (file_exists($sectionsPath)) {
    $secData = json_decode(file_get_contents($sectionsPath), true);
    if (is_array($secData)) { $sectionCount = count($secData); }
} else {
    // Cek alternatif jika menggunakan format file lain atau array kosong
    $sectionsPathAlt = __DIR__ . '/../data/sections.json';
    if (file_exists($sectionsPathAlt)) {
        $secDataAlt = json_decode(file_get_contents($sectionsPathAlt), true);
        if (is_array($secDataAlt)) { $sectionCount = count($secDataAlt); }
    }
}

// Hitung total Pesan Masuk
$msgCount = 0;
$resMsg = supabase('messages?select=id');
if (is_array($resMsg) && !isset($resMsg['error'])) {
    $msgCount = count($resMsg);
} else {
    $msgPath = __DIR__ . '/../data/messages.json';
    if (file_exists($msgPath)) {
        $mData = json_decode(file_get_contents($msgPath), true);
        if (is_array($mData)) { $msgCount = count($mData); }
    }
}

// Data Prestasi & Sertifikat
$achieveCount = 6;
?>

<style>
    .welcome-card-top {
        margin-bottom: 24px;
    }
    .welcome-title-top {
        font-size: 24px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 6px;
    }
    .welcome-sub-top {
        font-size: 13.5px;
        color: var(--text-muted);
    }

    /* GRID STATISTIK DESKTOP */
    .metrics-grid-pro {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }
    .metric-box-pro {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s, border-color 0.2s;
    }
    .metric-box-pro:hover {
        border-color: rgba(139, 92, 246, 0.4);
        transform: translateY(-2px);
    }
    .metric-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .metric-lbl {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-sub);
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }
    .metric-icon-bubble {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.03);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .metric-number {
        font-size: 26px;
        font-weight: 800;
        color: #fff;
    }
    .metric-note {
        font-size: 11.5px;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* KONTEN DUA KOLOM DESKTOP */
    .dashboard-split-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
    }
    .action-panel-box {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 24px;
    }
    .panel-headline {
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border-color);
    }
    .content-buttons-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .edit-shortcut-card {
        background: #090e17;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 14px 16px;
        text-decoration: none;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13.5px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .edit-shortcut-card:hover {
        background: rgba(139, 92, 246, 0.12);
        border-color: rgba(139, 92, 246, 0.4);
        color: #c4b5fd;
    }
    .shortcut-ico {
        font-size: 18px;
        width: 24px;
        text-align: center;
        flex-shrink: 0;
    }
    .shortcut-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* STATUS SISTEM */
    .sys-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .sys-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        background: #090e17;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        font-size: 12.5px;
    }
    .sys-row .label {
        color: var(--text-muted);
    }
    .sys-row .status-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        color: #34d399;
        font-size: 11.5px;
    }

    /* KHUSUS PENYESUAIAN MOBILE (< 768px) */
    @media (max-width: 768px) {
        .welcome-card-top {
            margin-bottom: 16px;
        }
        .welcome-title-top {
            font-size: 20px;
        }
        .welcome-sub-top {
            font-size: 12px;
        }

        .metrics-grid-pro {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }
        .metric-box-pro {
            padding: 12px 14px;
            border-radius: 10px;
        }
        .metric-head {
            margin-bottom: 6px;
        }
        .metric-lbl {
            font-size: 10px;
        }
        .metric-icon-bubble {
            width: 24px;
            height: 24px;
            font-size: 12px;
        }
        .metric-number {
            font-size: 20px;
        }
        .metric-note {
            font-size: 10.5px;
        }

        .dashboard-split-content {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .action-panel-box {
            padding: 16px 12px;
            border-radius: 12px;
        }
        .panel-headline {
            font-size: 13.5px;
            margin-bottom: 12px;
            padding-bottom: 8px;
        }

        .content-buttons-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        .edit-shortcut-card {
            padding: 10px 8px;
            border-radius: 8px;
            gap: 6px;
            font-size: 11.5px;
        }
        .shortcut-ico {
            font-size: 15px;
            width: 18px;
        }

        .sys-row {
            padding: 8px 10px;
            font-size: 11.5px;
        }
    }
</style>

<div class="welcome-card-top">
    <h1 class="welcome-title-top">Welcome back, Ibe Laia! 👋</h1>
    <p class="welcome-sub-top">Berikut ringkasan status kontrol dan pembaruan konten personal website Anda.</p>
</div>

<!-- 5 KARTU METRIK STATISTIK -->
<div class="metrics-grid-pro">
    <div class="metric-box-pro">
        <div class="metric-head">
            <span class="metric-lbl">Total Sections</span>
            <span class="metric-icon-bubble">🧩</span>
        </div>
        <div class="metric-number"><?= $sectionCount ?></div>
        <div class="metric-note">Custom Sections Aktif</div>
    </div>

    <div class="metric-box-pro">
        <div class="metric-head">
            <span class="metric-lbl">Achievements</span>
            <span class="metric-icon-bubble" style="background:rgba(251,191,36,0.1); color:#fbbf24;">🏆</span>
        </div>
        <div class="metric-number" style="color:#fbbf24;"><?= $achieveCount ?></div>
        <div class="metric-note">Medali &amp; Sertifikat</div>
    </div>

    <div class="metric-box-pro">
        <div class="metric-head">
            <span class="metric-lbl">Pesan Masuk</span>
            <span class="metric-icon-bubble">✉️</span>
        </div>
        <div class="metric-number"><?= $msgCount ?></div>
        <div class="metric-note">Formulir Kontak</div>
    </div>

    <div class="metric-box-pro">
        <div class="metric-head">
            <span class="metric-lbl">Bahasa Aktif</span>
            <span class="metric-icon-bubble">🌐</span>
        </div>
        <div class="metric-number">2 <span style="font-size:13px; font-weight:500; color:var(--text-muted);">(ID/EN)</span></div>
        <div class="metric-note">Bilingual JSON</div>
    </div>

    <div class="metric-box-pro">
        <div class="metric-head">
            <span class="metric-lbl">Database</span>
            <span class="metric-icon-bubble" style="color:#f59e0b;">⚡</span>
        </div>
        <div class="metric-number" style="font-size:18px; margin-top:2px;">Supabase</div>
        <div class="metric-note">PostgreSQL Cloud</div>
    </div>
</div>

<!-- DUA KOLOM PINTASAN & STATUS -->
<div class="dashboard-split-content">
    <!-- PINTASAN KONTEN HALAMAN -->
    <div class="action-panel-box">
        <div class="panel-headline">Kelola Konten Halaman Pengunjung</div>
        <div class="content-buttons-grid">
            <a href="edit-home.php" class="edit-shortcut-card">
                <span class="shortcut-ico">🏠</span>
                <span class="shortcut-text">Edit Home</span>
            </a>
            <a href="edit-about.php" class="edit-shortcut-card">
                <span class="shortcut-ico">👤</span>
                <span class="shortcut-text">Edit About</span>
            </a>
            <a href="edit-journey.php" class="edit-shortcut-card">
                <span class="shortcut-ico">🚀</span>
                <span class="shortcut-text">Edit Journey</span>
            </a>
            <a href="edit-education.php" class="edit-shortcut-card">
                <span class="shortcut-ico">🎓</span>
                <span class="shortcut-text">Edit Education</span>
            </a>
            <a href="edit-achievements.php" class="edit-shortcut-card">
                <span class="shortcut-ico">🏆</span>
                <span class="shortcut-text">Achievements</span>
            </a>
            <a href="custom-sections.php" class="edit-shortcut-card">
                <span class="shortcut-ico">🧩</span>
                <span class="shortcut-text">Custom Sections</span>
            </a>
            <a href="messages.php" class="edit-shortcut-card" style="border-color: rgba(45, 212, 191, 0.35);">
                <span class="shortcut-ico">📬</span>
                <span class="shortcut-text" style="color:#2dd4bf;">Kontak &amp; Pesan</span>
            </a>
        </div>
    </div>

    <!-- STATUS SISTEM -->
    <div class="action-panel-box">
        <div class="panel-headline">Status Sistem</div>
        <div class="sys-list">
            <div class="sys-row">
                <span class="label">Database Cloud</span>
                <span class="status-tag">● Connected</span>
            </div>
            <div class="sys-row">
                <span class="label">Mode Terjemahan</span>
                <span class="status-tag">● JSON + Supabase</span>
            </div>
            <div class="sys-row">
                <span class="label">Portofolio Web</span>
                <span class="status-tag">● Live Ready</span>
            </div>
            <div class="sys-row">
                <span class="label">Sesi Administrator</span>
                <span class="status-tag">● Aktif (Cookie 30 Hari)</span>
            </div>
            <div class="sys-row">
                <span class="label">Role Akun</span>
                <span style="font-weight:700; color:#fff;">Owner / Admin</span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>