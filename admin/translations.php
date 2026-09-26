<?php
require_once 'auth.php';
require_once __DIR__ . '/../supabase.php';

$success = '';
$error = '';

$enPath = __DIR__ . '/../lang/en.json';
$idPath = __DIR__ . '/../lang/id.json';

// Handle Simpan Massal Per Section / Halaman
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['translations'])) {
    $incoming = $_POST['translations']; // [ key => ['id' => '...', 'en' => '...'] ]
    $batch = [];
    
    // Baca file JSON lokal yang ada saat ini
    $enData = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
    $idData = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];

    foreach ($incoming as $k => $vals) {
        $idVal = trim($vals['id'] ?? '');
        $enVal = trim($vals['en'] ?? '');

        $batch[] = [
            'key'        => $k,
            'id_text'    => $idVal,
            'en_text'    => $enVal,
            'updated_at' => date('c')
        ];

        // Perbarui memory JSON
        $idData[$k] = $idVal;
        $enData[$k] = $enVal;
    }

    if (!empty($batch)) {
        // 1. Simpan ke Supabase
        $res = supabase('translations', 'POST', $batch, 'on_conflict=key');
        
        // 2. Tulis balik ke file lang/id.json & lang/en.json agar sinkron instan
        file_put_contents($idPath, json_encode($idData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents($enPath, json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $success = "Semua perubahan teks berhasil disimpan dan disinkronkan ke website!";
    }
}

// Ambil kamus aktif (prioritas dari file JSON agar cepat dimuat)
$enDict = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
$idDict = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];

$activeTab = $_GET['tab'] ?? 'home';

// Pemetaan Section agar form tersusun rapi sesuai urutan di tampilan pengunjung
$sections = [
    'home' => [
        'Hero Section (Bagian Paling Atas)' => [
            'hero.eyebrow'   => 'Sapaan Kecil (Eyebrow)',
            'hero.subtitle'  => 'Status / Subtitle Profesi',
            'hero.paragraph' => 'Deskripsi Profil Hero'
        ],
        'Pengantar About (Who I Am di Beranda)' => [
            'index.about.eyebrow' => 'Label Section',
            'index.about.title'   => 'Judul Utama',
            'index.about.p1'      => 'Paragraf 1',
            'index.about.p2'      => 'Paragraf 2',
            'index.about.p3'      => 'Paragraf 3',
            'index.about.button'  => 'Teks Tombol Arahkan ke About'
        ],
        'Cuplikan Journey (Linimasa Pertumbuhan)' => [
            'index.journey.eyebrow'      => 'Label Section',
            'index.journey.title'        => 'Judul Utama',
            'index.journey.2024.title'   => 'Judul Tahun 2024',
            'index.journey.2024.desc'    => 'Deskripsi 2024',
            'index.journey.2024.takeaway'=> 'Key Takeaway 2024',
            'index.journey.2025.title'   => 'Judul Tahun 2025',
            'index.journey.2025.desc'    => 'Deskripsi 2025',
            'index.journey.2025.takeaway'=> 'Key Takeaway 2025',
            'index.journey.2026.title'   => 'Judul Tahun 2026',
            'index.journey.2026.desc'    => 'Deskripsi 2026',
            'index.journey.2026.takeaway'=> 'Key Takeaway 2026',
            'index.journey.button'       => 'Teks Tombol Selengkapnya'
        ],
        'Statistik (A Quick Snapshot / Life in Numbers)' => [
            'index.stats.eyebrow'      => 'Label Section',
            'index.stats.title'        => 'Judul Utama',
            'index.stats.desc'         => 'Deskripsi Singkat',
            'index.stats.medals'       => 'Label Medali Kompetisi',
            'index.stats.projects'     => 'Label Proyek Dibuat',
            'index.stats.technologies' => 'Label Teknologi Dipelajari',
            'index.stats.started'      => 'Label Mulai Informatika'
        ],
        'Proyek Terpilih (What I\'ve Built)' => [
            'index.projects.eyebrow'         => 'Label Section',
            'index.projects.title'           => 'Judul Utama',
            'index.projects.desc'            => 'Deskripsi Singkat',
            'index.portfolio.library.title'  => 'Proyek 1 - Judul',
            'index.portfolio.library.desc'   => 'Proyek 1 - Deskripsi',
            'index.portfolio.family.title'   => 'Proyek 2 - Judul',
            'index.portfolio.family.desc'    => 'Proyek 2 - Deskripsi',
            'index.portfolio.personal.title' => 'Proyek 3 - Judul',
            'index.portfolio.personal.desc'  => 'Proyek 3 - Deskripsi',
            'index.portfolio.button'         => 'Teks Tombol Portofolio Utama'
        ]
    ],
    'about' => [
        'Header Halaman About' => [
            'about.header.title'    => 'Judul Utama Halaman',
            'about.header.subtitle' => 'Subjudul Deskripsi'
        ],
        'Biografi Lengkap (My Story)' => [
            'about.bio.eyebrow' => 'Label Section',
            'about.bio.heading' => 'Judul Biografi',
            'about.bio.p1'      => 'Paragraf 1 (Perkenalan & Kampus)',
            'about.bio.p2'      => 'Paragraf 2 (Latar Belakang & Adaptasi)',
            'about.bio.p3'      => 'Paragraf 3 (Awal Ketertarikan Teknologi)'
        ],
        'Kepribadian (Personality: Who I Am)' => [
            'about.personality.curious.title'     => 'Sifat 1 - Judul',
            'about.personality.curious.desc'      => 'Sifat 1 - Deskripsi',
            'about.personality.persistent.title'  => 'Sifat 2 - Judul',
            'about.personality.persistent.desc'   => 'Sifat 2 - Deskripsi',
            'about.personality.responsible.title' => 'Sifat 3 - Judul',
            'about.personality.responsible.desc'  => 'Sifat 3 - Deskripsi',
            'about.personality.growth.title'      => 'Sifat 4 - Judul',
            'about.personality.growth.desc'       => 'Sifat 4 - Deskripsi'
        ],
        'Prinsip Hidup (What I Believe In)' => [
            'about.values.consistency.title'    => 'Nilai 1 - Judul',
            'about.values.consistency.desc'     => 'Nilai 1 - Deskripsi',
            'about.values.responsibility.title' => 'Nilai 2 - Judul',
            'about.values.responsibility.desc'  => 'Nilai 2 - Deskripsi',
            'about.values.learning.title'       => 'Nilai 3 - Judul',
            'about.values.learning.desc'        => 'Nilai 3 - Deskripsi'
        ],
        'Visi Masa Depan (Looking Ahead)' => [
            'about.vision.eyebrow' => 'Label Section',
            'about.vision.heading' => 'Judul Visi',
            'about.vision.p1'      => 'Paragraf 1 (Target Karir)',
            'about.vision.p2'      => 'Paragraf 2 (Aspirasi Personal & Keluarga)',
            'about.vision.quote'   => 'Kutipan / Quote Pribadi'
        ]
    ],
    'nav' => [
        'Navigasi Atas & Menu Dropdown' => [
            'nav.home'          => 'Menu Beranda',
            'nav.about'         => 'Menu Tentang',
            'nav.journey'       => 'Menu Perjalanan',
            'nav.education'     => 'Menu Pendidikan',
            'nav.learning'      => 'Menu Pembelajaran',
            'nav.blog'          => 'Menu Jurnal',
            'nav.goals'         => 'Menu Tujuan',
            'nav.contact'       => 'Menu Kontak',
            'nav.contactBtn'    => 'Tombol Hubungi Saya',
            'dropdown.download' => 'Opsi Unduh CV'
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Konten Halaman - Dashboard</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #0b0f17;
            color: #c9d1d9;
            margin: 0;
            display: flex;
        }
        aside {
            width: 240px;
            background: #161b22;
            border-right: 1px solid #30363d;
            min-height: 100vh;
            padding: 24px 16px;
            box-sizing: border-box;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        aside h3 { color: #58a6ff; margin-top: 0; padding-left: 8px; }
        nav a {
            display: block;
            color: #8b949e;
            padding: 10px 12px;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 6px;
            font-size: 14px;
        }
        nav a:hover, nav a.active { background: #21262d; color: #f0f6fc; }
        main {
            flex: 1;
            padding: 32px 40px;
            overflow-y: auto;
            max-width: 1100px;
        }
        .tab-bar {
            display: flex;
            gap: 10px;
            border-bottom: 1px solid #30363d;
            margin-bottom: 24px;
            padding-bottom: 8px;
        }
        .tab-item {
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            color: #8b949e;
            font-weight: 500;
            font-size: 14px;
        }
        .tab-item.active {
            background: #1f6feb;
            color: #ffffff;
        }
        .section-card {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 8px;
            padding: 22px;
            margin-bottom: 24px;
        }
        .section-title {
            color: #58a6ff;
            font-size: 16px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #21262d;
        }
        .field-group {
            margin-bottom: 18px;
        }
        .field-label {
            font-size: 13px;
            font-weight: 600;
            color: #f0f6fc;
            margin-bottom: 6px;
            display: flex;
            justify-content: space-between;
        }
        .field-key {
            font-family: monospace;
            color: #8b949e;
            font-weight: normal;
        }
        .dual-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .input-col label {
            display: block;
            font-size: 11px;
            color: #8b949e;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        textarea, input[type="text"] {
            width: 100%;
            padding: 8px 12px;
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 6px;
            color: #c9d1d9;
            box-sizing: border-box;
            font-family: inherit;
            font-size: 13px;
            line-height: 1.5;
        }
        textarea:focus, input[type="text"]:focus {
            border-color: #58a6ff;
            outline: none;
        }
        .btn-save-bar {
            position: sticky;
            bottom: 20px;
            background: #161b22;
            border: 1px solid #30363d;
            padding: 14px 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 24px rgba(0,0,0,0.5);
            margin-top: 20px;
        }
        .btn-save {
            background: #238636;
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-save:hover { background: #2ea043; }
        .alert-success {
            background: rgba(46, 160, 67, 0.15);
            color: #3fb950;
            border: 1px solid rgba(46, 160, 67, 0.4);
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <aside>
        <h3>Admin Panel</h3>
        <nav>
            <a href="index.php">Overview</a>
            <a href="blog.php">Jurnal / Blog</a>
            <a href="messages.php">Pesan Masuk</a>
            <a href="translations.php" class="active">Konten Halaman (ID/EN)</a>
            <hr style="border:0; border-top:1px solid #30363d; margin:16px 0;">
            <a href="../index.php" target="_blank">Lihat Website ↗</a>
            <a href="logout.php" style="color:#f85149;">Logout</a>
        </nav>
    </aside>

    <main>
        <h2 style="margin-top:0;">Editor Konten Halaman Pengunjung</h2>
        <p style="color:#8b949e; margin-bottom:20px;">
            Sunting teks di bawah ini. Versi Bahasa Indonesia dan English akan langsung sinkron ke website dan tersimpan di database Supabase.
        </p>

        <?php if ($success): ?>
            <div class="alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Navigasi Tab -->
        <div class="tab-bar">
            <a href="translations.php?tab=home" class="tab-item <?= $activeTab === 'home' ? 'active' : '' ?>">🏠 Halaman Home</a>
            <a href="translations.php?tab=about" class="tab-item <?= $activeTab === 'about' ? 'active' : '' ?>">👤 Halaman About (Lengkap)</a>
            <a href="translations.php?tab=nav" class="tab-item <?= $activeTab === 'nav' ? 'active' : '' ?>">🧭 Navbar &amp; Menu</a>
        </div>

        <form method="POST">
            <?php if (isset($sections[$activeTab])): ?>
                <?php foreach ($sections[$activeTab] as $secHeading => $fields): ?>
                    <div class="section-card">
                        <div class="section-title"><?= htmlspecialchars($secHeading) ?></div>
                        
                        <?php foreach ($fields as $key => $labelName): 
                            $valId = $idDict[$key] ?? '';
                            $valEn = $enDict[$key] ?? '';
                            $isLong = (strlen($valEn) > 70 || str_contains($key, 'p1') || str_contains($key, 'p2') || str_contains($key, 'p3') || str_contains($key, 'desc') || str_contains($key, 'paragraph'));
                        ?>
                            <div class="field-group">
                                <div class="field-label">
                                    <span><?= htmlspecialchars($labelName) ?></span>
                                    <span class="field-key">data-i18n="<?= htmlspecialchars($key) ?>"</span>
                                </div>
                                <div class="dual-inputs">
                                    <div class="input-col">
                                        <label>🇮🇩 Bahasa Indonesia</label>
                                        <?php if ($isLong): ?>
                                            <textarea name="translations[<?= htmlspecialchars($key) ?>][id]" rows="3"><?= htmlspecialchars($valId) ?></textarea>
                                        <?php else: ?>
                                            <input type="text" name="translations[<?= htmlspecialchars($key) ?>][id]" value="<?= htmlspecialchars($valId) ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="input-col">
                                        <label>🇬🇧 English</label>
                                        <?php if ($isLong): ?>
                                            <textarea name="translations[<?= htmlspecialchars($key) ?>][en]" rows="3"><?= htmlspecialchars($valEn) ?></textarea>
                                        <?php else: ?>
                                            <input type="text" name="translations[<?= htmlspecialchars($key) ?>][en]" value="<?= htmlspecialchars($valEn) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <div class="btn-save-bar">
                    <span style="font-size:13px; color:#8b949e;">Pastikan terjemahan ID &amp; EN terisi rapi sebelum menyimpan.</span>
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </div>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>