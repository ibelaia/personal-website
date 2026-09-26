<?php
$pageTitle = 'Edit Education';
$topbarTitle = 'Website Content / Education';

require_once __DIR__ . '/../supabase.php';
require_once __DIR__ . '/partials/header.php';

$enPath  = __DIR__ . '/../lang/en.json';
$idPath  = __DIR__ . '/../lang/id.json';
$eduPath = __DIR__ . '/../data/education_cards.json';

// Baca data JSON saat ini
$eduData = file_exists($eduPath) ? (json_decode(file_get_contents($eduPath), true) ?: []) : [];

$defaultHigher = [
    [
        'period'     => '📍 2024–Present',
        'title_id'   => 'Universitas',
        'title_en'   => 'University',
        'school_id'  => 'UPN "Veteran" Jawa Timur — Sarjana Informatika',
        'school_en'  => 'UPN "Veteran" Jawa Timur — Bachelor of Informatics',
        'desc_id'    => 'Perkuliahan memperkenalkan saya pada berbagai cabang ilmu komputer, termasuk rekayasa perangkat lunak, algoritma, basis data, pengembangan web, dan banyak bidang lain yang terus memperkokoh fondasi teknis saya.',
        'desc_en'    => 'University has introduced me to various fields of computer science, including software engineering, algorithms, databases, web development, and many other areas that continue to shape my technical foundation.',
        'highlights' => 'Full-Stack Development, Web Development, Web3 Development, UI/UX Design, Software Engineering',
        'projects'   => 'Personal Website, Portfolio Website, Library Management System, Client-Based Web Projects, UI/UX Design, Full-Stack Development, Web3 Development',
        'summary_id' => 'Bagi saya, universitas bukan hanya tempat meraih gelar—tetapi tempat mengubah ilmu menjadi pengalaman nyata, menghadapi tantangan baru, dan mempersiapkan diri berkarier di industri teknologi.',
        'summary_en' => 'Today, university is more than just earning a degree—it is where I transform knowledge into practical experience, embrace new challenges, and prepare myself for a career in the technology industry.'
    ]
];

if (empty($eduData['higher_education'])) {
    $eduData['higher_education'] = $defaultHigher;
}

$schoolsData = $eduData['schools'] ?? [];
$higherData  = $eduData['higher_education'];

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enData = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
    $idData = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];
    $batch  = [];

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

    $newSchools = [];
    if (isset($_POST['schools']) && is_array($_POST['schools'])) {
        foreach ($_POST['schools'] as $s) {
            if (!empty(trim($s['level_id'] ?? ''))) {
                $newSchools[] = [
                    'period'     => trim($s['period'] ?? '📍 Tahun'),
                    'level_id'   => trim($s['level_id']),
                    'level_en'   => trim($s['level_en'] ?? $s['level_id']),
                    'school_id'  => trim($s['school_id'] ?? ''),
                    'school_en'  => trim($s['school_en'] ?? $s['school_id']),
                    'desc_id'    => trim($s['desc_id'] ?? ''),
                    'desc_en'    => trim($s['desc_en'] ?? ''),
                    'highlights' => trim($s['highlights'] ?? '')
                ];
            }
        }
    }

    $newHigher = [];
    if (isset($_POST['higher_education']) && is_array($_POST['higher_education'])) {
        foreach ($_POST['higher_education'] as $h) {
            if (!empty(trim($h['title_id'] ?? ''))) {
                $newHigher[] = [
                    'period'     => trim($h['period'] ?? '📍 Tahun'),
                    'title_id'   => trim($h['title_id']),
                    'title_en'   => trim($h['title_en'] ?? $h['title_id']),
                    'school_id'  => trim($h['school_id'] ?? ''),
                    'school_en'  => trim($h['school_en'] ?? $h['school_id']),
                    'desc_id'    => trim($h['desc_id'] ?? ''),
                    'desc_en'    => trim($h['desc_en'] ?? ''),
                    'highlights' => trim($h['highlights'] ?? ''),
                    'projects'   => trim($h['projects'] ?? ''),
                    'summary_id' => trim($h['summary_id'] ?? ''),
                    'summary_en' => trim($h['summary_en'] ?? '')
                ];
            }
        }
    }

    $eduData['schools']          = $newSchools;
    $eduData['higher_education'] = !empty($newHigher) ? $newHigher : $defaultHigher;
    file_put_contents($eduPath, json_encode($eduData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $schoolsData = $eduData['schools'];
    $higherData  = $eduData['higher_education'];
    $success = 'Perubahan Pendidikan berhasil disimpan!';
}

$enDict = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
$idDict = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];

