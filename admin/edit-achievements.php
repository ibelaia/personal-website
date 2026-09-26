<?php
$pageTitle = 'Edit Achievements';
$topbarTitle = 'Website Content / Achievements';

require_once __DIR__ . '/../supabase.php';
require_once __DIR__ . '/partials/header.php';

$enPath    = __DIR__ . '/../lang/en.json';
$idPath    = __DIR__ . '/../lang/id.json';
$achPath   = __DIR__ . '/../data/achievements_cards.json';
$uploadDir = __DIR__ . '/../assets/img/';

if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

// Default data awal
$defaultData = [
    'medals' => [
        [
            'id'        => 'med_1',
            'image'     => '',
            'badge_id'  => 'JUARA 1',
            'badge_en'  => '1ST PLACE',
            'year'      => '2023',
            'icon'      => '🥇',
            'issuer_id' => 'Olimpiade Sains Indonesia',
            'issuer_en' => 'National Science Olympiad',
            'title_id'  => 'Kompetisi Fisika Nasional',
            'title_en'  => 'National Physics Competition',
            'desc_id'   => 'Meraih medali emas tingkat nasional melalui penguasaan analisis mendalam teori fisika dan pemecahan masalah kompleks.',
            'desc_en'   => 'Secured national gold medal through advanced analytical problem solving and deep mastery of physical principles.',
            'link'      => '',
            'show_home' => 1
        ],
        [
            'id'        => 'med_2',
            'image'     => '',
            'badge_id'  => 'MEDALI PERUNGGU',
            'badge_en'  => 'BRONZE MEDAL',
            'year'      => '2023',
            'icon'      => '🥉',
            'issuer_id' => 'Kompetisi Sains Nasional',
            'issuer_en' => 'National Science Competition',
            'title_id'  => 'Kompetisi Matematika Nasional',
            'title_en'  => 'National Mathematics Competition',
            'desc_id'   => 'Memperoleh medali perunggu melalui penalaran matematika logis dan deduksi terstruktur tingkat nasional.',
            'desc_en'   => 'Awarded bronze medal through rigorous mathematical reasoning and structured analytical deduction.',
            'link'      => '',
            'show_home' => 1
        ]
    ],
    'certificates' => [
        [
            'id'        => 'cert_1',
            'image'     => '',
            'badge_id'  => 'SERTIFIKASI RESMI',
            'badge_en'  => 'OFFICIAL CERTIFICATE',
            'year'      => '2024',
            'icon'      => '☕',
            'issuer_id' => 'Oracle Academy / Coursera',
            'issuer_en' => 'Oracle Academy / Coursera',
            'title_id'  => 'Java Programming Masterclass',
            'title_en'  => 'Java Programming Masterclass',
            'desc_id'   => 'Menyelesaikan modul pemrograman berorientasi objek (OOP), struktur data, dan aplikasi desktop Java.',
            'desc_en'   => 'Mastered object-oriented programming (OOP), data structures, and practical desktop Java software development.',
            'link'      => '',
            'show_home' => 1
        ],
        [
            'id'        => 'cert_2',
            'image'     => '',
            'badge_id'  => 'WORKSHOP INTENSIF',
            'badge_en'  => 'INTENSIVE WORKSHOP',
            'year'      => '2025',
            'icon'      => '🌐',
            'issuer_id' => 'Tech Academy',
            'issuer_en' => 'Tech Academy',
            'title_id'  => 'Full-Stack Web Development',
            'title_en'  => 'Full-Stack Web Development',
            'desc_id'   => 'Pengembangan aplikasi web modern end-to-end dengan integrasi basis data dan RESTful API.',
            'desc_en'   => 'End-to-end full-stack web application development integrating databases and modern RESTful APIs.',
            'link'      => '',
            'show_home' => 1
        ]
    ]
];

$achData = file_exists($achPath) ? (json_decode(file_get_contents($achPath), true) ?: []) : [];
if (empty($achData['medals'])) {
    $achData['medals'] = $defaultData['medals'];
}
if (empty($achData['certificates'])) {
    $achData['certificates'] = $defaultData['certificates'];
}

$medals = $achData['medals'];
$certs  = $achData['certificates'];
$success = '';

