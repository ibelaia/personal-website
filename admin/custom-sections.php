<?php
$jsonPath = __DIR__ . '/../data/custom_sections.json';
$sections = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
if (!is_array($sections)) { $sections = []; }

$error = '';

if (isset($_GET['set_status']) && isset($_GET['id'])) {
    $targetId  = trim($_GET['id']);
    $newStatus = trim($_GET['set_status']);
    if (in_array($newStatus, ['home', 'archive', 'draft'])) {
        foreach ($sections as &$s) {
            if (($s['id'] ?? '') === $targetId) { $s['publish_status'] = $newStatus; break; }
        }
        file_put_contents($jsonPath, json_encode(array_values($sections), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header('Location: custom-sections.php?view=list&msg=status_updated');
        exit;
    }
}

if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $newSections = array();
    foreach ($sections as $s) {
        if (isset($s['id']) && $s['id'] !== $deleteId) { $newSections[] = $s; }
    }
    file_put_contents($jsonPath, json_encode($newSections, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header('Location: custom-sections.php?view=list&msg=deleted');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_save'])) {
    $secId       = trim($_POST['sec_id'] ?? '');
    $title_id    = trim($_POST['title_id'] ?? '');
    $title_en    = trim($_POST['title_en'] ?? '');
    $eyebrow_id  = trim($_POST['eyebrow_id'] ?? '');
    $eyebrow_en  = trim($_POST['eyebrow_en'] ?? '');
    $teaser_id   = trim($_POST['teaser_id'] ?? '');
    $teaser_en   = trim($_POST['teaser_en'] ?? '');
    $status      = trim($_POST['publish_status'] ?? 'home');
    $homeLayout  = trim($_POST['home_layout'] ?? 'cards');
    $btn_text_id = trim($_POST['btn_text_id'] ?? 'Pelajari Lebih Lanjut →');
    $btn_text_en = trim($_POST['btn_text_en'] ?? 'Learn More →');
    $customDate  = trim($_POST['custom_date'] ?? date('Y-m-d'));

    $blocks = array();
    if (!empty($_POST['block_type']) && is_array($_POST['block_type'])) {
        foreach ($_POST['block_type'] as $i => $type) {
            $blockData = array('type' => $type);

            if ($type === 'story') {
                $blockData['image_position'] = trim($_POST['block_story_img_pos'][$i] ?? 'left');
                $blockData['image'] = trim($_POST['block_story_image_existing'][$i] ?? '');
                if (isset($_FILES['block_story_image_file']['name'][$i]) && $_FILES['block_story_image_file']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['block_story_image_file']['tmp_name'][$i];
                    $origName = $_FILES['block_story_image_file']['name'][$i];
                    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        $newImgName = 'block_img_' . time() . '_' . rand(100,999) . '.' . $ext;
                        $uploadDir = __DIR__ . '/../assets/img/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                        if (move_uploaded_file($tmpName, $uploadDir . $newImgName)) {
                            $blockData['image'] = 'assets/img/' . $newImgName;
                        }
                    }
                }
                $blockData['body_id'] = trim($_POST['block_story_body_id'][$i] ?? '');
                $blockData['body_en'] = trim($_POST['block_story_body_en'][$i] ?? '');
            } 
            elseif ($type === 'cards') {
                $cardItems = array();
                if (!empty($_POST['bc_title_id'][$i]) && is_array($_POST['bc_title_id'][$i])) {
                    foreach ($_POST['bc_title_id'][$i] as $j => $bcTitle) {
                        $bcTitle = trim($bcTitle);
                        if ($bcTitle === '') continue;
                        $cardItems[] = array(
                            'icon'     => trim($_POST['bc_icon'][$i][$j] ?? '💡'),
                            'title_id' => $bcTitle,
                            'title_en' => trim($_POST['bc_title_en'][$i][$j] ?? $bcTitle),
                            'desc_id'  => trim($_POST['bc_desc_id'][$i][$j] ?? ''),
                            'desc_en'  => trim($_POST['bc_desc_en'][$i][$j] ?? '')
                        );
                    }
                }
                $blockData['items'] = $cardItems;
            } 
            elseif ($type === 'article') {
                $blockData['heading_id'] = trim($_POST['block_art_heading_id'][$i] ?? '');
                $blockData['heading_en'] = trim($_POST['block_art_heading_en'][$i] ?? '');
                $blockData['content_id'] = trim($_POST['block_art_content_id'][$i] ?? '');
                $blockData['content_en'] = trim($_POST['block_art_content_en'][$i] ?? '');
            } 
            elseif ($type === 'cta') {
                $blockData['title_id']  = trim($_POST['block_cta_title_id'][$i] ?? '');
                $blockData['title_en']  = trim($_POST['block_cta_title_en'][$i] ?? '');
                $blockData['btn_label'] = trim($_POST['block_cta_btn_label'][$i] ?? '');
                $blockData['btn_url']   = trim($_POST['block_cta_btn_url'][$i] ?? '');
            }
            elseif ($type === 'timeline') {
                $timelineItems = array();
                if (!empty($_POST['bt_year'][$i]) && is_array($_POST['bt_year'][$i])) {
                    foreach ($_POST['bt_year'][$i] as $j => $btYear) {
                        $btYear = trim($btYear);
                        if ($btYear === '') continue;
                        $timelineItems[] = array(
                            'year'        => $btYear,
                            'title_id'    => trim($_POST['bt_title_id'][$i][$j] ?? ''),
                            'title_en'    => trim($_POST['bt_title_en'][$i][$j] ?? ''),
                            'desc_id'     => trim($_POST['bt_desc_id'][$i][$j] ?? ''),
                            'desc_en'     => trim($_POST['bt_desc_en'][$i][$j] ?? ''),
                            'takeaway_id' => trim($_POST['bt_takeaway_id'][$i][$j] ?? ''),
                            'takeaway_en' => trim($_POST['bt_takeaway_en'][$i][$j] ?? '')
                        );
                    }
                }
                $blockData['items'] = $timelineItems;
            }

            $blocks[] = $blockData;
        }
    }

    if (empty($title_id)) {
        $error = 'Judul Utama (Bahasa Indonesia) wajib diisi!';
    } else {
        if (empty($secId)) {
            $newId = 'sec_' . time();
            $slug  = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title_en ?: $title_id));
            $sections[] = array(
                'id'             => $newId,
                'slug'           => $slug,
                'title_id'       => $title_id,
                'title_en'       => $title_en ?: $title_id,
                'eyebrow_id'     => $eyebrow_id,
                'eyebrow_en'     => $eyebrow_en,
                'teaser_id'      => $teaser_id,
                'teaser_en'      => $teaser_en ?: $teaser_id,
                'publish_status' => $status,
                'home_layout'    => $homeLayout,
                'btn_text_id'    => $btn_text_id,
                'btn_text_en'    => $btn_text_en,
                'created_at'     => $customDate . ' ' . date('H:i:s'),
                'blocks'         => $blocks
            );
        } else {
            foreach ($sections as &$sec) {
                if (isset($sec['id']) && $sec['id'] === $secId) {
                    $sec['title_id']       = $title_id;
                    $sec['title_en']       = $title_en ?: $title_id;
                    $sec['eyebrow_id']     = $eyebrow_id;
                    $sec['eyebrow_en']     = $eyebrow_en;
                    $sec['teaser_id']      = $teaser_id;
                    $sec['teaser_en']      = $teaser_en ?: $teaser_id;
                    $sec['publish_status'] = $status;
                    $sec['home_layout']    = $homeLayout;
                    $sec['btn_text_id']    = $btn_text_id;
                    $sec['btn_text_en']    = $btn_text_en;
                    $sec['created_at']     = $customDate . ' ' . (!empty($sec['created_at']) ? date('H:i:s', strtotime($sec['created_at'])) : date('H:i:s'));
                    $sec['blocks']         = $blocks;
                    break;
                }
            }
        }
        file_put_contents($jsonPath, json_encode(array_values($sections), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header('Location: custom-sections.php?view=list&msg=saved');
        exit;
    }
}

$view   = trim($_GET['view'] ?? 'hub');
$editId = trim($_GET['edit'] ?? '');
$editData = null;

if (!empty($editId)) {
    $view = 'form';
    foreach ($sections as $s) {
        if (($s['id'] ?? '') === $editId) { $editData = $s; break; }
    }
} elseif ($view === 'create') {
    $view = 'form';
}

$pageTitle   = 'Kelola Custom Sections';
$topbarTitle = 'Custom Sections Hub';
require_once __DIR__ . '/partials/header.php';
?>

<style>
    .hub-container { width: 100%; max-width: 980px; margin: 0 auto; box-sizing: border-box; }
    .hub-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px; }
    .project-card-hub { background: var(--bg-card, #0b1120); border: 1px solid var(--border-color, rgba(255,255,255,0.08)); border-radius: 16px; padding: 36px 28px; display: flex; flex-direction: column; justify-content: space-between; text-decoration: none; transition: transform 0.25s ease, border-color 0.25s ease; box-sizing: border-box; }
    .project-card-hub:hover { transform: translateY(-4px); border-color: rgba(139, 92, 246, 0.45); }
    .hub-icon { font-size: 42px; margin-bottom: 16px; display: inline-block; }
    .hub-title { font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 8px; }
    .hub-desc { font-size: 13.5px; color: var(--text-muted, #94a3b8); line-height: 1.6; margin-bottom: 24px; }
    .hub-action-btn { display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #c4b5fd; }
    .btn-back-hub { display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); color: var(--text-muted, #94a3b8); padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
    .panel-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 24px; margin-bottom: 24px; box-sizing: border-box; }
    .grid-2-form { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { margin-bottom: 14px; display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 13px; font-weight: 600; color: var(--text-main); }
    .form-control { width: 100%; padding: 10px 14px; background: #090d16; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: #fff; font-family: inherit; font-size: 13.5px; box-sizing: border-box; }
    .form-control:focus { outline: none; border-color: var(--primary); }
    .lang-tabs { display: flex; gap: 8px; margin-bottom: 16px; background: #090d16; padding: 4px; border-radius: 8px; width: fit-content; border: 1px solid var(--border-color); }
    .lang-tab-btn { padding: 6px 16px; border-radius: 6px; font-size: 12.5px; font-weight: 700; cursor: pointer; border: none; background: transparent; color: var(--text-muted); }
    .lang-tab-btn.active { background: var(--primary); color: #fff; }
    .btn-submit { background: var(--primary); color: #fff; border: none; padding: 11px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
    
    .dynamic-block-card { background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 18px; margin-bottom: 16px; position: relative; }
    .block-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.06); }
    .btn-remove-block { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); border-radius: 6px; padding: 5px 12px; font-size: 11.5px; cursor: pointer; font-weight: 600; }
    
    .status-btn-group { display: inline-flex; background: #090d16; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 3px; gap: 3px; }
    .status-btn { padding: 5px 12px; border-radius: 6px; font-size: 11.5px; font-weight: 700; text-decoration: none; color: var(--text-muted); }
    .status-btn.active-home { background: rgba(16, 185, 129, 0.2); color: #34d399; }
    .status-btn.active-archive { background: rgba(56, 189, 248, 0.2); color: #38bdf8; }
    .status-btn.active-draft { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }

    /* PERBAIKAN TAMPILAN MOBILE YANG LEBIH RAPI DAN PROPOSIONAL */
    @media (max-width: 768px) {
        .hub-container { padding: 0 12px; }
        .hub-grid { grid-template-columns: 1fr; gap: 16px; }
        .grid-2-form { grid-template-columns: 1fr; }
        .project-card-hub { padding: 24px 20px; }
        .panel-card { padding: 16px; }
        
        /* Merapikan daftar section pada tampilan mobile */
        .section-list-item {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
            padding: 14px !important;
        }
        .section-list-actions {
            display: flex;
            gap: 8px;
            width: 100%;
        }
        .section-list-actions a {
            flex: 1;
            text-align: center;
            justify-content: center;
        }
        .status-btn-group {
            display: flex;
            width: 100%;
        }
        .status-btn {
            flex: 1;
            text-align: center;
            padding: 6px 4px;
            font-size: 11px;
        }
    }
</style>

<div class="hub-container">
    <?php if ($view === 'hub'): ?>
        <div style="margin-top: 10px;">
            <p class="eyebrow" style="color: var(--primary, #a78bfa); font-weight:700; letter-spacing:1px; margin-bottom:6px; font-size:12px;">PUSAT KONTROL SECTION</p>
            <h2 style="font-size: 24px; color: #fff; font-weight: 800; margin-bottom: 8px;">Pilih Aksi Section</h2>
            <p style="color: var(--text-muted); font-size: 14px; margin: 0; line-height: 1.6;">Gunakan builder blok dinamis untuk menyusun halaman detail secara bebas.</p>
        </div>
        <div class="hub-grid">
            <a href="custom-sections.php?view=create" class="project-card-hub">
                <div>
                    <span class="hub-icon">➕</span>
                    <h3 class="hub-title">Buat Section Baru</h3>
                    <p class="hub-desc">Rancang halaman dengan menambah blok modular berulang kali sesuai kebutuhan.</p>
                </div>
                <div class="hub-action-btn"><span>Mulai Menyusun Form</span><span>→</span></div>
            </a>
            <a href="custom-sections.php?view=list" class="project-card-hub">
                <div>
                    <span class="hub-icon">📂</span>
                    <h3 class="hub-title">Kelola &amp; Daftar Section</h3>
                    <p class="hub-desc">Lihat ringkasan dari <?= count($sections) ?> section yang pernah dibuat.</p>
                </div>
                <div class="hub-action-btn"><span>Buka Daftar (<?= count($sections) ?>)</span><span>→</span></div>
            </a>
        </div>

    <?php elseif ($view === 'form'): ?>
        <a href="custom-sections.php" class="btn-back-hub">← Kembali ke Menu Utama</a>
        <?php if ($error): ?>
            <div style="padding: 12px; background: rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); border-radius:8px; color:#f87171; margin-bottom:20px; font-size:13.5px;">✕ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="panel-card">
            <h3 style="font-size: 17px; color: #fff; margin-bottom: 6px;">
                <?= $editData ? '✏️ Edit Halaman: ' . htmlspecialchars($editData['title_id']) : '➕ Buat Halaman & Section Baru' ?>
            </h3>
            <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Anda bisa menambahkan komponen blok modular berkali-kali di bawah.</p>

            <form method="POST" id="mainForm" enctype="multipart/form-data">
                <input type="hidden" name="action_save" value="1">
                <input type="hidden" name="sec_id" id="sec_id" value="<?= htmlspecialchars($editData['id'] ?? '') ?>">

                <div style="background: rgba(139,92,246,0.06); border: 1px solid rgba(139,92,246,0.25); border-radius: 10px; padding: 16px; margin-bottom: 24px;">
                    <label style="font-size: 13px; font-weight: 700; color: #fff; display:block; margin-bottom: 10px;">🎯 Lokasi Penayangan (Status Rilis):</label>
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                        <?php $curStatus = $editData['publish_status'] ?? 'home'; if ($curStatus !== 'archive' && $curStatus !== 'draft') { $curStatus = 'home'; } ?>
                        <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; background:#090d16; padding:12px; border-radius:8px; border:1px solid rgba(255,255,255,0.08);">
                            <input type="radio" name="publish_status" value="home" <?= $curStatus === 'home' ? 'checked' : '' ?> style="margin-top:3px; accent-color:var(--primary);">
                            <div><strong style="color:#34d399; font-size:13px; display:block;">🏠 Tampil di Beranda (Home)</strong><span style="font-size:11.5px; color:var(--text-muted);">Muncul di halaman depan &amp; halaman penuh.</span></div>
                        </label>
                        <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; background:#090d16; padding:12px; border-radius:8px; border:1px solid rgba(255,255,255,0.08);">
                            <input type="radio" name="publish_status" value="archive" <?= $curStatus === 'archive' ? 'checked' : '' ?> style="margin-top:3px; accent-color:var(--primary);">
                            <div><strong style="color:#38bdf8; font-size:13px; display:block;">📂 Hanya di Arsip (Umum)</strong><span style="font-size:11.5px; color:var(--text-muted);">Hanya muncul di menu Arsip.</span></div>
                        </label>
                        <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; background:#090d16; padding:12px; border-radius:8px; border:1px solid rgba(255,255,255,0.08);">
                            <input type="radio" name="publish_status" value="draft" <?= $curStatus === 'draft' ? 'checked' : '' ?> style="margin-top:3px; accent-color:var(--primary);">
                            <div><strong style="color:#f59e0b; font-size:13px; display:block;">🔒 Draf (Disembunyikan)</strong><span style="font-size:11.5px; color:var(--text-muted);">Disimpan saja, belum dipublikasikan.</span></div>
                        </label>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label>📅 Tanggal Publikasi</label>
                    <?php $defaultDate = !empty($editData['created_at']) ? date('Y-m-d', strtotime($editData['created_at'])) : date('Y-m-d'); ?>
                    <input type="date" name="custom_date" class="form-control" value="<?= $defaultDate ?>" style="max-width: 280px;">
                </div>

                <div style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
                    <div class="lang-tabs">
                        <button type="button" class="lang-tab-btn active" onclick="switchLang('id')" id="tab-btn-id">🇮🇩 Bahasa Indonesia</button>
                        <button type="button" class="lang-tab-btn" onclick="switchLang('en')" id="tab-btn-en">🇬🇧 English</button>
                    </div>

                    <div id="pane-id">
                        <div class="grid-2-form">
                            <div class="form-group"><label>Label Atas (Eyebrow ID)</label><input type="text" name="eyebrow_id" class="form-control" value="<?= htmlspecialchars($editData['eyebrow_id'] ?? '') ?>"></div>
                            <div class="form-group"><label>Judul Halaman / Section *</label><input type="text" name="title_id" class="form-control" value="<?= htmlspecialchars($editData['title_id'] ?? '') ?>" required></div>
                        </div>
                        <div class="form-group"><label>Cuplikan Singkat (Teaser ID)</label><textarea name="teaser_id" rows="2" class="form-control"><?= htmlspecialchars($editData['teaser_id'] ?? '') ?></textarea></div>
                        <div class="form-group"><label>Teks Tombol Buka Halaman (ID)</label><input type="text" name="btn_text_id" class="form-control" value="<?= htmlspecialchars($editData['btn_text_id'] ?? 'Pelajari Lebih Lanjut →') ?>"></div>
                    </div>

                    <div id="pane-en" style="display: none;">
                        <div class="grid-2-form">
                            <div class="form-group"><label>Eyebrow (EN)</label><input type="text" name="eyebrow_en" class="form-control" value="<?= htmlspecialchars($editData['eyebrow_en'] ?? '') ?>"></div>
                            <div class="form-group"><label>Page / Section Title (EN)</label><input type="text" name="title_en" class="form-control" value="<?= htmlspecialchars($editData['title_en'] ?? '') ?>"></div>
                        </div>
                        <div class="form-group"><label>Short Teaser (EN)</label><textarea name="teaser_en" rows="2" class="form-control"><?= htmlspecialchars($editData['teaser_en'] ?? '') ?></textarea></div>
                        <div class="form-group"><label>Open Page Button Text (EN)</label><input type="text" name="btn_text_en" class="form-control" value="<?= htmlspecialchars($editData['btn_text_en'] ?? 'Learn More →') ?>"></div>
                    </div>

                    <div style="margin-top: 14px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span style="font-size: 12.5px; color: var(--text-muted);">Gaya Tampilan Kartu di Home:</span>
                        <select name="home_layout" class="form-control" style="width: auto; min-width: 140px; padding: 6px 12px; font-size: 12.5px;">
                            <option value="cards" <?= ($editData['home_layout'] ?? '') === 'cards' ? 'selected' : '' ?>>Cards Grid</option>
                            <option value="split" <?= ($editData['home_layout'] ?? '') === 'split' ? 'selected' : '' ?>>Split Story</option>
                            <option value="banner" <?= ($editData['home_layout'] ?? '') === 'banner' ? 'selected' : '' ?>>Simple Banner</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                    <h4 style="font-size: 14px; color: #fff; margin:0;">🛠️ Susun Blok Halaman Detail:</h4>
                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                        <button type="button" onclick="addBlock('story')" style="background: rgba(139,92,246,0.15); border:1px solid rgba(139,92,246,0.3); color:#c4b5fd; padding:6px 12px; border-radius:6px; font-size:11.5px; cursor:pointer;">+ Narasi Foto</button>
                        <button type="button" onclick="addBlock('cards')" style="background: rgba(56,189,248,0.15); border:1px solid rgba(56,189,248,0.3); color:#38bdf8; padding:6px 12px; border-radius:6px; font-size:11.5px; cursor:pointer;">+ Kartu Proyek</button>
                        <button type="button" onclick="addBlock('article')" style="background: rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); color:#34d399; padding:6px 12px; border-radius:6px; font-size:11.5px; cursor:pointer;">+ Ulasan Artikel</button>
                        <button type="button" onclick="addBlock('cta')" style="background: rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); color:#f59e0b; padding:6px 12px; border-radius:6px; font-size:11.5px; cursor:pointer;">+ Tombol Aksi</button>
                        <button type="button" onclick="addBlock('timeline')" style="background: rgba(236,72,153,0.15); border:1px solid rgba(236,72,153,0.3); color:#f472b6; padding:6px 12px; border-radius:6px; font-size:11.5px; cursor:pointer;">+ Linimasa (Timeline)</button>
                    </div>
                </div>

                <div id="dynamic-blocks-container"></div>

                <div style="display: flex; gap: 12px; margin-top: 24px;">
                    <button type="submit" class="btn-submit" style="flex: 1;">💾 Simpan Halaman &amp; Section</button>
                    <a href="custom-sections.php" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); padding: 11px 20px; border-radius: 8px; text-decoration: none; font-size: 13.5px; display:inline-flex; align-items:center;">Batal</a>
                </div>
            </form>
        </div>

    <?php elseif ($view === 'list'): ?>
        <a href="custom-sections.php" class="btn-back-hub">← Kembali ke Menu Utama</a>
        <?php $msg = $_GET['msg'] ?? ''; ?>
        <?php if ($msg === 'status_updated'): ?>
            <div style="padding: 12px; background: rgba(56,189,248,0.12); border:1px solid rgba(56,189,248,0.3); border-radius:8px; color:#38bdf8; margin-bottom:20px; font-size:13.5px;">✓ Status penayangan berhasil diperbarui!</div>
        <?php elseif ($msg === 'saved'): ?>
            <div style="padding: 12px; background: rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.3); border-radius:8px; color:#34d399; margin-bottom:20px; font-size:13.5px;">✓ Section berhasil disimpan!</div>
        <?php elseif ($msg === 'deleted'): ?>
            <div style="padding: 12px; background: rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); border-radius:8px; color:#f87171; margin-bottom:20px; font-size:13.5px;">✓ Section berhasil dihapus!</div>
        <?php endif; ?>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; flex-wrap:wrap; gap:12px;">
            <div>
                <h3 style="font-size: 18px; color: #fff; margin-bottom: 4px;">📋 Daftar Kelola Section &amp; Halaman</h3>
                <p style="font-size: 13px; color: var(--text-muted); margin:0;">Kelola bagian mana yang tampil di Beranda (Home) atau Arsip (Umum) dengan mudah.</p>
            </div>
            <a href="custom-sections.php?view=create" style="background: var(--primary); color: #fff; padding: 9px 18px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600;">+ Buat Section Baru</a>
        </div>

        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 20px;">
            <?php if (empty($sections)): ?>
                <p style="color: var(--text-muted); font-size: 13px; text-align: center; padding: 40px 0;">Belum ada section kustom.</p>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:14px;">
                    <?php foreach ($sections as $sec): 
                        $stat = $sec['publish_status'] ?? 'home';
                        if ($stat !== 'archive' && $stat !== 'draft') { $stat = 'home'; }
                        $dateFormatted = !empty($sec['created_at']) ? date('d M Y', strtotime($sec['created_at'])) : date('d M Y');
                    ?>
                        <div class="section-list-item" style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 260px;">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px; flex-wrap:wrap;">
                                    <span style="font-size: 10.5px; text-transform: uppercase; color: var(--primary); font-weight: 700;"><?= htmlspecialchars($sec['eyebrow_id'] ?: 'INISIATIF') ?></span>
                                    <span style="font-size: 11px; color: #94a3b8; background: rgba(255,255,255,0.04); padding: 2px 7px; border-radius: 4px;">📅 <?= $dateFormatted ?></span>
                                    
                                    <?php if ($stat === 'home'): ?>
                                        <span style="background:rgba(16,185,129,0.18); color:#34d399; font-size:11px; font-weight:700; padding:3px 10px; border-radius:6px; border:1px solid rgba(16,185,129,0.3);">🏠 Tampil di Beranda (Home)</span>
                                    <?php elseif ($stat === 'archive'): ?>
                                        <span style="background:rgba(56,189,248,0.18); color:#38bdf8; font-size:11px; font-weight:700; padding:3px 10px; border-radius:6px; border:1px solid rgba(56,189,248,0.3);">📂 Hanya di Arsip (Umum)</span>
                                    <?php else: ?>
                                        <span style="background:rgba(245,158,11,0.18); color:#f59e0b; font-size:11px; font-weight:700; padding:3px 10px; border-radius:6px; border:1px solid rgba(245,158,11,0.3);">🔒 Draf (Disembunyikan)</span>
                                    <?php endif; ?>
                                </div>
                                <h4 style="font-size: 16px; color: #fff; margin-bottom: 4px;"><?= htmlspecialchars($sec['title_id'] ?? '') ?></h4>
                                <p style="font-size: 12.5px; color: var(--text-muted); margin:0;"><?= htmlspecialchars($sec['teaser_id'] ?? '') ?></p>
                                
                                <div style="margin-top: 14px; display: flex; align-items: center; gap: 8px; background: rgba(0,0,0,0.2); padding: 8px 12px; border-radius: 8px; width: fit-content; border:1px solid rgba(255,255,255,0.04); flex-wrap:wrap;">
                                    <span style="font-size: 12px; font-weight:600; color: #cbd5e1;">Pindahkan Lokasi:</span>
                                    <div class="status-btn-group">
                                        <a href="custom-sections.php?id=<?= urlencode($sec['id']) ?>&set_status=home" class="status-btn <?= $stat === 'home' ? 'active-home' : '' ?>">🏠 Beranda</a>
                                        <a href="custom-sections.php?id=<?= urlencode($sec['id']) ?>&set_status=archive" class="status-btn <?= $stat === 'archive' ? 'active-archive' : '' ?>">📂 Arsip</a>
                                        <a href="custom-sections.php?id=<?= urlencode($sec['id']) ?>&set_status=draft" class="status-btn <?= $stat === 'draft' ? 'active-draft' : '' ?>">🔒 Draf</a>
                                    </div>
                                </div>
                                <div style="margin-top: 8px; font-size: 11.5px;"><a href="../explore.php?slug=<?= urlencode($sec['slug'] ?? '') ?>" target="_blank" style="color: #38bdf8; text-decoration: none;">Pratinjau Halaman Publik ↗</a></div>
                            </div>
                            <div class="section-list-actions" style="display: flex; gap: 8px;">
                                <a href="custom-sections.php?edit=<?= urlencode($sec['id']) ?>" style="padding: 7px 14px; background: rgba(139,92,246,0.15); border:1px solid rgba(139,92,246,0.3); color:#c4b5fd; border-radius:6px; text-decoration:none; font-size:12px; font-weight:600; display:inline-flex; align-items:center; justify-content:center;">✏️ Edit</a>
                                <a href="custom-sections.php?delete=<?= urlencode($sec['id']) ?>" onclick="return confirm('Hapus section ini?')" style="padding: 7px 14px; background: rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); color:#f87171; border-radius:6px; text-decoration:none; font-size:12px; font-weight:600; display:inline-flex; align-items:center; justify-content:center;">🗑️ Hapus</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function switchLang(lang) {
    document.getElementById('tab-btn-id').classList.toggle('active', lang === 'id');
    document.getElementById('tab-btn-en').classList.toggle('active', lang === 'en');
    document.getElementById('pane-id').style.display = (lang === 'id') ? 'block' : 'none';
    document.getElementById('pane-en').style.display = (lang === 'en') ? 'block' : 'none';
}

let blockIndex = 0;
const container = document.getElementById('dynamic-blocks-container');

function addBlock(type, data = {}) {
    const idx = blockIndex++;
    const div = document.createElement('div');
    div.className = 'dynamic-block-card';
    div.dataset.index = idx;

    let titleName = '';
    let contentHtml = '';

    if (type === 'story') {
        titleName = '📖 Blok Narasi & Gambar (Split Story - Atur Posisi Kiri/Kanan)';
        const pos = data.image_position || 'left';
        contentHtml = `
            <input type="hidden" name="block_type[]" value="story">
            <input type="hidden" name="block_story_image_existing[${idx}]" value="${data.image || ''}">
            <div class="grid-2-form" style="margin-bottom:12px;">
                <div class="form-group">
                    <label>Posisi Gambar</label>
                    <select name="block_story_img_pos[${idx}]" class="form-control">
                        <option value="left" ${pos === 'left' ? 'selected' : ''}>Gambar di Kiri, Paragraf di Kanan</option>
                        <option value="right" ${pos === 'right' ? 'selected' : ''}>Paragraf di Kiri, Gambar di Kanan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Upload Gambar / Foto</label>
                    <input type="file" name="block_story_image_file[${idx}]" class="form-control" accept="image/*">
                    ${data.image ? `<small style="color:#34d399; margin-top:4px;">Tersimpan: ${data.image}</small>` : ''}
                </div>
            </div>
            <div class="grid-2-form">
                <div class="form-group"><label>Narasi (ID)</label><textarea name="block_story_body_id[${idx}]" rows="3" class="form-control">${data.body_id || ''}</textarea></div>
                <div class="form-group"><label>Narrative (EN)</label><textarea name="block_story_body_en[${idx}]" rows="3" class="form-control">${data.body_en || ''}</textarea></div>
            </div>
        `;
    } else if (type === 'cards') {
        titleName = '🎴 Blok Kumpulan Kartu Proyek (Cards Grid)';
        contentHtml = `
            <input type="hidden" name="block_type[]" value="cards">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <span style="font-size:12px; color:var(--text-muted);">Daftar Kartu dalam Blok Ini:</span>
                <button type="button" onclick="addCardToBlock(this, ${idx})" style="background:rgba(56,189,248,0.15); border:1px solid rgba(56,189,248,0.3); color:#38bdf8; padding:4px 10px; border-radius:4px; font-size:11px; cursor:pointer;">+ Tambah Kartu</button>
            </div>
            <div class="block-cards-subcontainer" id="block-cards-${idx}"></div>
        `;
    } else if (type === 'article') {
        titleName = '📝 Blok Ulasan Artikel (Deep Dive)';
        contentHtml = `
            <input type="hidden" name="block_type[]" value="article">
            <div class="grid-2-form">
                <div class="form-group"><label>Sub-Judul (ID)</label><input type="text" name="block_art_heading_id[${idx}]" class="form-control" value="${data.heading_id || ''}"></div>
                <div class="form-group"><label>Sub-Heading (EN)</label><input type="text" name="block_art_heading_en[${idx}]" class="form-control" value="${data.heading_en || ''}"></div>
            </div>
            <div class="grid-2-form">
                <div class="form-group"><label>Ulasan Lengkap (ID)</label><textarea name="block_art_content_id[${idx}]" rows="4" class="form-control">${data.content_id || ''}</textarea></div>
                <div class="form-group"><label>Deep Dive Content (EN)</label><textarea name="block_art_content_en[${idx}]" rows="4" class="form-control">${data.content_en || ''}</textarea></div>
            </div>
        `;
    } else if (type === 'cta') {
        titleName = '📢 Blok Tombol Aksi (Action Banner)';
        contentHtml = `
            <input type="hidden" name="block_type[]" value="cta">
            <div class="grid-2-form">
                <div class="form-group"><label>Headline Ajakan (ID)</label><input type="text" name="block_cta_title_id[${idx}]" class="form-control" value="${data.title_id || ''}"></div>
                <div class="form-group"><label>Headline (EN)</label><input type="text" name="block_cta_title_en[${idx}]" class="form-control" value="${data.title_en || ''}"></div>
            </div>
            <div class="grid-2-form">
                <div class="form-group"><label>Label Tombol</label><input type="text" name="block_cta_btn_label[${idx}]" class="form-control" value="${data.btn_label || ''}"></div>
                <div class="form-group"><label>URL / Tautan Tombol</label><input type="text" name="block_cta_btn_url[${idx}]" class="form-control" value="${data.btn_url || ''}"></div>
            </div>
        `;
    } else if (type === 'timeline') {
        titleName = '⏳ Blok Linimasa / Milestone (Timeline Estetik)';
        contentHtml = `
            <input type="hidden" name="block_type[]" value="timeline">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <span style="font-size:12px; color:var(--text-muted);">Daftar Milestone / Tahun dalam Linimasa:</span>
                <button type="button" onclick="addTimelineToBlock(this, ${idx})" style="background:rgba(236,72,153,0.15); border:1px solid rgba(236,72,153,0.3); color:#f472b6; padding:4px 10px; border-radius:4px; font-size:11px; cursor:pointer;">+ Tambah Milestone</button>
            </div>
            <div class="block-timeline-subcontainer" id="block-timeline-${idx}"></div>
        `;
    }

    div.innerHTML = `
        <div class="block-header">
            <strong style="font-size:13px; color:#fff;">${titleName}</strong>
            <button type="button" class="btn-remove-block" onclick="this.closest('.dynamic-block-card').remove()">✕ Hapus Blok Ini</button>
        </div>
        ${contentHtml}
    `;

    container.appendChild(div);

    if (type === 'cards' && data.items && Array.isArray(data.items)) {
        const subContainer = document.getElementById(`block-cards-${idx}`);
        data.items.forEach(item => { appendCardItemHtml(subContainer, idx, item); });
    } else if (type === 'cards') {
        const subContainer = document.getElementById(`block-cards-${idx}`);
        appendCardItemHtml(subContainer, idx, {});
    }

    if (type === 'timeline' && data.items && Array.isArray(data.items)) {
        const subContainer = document.getElementById(`block-timeline-${idx}`);
        data.items.forEach(item => { appendTimelineItemHtml(subContainer, idx, item); });
    } else if (type === 'timeline') {
        const subContainer = document.getElementById(`block-timeline-${idx}`);
        appendTimelineItemHtml(subContainer, idx, {});
    }
}

function addCardToBlock(btn, blockIdx) {
    const subContainer = document.getElementById(`block-cards-${blockIdx}`);
    appendCardItemHtml(subContainer, blockIdx, {});
}

function appendCardItemHtml(subContainer, blockIdx, card) {
    const cardDiv = document.createElement('div');
    cardDiv.style.cssText = "background:rgba(0,0,0,0.25); border:1px dashed rgba(255,255,255,0.15); border-radius:8px; padding:12px; margin-bottom:10px; position:relative;";
    cardDiv.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" style="position:absolute; top:8px; right:8px; background:rgba(239,68,68,0.15); color:#f87171; border:none; border-radius:4px; padding:2px 8px; font-size:10px; cursor:pointer;">✕</button>
        <div style="display:flex; gap:10px; margin-bottom:8px;">
            <div style="width:60px;"><label style="font-size:10px; color:#94a3b8;">Ikon</label><input type="text" name="bc_icon[${blockIdx}][]" value="${card.icon || '💡'}" class="form-control" style="text-align:center;"></div>
            <div style="flex:1;"><label style="font-size:10px; color:#94a3b8;">Judul Kartu (ID) *</label><input type="text" name="bc_title_id[${blockIdx}][]" value="${card.title_id || ''}" class="form-control" required></div>
            <div style="flex:1;"><label style="font-size:10px; color:#94a3b8;">Judul Kartu (EN)</label><input type="text" name="bc_title_en[${blockIdx}][]" value="${card.title_en || ''}" class="form-control"></div>
        </div>
        <div class="grid-2-form">
            <div><label style="font-size:10px; color:#94a3b8;">Ringkasan (ID)</label><textarea name="bc_desc_id[${blockIdx}][]" rows="2" class="form-control">${card.desc_id || ''}</textarea></div>
            <div><label style="font-size:10px; color:#94a3b8;">Summary (EN)</label><textarea name="bc_desc_en[${blockIdx}][]" rows="2" class="form-control">${card.desc_en || ''}</textarea></div>
        </div>
    `;
    subContainer.appendChild(cardDiv);
}

function addTimelineToBlock(btn, blockIdx) {
    const subContainer = document.getElementById(`block-timeline-${blockIdx}`);
    appendTimelineItemHtml(subContainer, blockIdx, {});
}

function appendTimelineItemHtml(subContainer, blockIdx, item) {
    const tDiv = document.createElement('div');
    tDiv.style.cssText = "background:rgba(0,0,0,0.25); border:1px dashed rgba(255,255,255,0.15); border-radius:8px; padding:12px; margin-bottom:10px; position:relative;";
    tDiv.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" style="position:absolute; top:8px; right:8px; background:rgba(239,68,68,0.15); color:#f87171; border:none; border-radius:4px; padding:2px 8px; font-size:10px; cursor:pointer;">✕</button>
        <div class="grid-2-form" style="margin-bottom:8px;">
            <div><label style="font-size:10px; color:#94a3b8;">Periode / Tahun *</label><input type="text" name="bt_year[${blockIdx}][]" value="${item.year || '2026'}" class="form-control" required></div>
            <div><label style="font-size:10px; color:#94a3b8;">Judul Milestone (ID)</label><input type="text" name="bt_title_id[${blockIdx}][]" value="${item.title_id || ''}" class="form-control"></div>
        </div>
        <div class="grid-2-form" style="margin-bottom:8px;">
            <div><label style="font-size:10px; color:#94a3b8;">Judul Milestone (EN)</label><input type="text" name="bt_title_en[${blockIdx}][]" value="${item.title_en || ''}" class="form-control"></div>
            <div><label style="font-size:10px; color:#94a3b8;">Takeaway / Catatan Kunci (ID)</label><input type="text" name="bt_takeaway_id[${blockIdx}][]" value="${item.takeaway_id || ''}" class="form-control"></div>
        </div>
        <div class="grid-2-form" style="margin-bottom:8px;">
            <div><label style="font-size:10px; color:#94a3b8;">Takeaway / Key Takeaway (EN)</label><input type="text" name="bt_takeaway_en[${blockIdx}][]" value="${item.takeaway_en || ''}" class="form-control"></div>
        </div>
        <div class="grid-2-form">
            <div><label style="font-size:10px; color:#94a3b8;">Deskripsi (ID)</label><textarea name="bt_desc_id[${blockIdx}][]" rows="2" class="form-control">${item.desc_id || ''}</textarea></div>
            <div><label style="font-size:10px; color:#94a3b8;">Description (EN)</label><textarea name="bt_desc_en[${blockIdx}][]" rows="2" class="form-control">${item.desc_en || ''}</textarea></div>
        </div>
    `;
    subContainer.appendChild(tDiv);
}

<?php if (!empty($editData['blocks']) && is_array($editData['blocks'])): ?>
    window.addEventListener('DOMContentLoaded', function() {
        const savedBlocks = <?= json_encode($editData['blocks']) ?>;
        savedBlocks.forEach(b => addBlock(b.type, b));
    });
<?php endif; ?>
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>