function getVal($dict, $key, $fallback = '') {
    return (!empty($dict[$key])) ? $dict[$key] : $fallback;
}

$homeEduCards = [
    'sd' => [
        'icon'   => '🏫',
        'badge'  => 'Sekolah Dasar',
        'title'  => 'index.edu.sd.title',
        'desc'   => 'index.edu.sd.desc',
        'def_id' => ['Sekolah Dasar', 'SD Methodist 5 Medan & SD Bintang Laut'],
        'def_en' => ['Primary School', 'SD Methodist 5 Medan & SD Bintang Laut']
    ],
    'smp' => [
        'icon'   => '🏫',
        'badge'  => 'Sekolah Menengah Pertama',
        'title'  => 'index.edu.smp.title',
        'desc'   => 'index.edu.smp.desc',
        'def_id' => ['Sekolah Menengah Pertama', 'SMP Swasta Bintang Laut, Nias Selatan'],
        'def_en' => ['Junior High School', 'SMP Swasta Bintang Laut, South Nias']
    ],
    'sma' => [
        'icon'   => '🏫',
        'badge'  => 'Sekolah Menengah Atas',
        'title'  => 'index.edu.sma.title',
        'desc'   => 'index.edu.sma.desc',
        'def_id' => ['Sekolah Menengah Atas', 'SMA Negeri 1 Teluk Dalam · Peraih Medali Olimpiade Nasional'],
        'def_en' => ['Senior High School', 'SMA Negeri 1 Teluk Dalam · National Olympiad Medalist']
    ],
    'univ' => [
        'icon'   => '🎓',
        'badge'  => 'Universitas',
        'title'  => 'index.edu.univ.title',
        'desc'   => 'index.edu.univ.desc',
        'def_id' => ['Universitas', 'UPN "Veteran" Jawa Timur · Sarjana Informatika'],
        'def_en' => ['University', 'UPN "Veteran" Jawa Timur · Bachelor of Informatics']
    ]
];
?>

