<?php
$pageTitle = 'Edit Home Hero';
$topbarTitle = 'Website Content / Home';

require_once __DIR__ . '/../supabase.php';
require_once __DIR__ . '/partials/header.php';

$enPath = __DIR__ . '/../lang/en.json';
$idPath = __DIR__ . '/../lang/id.json';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enData = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
    $idData = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];
    $batch = [];

    // 1. Upload Foto Profil Baru
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['hero_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $dest = __DIR__ . '/../assets/img/hero-1.jpg.png';
            move_uploaded_file($_FILES['hero_image']['tmp_name'], $dest);
        } else {
            $error = 'Format file foto harus JPG, PNG, atau WebP.';
        }
    }

    // 2. Simpan Nama Universal
    $namaLengkap = trim($_POST['hero_name'] ?? '');
    if ($namaLengkap !== '') {
        $idData['hero.name'] = $namaLengkap;
        $enData['hero.name'] = $namaLengkap;
        $batch[] = [
            'key'        => 'hero.name',
            'id_text'    => $namaLengkap,
            'en_text'    => $namaLengkap,
            'updated_at' => date('c')
        ];
    }

    // 3. Simpan Teks Terjemahan (Termasuk Tombol Hero)
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
    }

    if (!empty($batch)) {
        supabase('translations', 'POST', $batch, 'on_conflict=key');
        file_put_contents($idPath, json_encode($idData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents($enPath, json_encode($enData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        if (!$error) {
            $success = 'Perubahan Home Hero berhasil disimpan!';
        }
    }
}

$enDict = file_exists($enPath) ? json_decode(file_get_contents($enPath), true) : [];
$idDict = file_exists($idPath) ? json_decode(file_get_contents($idPath), true) : [];

$currentName = $idDict['hero.name'] ?? ($enDict['hero.name'] ?? 'Ibe Zisokhi Laia');

$heroFields = [
    'hero.eyebrow'   => ['label' => 'Sapaan (Eyebrow)', 'type' => 'input'],
    'hero.subtitle'  => ['label' => 'Profesi / Fokus Utama', 'type' => 'input'],
    'hero.paragraph' => ['label' => 'Deskripsi Profil Utama', 'type' => 'textarea']
];
?>

<style>
    .editor-layout-grid {
        display: grid;
        grid-template-columns: 1.65fr 1fr;
        gap: 24px;
        align-items: start;
    }
    .form-panel {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
    }
    .panel-header-clean {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--border-color);
        gap: 12px;
    }
    .lang-switcher {
        display: inline-flex;
        background: #090c13;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 3px;
        gap: 4px;
        flex-shrink: 0;
    }
    .lang-tab {
        background: transparent;
        border: none;
        color: var(--text-muted);
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .lang-tab.active {
        background: var(--primary);
        color: #fff;
    }
    .field-item {
        margin-bottom: 16px;
    }
    .field-item:last-child {
        margin-bottom: 0;
    }
    .field-item label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #e5e7eb;
        margin-bottom: 6px;
    }
    .clean-input {
        width: 100%;
        padding: 9px 12px;
        background: #090c13;
        border: 1px solid var(--border-color);
        border-radius: 7px;
        color: #fff;
        font-size: 13px;
        font-family: inherit;
        line-height: 1.45;
        transition: border-color 0.2s;
    }
    .clean-input:focus {
        border-color: var(--primary);
        outline: none;
    }
    textarea.clean-input {
        resize: vertical;
        min-height: 95px;
    }

    /* SIDE CARD */
    .side-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .side-title {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }
    .img-upload-box {
        background: #090c13;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 16px;
        text-align: center;
    }
    .avatar-thumb {
        width: 86px;
        height: 86px;
        border-radius: 16px;
        object-fit: cover;
        margin: 0 auto 12px;
        display: block;
        border: 2px solid rgba(139, 92, 246, 0.4);
    }
    .btn-choose-file {
        display: inline-block;
        font-size: 11.5px;
        font-weight: 600;
        color: #c4b5fd;
        background: rgba(139, 92, 246, 0.12);
        border: 1px dashed rgba(139, 92, 246, 0.4);
        padding: 6px 14px;
        border-radius: 6px;
        cursor: pointer;
    }
    .btn-save-final {
        width: 100%;
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 11px 20px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .btn-save-final:hover {
        opacity: 0.9;
    }
    .cta-settings {
        background: #090c13;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 14px;
    }
    .cta-settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: 4px;
    }
    .alert-ok {
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #34d399;
        padding: 10px 14px;
        border-radius: 7px;
        margin-bottom: 16px;
        font-size: 13px;
    }
    .alert-err {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #f87171;
        padding: 10px 14px;
        border-radius: 7px;
        margin-bottom: 16px;
        font-size: 13px;
    }

    /* RESPONSIVE MOBILE */
    @media (max-width: 960px) {
        .editor-layout-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .form-panel {
            padding: 16px 14px;
        }
        .panel-header-clean {
            margin-bottom: 14px;
            padding-bottom: 10px;
        }
        .panel-header-clean h3 {
            font-size: 14px !important;
        }
        .lang-switcher {
            padding: 2px;
            gap: 2px;
            border-radius: 6px;
        }
        .lang-tab {
            padding: 4px 8px;
            font-size: 11px;
            border-radius: 5px;
            gap: 4px;
            line-height: 1.2;
        }
        .lang-tab span.lang-full {
            display: none;
        }
    }
</style>

<div style="max-width: 1180px;">
    <?php if ($success): ?>
        <div class="alert-ok"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert-err"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="editor-layout-grid">
        <!-- FORM KONTEN (KIRI) DENGAN SWITCH BAHASA -->
        <div class="form-panel">
            <div class="panel-header-clean">
                <h3 style="font-size: 16px; color: #fff;">Pengaturan Teks Hero</h3>
                <div class="lang-switcher">
                    <button type="button" class="lang-tab active" onclick="switchTab('id')">
                        🇮🇩 ID<span class="lang-full"> / Indonesia</span>
                    </button>
                    <button type="button" class="lang-tab" onclick="switchTab('en')">
                        🇬🇧 EN<span class="lang-full"> / English</span>
                    </button>
                </div>
            </div>

            <!-- Nama Tunggal (Universal) -->
            <div class="field-item">
                <label>Nama Lengkap Utama</label>
                <input type="text" name="hero_name" class="clean-input" value="<?= htmlspecialchars($currentName) ?>" required>
            </div>

            <!-- Teks Sapaan, Profesi, Deskripsi -->
            <?php foreach ($heroFields as $k => $info): ?>
                <div class="field-item">
                    <label><?= htmlspecialchars($info['label']) ?></label>
                    <div class="lang-field lang-id">
                        <?php if ($info['type'] === 'textarea'): ?>
                            <textarea name="translations[<?= $k ?>][id]" class="clean-input"><?= htmlspecialchars($idDict[$k] ?? '') ?></textarea>
                        <?php else: ?>
                            <input type="text" name="translations[<?= $k ?>][id]" class="clean-input" value="<?= htmlspecialchars($idDict[$k] ?? '') ?>">
                        <?php endif; ?>
                    </div>
                    <div class="lang-field lang-en" style="display:none;">
                        <?php if ($info['type'] === 'textarea'): ?>
                            <textarea name="translations[<?= $k ?>][en]" class="clean-input"><?= htmlspecialchars($enDict[$k] ?? '') ?></textarea>
                        <?php else: ?>
                            <input type="text" name="translations[<?= $k ?>][en]" class="clean-input" value="<?= htmlspecialchars($enDict[$k] ?? '') ?>">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- PANEL KANAN (FOTO, TOMBOL CTA & TOMBOL SIMPAN DI BAWAHNYA) -->
        <div class="side-card">
            <div>
                <div class="side-title">📷 Foto Profil Hero</div>
                <div class="img-upload-box">
                    <img id="heroImgPreview" src="../assets/img/hero-1.jpg.png?v=<?= time() ?>" class="avatar-thumb" onerror="this.src='https://via.placeholder.com/86'">
                    <label for="heroImgFile" class="btn-choose-file">Pilih Gambar Hero</label>
                    <input type="file" id="heroImgFile" name="hero_image" accept="image/png, image/jpeg, image/webp" style="display:none;" onchange="previewImg(event, 'heroImgPreview')">
                </div>
            </div>

            <!-- PENGATURAN TOMBOL CTA HERO -->
            <div>
                <div class="side-title">🔘 Teks Tombol CTA Hero</div>
                <div class="cta-settings">
                    <div style="margin-bottom: 10px;">
                        <span style="font-size: 11.5px; color: var(--text-sub); display: block; margin-bottom: 4px;">Tombol Utama (Journey)</span>
                        <div class="cta-settings-grid">
                            <input type="text" name="translations[hero.btn.journey][id]" class="clean-input" value="<?= htmlspecialchars($idDict['hero.btn.journey'] ?? 'Journey') ?>" placeholder="ID">
                            <input type="text" name="translations[hero.btn.journey][en]" class="clean-input" value="<?= htmlspecialchars($enDict['hero.btn.journey'] ?? 'Journey') ?>" placeholder="EN">
                        </div>
                    </div>
                    <div>
                        <span style="font-size: 11.5px; color: var(--text-sub); display: block; margin-bottom: 4px;">Tombol Kedua (Contact)</span>
                        <div class="cta-settings-grid">
                            <input type="text" name="translations[hero.btn.contact][id]" class="clean-input" value="<?= htmlspecialchars($idDict['hero.btn.contact'] ?? 'Contact') ?>" placeholder="ID">
                            <input type="text" name="translations[hero.btn.contact][en]" class="clean-input" value="<?= htmlspecialchars($enDict['hero.btn.contact'] ?? 'Contact') ?>" placeholder="EN">
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOMBOL SIMPAN DI BAWAH GAMBAR & CTA -->
            <button type="submit" class="btn-save-final">Simpan Perubahan Home</button>

            <a href="edit-about.php" style="padding: 10px 12px; background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 8px; color: var(--text-muted); text-decoration: none; font-size: 12px; display: flex; justify-content: space-between; align-items: center;">
                <span>👤 Lanjut ke Halaman About</span>
                <span style="color: var(--primary);">→</span>
            </a>
        </div>
    </form>
</div>

<script>
function switchTab(lang) {
    document.querySelectorAll('.lang-tab').forEach(el => el.classList.remove('active'));
    if (lang === 'id') {
        document.querySelector('.lang-tab:nth-child(1)').classList.add('active');
        document.querySelectorAll('.lang-id').forEach(el => el.style.display = 'block');
        document.querySelectorAll('.lang-en').forEach(el => el.style.display = 'none');
    } else {
        document.querySelector('.lang-tab:nth-child(2)').classList.add('active');
        document.querySelectorAll('.lang-id').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.lang-en').forEach(el => el.style.display = 'block');
    }
}

function previewImg(e, id) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById(id).src = URL.createObjectURL(file);
    }
}
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>