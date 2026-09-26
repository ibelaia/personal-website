<?php
$pageTitle = 'Edit About';
$topbarTitle = 'Website Content / About';

require_once __DIR__ . '/../supabase.php';
require_once __DIR__ . '/partials/header.php';

$enPath    = __DIR__ . '/../lang/en.json';
$idPath    = __DIR__ . '/../lang/id.json';
$cardsPath = __DIR__ . '/../data/about_cards.json';

if (!file_exists($cardsPath)) {
    $defaultCards = [
        'personality' => [
            ['id' => 'curious', 'title_id' => 'Rasa Ingin Tahu Tinggi', 'title_en' => 'Curious', 'desc_id' => 'Saya senang mengeksplorasi teknologi baru, bereksperimen dengan ide, dan memahami cara kerja sesuatu lebih dalam.', 'desc_en' => 'I enjoy exploring new technologies, experimenting with ideas, and understanding how things work beyond the surface.'],
            ['id' => 'persistent', 'title_id' => 'Gigih', 'title_en' => 'Persistent', 'desc_id' => 'Saya percaya setiap tantangan adalah bagian dari proses belajar. Walau progres terasa lambat, saya memilih untuk terus maju.', 'desc_en' => 'I believe every challenge is part of the learning process. Even when progress feels slow, I choose to keep moving forward.'],
            ['id' => 'responsible', 'title_id' => 'Bertanggung Jawab', 'title_en' => 'Responsible', 'desc_id' => 'Saya selalu mengingatkan diri bahwa pendidikan dan karier saya bukan hanya untuk masa depan saya sendiri, tapi juga untuk orang-orang yang percaya pada saya, terutama keluarga.', 'desc_en' => 'I always remind myself that my education and career are not only for my own future but also for the people who believe in me, especially my family.'],
            ['id' => 'growth', 'title_id' => 'Berorientasi Perkembangan', 'title_en' => 'Growth-Oriented', 'desc_id' => 'Daripada mengejar kesuksesan instan, saya fokus memperbaiki diri setiap hari lewat pembelajaran konsisten dan pengalaman nyata.', 'desc_en' => 'Rather than chasing quick success, I focus on improving myself every day through consistent learning and real-world experience.']
        ],
        'values' => [
            ['id' => 'consistency', 'title_id' => 'Konsistensi di Atas Bakat', 'title_en' => 'Consistency Over Talent', 'desc_id' => 'Saya percaya usaha kecil yang konsisten pada akhirnya akan menghasilkan hasil yang berarti. Setiap pencapaian besar dimulai dari kebiasaan sederhana sehari-hari.', 'desc_en' => 'I believe that small, consistent efforts will eventually create meaningful results. Every big achievement starts from simple daily habits.'],
            ['id' => 'responsibility', 'title_id' => 'Tanggung Jawab', 'title_en' => 'Responsibility', 'desc_id' => 'Kesuksesan bukan hanya soal mencapai tujuan pribadi, tapi juga memenuhi tanggung jawab kepada diri sendiri, keluarga, dan masa depan yang ingin saya bangun.', 'desc_en' => 'Success is not only about reaching personal goals but also about fulfilling responsibilities toward myself, my family, and the future I want to build.'],
            ['id' => 'learning', 'title_id' => 'Belajar Seumur Hidup', 'title_en' => 'Lifelong Learning', 'desc_id' => 'Teknologi berkembang setiap hari, dan saya percaya pembelajaran berkelanjutan adalah kunci untuk tetap relevan dan menciptakan solusi yang bermakna.', 'desc_en' => 'Technology evolves every day, and I believe continuous learning is the key to staying relevant and creating meaningful solutions.']
        ],
        'interests' => [
            ['icon' => '🌐', 'title_id' => 'Pengembangan Web', 'title_en' => 'Web Development'],
            ['icon' => '⛓️', 'title_id' => 'Web3 & Blockchain', 'title_en' => 'Web3 & Blockchain'],
            ['icon' => '🤖', 'title_id' => 'Otomasi dengan n8n', 'title_en' => 'Automation with n8n'],
            ['icon' => '🎨', 'title_id' => 'Desain UI/UX', 'title_en' => 'UI/UX Design'],
            ['icon' => '🎵', 'title_id' => 'Musik', 'title_en' => 'Music'],
            ['icon' => '⚽', 'title_id' => 'Futsal', 'title_en' => 'Futsal'],
            ['icon' => '🎮', 'title_id' => 'Gaming', 'title_en' => 'Gaming'],
            ['icon' => '✈️', 'title_id' => 'Traveling', 'title_en' => 'Traveling']
        ]
    ];
    file_put_contents($cardsPath, json_encode($defaultCards, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$cardsData = json_decode(file_get_contents($cardsPath), true) ?: [];

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enData = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
    $idData = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];
    $batch = [];

    if (isset($_FILES['about_home_img']) && $_FILES['about_home_img']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['about_home_img']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            move_uploaded_file($_FILES['about_home_img']['tmp_name'], __DIR__ . '/../assets/img/about.jpg.png');
        } else {
            $error = 'Format file foto harus JPG, PNG, atau WebP.';
        }
    }

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

    $newCards = [
        'personality' => [],
        'values'      => [],
        'interests'   => []
    ];

    if (isset($_POST['personality']) && is_array($_POST['personality'])) {
        foreach ($_POST['personality'] as $p) {
            if (!empty(trim($p['title_id'] ?? ''))) {
                $newCards['personality'][] = [
                    'title_id' => trim($p['title_id']),
                    'title_en' => trim($p['title_en'] ?? $p['title_id']),
                    'desc_id'  => trim($p['desc_id'] ?? ''),
                    'desc_en'  => trim($p['desc_en'] ?? '')
                ];
            }
        }
    }

    if (isset($_POST['values']) && is_array($_POST['values'])) {
        foreach ($_POST['values'] as $v) {
            if (!empty(trim($v['title_id'] ?? ''))) {
                $newCards['values'][] = [
                    'title_id' => trim($v['title_id']),
                    'title_en' => trim($v['title_en'] ?? $v['title_id']),
                    'desc_id'  => trim($v['desc_id'] ?? ''),
                    'desc_en'  => trim($v['desc_en'] ?? '')
                ];
            }
        }
    }

    if (isset($_POST['interests']) && is_array($_POST['interests'])) {
        foreach ($_POST['interests'] as $int) {
            if (!empty(trim($int['title_id'] ?? ''))) {
                $newCards['interests'][] = [
                    'icon'     => trim($int['icon'] ?? '⚡'),
                    'title_id' => trim($int['title_id']),
                    'title_en' => trim($int['title_en'] ?? $int['title_id'])
                ];
            }
        }
    }

    file_put_contents($cardsPath, json_encode($newCards, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $cardsData = $newCards;

    if (!$error) {
        $success = 'Perubahan About berhasil disimpan!';
    }
}

$enDict = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
$idDict = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];

function getVal($dict, $key, $fallback = '') {
    return (!empty($dict[$key])) ? $dict[$key] : $fallback;
}
?>

<style>
    .editor-box-container {
        max-width: 920px;
        margin: 0 auto;
        position: relative;
    }
    .form-panel-compact {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px 20px;
    }

    /* 1. HEADER & LANGUAGE BAR STICKY (MELAYANG MENGIKUTI SCROLL) */
    .sticky-control-bar {
        position: sticky;
        top: 64px; /* Di bawah topbar admin */
        z-index: 50;
        background: rgba(19, 25, 38, 0.94);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        margin: -16px -20px 16px -20px;
        padding: 14px 20px 12px;
        border-bottom: 1px solid var(--border-color);
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .panel-header-clean {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }
    .lang-switcher {
        display: inline-flex;
        background: #090c13;
        border: 1px solid rgba(139, 92, 246, 0.3);
        border-radius: 8px;
        padding: 2px;
        gap: 2px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
    .lang-tab {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }
    .lang-tab.active {
        background: var(--primary);
        color: #fff;
    }

    /* SUB MENU SEKSI */
    .section-selector-bar {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
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
        padding: 7px 4px;
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

    .section-pane {
        display: none;
    }
    .section-pane.active {
        display: block;
    }

    /* KARTU DINAMIS */
    .card-block-compact {
        background: #090c13;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 12px;
    }
    .card-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .badge-tab-title {
        background: rgba(139, 92, 246, 0.15);
        color: #c4b5fd;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 5px;
    }

    /* MINI TOGGLE DI DALAM KARTU */
    .card-mini-lang-nav {
        display: inline-flex;
        gap: 4px;
        background: #05080e;
        padding: 2px;
        border-radius: 5px;
        border: 1px solid var(--border-color);
    }
    .card-mini-btn {
        background: transparent;
        border: none;
        color: var(--text-sub);
        font-size: 10.5px;
        font-weight: 700;
        padding: 2px 7px;
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
    .btn-delete-card:hover {
        background: #ef4444;
        color: #fff;
    }

    .btn-add-card {
        background: rgba(139, 92, 246, 0.1);
        color: #c4b5fd;
        border: 1px dashed rgba(139, 92, 246, 0.4);
        padding: 9px 12px;
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
    .btn-add-card:hover {
        background: rgba(139, 92, 246, 0.2);
        color: #fff;
    }

    .field-item {
        margin-bottom: 10px;
    }
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
        line-height: 1.4;
    }
    .clean-input:focus {
        border-color: var(--primary);
        outline: none;
    }
    textarea.clean-input {
        resize: vertical;
        min-height: 52px;
    }

    .grid-2-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .grid-3-col {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 10px;
    }

    .img-upload-box-inline {
        background: #05080e;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }
    .img-preview-thumb {
        width: 64px;
        height: 50px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid rgba(255, 255, 255, 0.1);
        flex-shrink: 0;
    }
    .btn-choose-file {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        color: #c4b5fd;
        background: rgba(139, 92, 246, 0.12);
        border: 1px dashed rgba(139, 92, 246, 0.4);
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .bottom-save-bar {
        margin-top: 18px;
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
        padding: 10px 22px;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-save-final:hover {
        opacity: 0.9;
    }

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
        .sticky-control-bar {
            top: 0; /* Di ponsel topbar desktop hilang */
            margin: -14px -12px 14px -12px;
            padding: 10px 12px;
        }
        .section-selector-bar {
            grid-template-columns: repeat(2, 1fr);
            gap: 4px;
        }
        .grid-2-col {
            grid-template-columns: 1fr;
        }
        .form-panel-compact {
            padding: 14px 12px;
        }
        .bottom-save-bar {
            flex-direction: column-reverse;
        }
        .btn-save-final {
            width: 100%;
        }
    }
</style>

<div class="editor-box-container">
    <?php if ($success): ?>
        <div class="alert-ok"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form-panel-compact">
        <!-- BAR KONTROL STICKY MELAYANG -->
        <div class="sticky-control-bar">
            <div class="panel-header-clean">
                <h3 style="font-size: 15px; color: #fff; margin:0;">Pengaturan About</h3>
                <!-- PENGALIH BAHASA GLOBAL (SELALU KELIHATAN MESKI DI-SCROLL KE BAWAH) -->
                <div class="lang-switcher">
                    <button type="button" class="lang-tab active" id="globalTabId" onclick="switchTab('id')">🇮🇩 ID</button>
                    <button type="button" class="lang-tab" id="globalTabEn" onclick="switchTab('en')">🇬🇧 EN</button>
                </div>
            </div>

            <div class="section-selector-bar">
                <button type="button" class="sec-tab-btn active" onclick="openSection('sec-home', this)">📌 Home</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-story', this)">📖 Story</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-personality', this)">🧠 Kepribadian</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-values', this)">💎 Nilai</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-interests', this)">🎨 Minat</button>
                <button type="button" class="sec-tab-btn" onclick="openSection('sec-vision', this)">🔭 Visi</button>
            </div>
        </div>

        <!-- 1. HOME -->
        <div id="sec-home" class="section-pane active">
            <div class="card-block-compact">
                <div class="img-upload-box-inline">
                    <img id="aboutHomePreview" src="../assets/img/about.jpg.png?v=<?= time() ?>" class="img-preview-thumb" onerror="this.src='https://via.placeholder.com/64x50'">
                    <div>
                        <label for="aboutHomeImgInput" class="btn-choose-file">Ganti Foto Cuplikan</label>
                        <input type="file" id="aboutHomeImgInput" name="about_home_img" accept="image/png, image/jpeg, image/webp" style="display:none;" onchange="previewImg(event, 'aboutHomePreview')">
                    </div>
                </div>

                <div class="grid-2-col">
                    <div class="field-item">
                        <label>Label Section</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[index.about.eyebrow][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.about.eyebrow', 'Tentang')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.about.eyebrow][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.about.eyebrow', 'About')) ?>"></div>
                    </div>
                    <div class="field-item">
                        <label>Judul Section</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[index.about.title][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.about.title', 'Siapa Saya')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.about.title][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.about.title', 'Who I Am')) ?>"></div>
                    </div>
                </div>

                <div class="field-item">
                    <label>Paragraf 1 (Perkenalan)</label>
                    <div class="lang-field lang-id"><textarea name="translations[index.about.p1][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'index.about.p1')) ?></textarea></div>
                    <div class="lang-field lang-en" style="display:none;"><textarea name="translations[index.about.p1][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'index.about.p1')) ?></textarea></div>
                </div>
                <div class="grid-2-col">
                    <div class="field-item">
                        <label>Paragraf 2 (Fokus Belajar)</label>
                        <div class="lang-field lang-id"><textarea name="translations[index.about.p2][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'index.about.p2')) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[index.about.p2][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'index.about.p2')) ?></textarea></div>
                    </div>
                    <div class="field-item">
                        <label>Paragraf 3 (Tujuan Web)</label>
                        <div class="lang-field lang-id"><textarea name="translations[index.about.p3][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'index.about.p3')) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[index.about.p3][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'index.about.p3')) ?></textarea></div>
                    </div>
                </div>
                <div class="field-item">
                    <label>Teks Tombol CTA</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[index.about.button][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'index.about.button', 'Pelajari Lebih Lanjut →')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[index.about.button][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'index.about.button', 'Learn More About Me →')) ?>"></div>
                </div>
            </div>
        </div>

        <!-- 2. STORY -->
        <div id="sec-story" class="section-pane">
            <div class="card-block-compact">
                <div class="grid-2-col">
                    <div class="field-item">
                        <label>Judul Halaman</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[about.header.title][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'about.header.title', 'Tentang Saya')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[about.header.title][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'about.header.title', 'About Me')) ?>"></div>
                    </div>
                    <div class="field-item">
                        <label>Judul Kisah (My Story)</label>
                        <div class="lang-field lang-id"><input type="text" name="translations[about.bio.heading][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'about.bio.heading', 'Cerita Saya')) ?>"></div>
                        <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[about.bio.heading][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'about.bio.heading', 'My Story')) ?>"></div>
                    </div>
                </div>
                <div class="field-item">
                    <label>Subjudul Halaman</label>
                    <div class="lang-field lang-id"><textarea name="translations[about.header.subtitle][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'about.header.subtitle')) ?></textarea></div>
                    <div class="lang-field lang-en" style="display:none;"><textarea name="translations[about.header.subtitle][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'about.header.subtitle')) ?></textarea></div>
                </div>
            </div>

            <div class="card-block-compact">
                <div class="field-item">
                    <label>Paragraf 1 (Perkenalan & Kampus)</label>
                    <div class="lang-field lang-id"><textarea name="translations[about.bio.p1][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'about.bio.p1')) ?></textarea></div>
                    <div class="lang-field lang-en" style="display:none;"><textarea name="translations[about.bio.p1][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'about.bio.p1')) ?></textarea></div>
                </div>
                <div class="grid-2-col">
                    <div class="field-item">
                        <label>Paragraf 2 (Latar Belakang & Adaptasi)</label>
                        <div class="lang-field lang-id"><textarea name="translations[about.bio.p2][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'about.bio.p2')) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[about.bio.p2][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'about.bio.p2')) ?></textarea></div>
                    </div>
                    <div class="field-item">
                        <label>Paragraf 3 (Minat Teknologi)</label>
                        <div class="lang-field lang-id"><textarea name="translations[about.bio.p3][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'about.bio.p3')) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[about.bio.p3][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'about.bio.p3')) ?></textarea></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. KEPRIBADIAN (DENGAN DUAL TOGGLE DI TIAP KARTU & GLOBAL) -->
        <div id="sec-personality" class="section-pane">
            <button type="button" class="btn-add-card" onclick="addPersonalityCard()">➕ Tambah Kepribadian</button>
            <div id="personalityContainer" class="grid-2-col">
                <?php foreach (($cardsData['personality'] ?? []) as $idx => $p): ?>
                    <div class="card-block-compact" id="pCard_<?= $idx ?>">
                        <div class="card-top-bar">
                            <span class="badge-tab-title">#<span class="card-num"><?= $idx + 1 ?></span></span>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <!-- MINI TOGGLE DI DALAM KARTU -->
                                <div class="card-mini-lang-nav">
                                    <button type="button" class="card-mini-btn active" onclick="switchCardLang('pCard_<?= $idx ?>', 'id', this)">ID</button>
                                    <button type="button" class="card-mini-btn" onclick="switchCardLang('pCard_<?= $idx ?>', 'en', this)">EN</button>
                                </div>
                                <button type="button" class="btn-delete-card" onclick="removeCard('pCard_<?= $idx ?>')">✕</button>
                            </div>
                        </div>
                        <div class="field-item">
                            <label>Judul Karakter</label>
                            <div class="card-lang card-lang-id"><input type="text" name="personality[<?= $idx ?>][title_id]" class="clean-input" value="<?= htmlspecialchars($p['title_id'] ?? '') ?>" placeholder="Judul (Bahasa Indonesia)" required></div>
                            <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="personality[<?= $idx ?>][title_en]" class="clean-input" value="<?= htmlspecialchars($p['title_en'] ?? '') ?>" placeholder="Title (English)"></div>
                        </div>
                        <div class="field-item">
                            <label>Deskripsi Sikap</label>
                            <div class="card-lang card-lang-id"><textarea name="personality[<?= $idx ?>][desc_id]" class="clean-input" placeholder="Deskripsi (Bahasa Indonesia)"><?= htmlspecialchars($p['desc_id'] ?? '') ?></textarea></div>
                            <div class="card-lang card-lang-en" style="display:none;"><textarea name="personality[<?= $idx ?>][desc_en]" class="clean-input" placeholder="Description (English)"><?= htmlspecialchars($p['desc_en'] ?? '') ?></textarea></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 4. NILAI (DENGAN DUAL TOGGLE DI TIAP KARTU & GLOBAL) -->
        <div id="sec-values" class="section-pane">
            <button type="button" class="btn-add-card" onclick="addValueCard()">➕ Tambah Nilai</button>
            <div id="valuesContainer" class="grid-2-col">
                <?php foreach (($cardsData['values'] ?? []) as $idx => $v): ?>
                    <div class="card-block-compact" id="vCard_<?= $idx ?>">
                        <div class="card-top-bar">
                            <span class="badge-tab-title">#<span class="card-num"><?= $idx + 1 ?></span></span>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <!-- MINI TOGGLE DI DALAM KARTU -->
                                <div class="card-mini-lang-nav">
                                    <button type="button" class="card-mini-btn active" onclick="switchCardLang('vCard_<?= $idx ?>', 'id', this)">ID</button>
                                    <button type="button" class="card-mini-btn" onclick="switchCardLang('vCard_<?= $idx ?>', 'en', this)">EN</button>
                                </div>
                                <button type="button" class="btn-delete-card" onclick="removeCard('vCard_<?= $idx ?>')">✕</button>
                            </div>
                        </div>
                        <div class="field-item">
                            <label>Judul Nilai</label>
                            <div class="card-lang card-lang-id"><input type="text" name="values[<?= $idx ?>][title_id]" class="clean-input" value="<?= htmlspecialchars($v['title_id'] ?? '') ?>" placeholder="Prinsip (Bahasa Indonesia)" required></div>
                            <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="values[<?= $idx ?>][title_en]" class="clean-input" value="<?= htmlspecialchars($v['title_en'] ?? '') ?>" placeholder="Principle (English)"></div>
                        </div>
                        <div class="field-item">
                            <label>Deskripsi Nilai</label>
                            <div class="card-lang card-lang-id"><textarea name="values[<?= $idx ?>][desc_id]" class="clean-input" placeholder="Penjelasan (Bahasa Indonesia)"><?= htmlspecialchars($v['desc_id'] ?? '') ?></textarea></div>
                            <div class="card-lang card-lang-en" style="display:none;"><textarea name="values[<?= $idx ?>][desc_en]" class="clean-input" placeholder="Explanation (English)"><?= htmlspecialchars($v['desc_en'] ?? '') ?></textarea></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 5. MINAT -->
        <div id="sec-interests" class="section-pane">
            <div class="card-block-compact">
                <div class="field-item">
                    <label>Teks Pengantar Minat</label>
                    <div class="lang-field lang-id"><textarea name="translations[about.interests.intro][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'about.interests.intro')) ?></textarea></div>
                    <div class="lang-field lang-en" style="display:none;"><textarea name="translations[about.interests.intro][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'about.interests.intro')) ?></textarea></div>
                </div>
            </div>
            <button type="button" class="btn-add-card" onclick="addInterestCard()">➕ Tambah Minat</button>
            <div id="interestsContainer" class="grid-3-col">
                <?php foreach (($cardsData['interests'] ?? []) as $idx => $int): ?>
                    <div class="card-block-compact" id="intCard_<?= $idx ?>">
                        <div class="card-top-bar">
                            <span class="badge-tab-title">#<span class="card-num"><?= $idx + 1 ?></span></span>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <div class="card-mini-lang-nav">
                                    <button type="button" class="card-mini-btn active" onclick="switchCardLang('intCard_<?= $idx ?>', 'id', this)">ID</button>
                                    <button type="button" class="card-mini-btn" onclick="switchCardLang('intCard_<?= $idx ?>', 'en', this)">EN</button>
                                </div>
                                <button type="button" class="btn-delete-card" onclick="removeCard('intCard_<?= $idx ?>')">✕</button>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 50px 1fr; gap: 6px;">
                            <div>
                                <label style="font-size:11px; font-weight:600; color:#cbd5e1; margin-bottom:3px; display:block;">Ikon</label>
                                <input type="text" name="interests[<?= $idx ?>][icon]" class="clean-input" value="<?= htmlspecialchars($int['icon'] ?? '⚡') ?>" style="text-align:center;" required>
                            </div>
                            <div>
                                <label style="font-size:11px; font-weight:600; color:#cbd5e1; margin-bottom:3px; display:block;">Nama</label>
                                <div class="card-lang card-lang-id"><input type="text" name="interests[<?= $idx ?>][title_id]" class="clean-input" value="<?= htmlspecialchars($int['title_id'] ?? '') ?>" placeholder="Nama (ID)" required></div>
                                <div class="card-lang card-lang-en" style="display:none;"><input type="text" name="interests[<?= $idx ?>][title_en]" class="clean-input" value="<?= htmlspecialchars($int['title_en'] ?? '') ?>" placeholder="Name (EN)"></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 6. VISI -->
        <div id="sec-vision" class="section-pane">
            <div class="card-block-compact">
                <div class="grid-2-col">
                    <div class="field-item">
                        <label>Paragraf 1 (Target Karier Jangka Panjang)</label>
                        <div class="lang-field lang-id"><textarea name="translations[about.vision.p1][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'about.vision.p1')) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[about.vision.p1][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'about.vision.p1')) ?></textarea></div>
                    </div>
                    <div class="field-item">
                        <label>Paragraf 2 (Aspirasi & Kontribusi)</label>
                        <div class="lang-field lang-id"><textarea name="translations[about.vision.p2][id]" class="clean-input"><?= htmlspecialchars(getVal($idDict, 'about.vision.p2')) ?></textarea></div>
                        <div class="lang-field lang-en" style="display:none;"><textarea name="translations[about.vision.p2][en]" class="clean-input"><?= htmlspecialchars(getVal($enDict, 'about.vision.p2')) ?></textarea></div>
                    </div>
                </div>
                <div class="field-item">
                    <label>Kutipan Visi (Quote)</label>
                    <div class="lang-field lang-id"><input type="text" name="translations[about.vision.quote][id]" class="clean-input" value="<?= htmlspecialchars(getVal($idDict, 'about.vision.quote')) ?>"></div>
                    <div class="lang-field lang-en" style="display:none;"><input type="text" name="translations[about.vision.quote][en]" class="clean-input" value="<?= htmlspecialchars(getVal($enDict, 'about.vision.quote')) ?>"></div>
                </div>
            </div>
        </div>

        <div class="bottom-save-bar">
            <a href="edit-journey.php" style="color: var(--text-muted); text-decoration: none; font-size: 12.5px; font-weight: 600;">
                🚀 Lanjut ke Journey →
            </a>
            <button type="submit" class="btn-save-final">Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
let currentLang = 'id';

// 1. FUNGSI SWITCH GLOBAL (SEMUA KARTU SEKALIGUS)
function switchTab(lang) {
    currentLang = lang;
    
    // Update tombol global
    if (lang === 'id') {
        document.getElementById('globalTabId').classList.add('active');
        document.getElementById('globalTabEn').classList.remove('active');
    } else {
        document.getElementById('globalTabId').classList.remove('active');
        document.getElementById('globalTabEn').classList.add('active');
    }

    // Ubah field statis
    document.querySelectorAll('.lang-id').forEach(el => el.style.display = (lang === 'id' ? 'block' : 'none'));
    document.querySelectorAll('.lang-en').forEach(el => el.style.display = (lang === 'en' ? 'block' : 'none'));

    // Ubah kartu dinamis
    document.querySelectorAll('.card-lang-id').forEach(el => el.style.display = (lang === 'id' ? 'block' : 'none'));
    document.querySelectorAll('.card-lang-en').forEach(el => el.style.display = (lang === 'en' ? 'block' : 'none'));

    // Samakan mini tab pada tiap kartu
    document.querySelectorAll('.card-block-compact').forEach(card => {
        const btns = card.querySelectorAll('.card-mini-btn');
        if (btns.length === 2) {
            btns[0].classList.toggle('active', lang === 'id');
            btns[1].classList.toggle('active', lang === 'en');
        }
    });
}

// 2. FUNGSI SWITCH LOKAL PADA SATU KARTU SAJA (TANPA MENGGANGGU KARTU LAIN)
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

function addPersonalityCard() {
    const container = document.getElementById('personalityContainer');
    const idx = Date.now();
    const isEn = (currentLang === 'en');
    const html = `
        <div class="card-block-compact" id="pCard_${idx}">
            <div class="card-top-bar">
                <span class="badge-tab-title">Baru</span>
                <div style="display:flex; align-items:center; gap:6px;">
                    <div class="card-mini-lang-nav">
                        <button type="button" class="card-mini-btn ${!isEn ? 'active' : ''}" onclick="switchCardLang('pCard_${idx}', 'id', this)">ID</button>
                        <button type="button" class="card-mini-btn ${isEn ? 'active' : ''}" onclick="switchCardLang('pCard_${idx}', 'en', this)">EN</button>
                    </div>
                    <button type="button" class="btn-delete-card" onclick="removeCard('pCard_${idx}')">✕</button>
                </div>
            </div>
            <div class="field-item">
                <label>Judul Karakter</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="personality[${idx}][title_id]" class="clean-input" placeholder="Judul (Bahasa Indonesia)" required></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="personality[${idx}][title_en]" class="clean-input" placeholder="Title (English)"></div>
            </div>
            <div class="field-item">
                <label>Deskripsi Sikap</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><textarea name="personality[${idx}][desc_id]" class="clean-input" placeholder="Deskripsi (Bahasa Indonesia)"></textarea></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><textarea name="personality[${idx}][desc_en]" class="clean-input" placeholder="Description (English)"></textarea></div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function addValueCard() {
    const container = document.getElementById('valuesContainer');
    const idx = Date.now();
    const isEn = (currentLang === 'en');
    const html = `
        <div class="card-block-compact" id="vCard_${idx}">
            <div class="card-top-bar">
                <span class="badge-tab-title">Baru</span>
                <div style="display:flex; align-items:center; gap:6px;">
                    <div class="card-mini-lang-nav">
                        <button type="button" class="card-mini-btn ${!isEn ? 'active' : ''}" onclick="switchCardLang('vCard_${idx}', 'id', this)">ID</button>
                        <button type="button" class="card-mini-btn ${isEn ? 'active' : ''}" onclick="switchCardLang('vCard_${idx}', 'en', this)">EN</button>
                    </div>
                    <button type="button" class="btn-delete-card" onclick="removeCard('vCard_${idx}')">✕</button>
                </div>
            </div>
            <div class="field-item">
                <label>Judul Nilai</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="values[${idx}][title_id]" class="clean-input" placeholder="Prinsip (Bahasa Indonesia)" required></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="values[${idx}][title_en]" class="clean-input" placeholder="Principle (English)"></div>
            </div>
            <div class="field-item">
                <label>Deskripsi Nilai</label>
                <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><textarea name="values[${idx}][desc_id]" class="clean-input" placeholder="Penjelasan (Bahasa Indonesia)"></textarea></div>
                <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><textarea name="values[${idx}][desc_en]" class="clean-input" placeholder="Explanation (English)"></textarea></div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function addInterestCard() {
    const container = document.getElementById('interestsContainer');
    const idx = Date.now();
    const isEn = (currentLang === 'en');
    const html = `
        <div class="card-block-compact" id="intCard_${idx}">
            <div class="card-top-bar">
                <span class="badge-tab-title">Baru</span>
                <div style="display:flex; align-items:center; gap:6px;">
                    <div class="card-mini-lang-nav">
                        <button type="button" class="card-mini-btn ${!isEn ? 'active' : ''}" onclick="switchCardLang('intCard_${idx}', 'id', this)">ID</button>
                        <button type="button" class="card-mini-btn ${isEn ? 'active' : ''}" onclick="switchCardLang('intCard_${idx}', 'en', this)">EN</button>
                    </div>
                    <button type="button" class="btn-delete-card" onclick="removeCard('intCard_${idx}')">✕</button>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 50px 1fr; gap: 6px;">
                <div>
                    <label style="font-size:11px; font-weight:600; color:#cbd5e1; margin-bottom:3px; display:block;">Ikon</label>
                    <input type="text" name="interests[${idx}][icon]" class="clean-input" value="⚡" style="text-align:center;" required>
                </div>
                <div>
                    <label style="font-size:11px; font-weight:600; color:#cbd5e1; margin-bottom:3px; display:block;">Nama</label>
                    <div class="card-lang card-lang-id" style="display:${isEn ? 'none' : 'block'};"><input type="text" name="interests[${idx}][title_id]" class="clean-input" placeholder="Nama (ID)" required></div>
                    <div class="card-lang card-lang-en" style="display:${isEn ? 'block' : 'none'};"><input type="text" name="interests[${idx}][title_en]" class="clean-input" placeholder="Name (EN)"></div>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

function previewImg(e, previewId) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById(previewId).src = URL.createObjectURL(file);
    }
}
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>