<style>
    .edu-container { max-width: 960px; margin: 0 auto; }
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
    .btn-add-card { background: rgba(139, 92, 246, 0.1); color: #c4b5fd; border: 1px dashed rgba(139, 92, 246, 0.4); padding: 10px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; width: 100%; margin-bottom: 14px; display: flex; align-items: center; justify-content: center; gap: 6px; }
    .btn-add-card:hover { background: rgba(139, 92, 246, 0.2); color: #fff; }
    .field-item { margin-bottom: 10px; }
    .field-item label { display: block; font-size: 11.5px; font-weight: 600; color: #cbd5e1; margin-bottom: 4px; }
    .clean-input { width: 100%; padding: 7px 10px; background: #05080e; border: 1px solid var(--border-color); border-radius: 6px; color: #fff; font-size: 12.5px; font-family: inherit; line-height: 1.45; }
    .clean-input:focus { border-color: var(--primary); outline: none; }
    textarea.clean-input { resize: vertical; min-height: 55px; }
    .grid-2-col { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .bottom-save-bar { margin-top: 20px; padding-top: 14px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; gap: 12px; }
    .btn-save-final { background: var(--primary); color: #fff; border: none; padding: 10px 24px; border-radius: 7px; font-size: 13.5px; font-weight: 600; cursor: pointer; }
    .btn-save-final:hover { opacity: 0.9; }
    .alert-ok { background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 8px 12px; border-radius: 6px; margin-bottom: 12px; font-size: 12px; }
</style>

<div class="edu-container">
    <?php if ($success): ?>
        <div class="alert-ok"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-panel-compact">
        <div class="sticky-control-bar">
            <div class="panel-header-clean">
                <h3 style="font-size: 15px; color: #fff; margin:0;">Editor Education</h3>
                <div class="lang-switcher">
                    <button type="button" class="lang-tab active" id="globalTabId" onclick="switchLang('id')">🇮🇩 ID</button>
                    <button type="button" class="lang-tab" id="globalTabEn" onclick="switchLang('en')">🇬🇧 EN</button>
                </div>
            </div>

            <div class="section-selector-bar">
                <button type="button" class="sec-tab-btn active" onclick="openSection('sec-home', this)">🏠 Cuplikan Home</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-timeline', this)">🏫 Linimasa Sekolah</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-univ', this)">🎓 Universitas &amp; Karier</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-beyond', this)">💡 Belajar Mandiri</button>
            </div>
        </div>

        <!-- 1. CUPLIKAN HOME -->
        <div id="sec-home" class="section-pane active">
            <div class="grid-2-col">
                <div class="field-item">
                    <label>Label Atas (Eyebrow)</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[index.edu.eyebrow][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.edu.eyebrow', 'Perjalanan Akademik')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.edu.eyebrow][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.edu.eyebrow', 'Academic Background')) ?>"></div>
                </div>
                <div class="field-item">
                    <label>Judul Section</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[index.edu.title][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.edu.title', 'Pendidikan')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.edu.title][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.edu.title', 'Education')) ?>"></div>
                </div>
            </div>

            <div class="field-item">
                <label>Deskripsi Pengantar Section</label>
                <div class="lang-field lang-id"><textarea name="translations[index.edu.desc][id]" class="clean-input" rows="2"><?= htmlspecialchars(getVal($idDict, 'index.edu.desc', 'Membangun pengetahuan melalui setiap tahap perjalanan akademik saya.')) ?></textarea></div>
                <div class="lang-field lang-en" style="display:none;"><textarea name="translations[index.edu.desc][en]" class="clean-input" rows="2"><?= htmlspecialchars(getVal($enDict, 'index.edu.desc', 'Building knowledge through every stage of my academic journey.')) ?></textarea></div>
            </div>

            <div style="font-size:12.5px; font-weight:700; color:#c4b5fd; margin: 16px 0 8px;">Kartu Cuplikan Pendidikan (Beranda)</div>
            <div class="grid-2-col">
                <?php foreach ($homeEduCards as $key => $card): ?>
                    <div class="card-block-compact">
                        <span class="badge-year-block"><?= $card['icon'] ?> <?= $card['badge'] ?></span>
                        <div class="field-item" style="margin-top:6px;">
                            <label>Judul Kartu</label>
                            <div class="lang-field lang-id"><input type="text" name="translations[<?= $card['title'] ?>][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, $card['title'], $card['def_id'][0])) ?>"></div>
                            <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[<?= $card['title'] ?>][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, $card['title'], $card['def_en'][0])) ?>"></div>
                        </div>
                        <div class="field-item">
                            <label>Keterangan / Nama Sekolah</label>
                            <div class="lang-field lang-id"><input type="text" name="translations[<?= $card['desc'] ?>][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, $card['desc'], $card['def_id'][1])) ?>"></div>
                            <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[<?= $card['desc'] ?>][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, $card['desc'], $card['def_en'][1])) ?>"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="field-item" style="margin-top:10px;">
                <label>Teks Tombol CTA Beranda</label>
                <div class="lang-field lang-id"><input type="text" name="translations[index.edu.button][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.edu.button', 'Jelajahi Pendidikan Saya →')) ?>"></div>
                <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.edu.button][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.edu.button', 'Explore My Education →')) ?>"></div>
            </div>
        </div>

        <!-- 2. LINIMASA SEKOLAH DINAMIS -->
        <div id="sec-timeline" class="section-pane">
            <div class="grid-2-col">
                <div class="field-item">
                    <label>Judul Halaman Education</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[edu.header.title][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'edu.header.title', 'Pendidikan')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[edu.header.title][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'edu.header.title', 'Education')) ?>"></div>
                </div>
                <div class="field-item">
                    <label>Subjudul Pengantar</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[edu.header.subtitle][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'edu.header.subtitle', 'Setiap tempat belajar membentuk pengetahuan dan karakter saya.')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[edu.header.subtitle][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'edu.header.subtitle', 'Every place I studied became part of my journey, shaping not only my knowledge but also my character and ambitions.')) ?>"></div>
                </div>
            </div>

            <button type="button" class="btn-add-card" onclick="addSchoolCard()">➕ Tambah Jenjang Sekolah Baru</button>

            <div id="schoolsContainer">
                <?php foreach ($schoolsData as $idx => $s): ?>
                    <div class="card-block-compact school-card-item" id="schCard_<?= $idx ?>">
                        <div class="card-top-bar">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="number-badge card-num-label">1</span>
                                <input type="text" name="schools[<?= $idx ?>][period]" class="clean-input" value="<?= htmlspecialchars($s['period'] ?? '📍 Tahun') ?>" style="width:130px; padding:4px 8px; font-weight:700; color:#c4b5fd;">
                            </div>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div class="card-mini-lang-nav">
                                    <button type="button" class="card-mini-btn active" onclick="switchCardLang('schCard_<?= $idx ?>', 'id', this)">ID</button>
                                    <button type="button" class="card-mini-btn" onclick="switchCardLang('schCard_<?= $idx ?>', 'en', this)">EN</button>
                                </div>
                                <button type="button" class="btn-action-card" onclick="moveCardUp(this)" title="Geser ke Atas">⬆ Naik</button>
                                <button type="button" class="btn-action-card" onclick="moveCardDown(this)" title="Geser ke Bawah">⬇ Turun</button>
                                <button type="button" class="btn-delete-card" onclick="removeCard('schCard_<?= $idx ?>')">✕</button>
                            </div>
                        </div>

                        <div class="grid-2-col">
                            <div class="field-item">
                                <label>Tingkat / Jenjang</label>
                                <div class="card-lang card-lang-id"><input type="text" name="schools[<?= $idx ?>][level_id]" class="clean-input" value="<?= htmlspecialchars($s['level_id'] ?? '') ?>" placeholder="Tingkat (ID)" required></div>
                                <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="schools[<?= $idx ?>][level_en]" class="clean-input" value="<?= htmlspecialchars($s['level_en'] ?? '') ?>" placeholder="Level (EN)"></div>
                            </div>
                            <div class="field-item">
                                <label>Nama Sekolah</label>
                                <div class="card-lang card-lang-id"><input type="text" name="schools[<?= $idx ?>][school_id]" class="clean-input" value="<?= htmlspecialchars($s['school_id'] ?? '') ?>" placeholder="Nama Sekolah (ID)" required></div>
                                <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="schools[<?= $idx ?>][school_en]" class="clean-input" value="<?= htmlspecialchars($s['school_en'] ?? '') ?>" placeholder="School Name (EN)"></div>
                            </div>
                        </div>

                        <div class="field-item">
                            <label>Deskripsi Cerita</label>
                            <div class="card-lang card-lang-id"><textarea name="schools[<?= $idx ?>][desc_id]" class="clean-input" placeholder="Deskripsi (ID)"><?= htmlspecialchars($s['desc_id'] ?? '') ?></textarea></div>
                            <div class="card-lang card-lang-en" style="display:none;"><textarea name="schools[<?= $idx ?>][desc_en]" class="clean-input" placeholder="Description (EN)"><?= htmlspecialchars($s['desc_en'] ?? '') ?></textarea></div>
                        </div>

                        <div class="field-item">
                            <label>Sorotan Utama / Highlights (Pisahkan koma)</label>
                            <input type="text" name="schools[<?= $idx ?>][highlights]" class="clean-input" value="<?= htmlspecialchars($s['highlights'] ?? '') ?>" placeholder="Misal: Matematika, Fisika, Logika">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 3. UNIVERSITAS & PENDIDIKAN TINGGI / KARIER -->
        <div id="sec-univ" class="section-pane">
            <div style="margin-bottom:12px;">
                <span style="font-size:12.5px; font-weight:700; color:#c4b5fd;">Riwayat Pendidikan Tinggi, Magister (S2), &amp; Karier</span>
            </div>

            <button type="button" class="btn-add-card" onclick="addHigherEduCard()">➕ Tambah Jenjang Kuliah / S2 / Karier Baru</button>

            <div id="higherEduContainer">
                <?php foreach ($higherData as $idx => $h): ?>
                    <div class="card-block-compact higher-card-item" id="hCard_<?= $idx ?>">
                        <div class="card-top-bar">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="number-badge higher-num-label">1</span>
                                <input type="text" name="higher_education[<?= $idx ?>][period]" class="clean-input" value="<?= htmlspecialchars($h['period'] ?? '📍 2024–Present') ?>" style="width:150px; padding:4px 8px; font-weight:700; color:#c4b5fd;">
                            </div>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div class="card-mini-lang-nav">
                                    <button type="button" class="card-mini-btn active" onclick="switchCardLang('hCard_<?= $idx ?>', 'id', this)">ID</button>
                                    <button type="button" class="card-mini-btn" onclick="switchCardLang('hCard_<?= $idx ?>', 'en', this)">EN</button>
                                </div>
                                <button type="button" class="btn-action-card" onclick="moveCardUp(this)" title="Geser ke Atas">⬆ Naik</button>
                                <button type="button" class="btn-action-card" onclick="moveCardDown(this)" title="Geser ke Bawah">⬇ Turun</button>
                                <button type="button" class="btn-delete-card" onclick="removeCard('hCard_<?= $idx ?>')">✕</button>
                            </div>
                        </div>

                        <div class="grid-2-col">
                            <div class="field-item">
                                <label>Gelar / Jenjang / Posisi</label>
                                <div class="card-lang card-lang-id"><input type="text" name="higher_education[<?= $idx ?>][title_id]" class="clean-input" value="<?= htmlspecialchars($h['title_id'] ?? '') ?>" placeholder="Misal: Universitas (Sarjana S1)" required></div>
                                <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="higher_education[<?= $idx ?>][title_en]" class="clean-input" value="<?= htmlspecialchars($h['title_en'] ?? '') ?>" placeholder="e.g. University (Undergraduate)"></div>
                            </div>
                            <div class="field-item">
                                <label>Nama Kampus / Institusi / Perusahaan</label>
                                <div class="card-lang card-lang-id"><input type="text" name="higher_education[<?= $idx ?>][school_id]" class="clean-input" value="<?= htmlspecialchars($h['school_id'] ?? '') ?>" placeholder="Nama Kampus / Perusahaan (ID)" required></div>
                                <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="higher_education[<?= $idx ?>][school_en]" class="clean-input" value="<?= htmlspecialchars($h['school_en'] ?? '') ?>" placeholder="Institution Name (EN)"></div>
                            </div>
                        </div>

                        <div class="field-item">
                            <label>Deskripsi Masa Kuliah / Pekerjaan</label>
                            <div class="card-lang card-lang-id"><textarea name="higher_education[<?= $idx ?>][desc_id]" class="clean-input" rows="3" placeholder="Deskripsi pengalaman (ID)"><?= htmlspecialchars($h['desc_id'] ?? '') ?></textarea></div>
                            <div class="card-lang card-lang-en" style="display:none;"><textarea name="higher_education[<?= $idx ?>][desc_en]" class="clean-input" rows="3" placeholder="Experience description (EN)"><?= htmlspecialchars($h['desc_en'] ?? '') ?></textarea></div>
                        </div>

                        <div class="field-item">
                            <label>Sorotan Bidang / Key Highlights (Pisahkan koma)</label>
                            <input type="text" name="higher_education[<?= $idx ?>][highlights]" class="clean-input" value="<?= htmlspecialchars($h['highlights'] ?? '') ?>" placeholder="Full-Stack Development, Web3, Software Engineering">
                        </div>

                        <div class="field-item">
                            <label>Proyek Nyata / Portofolio (Pisahkan koma)</label>
                            <input type="text" name="higher_education[<?= $idx ?>][projects]" class="clean-input" value="<?= htmlspecialchars($h['projects'] ?? '') ?>" placeholder="Personal Website, E-commerce, Library System">
                        </div>

                        <div class="field-item">
                            <label>Ringkasan Nilai &amp; Filosofi Pengalaman</label>
                            <div class="card-lang card-lang-id"><textarea name="higher_education[<?= $idx ?>][summary_id]" class="clean-input" rows="2" placeholder="Filosofi (ID)"><?= htmlspecialchars($h['summary_id'] ?? '') ?></textarea></div>
                            <div class="card-lang card-lang-en" style="display:none;"><textarea name="higher_education[<?= $idx ?>][summary_en]" class="clean-input" rows="2" placeholder="Summary & takeaways (EN)"><?= htmlspecialchars($h['summary_en'] ?? '') ?></textarea></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 4. BELAJAR MANDIRI (BEYOND THE CLASSROOM) -->
        <div id="sec-beyond" class="section-pane">
            <div class="card-block-compact">
                <span class="badge-year-block">💡 Beyond the Classroom</span>
                <div class="grid-2-col" style="margin-top:8px;">
                    <div class="field-item">
                        <label>Label Eyebrow</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[edu.beyond.eyebrow][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'edu.beyond.eyebrow', 'Melampaui Ruang Kelas')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[edu.beyond.eyebrow][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'edu.beyond.eyebrow', 'Beyond the Classroom')) ?>"></div>
                    </div>
                    <div class="field-item">
                        <label>Judul Bagian</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[edu.beyond.heading][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'edu.beyond.heading', 'Melanjutkan Proses Belajar')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[edu.beyond.heading][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'edu.beyond.heading', 'Continuing My Learning')) ?>"></div>
                    </div>
                </div>

                <div class="field-item">
                    <label>Paragraf Pengantar</label>
                    <div class="lang-field lang-id"><textarea name="translations[edu.beyond.p2][id]" class="clean-input" rows="3"><?= htmlspecialchars(getVal($idDict, 'edu.beyond.p2', 'Di luar kampus, saya meluangkan waktu belajar lewat kursus online, dokumentasi teknis, dan proyek mandiri...')) ?></textarea></div>
                    <div class="lang-field lang-en" style="display:none;"><textarea name="translations[edu.beyond.p2][en]" class="clean-input" rows="3"><?= htmlspecialchars(getVal($enDict, 'edu.beyond.p2', 'Beyond university, I dedicate time to learning through online courses, technical documentation, and personal projects...')) ?></textarea></div>
                </div>

                <div class="grid-2-col">
                    <div class="field-item">
                        <label>Kutipan (Quote)</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[edu.beyond.quote][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'edu.beyond.quote', 'Pendidikan bukan hanya soal meraih gelar—tetapi tentang membangun pola pikir untuk terus belajar seumur hidup.')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[edu.beyond.quote][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'edu.beyond.quote', 'Education is not only about earning degrees—it is about building the mindset to keep learning for a lifetime.')) ?>"></div>
                    </div>
                    <div class="field-item">
                        <label>Teks Tombol CTA</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[edu.beyond.btn][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'edu.beyond.btn', 'Lihat Apa yang Saya Pelajari →')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[edu.beyond.btn][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'edu.beyond.btn', "Discover What I'm Learning →")) ?>"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOMBOL SIMPAN DI PALING BAWAH -->
        <div class="bottom-save-bar">
            <a href="edit-achievements.php" style="color: var(--text-muted); text-decoration: none; font-size: 12.5px; font-weight: 600;">
                🏆 Lanjut ke Achievements →
            </a>
            <button type="submit" class="btn-save-final">Simpan Perubahan Education</button>
        </div>
    </form>
