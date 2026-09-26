<?php
$page  = 'archive';
$title = 'Arsip Inisiatif';

include 'partials/header.php';

$jsonPath = __DIR__ . '/data/custom_sections.json';
$allSections = file_exists($jsonPath) ? json_decode(file_get_contents($jsonPath), true) : [];
if (!is_array($allSections)) {
    $allSections = [];
}

// FILTER MUTLAK: HANYA YANG STATUSNYA 'archive'
$archiveOnlyList = array();
foreach ($allSections as $s) {
    if (($s['publish_status'] ?? '') === 'archive') {
        $archiveOnlyList[] = $s;
    }
}
?>

<style>
    .archive-container {
        padding-top: 105px;
        padding-bottom: 80px;
        width: 100%;
        max-width: 1040px;
        margin: 0 auto;
        padding-left: 20px;
        padding-right: 20px;
        box-sizing: border-box;
    }
    .archive-header {
        margin-bottom: 36px;
        padding-bottom: 22px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }
    
    /* GRID PROPER AGAR AMAN UNTUK 1, 2, MAUPUN 20 PROYEK */
    .archive-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
        gap: 22px;
        justify-content: start;
    }

    .archive-card {
        background: var(--bg-card, #0b1120);
        border: 1px solid var(--border-color, rgba(255,255,255,0.08));
        border-radius: 14px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-sizing: border-box;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .archive-card:hover {
        transform: translateY(-3px);
        border-color: rgba(139, 92, 246, 0.4);
    }
</style>

<div class="archive-container">
    <div style="margin-bottom: 20px;">
        <a href="index.php" class="btn outline" style="font-size: 12.5px; padding: 5px 14px; display:inline-flex; align-items:center; gap:6px;">
            ← Kembali ke Beranda
        </a>
    </div>

    <div class="archive-header">
        <p class="eyebrow" data-i18n="archive.eyebrow">DOKUMENTASI LENGKAP</p>
        <h1 style="font-size: clamp(26px, 4vw, 34px); color:#fff; margin-bottom:12px; font-weight:800;" data-i18n="archive.title">
            Arsip Inisiatif &amp; Rekam Jejak
        </h1>
        <p style="color: var(--text-muted); font-size: 14.5px; line-height: 1.6; max-width: 750px; margin:0;" data-i18n="archive.subtitle">
            Kumpulan seluruh inisiatif, eksplorasi masa lalu, dan riset yang telah selesai atau diarsipkan.
        </p>
    </div>

    <?php if (empty($archiveOnlyList)): ?>
        <div style="text-align:center; padding: 50px 0; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px;">
            <span style="font-size: 32px; display: block; margin-bottom: 10px;">📂</span>
            <p style="color:var(--text-muted); font-size:14px; margin:0;">
                Belum ada section yang dipindahkan ke arsip.
            </p>
        </div>
    <?php else: ?>
        <div class="archive-grid">
            <?php foreach ($archiveOnlyList as $item): 
                $dateFormatted = !empty($item['created_at']) ? date('d M Y', strtotime($item['created_at'])) : date('d M Y');
            ?>
                <div class="archive-card">
                    <!-- HEADER KARTU: LABEL & TANGGAL LENGKAP -->
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
                        <span style="font-size: 10.5px; text-transform: uppercase; color: #a78bfa; font-weight: 700; display:block;">
                            <?= htmlspecialchars($item['eyebrow_id'] ?: 'ARSIP') ?>
                        </span>
                        <span style="font-size: 11px; color: var(--text-muted, #94a3b8); background: rgba(255,255,255,0.04); padding: 2px 7px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                            <span>📅</span> <span><?= $dateFormatted ?></span>
                        </span>
                    </div>

                    <h3 style="font-size: 17px; color: #fff; margin-bottom: 8px; font-weight: 700; line-height: 1.35;">
                        <?= htmlspecialchars($item['title_id'] ?? '') ?>
                    </h3>
                    <p style="font-size: 12.5px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px; flex: 1;">
                        <?= htmlspecialchars($item['teaser_id'] ?? '') ?>
                    </p>
                    <div style="margin-top: auto; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.05);">
                        <a href="explore.php?slug=<?= urlencode($item['slug'] ?? '') ?>" class="btn outline" style="font-size: 12px; padding: 6px 14px; display: inline-block;">
                            Buka Catatan Lengkap →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'partials/footer.php'; ?>