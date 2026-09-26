<?php
$page  = 'achievement';
$title = 'Achievements';
include 'partials/header.php';

// Baca data prestasi & sertifikat dinamis dari JSON
$achPath = __DIR__ . '/data/achievements_cards.json';
$achData = file_exists($achPath) ? (json_decode(file_get_contents($achPath), true) ?: []) : [];

$medals = !empty($achData['medals']) ? $achData['medals'] : [
    [
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
        'link'      => ''
    ],
    [
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
        'link'      => ''
    ]
];

$certs = !empty($achData['certificates']) ? $achData['certificates'] : [
    [
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
        'link'      => ''
    ],
    [
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
        'link'      => ''
    ],
    [
        'image'     => '',
        'badge_id'  => 'SEMINAR TEKNOLOGI',
        'badge_en'  => 'TECH SEMINAR',
        'year'      => '2025',
        'icon'      => '🎨',
        'issuer_id' => 'Design Community',
        'issuer_en' => 'Design Community',
        'title_id'  => 'UI/UX Interface Design',
        'title_en'  => 'UI/UX Interface Design',
        'desc_id'   => 'Merancang wireframe, user flow, dan desain antarmuka interaktif yang berorientasi pada kepuasan pengguna.',
        'desc_en'   => 'Designed wireframes, user journeys, and interactive user-centered modern digital interface prototypes.',
        'link'      => ''
    ],
    [
        'image'     => '',
        'badge_id'  => 'WEBINAR KHUSUS',
        'badge_en'  => 'SPECIAL WEBINAR',
        'year'      => '2026',
        'icon'      => '⛓️',
        'issuer_id' => 'Web3 Developer Hub',
        'issuer_en' => 'Web3 Developer Hub',
        'title_id'  => 'Web3 & Smart Contract',
        'title_en'  => 'Web3 & Smart Contract',
        'desc_id'   => 'Pemahaman fundamental blockchain, smart contract development, dan integrasi dApp terdesentralisasi.',
        'desc_en'   => 'Deep dive into blockchain fundamentals, smart contract architecture, and decentralized dApp integration.',
        'link'      => ''
    ]
];
?>

