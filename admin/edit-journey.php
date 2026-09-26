<?php
$pageTitle = 'Edit Journey';
$topbarTitle = 'Website Content / Journey';

require_once __DIR__ . '/../supabase.php';
require_once __DIR__ . '/partials/header.php';

$enPath       = __DIR__ . '/../lang/en.json';
$idPath       = __DIR__ . '/../lang/id.json';
$statusPath   = __DIR__ . '/../data/journey_status.json';
$timelinePath = __DIR__ . '/../data/journey_timeline.json';

// Inisialisasi status jika file belum ada
if (!file_exists($statusPath)) {
    $defaultStatus = [
        ['icon' => '🖥', 'label_id' => 'Mulai Coding', 'label_en' => 'Started Coding', 'val_id' => '2024', 'val_en' => '2024'],
        ['icon' => '📚', 'label_id' => 'Status Saat Ini', 'label_en' => 'Current Status', 'val_id' => 'Mahasiswa Informatika', 'val_en' => 'Informatics Student'],
        ['icon' => '🚀', 'label_id' => 'Fokus Saat Ini', 'label_en' => 'Current Focus', 'val_id' => 'Full-Stack & Web3', 'val_en' => 'Full-Stack & Web3'],
        ['icon' => '🎯', 'label_id' => 'Target Masa Depan', 'label_en' => 'Future Goal', 'val_id' => 'Software Engineer', 'val_en' => 'Software Engineer']
    ];
    file_put_contents($statusPath, json_encode($defaultStatus, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Inisialisasi timeline jika file belum ada
if (!file_exists($timelinePath)) {
    $defaultTimeline = [
        [
            'badge'    => '2024',
            'title_id' => 'Menemukan Minat Saya',
            'title_en' => 'Discovering My Passion',
            'desc_id'  => 'Pada tahun 2024, saya memulai perjalanan sebagai mahasiswa Informatika di UPN "Veteran" Jawa Timur. Selama tahun ini, saya mempelajari dasar pemrograman melalui C, C++, dan Python sambil membangun pemahaman tentang algoritma.',
            'desc_en'  => 'In 2024, I began my journey as an Informatics student at UPN "Veteran" Jawa Timur. During this year, I learned the fundamentals of programming through C, C++, and Python while building my understanding of algorithms.',
            'learn'    => 'Setiap ahli memulai langkahnya dengan menguasai hal mendasar.'
        ],
        [
            'badge'    => '2025',
            'title_id' => 'Membangun Fondasi yang Kuat',
            'title_en' => 'Building Strong Foundations',
            'desc_id'  => 'Seiring bertambahnya pemahaman, saya mulai menerapkan ilmu yang didapat lewat proyek akademik dan mandiri seperti sistem perpustakaan Java dan desain UI/UX.',
            'desc_en'  => 'As my understanding grew, I started applying what I had learned through academic and personal projects such as Java systems and UI/UX design.',
            'learn'    => 'Kemajuan datang dari latihan nyata, bukan dari kesempurnaan instan.'
        ],
        [
            'badge'    => '2026',
            'title_id' => 'Menciptakan Solusi Nyata',
            'title_en' => 'Creating Real-World Solutions',
            'desc_id'  => 'Tahun 2026 menjadi fase menantang di mana saya membangun portofolio pribadi, website e-commerce, dan sistem silsilah keluarga digital.',
            'desc_en'  => 'In 2026, I challenged myself with more advanced projects. I started building my personal website portfolio and client-based web platforms.',
            'learn'    => 'Teknologi memberi dampak terbesar ketika mampu menyelesaikan masalah nyata.'
        ],
        [
            'badge'    => 'Present',
            'title_id' => 'Terus Bertumbuh',
            'title_en' => 'Still Growing',
            'desc_id'  => 'Saat ini, saya terus memperluas pengetahuan di bidang Full-Stack Development, Web3, UI/UX Design, dan rekayasa perangkat lunak.',
            'desc_en'  => 'Today, I continue expanding my knowledge in Full-Stack Development, Web3, UI/UX Design, and software engineering.',
            'learn'    => 'Pertumbuhan adalah perjalanan berkelanjutan, bukan sekadar garis akhir.'
        ]
    ];
    file_put_contents($timelinePath, json_encode($defaultTimeline, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$statusData   = json_decode(file_get_contents($statusPath), true) ?: [];
$timelineData = json_decode(file_get_contents($timelinePath), true) ?: [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enData = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
    $idData = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];
    $batch = [];

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

    $newStatus = [];
    if (isset($_POST['status_cards']) && is_array($_POST['status_cards'])) {
        foreach ($_POST['status_cards'] as $st) {
            if (!empty(trim($st['label_id'] ?? ''))) {
                $newStatus[] = [
                    'icon'     => trim($st['icon'] ?? '🚀'),
                    'label_id' => trim($st['label_id']),
                    'label_en' => trim($st['label_en'] ?? $st['label_id']),
                    'val_id'   => trim($st['val_id'] ?? ''),
                    'val_en'   => trim($st['val_en'] ?? $st['val_id'])
                ];
            }
        }
    }
    file_put_contents($statusPath, json_encode($newStatus, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $statusData = $newStatus;

    $newTimeline = [];
    if (isset($_POST['timeline']) && is_array($_POST['timeline'])) {
        foreach ($_POST['timeline'] as $t) {
            if (!empty(trim($t['title_id'] ?? ''))) {
                $newTimeline[] = [
                    'badge'    => trim($t['badge'] ?? '🚀 Tahun'),
                    'title_id' => trim($t['title_id']),
                    'title_en' => trim($t['title_en'] ?? $t['title_id']),
                    'desc_id'  => trim($t['desc_id'] ?? ''),
                    'desc_en'  => trim($t['desc_en'] ?? ''),
                    'learn'    => trim($t['learn'] ?? '')
                ];
            }
        }
    }
    file_put_contents($timelinePath, json_encode($newTimeline, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $timelineData = $newTimeline;

    $success = 'Seluruh konten dan kartu Journey berhasil disimpan!';
}

$enDict = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
$idDict = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];

function getVal($dict, $key, $fallback = '') {
    return (!empty($dict[$key])) ? $dict[$key] : $fallback;
}

$homeCards = [
    '2024' => ['badge' => '2024', 'title' => 'index.journey.2024.title', 'desc' => 'index.journey.2024.desc', 'take' => 'index.journey.2024.takeaway', 'def_id' => ['🌱 Menemukan Dunia Pemrograman', 'Perjalanan saya dimulai dengan mempelajari dasar-dasar pemrograman...', 'Setiap ahli memulai dari dasar.'], 'def_en' => ['🌱 Discovering the World of Programming', 'My journey began with learning the fundamentals of programming...', 'Every expert starts with the basics.']],
    '2025' => ['badge' => '2025', 'title' => 'index.journey.2025.title', 'desc' => 'index.journey.2025.desc', 'take' => 'index.journey.2025.takeaway', 'def_id' => ['🚀 Membangun Pengetahuan Melalui Proyek', 'Setelah memahami dasar-dasarnya, saya mulai menerapkan pengetahuan...', 'Belajar menjadi lebih bermakna ketika diterapkan melalui proyek nyata.'], 'def_en' => ['🚀 Building Knowledge Through Projects', 'After understanding the fundamentals, I started applying my knowledge...', 'Learning becomes meaningful when knowledge is applied through real projects.']],
    '2026' => ['badge' => '2026', 'title' => 'index.journey.2026.title', 'desc' => 'index.journey.2026.desc', 'take' => 'index.journey.2026.takeaway', 'def_id' => ['💡 Menciptakan Solusi Dunia Nyata', 'Tahun ini menjadi salah satu tonggak penting dalam perjalanan saya...', 'Pertumbuhan yang sesungguhnya dimulai ketika kita mampu menyelesaikan masalah nyata.'], 'def_en' => ['💡 Creating Real-World Solutions', 'This year marks an important milestone in my journey...', 'Real growth begins when solving real problems for real people.']]
];
?>

<style>
    .journey-container { max-width: 960px; margin: 0 auto; }
    .form-panel-compact { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px 20px; }
    .sticky-control-bar { position: sticky; top: 64px; z-index: 50; background: rgba(19, 25, 38, 0.94); backdrop-filter: blur(10px); margin: -16px -20px 16px -20px; padding: 12px 20px; border-bottom: 1px solid var(--border-color); border-top-left-radius: 12px; border-top-right-radius: 12px; }
    .panel-header-clean { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 10px; }
    .lang-switcher { display: inline-flex; background: #090c13; border: 1px solid rgba(139, 92, 246, 0.3); border-radius: 6px; padding: 2px; gap: 2px; }
    .lang-tab { background: transparent; border: none; color: var(--text-muted); padding: 5px 12px; border-radius: 5px; font-size: 11.5px; font-weight: 700; cursor: pointer; transition: all 0.2s; }
    .lang-tab.active { background: var(--primary); color: #fff; }
    .section-selector-bar { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; background: #090c13; padding: 4px; border-radius: 8px; border: 1px solid var(--border-color); }
    .sec-tab-btn { background: transparent; border: none; color: var(--text-muted); padding: 8px 4px; border-radius: 6px; font-size: 11.5px; font-weight: 600; cursor: pointer; transition: all 0.2s; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sec-tab-btn.active { background: rgba(139, 92, 246, 0.18); color: #c4b5fd; border: 1px solid rgba(139, 92, 246, 0.4); }
    .section-pane { display: none; }
    .section-pane.active { display: block; }
    .card-block-compact { background: #090c13; border: 1px solid var(--border-color); border-radius: 8px; padding: 14px 16px; margin-bottom: 12px; }
    .card-top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; gap: 8px; }
    .badge-year-block { background: rgba(139, 92, 246, 0.18); color: #c4b5fd; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 5px; }
    .card-mini-lang-nav { display: inline-flex; gap: 3px; background: #05080e; padding: 2px; border-radius: 5px; border: 1px solid var(--border-color); }
    .card-mini-btn { background: transparent; border: none; color: var(--text-sub); font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; cursor: pointer; }
    .card-mini-btn.active { background: rgba(139, 92, 246, 0.25); color: #c4b5fd; }
    .number-badge { background: var(--primary); color: #fff; width: 22px; height: 22px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
    .btn-action-card { background: rgba(255, 255, 255, 0.05); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.1); padding: 3px 8px; border-radius: 5px; font-size: 11px; font-weight: 600; cursor: pointer; }
    .btn-action-card:hover { background: rgba(139, 92, 246, 0.2); color: #fff; }
    .btn-delete-card { background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); padding: 3px 8px; border-radius: 5px; font-size: 10.5px; font-weight: 600; cursor: pointer; }
    .btn-delete-card:hover { background: #ef4444; color: #fff; }
    .btn-add-card { background: rgba(139, 92, 246, 0.1); color: #c4b5fd; border: 1px dashed rgba(139, 92, 246, 0.4); padding: 9px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; width: 100%; margin-bottom: 14px; display: flex; align-items: center; justify-content: center; gap: 6px; }
    .btn-add-card:hover { background: rgba(139, 92, 246, 0.2); color: #fff; }
    .field-item { margin-bottom: 10px; }
    .field-item label { display: block; font-size: 11.5px; font-weight: 600; color: #cbd5e1; margin-bottom: 4px; }
    .clean-input { width: 100%; padding: 7px 10px; background: #05080e; border: 1px solid var(--border-color); border-radius: 6px; color: #fff; font-size: 12.5px; font-family: inherit; line-height: 1.45; }
    .clean-input:focus { border-color: var(--primary); outline: none; }
    textarea.clean-input { resize: vertical; min-height: 55px; }
    .grid-2-col { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .grid-flexible-status { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; }
    .bottom-save-bar { margin-top: 20px; padding-top: 14px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; gap: 12px; }
    .btn-save-final { background: var(--primary); color: #fff; border: none; padding: 10px 24px; border-radius: 7px; font-size: 13.5px; font-weight: 600; cursor: pointer; }
    .btn-save-final:hover { opacity: 0.9; }
    .alert-ok { background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 8px 12px; border-radius: 6px; margin-bottom: 12px; font-size: 12px; }
</style>

<div class="journey-container">
    <?php if ($success): ?>
        <div class="alert-ok"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-panel-compact">
        <!-- STICKY CONTROL BAR -->
        <div class="sticky-control-bar">
            <div class="panel-header-clean">
                <h3 style="font-size: 15px; color: #fff; margin:0;">Editor Journey</h3>
                <div class="lang-switcher">
                    <button type="button" class="lang-tab active" id="globalTabId" onclick="switchLang('id')">🇮🇩 ID</button>
                    <button type="button" class="lang-tab" id="globalTabEn" onclick="switchLang('en')">🇬🇧 EN</button>
                </div>
            </div>

            <div class="section-selector-bar">
                <button type="button" class="sec-tab-btn active" onclick="openSection('sec-home', this)">🏠 Cuplikan Home</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-header', this)">🚀 Header &amp; Status</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-timeline', this)">📖 Bab Cerita</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-closing', this)">💡 Titik Balik &amp; Penutup</button>
            </div>
        </div>

        <!-- 1. CUPLIKAN HOME -->
        <div id="sec-home" class="section-pane active">
            <div class="grid-2-col">
                <div class="field-item">
                    <label>Label Atas</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[index.journey.eyebrow][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.journey.eyebrow', 'Perjalanan Saya')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.journey.eyebrow][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.journey.eyebrow', 'My Story')) ?>"></div>
                </div>
                <div class="field-item">
                    <label>Judul Utama</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[index.journey.title][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.journey.title', 'Perjalanan Pertumbuhan Saya')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.journey.title][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.journey.title', 'My Growth Journey')) ?>"></div>
                </div>
            </div>

            <?php foreach ($homeCards as $year => $c): ?>
                <div class="card-block-compact">
                    <span class="badge-year-block">Tahun <?= $c['badge'] ?></span>
                    <div class="field-item">
                        <label>Judul Kartu</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[<?= $c['title'] ?>][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, $c['title'], $c['def_id'][0])) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[<?= $c['title'] ?>][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, $c['title'], $c['def_en'][0])) ?>"></div>
                    </div>
                    <div class="field-item">
                        <label>Deskripsi Kalimat</label>
                        <div class="lang-field lang-id"><textarea name="translations[<?= $c['desc'] ?>][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, $c['desc'], $c['def_id'][1])) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[<?= $c['desc'] ?>][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, $c['desc'], $c['def_en'][1])) ?></textarea></div>
                    </div>
                    <div class="field-item">
                        <label>Pelajaran Utama (Takeaway)</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[<?= $c['take'] ?>][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, $c['take'], $c['def_id'][2])) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[<?= $c['take'] ?>][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, $c['take'], $c['def_en'][2])) ?>"></div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="field-item">
                <label>Teks Tombol CTA Home</label>
                <div class="lang-field lang-id"><input type="text" name="translations[index.journey.button][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.journey.button', 'Jelajahi Perjalanan Saya →')) ?>"></div>
                <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.journey.button][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.journey.button', 'Explore My Complete Journey →')) ?>"></div>
            </div>
        </div>

        <!-- 2. HEADER & KARTU STATUS -->
        <div id="sec-header" class="section-pane">
            <div class="grid-2-col">
                <div class="field-item">
                    <label>Judul Halaman</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[journey.header.title][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'journey.header.title', 'Perjalanan Saya')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[journey.header.title][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'journey.header.title', 'My Journey')) ?>"></div>
                </div>
                <div class="field-item">
                    <label>Subjudul</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[journey.header.subtitle][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'journey.header.subtitle', 'Momen, pengalaman, dan keputusan yang membentuk diri saya hari ini.')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[journey.header.subtitle][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'journey.header.subtitle', 'The moments, experiences, and decisions that have shaped the person I am today.')) ?>"></div>
                </div>
            </div>

            <div class="field-item">
                <label>Paragraf Pengantar Utama</label>
                <div class="lang-field lang-id"><textarea name="translations[journey.header.desc][id]" class="clean-input" rows="3"><?= htmlspecialchars(getVal($idDict, 'journey.header.desc', 'Setiap perjalanan berawal dari satu langkah kecil...')) ?></textarea></div>
                <div class="lang-field lang-en" style="display:none;"><textarea name="translations[journey.header.desc][en]" class="clean-input" rows="3"><?= htmlspecialchars(getVal($enDict, 'journey.header.desc', 'Every journey begins with a single step...')) ?></textarea></div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin: 16px 0 10px;">
                <span style="font-size:13px; font-weight:700; color:#c4b5fd;">Kartu Status Perjalanan</span>
                <button type="button" class="btn-add-card" style="width:auto; margin:0; padding:6px 14px;" onclick="addStatusCard()">➕ Tambah Status</button>
            </div>

            <div id="statusContainer" class="grid-flexible-status">
                <?php foreach ($statusData as $idx => $st): ?>
                    <div class="card-block-compact" id="stCard_<?= $idx ?>">
                        <div class="card-top-bar">
                            <input type="text" name="status_cards[<?= $idx ?>][icon]" class="clean-input" value="<?= htmlspecialchars($st['icon'] ?? '🚀') ?>" style="width:50px; text-align:center; padding:3px 6px;">
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div class="card-mini-lang-nav">
                                    <button type="button" class="card-mini-btn active" onclick="switchCardLang('stCard_<?= $idx ?>', 'id', this)">ID</button>
                                    <button type="button" class="card-mini-btn" onclick="switchCardLang('stCard_<?= $idx ?>', 'en', this)">EN</button>
                                </div>
                                <button type="button" class="btn-delete-card" onclick="removeCard('stCard_<?= $idx ?>')">✕</button>
                            </div>
                        </div>
                        <div class="field-item">
                            <label>Teks Label</label>
                            <div class="card-lang card-lang-id"><input type="text" name="status_cards[<?= $idx ?>][label_id]" class="clean-input" value="<?= htmlspecialchars($st['label_id'] ?? '') ?>" placeholder="Label (ID)" required></div>
                            <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="status_cards[<?= $idx ?>][label_en]" class="clean-input" value="<?= htmlspecialchars($st['label_en'] ?? '') ?>" placeholder="Label (EN)"></div>
                        </div>
                        <div class="field-item">
                            <label>Nilai / Status</label>
                            <div class="card-lang card-lang-id"><input type="text" name="status_cards[<?= $idx ?>][val_id]" class="clean-input" value="<?= htmlspecialchars($st['val_id'] ?? '') ?>" placeholder="Nilai (ID)" required></div>
                            <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="status_cards[<?= $idx ?>][val_en]" class="clean-input" value="<?= htmlspecialchars($st['val_en'] ?? '') ?>" placeholder="Value (EN)"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 3. BAB CERITA (DENGAN NOMOR URUT & TOMBOL NAIK/TURUN) -->
        <div id="sec-timeline" class="section-pane">
            <div class="field-item">
                <label>Judul Section Linimasa</label>
                <div class="lang-field lang-id"><input type="text" name="translations[journey.timeline.heading][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'journey.timeline.heading', 'Setiap Bab Punya Cerita')) ?>"></div>
                <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[journey.timeline.heading][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'journey.timeline.heading', 'Every Chapter Has a Story')) ?>"></div>
            </div>

            <button type="button" class="btn-add-card" onclick="addTimelineChapter()">➕ Tambah Bab Cerita Baru</button>

            <div id="timelineContainer">
                <?php foreach ($timelineData as $idx => $t): ?>
                    <div class="card-block-compact timeline-card-item" id="tCard_<?= $idx ?>">
                        <div class="card-top-bar">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="number-badge timeline-num-label">1</span>
                                <input type="text" name="timeline[<?= $idx ?>][badge]" class="clean-input" value="<?= htmlspecialchars($t['badge'] ?? '🚀 Tahun') ?>" style="width:130px; padding:4px 8px; font-weight:700; color:#c4b5fd;">
                            </div>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div class="card-mini-lang-nav">
                                    <button type="button" class="card-mini-btn active" onclick="switchCardLang('tCard_<?= $idx ?>', 'id', this)">ID</button>
                                    <button type="button" class="card-mini-btn" onclick="switchCardLang('tCard_<?= $idx ?>', 'en', this)">EN</button>
                                </div>
                                <button type="button" class="btn-action-card" onclick="moveCardUp(this)" title="Geser ke Atas">⬆ Naik</button>
                                <button type="button" class="btn-action-card" onclick="moveCardDown(this)" title="Geser ke Bawah">⬇ Turun</button>
                                <button type="button" class="btn-delete-card" onclick="removeCard('tCard_<?= $idx ?>')">✕</button>
                            </div>
                        </div>
                        <div class="field-item">
                            <label>Judul Bab</label>
                            <div class="card-lang card-lang-id"><input type="text" name="timeline[<?= $idx ?>][title_id]" class="clean-input" value="<?= htmlspecialchars($t['title_id'] ?? '') ?>" placeholder="Judul Bab (ID)" required></div>
                            <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="timeline[<?= $idx ?>][title_en]" class="clean-input" value="<?= htmlspecialchars($t['title_en'] ?? '') ?>" placeholder="Chapter Title (EN)"></div>
                        </div>
                        <div class="field-item">
                            <label>Deskripsi Cerita</label>
                            <div class="card-lang card-lang-id"><textarea name="timeline[<?= $idx ?>][desc_id]" class="clean-input" placeholder="Cerita (ID)"><?= htmlspecialchars($t['desc_id'] ?? '') ?></textarea></div>
                            <div class="card-lang card-lang-en" style="display:none;"><textarea name="timeline[<?= $idx ?>][desc_en]" class="clean-input" placeholder="Story (EN)"><?= htmlspecialchars($t['desc_en'] ?? '') ?></textarea></div>
                        </div>
                        <div class="field-item">
                            <label>Pelajaran yang Dipetik / Takeaway (Hanya Bahasa Indonesia)</label>
                            <input type="text" name="timeline[<?= $idx ?>][learn]" class="clean-input" value="<?= htmlspecialchars($t['learn'] ?? '') ?>" placeholder="Pelajaran (ID)">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 4. TITIK BALIK, LOOKING AHEAD & CLOSING REFLECTION -->
        <div id="sec-closing" class="section-pane">
            <div class="field-item">
                <label>Judul Section Titik Balik</label>
                <div class="lang-field lang-id"><input type="text" name="translations[journey.tp.heading][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'journey.tp.heading', 'Momen yang Mengubah Arah Saya')) ?>"></div>
                <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[journey.tp.heading][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'journey.tp.heading', 'The Moments That Changed My Direction')) ?>"></div>
            </div>

            <div class="grid-2-col">
                <div class="card-block-compact">
                    <span class="badge-year-block">Titik Balik 1</span>
                    <div class="field-item" style="margin-top:6px;">
                        <label>Judul</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[journey.tp.card1.title][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'journey.tp.card1.title', 'Menemukan Arah Hidup')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[journey.tp.card1.title][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'journey.tp.card1.title', 'Discovering My Direction')) ?>"></div>
                    </div>
                    <div class="field-item">
                        <label>Momen</label>
                        <div class="lang-field lang-id"><textarea name="translations[journey.tp.card1.desc][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'journey.tp.card1.desc')) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[journey.tp.card1.desc][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'journey.tp.card1.desc')) ?></textarea></div>
                    </div>
                    <div class="field-item">
                        <label>Mengapa Penting</label>
                        <div class="lang-field lang-id"><textarea name="translations[journey.tp.card1.why][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'journey.tp.card1.why')) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[journey.tp.card1.why][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'journey.tp.card1.why')) ?></textarea></div>
                    </div>
                </div>

                <div class="card-block-compact">
                    <span class="badge-year-block">Titik Balik 2</span>
                    <div class="field-item" style="margin-top:6px;">
                        <label>Judul</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[journey.tp.card2.title][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'journey.tp.card2.title', 'Belajar Melampaui Ruang Kelas')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[journey.tp.card2.title][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'journey.tp.card2.title', 'Building Beyond the Classroom')) ?>"></div>
                    </div>
                    <div class="field-item">
                        <label>Momen</label>
                        <div class="lang-field lang-id"><textarea name="translations[journey.tp.card2.desc][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'journey.tp.card2.desc')) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[journey.tp.card2.desc][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'journey.tp.card2.desc')) ?></textarea></div>
                    </div>
                    <div class="field-item">
                        <label>Mengapa Penting</label>
                        <div class="lang-field lang-id"><textarea name="translations[journey.tp.card2.why][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'journey.tp.card2.why')) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[journey.tp.card2.why][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'journey.tp.card2.why')) ?></textarea></div>
                    </div>
                </div>
            </div>

            <div class="card-block-compact" style="margin-top:14px;">
                <span class="badge-year-block">🔭 Bab Selanjutnya (Looking Ahead)</span>
                <div class="grid-2-col" style="margin-top:8px;">
                    <div class="field-item">
                        <label>Label Eyebrow</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[journey.next.eyebrow][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'journey.next.eyebrow', 'Menatap Masa Depan')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[journey.next.eyebrow][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'journey.next.eyebrow', 'Looking Ahead')) ?>"></div>
                    </div>
                    <div class="field-item">
                        <label>Judul Bab Selanjutnya</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[journey.next.heading][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'journey.next.heading', 'Bab Selanjutnya')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[journey.next.heading][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'journey.next.heading', 'The Next Chapter')) ?>"></div>
                    </div>
                </div>
                <div class="field-item">
                    <label>Deskripsi Looking Ahead</label>
                    <div class="lang-field lang-id"><textarea name="translations[journey.next.desc][id]" class="clean-input" rows="3"><?= htmlspecialchars(getVal($idDict, 'journey.next.desc', 'Setiap pengalaman telah mempersiapkan saya untuk melangkah ke bab berikutnya...')) ?></textarea></div>
                    <div class="lang-field lang-en" style="display:none;"><textarea name="translations[journey.next.desc][en]" class="clean-input" rows="3"><?= htmlspecialchars(getVal($enDict, 'journey.next.desc', 'Every experience has prepared me for the next chapter of my journey...')) ?></textarea></div>
                </div>
            </div>

            <div class="card-block-compact" style="margin-top:10px;">
                <span class="badge-year-block">💬 Refleksi Akhir &amp; Kutipan (Closing Reflection)</span>
                <div class="field-item" style="margin-top:8px;">
                    <label>Label Eyebrow</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[journey.closing.eyebrow][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'journey.closing.eyebrow', 'Refleksi Akhir')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[journey.closing.eyebrow][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'journey.closing.eyebrow', 'Closing Reflection')) ?>"></div>
                </div>
                <div class="field-item">
                    <label>Paragraf Refleksi Penutup</label>
                    <div class="lang-field lang-id"><textarea name="translations[journey.closing.desc][id]" class="clean-input" rows="4"><?= htmlspecialchars(getVal($idDict, 'journey.closing.desc', 'Melihat ke belakang, setiap tantangan, kesuksesan, dan kegagalan telah berkontribusi membentuk diri saya hari ini...')) ?></textarea></div>
                    <div class="lang-field lang-en" style="display:none;"><textarea name="translations[journey.closing.desc][en]" class="clean-input" rows="4"><?= htmlspecialchars(getVal($enDict, 'journey.closing.desc', 'Looking back, every challenge, every success, and every failure has contributed to the person I am today...')) ?></textarea></div>
                </div>
                <div class="grid-2-col">
                    <div class="field-item">
                        <label>Kutipan Refleksi (Quote)</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[journey.closing.quote][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'journey.closing.quote', 'The best chapters are still waiting to be written.')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[journey.closing.quote][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'journey.closing.quote', 'The best chapters are still waiting to be written.')) ?>"></div>
                    </div>
                    <div class="field-item">
                        <label>Teks Tombol CTA Penutup</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[journey.closing.btn][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'journey.closing.btn', 'Lihat Langkah Selanjutnya →')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[journey.closing.btn][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'journey.closing.btn', "See What's Next →")) ?>"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOMBOL SIMPAN DI PALING BAWAH -->
        <div class="bottom-save-bar">
            <a href="edit-education.php" style="color: var(--text-muted); text-decoration: none; font-size: 12.5px; font-weight: 600;">
                🎓 Lanjut ke Education →
            </a>
            <button type="submit" class="btn-save-final">Simpan Perubahan Journey</button>
        </div>
    </form>
</div>

<script>
let currentLang = 'id';

function updateTimelineNumbers() {
    document.querySelectorAll('#timelineContainer .timeline-card-item').forEach((card, idx) => {
        const badge = card.querySelector('.timeline-num-label');
        if (badge) badge.textContent = idx + 1;
    });
}

document.addEventListener('DOMContentLoaded', updateTimelineNumbers);

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
    if (el) {
        el.remove();
        updateTimelineNumbers();
    }
}

function moveCardUp(btn) {
    const card = btn.closest('.card-block-compact');
    if (card && card.previousElementSibling) {
        card.parentNode.insertBefore(card, card.previousElementSibling);
        updateTimelineNumbers();
    }
}

function moveCardDown(btn) {
    const card = btn.closest('.card-block-compact');
    if (card && card.nextElementSibling) {
        card.parentNode.insertBefore(card.nextElementSibling, card);
        updateTimelineNumbers();
    }
}

function addStatusCard() {
    const container = document.getElementById('statusContainer');
    const idx = Date.now();
    const isEn = (currentLang === 'en');

    const html = `
        <div class="card-block-compact" id="stCard_${idx}">
            <div class="card-top-bar">
                <input type="text" name="status_cards[${idx}][icon]" class="clean-input" value="⚡" style="width:50px; text-align:center; padding:3px 6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <div class="card-mini-lang-nav">
                        <button type="button" class="card-mini-btn ${!isEn ? 'active' : ''}" onclick="switchCardLang('stCard_${idx}', 'id', this)">ID</button>
                        <button type="button" class="card-mini-btn ${isEn ? 'active' : ''}" onclick="switchCardLang('stCard_${idx}', 'en', this)">EN</button>
                    </div>
                    <button type="button" class="btn-delete-card" onclick="removeCard('stCard_${idx}')">✕</button>
                </div>
            </div>
            <div class="field-item">
                <label>Teks Label</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="status_cards[${idx}][label_id]" class="clean-input" placeholder="Label (ID)" required></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="status_cards[${idx}][label_en]" class="clean-input" placeholder="Label (EN)"></div>
            </div>
            <div class="field-item">
                <label>Nilai / Status</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="status_cards[${idx}][val_id]" class="clean-input" placeholder="Nilai (ID)" required></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="status_cards[${idx}][val_en]" class="clean-input" placeholder="Value (EN)"></div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function addTimelineChapter() {
    const container = document.getElementById('timelineContainer');
    const idx = Date.now();
    const isEn = (currentLang === 'en');

    const html = `
        <div class="card-block-compact timeline-card-item" id="tCard_${idx}">
            <div class="card-top-bar">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="number-badge timeline-num-label">1</span>
                    <input type="text" name="timeline[${idx}][badge]" class="clean-input" value="💡 Tahun" style="width:130px; padding:4px 8px; font-weight:700; color:#c4b5fd;">
                </div>
                <div style="display:flex; align-items:center; gap:6px;">
                    <div class="card-mini-lang-nav">
                        <button type="button" class="card-mini-btn ${!isEn ? 'active' : ''}" onclick="switchCardLang('tCard_${idx}', 'id', this)">ID</button>
                        <button type="button" class="card-mini-btn ${isEn ? 'active' : ''}" onclick="switchCardLang('tCard_${idx}', 'en', this)">EN</button>
                    </div>
                    <button type="button" class="btn-action-card" onclick="moveCardUp(this)" title="Geser ke Atas">⬆ Naik</button>
                    <button type="button" class="btn-action-card" onclick="moveCardDown(this)" title="Geser ke Bawah">⬇ Turun</button>
                    <button type="button" class="btn-delete-card" onclick="removeCard('tCard_${idx}')">✕</button>
                </div>
            </div>
            <div class="field-item">
                <label>Judul Bab</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="timeline[${idx}][title_id]" class="clean-input" placeholder="Judul Bab (ID)" required></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="timeline[${idx}][title_en]" class="clean-input" placeholder="Chapter Title (EN)"></div>
            </div>
            <div class="field-item">
                <label>Deskripsi Cerita</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><textarea name="timeline[${idx}][desc_id]" class="clean-input" placeholder="Cerita (ID)"></textarea></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'none' : 'block'};"><textarea name="timeline[${idx}][desc_en]" class="clean-input" placeholder="Story (EN)"></textarea></div>
            </div>
            <div class="field-item">
                <label>Pelajaran yang Dipetik / Takeaway (Hanya Bahasa Indonesia)</label>
                <input type="text" name="timeline[${idx}][learn]" class="clean-input" placeholder="Pelajaran (ID)">
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    updateTimelineNumbers();
}
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>