</div>

<script>
let currentLang = 'id';

function updateCardNumbers() {
    document.querySelectorAll('#schoolsContainer .school-card-item').forEach((card, idx) => {
        const badge = card.querySelector('.card-num-label');
        if (badge) badge.textContent = idx + 1;
    });
    document.querySelectorAll('#higherEduContainer .higher-card-item').forEach((card, idx) => {
        const badge = card.querySelector('.higher-num-label');
        if (badge) badge.textContent = idx + 1;
    });
}

document.addEventListener('DOMContentLoaded', updateCardNumbers);

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
        updateCardNumbers();
    }
}

function moveCardUp(btn) {
    const card = btn.closest('.card-block-compact');
    if (card && card.previousElementSibling) {
        card.parentNode.insertBefore(card, card.previousElementSibling);
        updateCardNumbers();
    }
}

function moveCardDown(btn) {
    const card = btn.closest('.card-block-compact');
    if (card && card.nextElementSibling) {
        card.parentNode.insertBefore(card.nextElementSibling, card);
        updateCardNumbers();
    }
}

function addSchoolCard() {
    const container = document.getElementById('schoolsContainer');
    const idx = Date.now();
    const isEn = (currentLang === 'en');

    const html = `
        <div class="card-block-compact school-card-item" id="schCard_${idx}">
            <div class="card-top-bar">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="number-badge card-num-label">1</span>
                    <input type="text" name="schools[${idx}][period]" class="clean-input" value="📍 Tahun" style="width:130px; padding:4px 8px; font-weight:700; color:#c4b5fd;">
                </div>
                <div style="display:flex; align-items:center; gap:6px;">
                    <div class="card-mini-lang-nav">
                        <button type="button" class="card-mini-btn ${!isEn ? 'active' : ''}" onclick="switchCardLang('schCard_${idx}', 'id', this)">ID</button>
                        <button type="button" class="card-mini-btn ${isEn ? 'active' : ''}" onclick="switchCardLang('schCard_${idx}', 'en', this)">EN</button>
                    </div>
                    <button type="button" class="btn-action-card" onclick="moveCardUp(this)" title="Geser ke Atas">⬆ Naik</button>
                    <button type="button" class="btn-action-card" onclick="moveCardDown(this)" title="Geser ke Bawah">⬇ Turun</button>
                    <button type="button" class="btn-delete-card" onclick="removeCard('schCard_${idx}')">✕</button>
                </div>
            </div>
            <div class="grid-2-col">
                <div class="field-item">
                    <label>Tingkat / Jenjang</label>
                    <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="schools[${idx}][level_id]" class="clean-input" placeholder="Tingkat (ID)" required></div>
                    <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="schools[${idx}][level_en]" class="clean-input" placeholder="Level (EN)"></div>
                </div>
                <div class="field-item">
                    <label>Nama Sekolah</label>
                    <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="schools[${idx}][school_id]" class="clean-input" placeholder="Nama Sekolah (ID)" required></div>
                    <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="schools[${idx}][school_en]" class="clean-input" placeholder="School Name (EN)"></div>
                </div>
            </div>
            <div class="field-item">
                <label>Deskripsi Cerita</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><textarea name="schools[${idx}][desc_id]" class="clean-input" placeholder="Deskripsi (ID)"></textarea></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><textarea name="schools[${idx}][desc_en]" class="clean-input" placeholder="Description (EN)"></textarea></div>
            </div>
            <div class="field-item">
                <label>Sorotan Utama / Highlights (Pisahkan koma)</label>
                <input type="text" name="schools[${idx}][highlights]" class="clean-input" placeholder="Sorotan 1, Sorotan 2">
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    updateCardNumbers();
}

function addHigherEduCard() {
    const container = document.getElementById('higherEduContainer');
    const idx = Date.now();
    const isEn = (currentLang === 'en');

    const html = `
        <div class="card-block-compact higher-card-item" id="hCard_${idx}">
            <div class="card-top-bar">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span class="number-badge higher-num-label">1</span>
                    <input type="text" name="higher_education[${idx}][period]" class="clean-input" value="📍 Tahun" style="width:150px; padding:4px 8px; font-weight:700; color:#c4b5fd;">
                </div>
                <div style="display:flex; align-items:center; gap:6px;">
                    <div class="card-mini-lang-nav">
                        <button type="button" class="card-mini-btn ${!isEn ? 'active' : ''}" onclick="switchCardLang('hCard_${idx}', 'id', this)">ID</button>
                        <button type="button" class="card-mini-btn ${isEn ? 'active' : ''}" onclick="switchCardLang('hCard_${idx}', 'en', this)">EN</button>
                    </div>
                    <button type="button" class="btn-action-card" onclick="moveCardUp(this)" title="Geser ke Atas">⬆ Naik</button>
                    <button type="button" class="btn-action-card" onclick="moveCardDown(this)" title="Geser ke Bawah">⬇ Turun</button>
                    <button type="button" class="btn-delete-card" onclick="removeCard('hCard_${idx}')">✕</button>
                </div>
            </div>
            <div class="grid-2-col">
                <div class="field-item">
                    <label>Gelar / Jenjang / Posisi</label>
                    <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="higher_education[${idx}][title_id]" class="clean-input" placeholder="Misal: Magister S2 / Senior Engineer" required></div>
                    <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="higher_education[${idx}][title_en]" class="clean-input" placeholder="e.g. Master's Degree / Software Engineer"></div>
                </div>
                <div class="field-item">
                    <label>Nama Kampus / Perusahaan</label>
                    <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="higher_education[${idx}][school_id]" class="clean-input" placeholder="Institusi / Perusahaan (ID)" required></div>
                    <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="higher_education[${idx}][school_en]" class="clean-input" placeholder="Institution Name (EN)"></div>
                </div>
            </div>
            <div class="field-item">
                <label>Deskripsi Pengalaman</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><textarea name="higher_education[${idx}][desc_id]" class="clean-input" rows="3" placeholder="Deskripsi pengalaman (ID)"></textarea></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><textarea name="higher_education[${idx}][desc_en]" class="clean-input" rows="3" placeholder="Experience description (EN)"></textarea></div>
            </div>
            <div class="field-item">
                <label>Sorotan Bidang (Pisahkan koma)</label>
                <input type="text" name="higher_education[${idx}][highlights]" class="clean-input" placeholder="AI Engineering, Cloud Computing">
            </div>
            <div class="field-item">
                <label>Proyek Nyata (Pisahkan koma)</label>
                <input type="text" name="higher_education[${idx}][projects]" class="clean-input" placeholder="AI Platform, Distributed System">
            </div>
            <div class="field-item">
                <label>Ringkasan Nilai &amp; Filosofi</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><textarea name="higher_education[${idx}][summary_id]" class="clean-input" rows="2" placeholder="Filosofi (ID)"></textarea></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><textarea name="higher_education[${idx}][summary_en]" class="clean-input" rows="2" placeholder="Summary (EN)"></textarea></div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    updateCardNumbers();
}
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>