<style>
    /* PENANDA FASE ELEGAN */
    .ach-section-divider {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: rgba(139, 92, 246, 0.12);
        border: 1px solid rgba(139, 92, 246, 0.3);
        border-radius: 20px;
        color: #c4b5fd;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 16px;
    }
    .ach-section-divider::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary, #8b5cf6);
        box-shadow: 0 0 8px #8b5cf6;
    }

    /* GRID 4 KOLOM MAKSIMAL DI DESKTOP, TIDAK KAKU */
    .ach-grid-4-cols {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin-top: 14px;
    }
    @media (min-width: 1200px) {
        .ach-grid-4-cols {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* KARTU PRESTASI & SERTIFIKAT */
    .ach-compact-card {
        background: var(--bg-card, #0b1120);
        border: 1px solid var(--border-color, rgba(255, 255, 255, 0.08));
        border-radius: 14px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .ach-compact-card:hover {
        transform: translateY(-3px);
        border-color: rgba(139, 92, 246, 0.45);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.35);
    }

    /* PREVIEW GAMBAR SERTIFIKAT */
    .ach-media-box {
        width: 100%;
        height: 130px;
        background: #060913;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .ach-media-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .ach-compact-card:hover .ach-media-box img {
        transform: scale(1.04);
    }
    .media-fallback-art {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }
    .media-fallback-art span.icon {
        font-size: 34px;
        opacity: 0.75;
        filter: drop-shadow(0 3px 8px rgba(139, 92, 246, 0.3));
    }
    .media-fallback-art span.text {
        font-size: 10px;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.45);
        font-weight: 700;
    }

    /* BADGE & KONTEN KARTU */
    .ach-card-inner {
        padding: 14px 16px 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .ach-meta-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .ach-badge-tag {
        background: rgba(139, 92, 246, 0.12);
        color: #c4b5fd;
        font-size: 10.5px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        letter-spacing: 0.4px;
        text-transform: uppercase;
    }
    .ach-year-tag {
        background: rgba(255, 255, 255, 0.04);
        color: var(--text-muted, #94a3b8);
        font-size: 10.5px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
    }

    .ach-issuer-name {
        font-size: 10.5px;
        font-weight: 700;
        color: #38bdf8;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        margin-bottom: 4px;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ach-title-main {
        font-size: 14.5px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 6px;
        line-height: 1.35;
    }
    .ach-desc-text {
        font-size: 12px;
        color: var(--text-muted, #94a3b8);
        line-height: 1.55;
        margin-bottom: 12px;
        flex: 1;
    }

    /* FOOTER AKSI */
    .ach-action-bar {
        margin-top: auto;
        padding-top: 10px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .btn-action-cert {
        background: transparent;
        border: none;
        color: #c4b5fd;
        font-size: 11.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        padding: 0;
        text-decoration: none;
        transition: color 0.2s, transform 0.2s;
    }
    .btn-action-cert:hover {
        color: #fff;
        transform: translateX(3px);
    }

    /* MODAL LIGHTBOX VIEW CERTIFICATE */
    .cert-lightbox-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(5, 8, 14, 0.88);
        backdrop-filter: blur(10px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .cert-lightbox-overlay.active {
        display: flex;
    }
    .cert-lightbox-panel {
        max-width: 800px;
        width: 100%;
        background: #090c14;
        border: 1px solid rgba(139, 92, 246, 0.3);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7);
    }
    .cert-lightbox-head {
        padding: 12px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #0c111d;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .cert-lightbox-img {
        width: 100%;
        max-height: 520px;
        object-fit: contain;
        background: #05080e;
        display: block;
    }

    @media (max-width: 768px) {
        .ach-grid-4-cols {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-header" style="padding-bottom:0;">
    <h1 data-i18n="ach.header.title">Prestasi</h1>
    <p data-i18n="ach.header.subtitle">Setiap pencapaian bercerita—bukan hanya tentang keberhasilan, tetapi juga dedikasi, kegigihan, dan pertumbuhan berkelanjutan.</p>
</div>

<!-- INTRODUCTION -->
<section class="reveal" style="padding-top:20px;">
    <p class="eyebrow" data-i18n="ach.intro.eyebrow">PENGANTAR</p>
    <h2 class="section-heading" data-i18n="ach.intro.heading">Tonggak Pencapaian di Sepanjang Perjalanan</h2>
    <p data-i18n="ach.intro.p1">
        Sepanjang perjalanan akademik, saya berkesempatan mengikuti berbagai kompetisi dan proses belajar yang menantang saya untuk terus berkembang melampaui ruang kelas.
    </p>
</section>

<!-- SEKSI 1: MEDALI & KOMPETISI AKADEMIK (MAKSIMAL 4 KOLOM RAPI) -->
<section class="reveal" style="margin-top:16px;">
    <div class="ach-section-divider">
        <span>🏆</span>
        <span class="ach-dyn-text" data-id="Medali &amp; Kompetisi Sains" data-en="Medals &amp; Science Competitions">Medali &amp; Kompetisi Sains</span>
    </div>
    <h2 class="section-heading" style="font-size:22px; margin-bottom:12px;" data-i18n="ach.feat.heading">Momen yang Berharga</h2>

    <div class="ach-grid-4-cols">
        <?php foreach ($medals as $m): 
            $hasImage = !empty($m['image']) && file_exists($m['image']);
            $hasLink  = !empty($m['link']) && $m['link'] !== '#';
        ?>
            <div class="ach-compact-card">
                <div class="ach-media-box">
                    <?php if ($hasImage): ?>
                        <img src="<?= htmlspecialchars($m['image']) ?>" alt="<?= htmlspecialchars($m['title_en'] ?? '') ?>" loading="lazy">
                    <?php else: ?>
                        <div class="media-fallback-art">
                            <span class="icon"><?= htmlspecialchars($m['icon'] ?? '🥇') ?></span>
                            <span class="text">Official Award</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="ach-card-inner">
                    <div class="ach-meta-bar">
                        <span class="ach-badge-tag ach-dyn-text" data-id="<?= htmlspecialchars($m['badge_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($m['badge_en']) ? $m['badge_en'] : ($m['badge_id'] ?? '')) ?>"><?= htmlspecialchars($m['badge_en'] ?? $m['badge_id'] ?? '') ?></span>
                        <span class="ach-year-tag"><?= htmlspecialchars($m['year'] ?? '2023') ?></span>
                    </div>

                    <span class="ach-issuer-name ach-dyn-text" data-id="<?= htmlspecialchars($m['issuer_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($m['issuer_en']) ? $m['issuer_en'] : ($m['issuer_id'] ?? '')) ?>"><?= htmlspecialchars($m['issuer_en'] ?? $m['issuer_id'] ?? '') ?></span>
                    <h3 class="ach-title-main ach-dyn-text" data-id="<?= htmlspecialchars($m['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($m['title_en']) ? $m['title_en'] : ($m['title_id'] ?? '')) ?>"><?= htmlspecialchars($m['title_en'] ?? $m['title_id'] ?? '') ?></h3>
                    <p class="ach-desc-text ach-dyn-text" data-id="<?= htmlspecialchars($m['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($m['desc_en']) ? $m['desc_en'] : ($m['desc_id'] ?? '')) ?>"><?= htmlspecialchars($m['desc_en'] ?? $m['desc_id'] ?? '') ?></p>

                    <div class="ach-action-bar">
                        <?php if ($hasImage): ?>
                            <button type="button" class="btn-action-cert" onclick="openCertLightbox('<?= htmlspecialchars($m['image']) ?>', '<?= htmlspecialchars(addslashes($m['title_en'] ?? $m['title_id'] ?? 'Certificate')) ?>')">
                                <span>Lihat Sertifikat</span> <span>↗</span>
                            </button>
                        <?php elseif ($hasLink): ?>
                            <a href="<?= htmlspecialchars($m['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn-action-cert">
                                <span>Verifikasi Bukti</span> <span>↗</span>
                            </a>
                        <?php else: ?>
                            <span style="font-size:11px; color:var(--text-muted); font-weight:600;">Terverifikasi Resmi ✓</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- SEKSI 2: SERTIFIKAT, WORKSHOP & WEBINAR (MAKSIMAL 4 KOLOM RAPI) -->
<section class="reveal" style="margin-top:36px;">
    <div class="ach-section-divider">
        <span>📜</span>
        <span class="ach-dyn-text" data-id="Sertifikasi &amp; Pelatihan Teknologi" data-en="Certifications &amp; Tech Workshops">Sertifikasi &amp; Pelatihan Teknologi</span>
    </div>
    <h2 class="section-heading" style="font-size:22px; margin-bottom:12px;" data-i18n="ach.cert.heading">Pengakuan Tambahan</h2>

    <div class="ach-grid-4-cols">
        <?php foreach ($certs as $c): 
            $hasImage = !empty($c['image']) && file_exists($c['image']);
            $hasLink  = !empty($c['link']) && $c['link'] !== '#';
        ?>
            <div class="ach-compact-card">
                <div class="ach-media-box">
                    <?php if ($hasImage): ?>
                        <img src="<?= htmlspecialchars($c['image']) ?>" alt="<?= htmlspecialchars($c['title_en'] ?? '') ?>" loading="lazy">
                    <?php else: ?>
                        <div class="media-fallback-art">
                            <span class="icon"><?= htmlspecialchars($c['icon'] ?? '📜') ?></span>
                            <span class="text">Certified Credential</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="ach-card-inner">
                    <div class="ach-meta-bar">
                        <span class="ach-badge-tag ach-dyn-text" data-id="<?= htmlspecialchars($c['badge_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($c['badge_en']) ? $c['badge_en'] : ($c['badge_id'] ?? '')) ?>"><?= htmlspecialchars($c['badge_en'] ?? $c['badge_id'] ?? '') ?></span>
                        <span class="ach-year-tag"><?= htmlspecialchars($c['year'] ?? '2025') ?></span>
                    </div>

                    <span class="ach-issuer-name ach-dyn-text" data-id="<?= htmlspecialchars($c['issuer_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($c['issuer_en']) ? $c['issuer_en'] : ($c['issuer_id'] ?? '')) ?>"><?= htmlspecialchars($c['issuer_en'] ?? $c['issuer_id'] ?? '') ?></span>
                    <h3 class="ach-title-main ach-dyn-text" data-id="<?= htmlspecialchars($c['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($c['title_en']) ? $c['title_en'] : ($c['title_id'] ?? '')) ?>"><?= htmlspecialchars($c['title_en'] ?? $c['title_id'] ?? '') ?></h3>
                    <p class="ach-desc-text ach-dyn-text" data-id="<?= htmlspecialchars($c['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($c['desc_en']) ? $c['desc_en'] : ($c['desc_id'] ?? '')) ?>"><?= htmlspecialchars($c['desc_en'] ?? $c['desc_id'] ?? '') ?></p>

                    <div class="ach-action-bar">
                        <?php if ($hasImage): ?>
                            <button type="button" class="btn-action-cert" onclick="openCertLightbox('<?= htmlspecialchars($c['image']) ?>', '<?= htmlspecialchars(addslashes($c['title_en'] ?? $c['title_id'] ?? 'Certificate')) ?>')">
                                <span>Lihat Sertifikat</span> <span>↗</span>
                            </button>
                        <?php elseif ($hasLink): ?>
                            <a href="<?= htmlspecialchars($c['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn-action-cert">
                                <span>Verifikasi Bukti</span> <span>↗</span>
                            </a>
                        <?php else: ?>
                            <span style="font-size:11px; color:var(--text-muted); font-weight:600;">Terverifikasi Resmi ✓</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- SEKSI 3: REFLEKSI & CTA -->
<section class="reveal" style="margin-top:40px; margin-bottom:40px;">
    <p class="eyebrow" data-i18n="ach.refl.eyebrow">REFLEKSI</p>
    <h2 class="section-heading" data-i18n="ach.refl.heading">Lebih dari Sekadar Penghargaan</h2>
    <p data-i18n="ach.refl.p1">
        Meskipun medali dan sertifikat adalah pencapaian yang berarti, saya percaya pencapaian terbesar adalah pola pikir yang terbentuk dari proses tersebut.
    </p>

    <blockquote class="quote-block" style="margin-top:24px;">
        <span data-i18n="ach.refl.quote">"Keberhasilan bukanlah akhir, kegagalan bukanlah hal yang fatal: keberanian untuk terus melangkahlah yang terpenting."</span>
        <br>— <span data-i18n="hero.name">Ibe Zisokhi Laia</span>
    </blockquote>

    <a href="index.php" class="btn outline" style="margin-top:24px; display:inline-block;" data-i18n="ach.refl.btn">Lihat Karya yang Telah Saya Bangun →</a>
</section>

<!-- MODAL LIGHTBOX FOTO SERTIFIKAT -->
<div id="certLightbox" class="cert-lightbox-overlay" onclick="closeCertLightbox()">
    <div class="cert-lightbox-panel" onclick="event.stopPropagation()">
        <div class="cert-lightbox-head">
            <span id="certLightboxTitle" style="color:#fff; font-size:13px; font-weight:700;">Pratinjau Sertifikat</span>
            <button type="button" onclick="closeCertLightbox()" style="background:none; border:none; color:#f87171; font-size:20px; cursor:pointer; font-weight:bold; line-height:1;">✕</button>
        </div>
        <img id="certLightboxImg" src="" alt="Pratinjau Sertifikat" class="cert-lightbox-img">
    </div>
</div>

<script>
// Lightbox Modal
function openCertLightbox(imgSrc, title) {
    document.getElementById('certLightboxImg').src = imgSrc;
    document.getElementById('certLightboxTitle').textContent = title || 'Pratinjau Sertifikat';
    document.getElementById('certLightbox').classList.add('active');
}
function closeCertLightbox() {
    document.getElementById('certLightbox').classList.remove('active');
}

// Sinkronisasi dinamis ID/EN tanpa loop
function syncAchievementsLanguages() {
    const currentLang = localStorage.getItem('lang') || localStorage.getItem('preferred_lang') || 'id';
    document.querySelectorAll('.ach-dyn-text').forEach(el => {
        const text = (currentLang === 'en') ? el.getAttribute('data-en') : el.getAttribute('data-id');
        if (text) {
            el.textContent = text;
        }
    });
}

document.addEventListener('DOMContentLoaded', syncAchievementsLanguages);
syncAchievementsLanguages();

document.addEventListener('click', function(e) {
    if (e.target.closest('[data-lang]') || e.target.closest('.lang-btn') || e.target.closest('.lang-option')) {
        setTimeout(syncAchievementsLanguages, 80);
    }
});
</script>

<?php include 'partials/footer.php'; ?>