// Helper fungsi upload gambar
function handleFileUpload($fileInputName, $oldImagePath = '') {
    global $uploadDir;
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $tmpName   = $_FILES[$fileInputName]['tmp_name'];
        $origName  = basename($_FILES[$fileInputName]['name']);
        $extension = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed   = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        
        if (in_array($extension, $allowed)) {
            $newName = 'cert_' . time() . '_' . uniqid() . '.' . $extension;
            $destPath = $uploadDir . $newName;
            if (move_uploaded_file($tmpName, $destPath)) {
                return 'assets/img/' . $newName;
            }
        }
    }
    return $oldImagePath;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enData = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
    $idData = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];
    $batch  = [];

    // 1. Simpan Terjemahan Statis (Home Preview, Header, Refleksi)
    if (isset($_POST['translations']) && is_array($_POST['translations'])) {
        foreach ($_POST['translations'] as $k => $vals) {
            $idVal = trim($vals['id'] ?? '');
            $enVal = trim($vals['en'] ?? '');

            $batch[] = [
                'key'        => $k,
                'id_text'    => $idVal,
                'en_text'    => $enVal,
                'updated_at' => date('c')
            ];

            $idData[$k] = $idVal;
            $enData[$k] = $enVal;
        }
        supabase('translations', 'POST', $batch, 'on_conflict=key');
        file_put_contents($idPath, json_encode($idData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents($enPath, json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // 2. Simpan Medali
    $newMedals = [];
    if (isset($_POST['medals']) && is_array($_POST['medals'])) {
        foreach ($_POST['medals'] as $idx => $m) {
            if (!empty(trim($m['title_id'] ?? ''))) {
                $imageVal = trim($m['image'] ?? '');
                // Cek upload file gambar baru jika ada
                if (isset($_FILES['medals_file_' . $idx])) {
                    $uploaded = handleFileUpload('medals_file_' . $idx, $imageVal);
                    if ($uploaded !== '') $imageVal = $uploaded;
                }

                $newMedals[] = [
                    'id'        => $m['id'] ?? ('med_' . $idx),
                    'image'     => $imageVal,
                    'badge_id'  => trim($m['badge_id'] ?? 'JUARA'),
                    'badge_en'  => trim($m['badge_en'] ?? 'AWARD'),
                    'year'      => trim($m['year'] ?? date('Y')),
                    'icon'      => trim($m['icon'] ?? '🏆'),
                    'issuer_id' => trim($m['issuer_id'] ?? ''),
                    'issuer_en' => trim($m['issuer_en'] ?? $m['issuer_id'] ?? ''),
                    'title_id'  => trim($m['title_id']),
                    'title_en'  => trim($m['title_en'] ?? $m['title_id']),
                    'desc_id'   => trim($m['desc_id'] ?? ''),
                    'desc_en'   => trim($m['desc_en'] ?? ''),
                    'link'      => trim($m['link'] ?? ''),
                    'show_home' => isset($m['show_home']) ? 1 : 0
                ];
            }
        }
    }

    // 3. Simpan Sertifikat
    $newCerts = [];
    if (isset($_POST['certificates']) && is_array($_POST['certificates'])) {
        foreach ($_POST['certificates'] as $idx => $c) {
            if (!empty(trim($c['title_id'] ?? ''))) {
                $imageVal = trim($c['image'] ?? '');
                // Cek upload file gambar baru jika ada
                if (isset($_FILES['certs_file_' . $idx])) {
                    $uploaded = handleFileUpload('certs_file_' . $idx, $imageVal);
                    if ($uploaded !== '') $imageVal = $uploaded;
                }

                $newCerts[] = [
                    'id'        => $c['id'] ?? ('cert_' . $idx),
                    'image'     => $imageVal,
                    'badge_id'  => trim($c['badge_id'] ?? 'SERTIFIKASI'),
                    'badge_en'  => trim($c['badge_en'] ?? 'CERTIFICATE'),
                    'year'      => trim($c['year'] ?? date('Y')),
                    'icon'      => trim($c['icon'] ?? '📜'),
                    'issuer_id' => trim($c['issuer_id'] ?? ''),
                    'issuer_en' => trim($c['issuer_en'] ?? $c['issuer_id'] ?? ''),
                    'title_id'  => trim($c['title_id']),
                    'title_en'  => trim($c['title_en'] ?? $c['title_id']),
                    'desc_id'   => trim($c['desc_id'] ?? ''),
                    'desc_en'   => trim($c['desc_en'] ?? ''),
                    'link'      => trim($c['link'] ?? ''),
                    'show_home' => isset($c['show_home']) ? 1 : 0
                ];
            }
        }
    }

    $achData['medals']       = !empty($newMedals) ? $newMedals : $defaultData['medals'];
    $achData['certificates'] = !empty($newCerts) ? $newCerts : $defaultData['certificates'];
    file_put_contents($achPath, json_encode($achData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $medals = $achData['medals'];
    $certs  = $achData['certificates'];
    $success = 'Data Prestasi & Sertifikat berhasil disimpan!';
}

$enDict = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
$idDict = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];

function getVal($dict, $key, $fallback = '') {
    return (!empty($dict[$key])) ? $dict[$key] : $fallback;
}
?>

<style>
    .ach-container {
        max-width: 960px;
        margin: 0 auto;
    }
    .form-panel-compact {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
    }

    .sticky-control-bar {
        position: sticky;
        top: 64px;
        z-index: 50;
        background: rgba(19, 25, 38, 0.94);
        backdrop-filter: blur(10px);
        margin: -16px -20px 16px -20px;
        padding: 12px 20px;
        border-bottom: 1px solid var(--border-color);
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .panel-header-clean {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }
    .lang-switcher {
        display: inline-flex;
        background: #090c13;
        border: 1px solid rgba(139, 92, 246, 0.3);
        border-radius: 6px;
        padding: 2px;
        gap: 2px;
    }
    .lang-tab {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 5px 12px;
        border-radius: 5px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }
    .lang-tab.active {
        background: var(--primary);
        color: #fff;
    }

    .section-selector-bar {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 6px;
        background: #090c13;
        padding: 4px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }
    .sec-tab-btn {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 8px 4px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sec-tab-btn.active {
        background: rgba(139, 92, 246, 0.18);
        color: #c4b5fd;
        border: 1px solid rgba(139, 92, 246, 0.4);
    }

    .section-pane { display: none; }
    .section-pane.active { display: block; }

    .card-block-compact {
        background: #090c13;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 12px;
    }
    .card-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .badge-year-block {
        background: rgba(139, 92, 246, 0.18);
        color: #c4b5fd;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 5px;
    }
    .card-mini-lang-nav {
        display: inline-flex;
        gap: 3px;
        background: #05080e;
        padding: 2px;
        border-radius: 5px;
        border: 1px solid var(--border-color);
    }
    .card-mini-btn {
        background: transparent;
        border: none;
        color: var(--text-sub);
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        cursor: pointer;
    }
    .card-mini-btn.active {
        background: rgba(139, 92, 246, 0.25);
        color: #c4b5fd;
    }
    .btn-delete-card {
        background: rgba(239, 68, 68, 0.12);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.25);
        padding: 3px 8px;
        border-radius: 5px;
        font-size: 10.5px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-delete-card:hover { background: #ef4444; color: #fff; }

    .home-toggle-box {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 3px 8px;
        border-radius: 5px;
        color: #34d399;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
    }
    .home-toggle-box input[type="checkbox"] {
        cursor: pointer;
        accent-color: #10b981;
    }

    .btn-add-card {
        background: rgba(139, 92, 246, 0.1);
        color: #c4b5fd;
        border: 1px dashed rgba(139, 92, 246, 0.4);
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .btn-add-card:hover { background: rgba(139, 92, 246, 0.2); color: #fff; }

    .field-item { margin-bottom: 10px; }
    .field-item label {
        display: block;
        font-size: 11.5px;
        font-weight: 600;
        color: #cbd5e1;
        margin-bottom: 4px;
    }
    .clean-input {
        width: 100%;
        padding: 7px 10px;
        background: #05080e;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: #fff;
        font-size: 12.5px;
        font-family: inherit;
        line-height: 1.45;
    }
    .clean-input:focus { border-color: var(--primary); outline: none; }
    textarea.clean-input { resize: vertical; min-height: 55px; }

    .img-upload-combo {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        background: rgba(255,255,255,0.02);
        padding: 8px 10px;
        border-radius: 6px;
        border: 1px dashed rgba(255,255,255,0.1);
    }

    .grid-2-col { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .grid-3-col { display: grid; grid-template-columns: 75px 1fr 90px; gap: 8px; }

    .bottom-save-bar {
        margin-top: 20px;
        padding-top: 14px;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    .btn-save-final {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 7px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-save-final:hover { opacity: 0.9; }

    .alert-ok {
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #34d399;
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 12px;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .sticky-control-bar { top: 0; margin: -14px -12px 14px -12px; padding: 10px 12px; }
        .section-selector-bar { grid-template-columns: repeat(2, 1fr); gap: 4px; }
        .grid-2-col, .grid-3-col, .img-upload-combo { grid-template-columns: 1fr; }
        .form-panel-compact { padding: 14px 12px; }
        .bottom-save-bar { flex-direction: column-reverse; }
        .btn-save-final { width: 100%; }
    }
</style>

<div class="ach-container">
    <?php if ($success): ?>
        <div class="alert-ok"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form-panel-compact">
        <!-- STICKY CONTROL BAR -->
        <div class="sticky-control-bar">
            <div class="panel-header-clean">
                <h3 style="font-size: 15px; color: #fff; margin:0;">Editor Achievements</h3>
                <div class="lang-switcher">
                    <button type="button" class="lang-tab active" id="globalTabId" onclick="switchLang('id')">🇮🇩 ID</button>
                    <button type="button" class="lang-tab" id="globalTabEn" onclick="switchLang('en')">🇬🇧 EN</button>
                </div>
            </div>

            <div class="section-selector-bar">
                <button type="button" class="sec-tab-btn active" onclick="openSection('sec-home', this)">🏠 Cuplikan Home</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-header', this)">📌 Header &amp; Intro</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-medals', this)">🥇 Medali Utama</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-certs', this)">📜 Sertifikat</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-refl', this)">💡 Refleksi</button>
            </div>
        </div>

        <!-- 1. CUPLIKAN HOME -->
        <div id="sec-home" class="section-pane active">
            <div class="grid-2-col">
                <div class="field-item">
                    <label>Label Atas (Eyebrow)</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[index.achievement.eyebrow][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.achievement.eyebrow', 'Prestasi')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.achievement.eyebrow][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.achievement.eyebrow', 'Achievements')) ?>"></div>
                </div>
                <div class="field-item">
                    <label>Judul Section Beranda</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[index.achievement.title][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.achievement.title', 'Sekilas Perjalanan Saya')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.achievement.title][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.achievement.title', 'A Glimpse of the Journey')) ?>"></div>
                </div>
            </div>

            <div class="field-item">
                <label>Deskripsi Pengantar Section</label>
                <div class="lang-field lang-id"><textarea name="translations[index.achievement.desc][id]" class="clean-input" rows="2"><?= htmlspecialchars(getVal($idDict, 'index.achievement.desc', '🏆 Sekilas pencapaian yang membentuk perjalanan akademik saya.')) ?></textarea></div>
                <div class="lang-field lang-en" style="display:none;"><textarea name="translations[index.achievement.desc][en]" class="clean-input" rows="2"><?= htmlspecialchars(getVal($enDict, 'index.achievement.desc', '🏆 A glimpse of the milestones that shaped my academic journey.')) ?></textarea></div>
            </div>

            <div class="card-block-compact" style="background: rgba(139, 92, 246, 0.08); border-color: rgba(139, 92, 246, 0.25);">
                <div style="font-size:12px; font-weight:700; color:#c4b5fd; margin-bottom:4px;">💡 Pengaturan Tampilan di Beranda:</div>
                <div style="font-size:12px; color:var(--text-muted); line-height:1.5;">
                    Pilih minimal 4 kartu rekomendasi yang ingin ditampilkan di Beranda dengan mencentang <strong>"⭐ Tampilkan di Home"</strong> pada tab <strong>Medali Utama</strong> atau <strong>Sertifikat</strong>.
                </div>
            </div>

            <div class="field-item" style="margin-top:10px;">
                <label>Teks Tombol CTA Beranda</label>
                <div class="lang-field lang-id"><input type="text" name="translations[index.achievement.button][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.achievement.button', 'Lihat Semua Prestasi →')) ?>"></div>
                <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.achievement.button][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.achievement.button', 'View All Achievements →')) ?>"></div>
            </div>
        </div>

        <!-- 2. HEADER & INTRO PUBLIC PAGE -->
        <div id="sec-header" class="section-pane">
            <div class="card-block-compact">
                <span class="badge-year-block">Header Halaman</span>
                <div class="grid-2-col" style="margin-top:8px;">
                    <div class="field-item">
                        <label>Judul Halaman</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[ach.header.title][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'ach.header.title', 'Prestasi')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[ach.header.title][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'ach.header.title', 'Achievements')) ?>"></div>
                    </div>
                    <div class="field-item">
                        <label>Subjudul Halaman</label>
                        <div class="lang-field lang-id"><textarea name="translations[ach.header.subtitle][id]" class="clean-input" rows="2"><?= htmlspecialchars(getVal($idDict, 'ach.header.subtitle', 'Setiap pencapaian bercerita—bukan hanya tentang keberhasilan, tetapi juga dedikasi dan pertumbuhan berkelanjutan.')) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[ach.header.subtitle][en]" class="clean-input" rows="2"><?= htmlspecialchars(getVal($enDict, 'ach.header.subtitle', 'Every achievement tells a story—not only of success, but also of dedication, perseverance, and continuous growth.')) ?></textarea></div>
                    </div>
                </div>
            </div>

            <div class="card-block-compact">
                <span class="badge-year-block">Pengantar (Introduction)</span>
                <div class="field-item" style="margin-top:8px;">
                    <label>Judul Pengantar</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[ach.intro.heading][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'ach.intro.heading', 'Tonggak Pencapaian di Sepanjang Perjalanan')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[ach.intro.heading][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'ach.intro.heading', 'Milestones Along My Journey')) ?>"></div>
                </div>
                <div class="field-item">
                    <label>Paragraf Pengantar</label>
                    <div class="lang-field lang-id"><textarea name="translations[ach.intro.p1][id]" class="clean-input" rows="3"><?= htmlspecialchars(getVal($idDict, 'ach.intro.p1', 'Sepanjang perjalanan akademik, saya berkesempatan mengikuti berbagai kompetisi dan proses belajar yang menantang saya untuk terus berkembang melampaui ruang kelas.')) ?></textarea></div>
                    <div class="lang-field lang-en" style="display:none;"><textarea name="translations[ach.intro.p1][en]" class="clean-input" rows="3"><?= htmlspecialchars(getVal($enDict, 'ach.intro.p1', 'Throughout my academic journey, I have had the opportunity to participate in competitions and learning activities that challenged me to grow beyond the classroom.')) ?></textarea></div>
                </div>
            </div>
        </div>

        <!-- 3. MEDALI UTAMA (PILIH FILE ATAU INPUT URL) -->
        <div id="sec-medals" class="section-pane">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <span style="font-size:12.5px; font-weight:700; color:#c4b5fd;">Daftar Medali &amp; Kompetisi Sains</span>
                <button type="button" class="btn-add-card" style="width:auto; margin:0; padding:6px 14px;" onclick="addMedalCard()">➕ Tambah Medali Baru</button>
            </div>

            <div id="medalsContainer">
                <?php foreach ($medals as $idx => $m): ?>
                    <div class="card-block-compact" id="mCard_<?= $idx ?>">
                        <div class="card-top-bar">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="badge-year-block">#<?= $idx + 1 ?> Medali</span>
                                <label class="home-toggle-box">
                                    <input type="checkbox" name="medals[<?= $idx ?>][show_home]" value="1" <?= !empty($m['show_home']) ? 'checked' : '' ?>>
                                    <span>⭐ Tampilkan di Home</span>
                                </label>
                            </div>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div class="card-mini-lang-nav">
                                    <button type="button" class="card-mini-btn active" onclick="switchCardLang('mCard_<?= $idx ?>', 'id', this)">ID</button>
                                    <button type="button" class="card-mini-btn" onclick="switchCardLang('mCard_<?= $idx ?>', 'en', this)">EN</button>
                                </div>
                                <button type="button" class="btn-delete-card" onclick="removeCard('mCard_<?= $idx ?>')">✕</button>
                            </div>
                        </div>

                        <input type="hidden" name="medals[<?= $idx ?>][id]" value="<?= htmlspecialchars($m['id'] ?? ('med_' . $idx)) ?>">

                        <!-- Pilihan Gambar: Upload File ATAU Input Path/URL -->
                        <div class="field-item">
                            <label>Foto / Sertifikat Piagam (Pilih Gambar ATAU Masukkan Link/Path)</label>
                            <div class="img-upload-combo">
                                <div>
                                    <span style="font-size:10.5px; color:#94a3b8; display:block; margin-bottom:3px;">📁 Upload File Gambar:</span>
                                    <input type="file" name="medals_file_<?= $idx ?>" class="clean-input" accept="image/*" style="padding:4px 8px; font-size:11.5px;">
                                </div>
                                <div>
                                    <span style="font-size:10.5px; color:#94a3b8; display:block; margin-bottom:3px;">🔗 Atau Masukkan Path / URL Gambar:</span>
                                    <input type="text" name="medals[<?= $idx ?>][image]" class="clean-input" value="<?= htmlspecialchars($m['image'] ?? '') ?>" placeholder="assets/img/cert-physics.png">
                                </div>
                            </div>
                        </div>

                        <div class="grid-3-col">
                            <div class="field-item">
                                <label>Ikon</label>
                                <input type="text" name="medals[<?= $idx ?>][icon]" class="clean-input" value="<?= htmlspecialchars($m['icon'] ?? '🥇') ?>" style="text-align:center;">
                            </div>
                            <div class="field-item">
                                <label>Badge Peringkat</label>
                                <div class="card-lang card-lang-id"><input type="text" name="medals[<?= $idx ?>][badge_id]" class="clean-input" value="<?= htmlspecialchars($m['badge_id'] ?? '') ?>" placeholder="JUARA 1"></div>
                                <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="medals[<?= $idx ?>][badge_en]" class="clean-input" value="<?= htmlspecialchars($m['badge_en'] ?? '') ?>" placeholder="1ST PLACE"></div>
                            </div>
                            <div class="field-item">
                                <label>Tahun</label>
                                <input type="text" name="medals[<?= $idx ?>][year]" class="clean-input" value="<?= htmlspecialchars($m['year'] ?? '') ?>" placeholder="2023">
                            </div>
                        </div>

                        <div class="grid-2-col">
                            <div class="field-item">
                                <label>Penyelenggara / Kategori</label>
                                <div class="card-lang card-lang-id"><input type="text" name="medals[<?= $idx ?>][issuer_id]" class="clean-input" value="<?= htmlspecialchars($m['issuer_id'] ?? '') ?>" placeholder="Penyelenggara (ID)"></div>
                                <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="medals[<?= $idx ?>][issuer_en]" class="clean-input" value="<?= htmlspecialchars($m['issuer_en'] ?? '') ?>" placeholder="Issuer (EN)"></div>
                            </div>
                            <div class="field-item">
                                <label>Nama Kompetisi</label>
                                <div class="card-lang card-lang-id"><input type="text" name="medals[<?= $idx ?>][title_id]" class="clean-input" value="<?= htmlspecialchars($m['title_id'] ?? '') ?>" placeholder="Kompetisi (ID)" required></div>
                                <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="medals[<?= $idx ?>][title_en]" class="clean-input" value="<?= htmlspecialchars($m['title_en'] ?? '') ?>" placeholder="Competition (EN)"></div>
                            </div>
                        </div>

                        <div class="field-item">
                            <label>Deskripsi &amp; Pelajaran (What I Learned)</label>
                            <div class="card-lang card-lang-id"><textarea name="medals[<?= $idx ?>][desc_id]" class="clean-input" placeholder="Deskripsi pengalaman (ID)"><?= htmlspecialchars($m['desc_id'] ?? '') ?></textarea></div>
                            <div class="card-lang card-lang-en" style="display:none;"><textarea name="medals[<?= $idx ?>][desc_en]" class="clean-input" placeholder="Description and takeaways (EN)"><?= htmlspecialchars($m['desc_en'] ?? '') ?></textarea></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 4. SERTIFIKAT LAINNYA (PILIH FILE ATAU INPUT URL) -->
        <div id="sec-certs" class="section-pane">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                <span style="font-size:12.5px; font-weight:700; color:#c4b5fd;">Daftar Sertifikasi Kursus &amp; Workshop</span>
                <button type="button" class="btn-add-card" style="width:auto; margin:0; padding:6px 14px;" onclick="addCertCard()">➕ Tambah Sertifikat Baru</button>
            </div>

            <div id="certsContainer">
                <?php foreach ($certs as $idx => $c): ?>
                    <div class="card-block-compact" id="cCard_<?= $idx ?>">
                        <div class="card-top-bar">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="badge-year-block">#<?= $idx + 1 ?> Sertifikat</span>
                                <label class="home-toggle-box">
                                    <input type="checkbox" name="certificates[<?= $idx ?>][show_home]" value="1" <?= !empty($c['show_home']) ? 'checked' : '' ?>>
                                    <span>⭐ Tampilkan di Home</span>
                                </label>
                            </div>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div class="card-mini-lang-nav">
                                    <button type="button" class="card-mini-btn active" onclick="switchCardLang('cCard_<?= $idx ?>', 'id', this)">ID</button>
                                    <button type="button" class="card-mini-btn" onclick="switchCardLang('cCard_<?= $idx ?>', 'en', this)">EN</button>
                                </div>
                                <button type="button" class="btn-delete-card" onclick="removeCard('cCard_<?= $idx ?>')">✕</button>
                            </div>
                        </div>

                        <input type="hidden" name="certificates[<?= $idx ?>][id]" value="<?= htmlspecialchars($c['id'] ?? ('cert_' . $idx)) ?>">

                        <!-- Pilihan Gambar: Upload File ATAU Input Path/URL -->
                        <div class="field-item">
                            <label>File / Gambar Sertifikat (Pilih Gambar ATAU Masukkan Link/Path)</label>
                            <div class="img-upload-combo">
                                <div>
                                    <span style="font-size:10.5px; color:#94a3b8; display:block; margin-bottom:3px;">📁 Upload File Gambar:</span>
                                    <input type="file" name="certs_file_<?= $idx ?>" class="clean-input" accept="image/*" style="padding:4px 8px; font-size:11.5px;">
                                </div>
                                <div>
                                    <span style="font-size:10.5px; color:#94a3b8; display:block; margin-bottom:3px;">🔗 Atau Masukkan Path / URL Gambar:</span>
                                    <input type="text" name="certificates[<?= $idx ?>][image]" class="clean-input" value="<?= htmlspecialchars($c['image'] ?? '') ?>" placeholder="assets/img/cert-sample.png">
                                </div>
                            </div>
                        </div>

                        <div class="grid-3-col">
                            <div class="field-item">
                                <label>Ikon</label>
                                <input type="text" name="certificates[<?= $idx ?>][icon]" class="clean-input" value="<?= htmlspecialchars($c['icon'] ?? '📜') ?>" style="text-align:center;">
                            </div>
                            <div class="field-item">
                                <label>Badge Tipe</label>
                                <div class="card-lang card-lang-id"><input type="text" name="certificates[<?= $idx ?>][badge_id]" class="clean-input" value="<?= htmlspecialchars($c['badge_id'] ?? '') ?>" placeholder="SERTIFIKASI RESMI"></div>
                                <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="certificates[<?= $idx ?>][badge_en]" class="clean-input" value="<?= htmlspecialchars($c['badge_en'] ?? '') ?>" placeholder="OFFICIAL CERTIFICATE"></div>
                            </div>
                            <div class="field-item">
                                <label>Tahun</label>
                                <input type="text" name="certificates[<?= $idx ?>][year]" class="clean-input" value="<?= htmlspecialchars($c['year'] ?? '') ?>" placeholder="2025">
                            </div>
                        </div>

                        <div class="grid-2-col">
                            <div class="field-item">
                                <label>Pemberi / Platform</label>
                                <div class="card-lang card-lang-id"><input type="text" name="certificates[<?= $idx ?>][issuer_id]" class="clean-input" value="<?= htmlspecialchars($c['issuer_id'] ?? '') ?>" placeholder="Platform (ID)"></div>
                                <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="certificates[<?= $idx ?>][issuer_en]" class="clean-input" value="<?= htmlspecialchars($c['issuer_en'] ?? '') ?>" placeholder="Issuer (EN)"></div>
                            </div>
                            <div class="field-item">
                                <label>Nama Sertifikat</label>
                                <div class="card-lang card-lang-id"><input type="text" name="certificates[<?= $idx ?>][title_id]" class="clean-input" value="<?= htmlspecialchars($c['title_id'] ?? '') ?>" placeholder="Nama Sertifikat (ID)" required></div>
                                <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="certificates[<?= $idx ?>][title_en]" class="clean-input" value="<?= htmlspecialchars($c['title_en'] ?? '') ?>" placeholder="Certificate Title (EN)"></div>
                            </div>
                        </div>

                        <div class="field-item">
                            <label>Deskripsi Singkat</label>
                            <div class="card-lang card-lang-id"><textarea name="certificates[<?= $idx ?>][desc_id]" class="clean-input" placeholder="Materi yang dipelajari (ID)"><?= htmlspecialchars($c['desc_id'] ?? '') ?></textarea></div>
                            <div class="card-lang card-lang-en" style="display:none;"><textarea name="certificates[<?= $idx ?>][desc_en]" class="clean-input" placeholder="Skills gained (EN)"><?= htmlspecialchars($c['desc_en'] ?? '') ?></textarea></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 5. REFLEKSI & KUTIPAN -->
        <div id="sec-refl" class="section-pane">
            <div class="card-block-compact">
                <span class="badge-year-block">Refleksi &amp; Kutipan</span>
                <div class="field-item" style="margin-top:8px;">
                    <label>Judul Section Refleksi</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[ach.refl.heading][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'ach.refl.heading', 'Lebih dari Sekadar Penghargaan')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[ach.refl.heading][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'ach.refl.heading', 'More Than Awards')) ?>"></div>
                </div>
                <div class="field-item">
                    <label>Paragraf Refleksi</label>
                    <div class="lang-field lang-id"><textarea name="translations[ach.refl.p1][id]" class="clean-input" rows="3"><?= htmlspecialchars(getVal($idDict, 'ach.refl.p1', 'Meskipun medali dan sertifikat adalah pencapaian yang berarti, saya percaya pencapaian terbesar adalah pola pikir yang terbentuk dari proses tersebut...')) ?></textarea></div>
                    <div class="lang-field lang-en" style="display:none;"><textarea name="translations[ach.refl.p1][en]" class="clean-input" rows="3"><?= htmlspecialchars(getVal($enDict, 'ach.refl.p1', 'While medals and certificates are meaningful milestones, I believe the greatest achievement is the mindset they helped me build...')) ?></textarea></div>
                </div>
                <div class="grid-2-col">
                    <div class="field-item">
                        <label>Kutipan (Quote)</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[ach.refl.quote][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'ach.refl.quote', 'Keberhasilan bukanlah akhir, kegagalan bukanlah hal yang fatal: keberanian untuk terus melangkahlah yang terpenting.')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[ach.refl.quote][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'ach.refl.quote', 'Success is not final, failure is not fatal: it is the courage to continue that counts.')) ?>"></div>
                    </div>
                    <div class="field-item">
                        <label>Teks Tombol CTA</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[ach.refl.btn][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'ach.refl.btn', 'Lihat Apa yang Saya Bangun →')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[ach.refl.btn][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'ach.refl.btn', "See What I've Built →")) ?>"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOMBOL SIMPAN DI PALING BAWAH -->
        <div class="bottom-save-bar">
            <a href="edit-goals.php" style="color: var(--text-muted); text-decoration: none; font-size: 12.5px; font-weight: 600;">
                🎯 Lanjut ke Goals →
            </a>
            <button type="submit" class="btn-save-final">Simpan Perubahan Achievements</button>
        </div>
    </form>
</div>

<script>
let currentLang = 'id';

function switchLang(lang) {
    currentLang = lang;
    if (lang === 'id') {
        document.getElementById('globalTabId').classList.add('active');
        document.getElementById('globalTabEn').classList.remove('active');
    } else {
        document.getElementById('globalTabId').classList.remove('active');
        document.getElementById('globalTabEn').classList.add('active');
    }

    document.querySelectorAll('.lang-id').forEach(el => el.style.display = (lang === 'id' ? 'block' : 'none'));
    document.querySelectorAll('.lang-en').forEach(el => el.style.display = (lang === 'en' ? 'block' : 'none'));

    document.querySelectorAll('.card-lang-id').forEach(el => el.style.display = (lang === 'id' ? 'block' : 'none'));
    document.querySelectorAll('.card-lang-en').forEach(el => el.style.display = (lang === 'en' ? 'block' : 'none'));

    document.querySelectorAll('.card-block-compact').forEach(card => {
        const btns = card.querySelectorAll('.card-mini-btn');
        if (btns.length === 2) {
            btns[0].classList.toggle('active', lang === 'id');
            btns[1].classList.toggle('active', lang === 'en');
        }
    });
}

function switchCardLang(cardId, lang, btn) {
    const card = document.getElementById(cardId);
    if (!card) return;

    card.querySelectorAll('.card-mini-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    card.querySelectorAll('.card-lang-id').forEach(el => el.style.display = (lang === 'id' ? 'block' : 'none'));
    card.querySelectorAll('.card-lang-en').forEach(el => el.style.display = (lang === 'en' ? 'block' : 'none'));
}

function openSection(secId, btn) {
    document.querySelectorAll('.sec-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.section-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(secId).classList.add('active');
}

function removeCard(cardId) {
    const el = document.getElementById(cardId);
    if (el) el.remove();
}

function addMedalCard() {
    const container = document.getElementById('medalsContainer');
    const idx = Date.now();
    const isEn = (currentLang === 'en');

    const html = `
        <div class="card-block-compact" id="mCard_${idx}">
            <div class="card-top-bar">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="badge-year-block">Baru</span>
                    <label class="home-toggle-box">
                        <input type="checkbox" name="medals[${idx}][show_home]" value="1" checked>
                        <span>⭐ Tampilkan di Home</span>
                    </label>
                </div>
                <div style="display:flex; align-items:center; gap:6px;">
                    <div class="card-mini-lang-nav">
                        <button type="button" class="card-mini-btn ${!isEn ? 'active' : ''}" onclick="switchCardLang('mCard_${idx}', 'id', this)">ID</button>
                        <button type="button" class="card-mini-btn ${isEn ? 'active' : ''}" onclick="switchCardLang('mCard_${idx}', 'en', this)">EN</button>
                    </div>
                    <button type="button" class="btn-delete-card" onclick="removeCard('mCard_${idx}')">✕</button>
                </div>
            </div>
            <input type="hidden" name="medals[${idx}][id]" value="med_${idx}">
            <div class="field-item">
                <label>Foto / Sertifikat Piagam (Pilih Gambar ATAU Masukkan Link/Path)</label>
                <div class="img-upload-combo">
                    <div>
                        <span style="font-size:10.5px; color:#94a3b8; display:block; margin-bottom:3px;">📁 Upload File Gambar:</span>
                        <input type="file" name="medals_file_${idx}" class="clean-input" accept="image/*" style="padding:4px 8px; font-size:11.5px;">
                    </div>
                    <div>
                        <span style="font-size:10.5px; color:#94a3b8; display:block; margin-bottom:3px;">🔗 Atau Masukkan Path / URL:</span>
                        <input type="text" name="medals[${idx}][image]" class="clean-input" placeholder="assets/img/cert.png">
                    </div>
                </div>
            </div>
            <div class="grid-3-col">
                <div class="field-item">
                    <label>Ikon</label>
                    <input type="text" name="medals[${idx}][icon]" class="clean-input" value="🥇" style="text-align:center;">
                </div>
                <div class="field-item">
                    <label>Badge Peringkat</label>
                    <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="medals[${idx}][badge_id]" class="clean-input" placeholder="JUARA 1"></div>
                    <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="medals[${idx}][badge_en]" class="clean-input" placeholder="1ST PLACE"></div>
                </div>
                <div class="field-item">
                    <label>Tahun</label>
                    <input type="text" name="medals[${idx}][year]" class="clean-input" value="${new Date().getFullYear()}">
                </div>
            </div>
            <div class="grid-2-col">
                <div class="field-item">
                    <label>Penyelenggara</label>
                    <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="medals[${idx}][issuer_id]" class="clean-input" placeholder="Penyelenggara (ID)"></div>
                    <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="medals[${idx}][issuer_en]" class="clean-input" placeholder="Issuer (EN)"></div>
                </div>
                <div class="field-item">
                    <label>Nama Kompetisi</label>
                    <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="medals[${idx}][title_id]" class="clean-input" placeholder="Kompetisi (ID)" required></div>
                    <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="medals[${idx}][title_en]" class="clean-input" placeholder="Competition (EN)"></div>
                </div>
            </div>
            <div class="field-item">
                <label>Deskripsi &amp; Pelajaran</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><textarea name="medals[${idx}][desc_id]" class="clean-input" placeholder="Deskripsi (ID)"></textarea></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><textarea name="medals[${idx}][desc_en]" class="clean-input" placeholder="Description (EN)"></textarea></div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function addCertCard() {
    const container = document.getElementById('certsContainer');
    const idx = Date.now();
    const isEn = (currentLang === 'en');

    const html = `
        <div class="card-block-compact" id="cCard_${idx}">
            <div class="card-top-bar">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="badge-year-block">Baru</span>
                    <label class="home-toggle-box">
                        <input type="checkbox" name="certificates[${idx}][show_home]" value="1" checked>
                        <span>⭐ Tampilkan di Home</span>
                    </label>
                </div>
                <div style="display:flex; align-items:center; gap:6px;">
                    <div class="card-mini-lang-nav">
                        <button type="button" class="card-mini-btn ${!isEn ? 'active' : ''}" onclick="switchCardLang('cCard_${idx}', 'id', this)">ID</button>
                        <button type="button" class="card-mini-btn ${isEn ? 'active' : ''}" onclick="switchCardLang('cCard_${idx}', 'en', this)">EN</button>
                    </div>
                    <button type="button" class="btn-delete-card" onclick="removeCard('cCard_${idx}')">✕</button>
                </div>
            </div>
            <input type="hidden" name="certificates[${idx}][id]" value="cert_${idx}">
            <div class="field-item">
                <label>File / Gambar Sertifikat (Pilih Gambar ATAU Masukkan Link/Path)</label>
                <div class="img-upload-combo">
                    <div>
                        <span style="font-size:10.5px; color:#94a3b8; display:block; margin-bottom:3px;">📁 Upload File Gambar:</span>
                        <input type="file" name="certs_file_${idx}" class="clean-input" accept="image/*" style="padding:4px 8px; font-size:11.5px;">
                    </div>
                    <div>
                        <span style="font-size:10.5px; color:#94a3b8; display:block; margin-bottom:3px;">🔗 Atau Masukkan Path / URL:</span>
                        <input type="text" name="certificates[${idx}][image]" class="clean-input" placeholder="assets/img/cert.png">
                    </div>
                </div>
            </div>
            <div class="grid-3-col">
                <div class="field-item">
                    <label>Ikon</label>
                    <input type="text" name="certificates[${idx}][icon]" class="clean-input" value="📜" style="text-align:center;">
                </div>
                <div class="field-item">
                    <label>Badge Tipe</label>
                    <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="certificates[${idx}][badge_id]" class="clean-input" placeholder="SERTIFIKASI RESMI"></div>
                    <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="certificates[${idx}][badge_en]" class="clean-input" placeholder="OFFICIAL CERTIFICATE"></div>
                </div>
                <div class="field-item">
                    <label>Tahun</label>
                    <input type="text" name="certificates[${idx}][year]" class="clean-input" value="${new Date().getFullYear()}">
                </div>
            </div>
            <div class="grid-2-col">
                <div class="field-item">
                    <label>Pemberi / Platform</label>
                    <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="certificates[${idx}][issuer_id]" class="clean-input" placeholder="Platform (ID)"></div>
                    <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="certificates[${idx}][issuer_en]" class="clean-input" placeholder="Issuer (EN)"></div>
                </div>
                <div class="field-item">
                    <label>Nama Sertifikat</label>
                    <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="certificates[${idx}][title_id]" class="clean-input" placeholder="Nama Sertifikat (ID)" required></div>
                    <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="certificates[${idx}][title_en]" class="clean-input" placeholder="Certificate Title (EN)"></div>
                </div>
            </div>
            <div class="field-item">
                <label>Deskripsi Singkat</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><textarea name="certificates[${idx}][desc_id]" class="clean-input" placeholder="Materi (ID)"></textarea></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><textarea name="certificates[${idx}][desc_en]" class="clean-input" placeholder="Skills (EN)"></textarea></div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>