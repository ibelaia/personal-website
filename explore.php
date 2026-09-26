<?php
$slug = trim($_GET['slug'] ?? $_GET['id'] ?? '');

$jsonPath = __DIR__ . '/data/custom_sections.json';
$pageData = null;

if (file_exists($jsonPath) && !empty($slug)) {
    $sections = json_decode(file_get_contents($jsonPath), true);
    if (is_array($sections)) {
        foreach ($sections as $s) {
            if (($s['slug'] ?? '') === $slug || ($s['id'] ?? '') === $slug) {
                $pageData = $s;
                break;
            }
        }
    }
}

if (!$pageData) {
    header("Location: index.php");
    exit;
}

$page  = 'explore';
$title = $pageData['title_id'] ?? 'Detail Eksplorasi';
$dateFormatted = !empty($pageData['created_at']) ? date('d F Y', strtotime($pageData['created_at'])) : date('d F Y');

include 'partials/header.php';
?>

<style>
    .explore-main-container { padding-top: 105px; padding-bottom: 80px; width: 100%; max-width: 1000px; margin: 0 auto; padding-left: 20px; padding-right: 20px; box-sizing: border-box; }
    .top-nav-bar { display: flex; justify-content: flex-start; margin-bottom: 24px; }
    .btn-back-link { display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted, #94a3b8); font-size: 13px; font-weight: 600; text-decoration: none; padding: 6px 14px; border-radius: 8px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); transition: all 0.2s ease; cursor: pointer; }
    .btn-back-link:hover { color: #fff; background: rgba(139, 92, 246, 0.15); border-color: rgba(139, 92, 246, 0.35); transform: translateX(-2px); }
    .explore-header-box { margin-bottom: 36px; padding-bottom: 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); }
    .explore-header-box .eyebrow-row { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; flex-wrap: wrap; }
    .explore-header-box .eyebrow { font-size: 11.5px; letter-spacing: 1.5px; text-transform: uppercase; color: #a78bfa; font-weight: 700; }
    .explore-header-box .publish-date { font-size: 12px; color: var(--text-muted, #94a3b8); display: inline-flex; align-items: center; gap: 5px; }
    .explore-header-box h1 { font-size: clamp(24px, 3.5vw, 34px); color: #fff; line-height: 1.3; margin-bottom: 12px; font-weight: 800; }
    .explore-header-box .lead-teaser { color: var(--text-muted, #94a3b8); font-size: clamp(14px, 1.8vw, 15.5px); line-height: 1.7; margin: 0; max-width: 820px; }
    .explore-content-wrap { display: flex; flex-direction: column; gap: 30px; width: 100%; }
    .section-block { background: var(--bg-card, #0b1120); border: 1px solid var(--border-color, rgba(255,255,255,0.08)); border-radius: 14px; padding: 28px; box-sizing: border-box; }
    .split-story-grid { display: grid; grid-template-columns: 1fr 1.3fr; gap: 28px; align-items: center; }
    .split-story-grid.img-right { grid-template-columns: 1.3fr 1fr; }
    .split-story-grid.img-right .story-img-wrap { order: 2; }
    .story-img-wrap { border-radius: 10px; overflow: hidden; width: 100%; min-height: 220px; max-height: 340px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; }
    .story-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .explore-cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; width: 100%; }
    .explore-card-item { background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 12px; padding: 20px; box-sizing: border-box; }
    .article-body-text { color: #cbd5e1; font-size: 14.5px; line-height: 1.8; }
    .cta-banner-box { text-align: center; border-color: rgba(139, 92, 246, 0.3); background: linear-gradient(135deg, rgba(30, 27, 75, 0.4), rgba(15, 23, 42, 0.8)); padding: 32px 20px; }

    @media (max-width: 768px) {
        .explore-main-container { padding-top: 85px; padding-left: 16px; padding-right: 16px; }
        .section-block { padding: 18px; }
        .split-story-grid, .split-story-grid.img-right { grid-template-columns: 1fr; gap: 18px; }
        .split-story-grid.img-right .story-img-wrap { order: 0; }
        .story-img-wrap { max-height: 220px; }
        .explore-cards-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="explore-main-container">
    <div class="top-nav-bar">
        <a href="index.php" id="smartBackBtn" class="btn-back-link">
            <span>←</span> <span id="backBtnText">Kembali ke Beranda</span>
        </a>
    </div>

    <div class="explore-header-box">
        <div class="eyebrow-row">
            <?php if (!empty($pageData['eyebrow_id'])): ?>
                <span class="eyebrow dyn-lang" data-id="<?= htmlspecialchars($pageData['eyebrow_id']) ?>" data-en="<?= htmlspecialchars(!empty($pageData['eyebrow_en']) ? $pageData['eyebrow_en'] : $pageData['eyebrow_id']) ?>">
                    <?= htmlspecialchars($pageData['eyebrow_id']) ?>
                </span>
            <?php endif; ?>
            <span class="publish-date"><span>•</span> <span>📅 <?= $dateFormatted ?></span></span>
        </div>

        <h1 class="dyn-lang" data-id="<?= htmlspecialchars($pageData['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($pageData['title_en']) ? $pageData['title_en'] : ($pageData['title_id'] ?? '')) ?>">
            <?= htmlspecialchars($pageData['title_id'] ?? '') ?>
        </h1>

        <?php if (!empty($pageData['teaser_id'])): ?>
            <p class="lead-teaser dyn-lang" data-id="<?= htmlspecialchars($pageData['teaser_id']) ?>" data-en="<?= htmlspecialchars(!empty($pageData['teaser_en']) ? $pageData['teaser_en'] : $pageData['teaser_id']) ?>">
                <?= htmlspecialchars($pageData['teaser_id']) ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="explore-content-wrap">
        <?php 
        if (!empty($pageData['blocks']) && is_array($pageData['blocks'])):
            foreach ($pageData['blocks'] as $block):
                $type = $block['type'] ?? '';

                if ($type === 'story'):
                    $hasImg = !empty($block['image']);
                    $imgPos = $block['image_position'] ?? 'left';
        ?>
                    <div class="section-block">
                        <div class="split-story-grid <?= $imgPos === 'right' ? 'img-right' : '' ?>" style="<?= !$hasImg ? 'grid-template-columns: 1fr;' : '' ?>">
                            <?php if ($hasImg): ?>
                                <div class="story-img-wrap"><img src="<?= htmlspecialchars($block['image']) ?>" alt="Visual"></div>
                            <?php endif; ?>
                            <div>
                                <div class="dyn-lang" data-id="<?= nl2br(htmlspecialchars($block['body_id'] ?? '')) ?>" data-en="<?= nl2br(htmlspecialchars(!empty($block['body_en']) ? $block['body_en'] : ($block['body_id'] ?? ''))) ?>" style="line-height: 1.85; color: #cbd5e1; font-size: 15px;">
                                    <?= nl2br(htmlspecialchars($block['body_id'] ?? '')) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                elseif ($type === 'cards' && !empty($block['items'])):
                ?>
                    <div class="explore-cards-grid">
                        <?php foreach ($block['items'] as $item): ?>
                            <div class="explore-card-item">
                                <span style="font-size: 28px; display: block; margin-bottom: 10px;"><?= htmlspecialchars($item['icon'] ?? '💡') ?></span>
                                <h3 class="dyn-lang" data-id="<?= htmlspecialchars($item['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($item['title_en']) ? $item['title_en'] : ($item['title_id'] ?? '')) ?>" style="color: #fff; font-size: 16px; margin-bottom: 6px; font-weight: 700;">
                                    <?= htmlspecialchars($item['title_id'] ?? '') ?>
                                </h3>
                                <p class="dyn-lang" data-id="<?= htmlspecialchars($item['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($item['desc_en']) ? $item['desc_en'] : ($item['desc_id'] ?? '')) ?>" style="color: var(--text-muted); font-size: 13px; line-height: 1.6; margin: 0;">
                                    <?= htmlspecialchars($item['desc_id'] ?? '') ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php 
                elseif ($type === 'article'):
                ?>
                    <div class="section-block">
                        <?php if (!empty($block['heading_id'])): ?>
                            <h2 class="dyn-lang" data-id="<?= htmlspecialchars($block['heading_id']) ?>" data-en="<?= htmlspecialchars(!empty($block['heading_en']) ? $block['heading_en'] : $block['heading_id']) ?>" style="color: #fff; font-size: 20px; margin-bottom: 14px; font-weight: 700;">
                                <?= htmlspecialchars($block['heading_id']) ?>
                            </h2>
                        <?php endif; ?>
                        <div class="article-body-text dyn-lang" data-id="<?= nl2br(htmlspecialchars($block['content_id'] ?? '')) ?>" data-en="<?= nl2br(htmlspecialchars(!empty($block['content_en']) ? $block['content_en'] : ($block['content_id'] ?? ''))) ?>">
                            <?= nl2br(htmlspecialchars($block['content_id'] ?? '')) ?>
                        </div>
                    </div>
                <?php 
                elseif ($type === 'cta'):
                ?>
                    <div class="section-block cta-banner-box">
                        <h3 class="dyn-lang" data-id="<?= htmlspecialchars($block['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($block['title_en']) ? $block['title_en'] : ($block['title_id'] ?? '')) ?>" style="color: #fff; font-size: 19px; margin-bottom: 16px; font-weight: 700;">
                            <?= htmlspecialchars($block['title_id'] ?? '') ?>
                        </h3>
                        <?php if (!empty($block['btn_url'])): ?>
                            <a href="<?= htmlspecialchars($block['btn_url']) ?>" class="btn primary" style="display: inline-block;">
                                <?= htmlspecialchars($block['btn_label'] ?: 'Kunjungi Tautan →') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php
                elseif ($type === 'timeline' && !empty($block['items'])):
                ?>
                    <div class="timeline" style="margin-top: 10px;">
                        <?php foreach ($block['items'] as $tItem): ?>
                            <div class="timeline-item">
                                <span class="card-year"><?= htmlspecialchars($tItem['year'] ?? '') ?></span>
                                <h3 class="dyn-lang" data-id="<?= htmlspecialchars($tItem['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($tItem['title_en']) ? $tItem['title_en'] : ($tItem['title_id'] ?? '')) ?>">
                                    <?= htmlspecialchars($tItem['title_id'] ?? '') ?>
                                </h3>
                                <p class="dyn-lang" data-id="<?= htmlspecialchars($tItem['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($tItem['desc_en']) ? $tItem['desc_en'] : ($tItem['desc_id'] ?? '')) ?>">
                                    <?= htmlspecialchars($tItem['desc_id'] ?? '') ?>
                                </p>
                                <?php if (!empty($tItem['takeaway_id'])): ?>
                                    <div class="takeaway" style="margin-top: 12px;">
                                        <strong>Pelajaran Utama:</strong> 
                                        <span class="dyn-lang" data-id="<?= htmlspecialchars($tItem['takeaway_id']) ?>" data-en="<?= htmlspecialchars(!empty($tItem['takeaway_en']) ? $tItem['takeaway_en'] : $tItem['takeaway_id']) ?>">
                                            <?= htmlspecialchars($tItem['takeaway_id']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php
                endif;
            endforeach;
        endif;
        ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const backBtn = document.getElementById('smartBackBtn');
    const backText = document.getElementById('backBtnText');
    const referrer = document.referrer;
    const currentLang = localStorage.getItem('lang') || localStorage.getItem('preferred_lang') || 'id';

    if (referrer && referrer.includes('archive.php')) {
        backBtn.href = 'archive.php';
        backText.textContent = (currentLang === 'en') ? 'Back to Archive' : 'Kembali ke Arsip';
    } else {
        backBtn.href = 'index.php';
        backText.textContent = (currentLang === 'en') ? 'Back to Home' : 'Kembali ke Beranda';
    }
});

function syncExploreLang() {
    const currentLang = localStorage.getItem('lang') || localStorage.getItem('preferred_lang') || 'id';
    document.querySelectorAll('.dyn-lang').forEach(el => {
        const text = (currentLang === 'en') ? el.getAttribute('data-en') : el.getAttribute('data-id');
        if (text) el.innerHTML = text;
    });
}
document.addEventListener('DOMContentLoaded', syncExploreLang);
document.addEventListener('click', function(e) {
    if (e.target.closest('[data-lang]') || e.target.closest('.lang-btn')) {
        setTimeout(syncExploreLang, 80);
    }
});
</script>

<?php include 'partials/footer.php'; ?>