<?php
$page  = 'home';
$title = 'Home';

$achJsonPath = __DIR__ . '/data/achievements_cards.json';
$homeAchCardsList = [];

if (file_exists($achJsonPath)) {
    $achFullData = json_decode(file_get_contents($achJsonPath), true);
    if (is_array($achFullData)) {
        $allCards = array_merge($achFullData['medals'] ?? [], $achFullData['certificates'] ?? []);
        foreach ($allCards as $item) {
            if (!empty($item['show_home'])) {
                $homeAchCardsList[] = $item;
            }
            if (count($homeAchCardsList) >= 4) {
                break;
            }
        }
    }
}

if (empty($homeAchCardsList)) {
    $homeAchCardsList = [
        [
            'image'     => '',
            'icon'      => '🥇',
            'badge_id'  => 'JUARA 1',
            'badge_en'  => '1ST PLACE',
            'year'      => '2023',
            'issuer_id' => 'Olimpiade Sains Indonesia',
            'issuer_en' => 'National Science Olympiad',
            'title_id'  => 'Kompetisi Fisika Nasional',
            'title_en'  => 'National Physics Competition',
            'desc_id'   => 'Meraih medali emas tingkat nasional melalui analisis mendalam teori fisika.',
            'desc_en'   => 'Secured national gold medal through analytical mastery of physics.',
            'link'      => ''
        ],
        [
            'image'     => '',
            'icon'      => '🥉',
            'badge_id'  => 'MEDALI PERUNGGU',
            'badge_en'  => 'BRONZE MEDAL',
            'year'      => '2023',
            'issuer_id' => 'Kompetisi Sains Nasional',
            'issuer_en' => 'National Science Competition',
            'title_id'  => 'Kompetisi Matematika Nasional',
            'title_en'  => 'National Mathematics Competition',
            'desc_id'   => 'Memperoleh medali perunggu melalui penalaran matematika logis tingkat nasional.',
            'desc_en'   => 'Awarded bronze medal through rigorous mathematical reasoning.',
            'link'      => ''
        ],
        [
            'image'     => '',
            'icon'      => '☕',
            'badge_id'  => 'SERTIFIKASI RESMI',
            'badge_en'  => 'OFFICIAL CERTIFICATE',
            'year'      => '2024',
            'issuer_id' => 'Oracle Academy / Coursera',
            'issuer_en' => 'Oracle Academy / Coursera',
            'title_id'  => 'Java Programming Masterclass',
            'title_en'  => 'Java Programming Masterclass',
            'desc_id'   => 'Menyelesaikan modul OOP, struktur data, dan aplikasi desktop Java.',
            'desc_en'   => 'Mastered OOP, data structures, and practical desktop Java development.',
            'link'      => ''
        ],
        [
            'image'     => '',
            'icon'      => '🌐',
            'badge_id'  => 'WORKSHOP INTENSIF',
            'badge_en'  => 'INTENSIVE WORKSHOP',
            'year'      => '2025',
            'issuer_id' => 'Tech Academy',
            'issuer_en' => 'Tech Academy',
            'title_id'  => 'Full-Stack Web Development',
            'title_en'  => 'Full-Stack Web Development',
            'desc_id'   => 'Pengembangan aplikasi web modern end-to-end dengan database dan RESTful API.',
            'desc_en'   => 'End-to-end full-stack web application development integrating databases.',
            'link'      => ''
        ]
    ];
}

$customSecPath = __DIR__ . '/data/custom_sections.json';
$customSections = file_exists($customSecPath) ? json_decode(file_get_contents($customSecPath), true) : [];
if (!is_array($customSections)) {
    $customSections = [];
}

$homeOnlySections = array();
$archiveCount = 0;

foreach ($customSections as $s) {
    $st = $s['publish_status'] ?? 'home';
    if ($st === 'home') {
        $homeOnlySections[] = $s;
    } elseif ($st === 'archive') {
        $archiveCount++;
    }
}

// Mengambil proyek langsung dari data/projects.json
$homeProjectsList = [];
$projJsonPath = __DIR__ . '/data/projects.json';

if (file_exists($projJsonPath)) {
    $projFullData = json_decode(file_get_contents($projJsonPath), true);
    if (is_array($projFullData)) {
        foreach ($projFullData as $proj) {
            $loc = $proj['location'] ?? 'home';
            if ($loc === 'home' || empty($loc)) {
                $homeProjectsList[] = [
                    'title'     => $proj['title'] ?? 'Proyek Unggulan',
                    'category'  => $proj['category'] ?? 'Development',
                    'overview'  => $proj['overview'] ?? 'Deskripsi proyek.',
                    'thumbnail' => $proj['thumbnail'] ?? '',
                    'slug'      => $proj['slug'] ?? '',
                    'type'      => $proj['type'] ?? 'single'
                ];
            }
            if (count($homeProjectsList) >= 3) break;
        }
    }
}

if (empty($homeProjectsList)) {
    $homeProjectsList = [
        [
            'title' => 'Smart AgriSense IoT Platform',
            'category' => 'Internet of Things (IoT)',
            'overview' => 'Sistem pemantauan kelembapan tanah otomatis dan kendali pompa irigasi.',
            'slug' => 'smart-agrisense-iot-platform',
            'type' => 'single'
        ],
        [
            'title' => 'UI/UX Design Projects Collection',
            'category' => 'UI/UX Design',
            'overview' => 'Arsip komprehensif eksplorasi desain antarmuka mobile dan sistem desain.',
            'slug' => 'ui-design-projects',
            'type' => 'multi'
        ],
        [
            'title' => 'NexusFlow Analytics Platform',
            'category' => 'Full-Stack System',
            'overview' => 'Enterprise-grade analytics engine delivering real-time telemetry.',
            'slug' => 'nexusflow-analytics-platform',
            'type' => 'single'
        ]
    ];
}

include 'partials/header.php';
?>

<style>
    .fluid-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin-top: 22px;
        width: 100%;
    }
    .project-card-custom, .ach-preview-card {
        background: var(--bg-card, #0b1120);
        border: 1px solid var(--border-color, rgba(255,255,255,0.08));
        border-radius: 14px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
        transition: transform 0.25s ease, border-color 0.25s ease;
    }
    .project-card-custom:hover, .ach-preview-card:hover {
        transform: translateY(-3px);
        border-color: rgba(139, 92, 246, 0.4);
    }
    .card-thumb-banner {
        width: 100%;
        height: 140px;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.6), rgba(15, 23, 42, 0.9));
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        position: relative;
    }
    .card-thumb-banner img { width: 100%; height: 100%; object-fit: cover; }
    .card-thumb-icon { font-size: 34px; filter: drop-shadow(0 4px 10px rgba(139, 92, 246, 0.35)); }
    .card-text-container { padding: 16px 18px 18px; display: flex; flex-direction: column; flex: 1; }

    .home-custom-split {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        margin-top: 30px;
    }
    .home-custom-split.img-right {
        grid-template-columns: 1fr 1fr;
    }
    .home-custom-split.img-right .home-custom-media-box {
        order: 2;
    }

    .home-custom-media-box {
        width: 100%;
        max-width: 420px;
        aspect-ratio: 4 / 3;
        border-radius: 20px;
        overflow: hidden;
        background: var(--bg-card, #0b1120);
        border: 1px solid var(--border-color, rgba(255,255,255,0.08));
        box-shadow: 0 12px 35px rgba(0,0,0,0.35);
        margin: 0 auto;
    }
    .home-custom-media-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .home-timeline-wrapper {
        position: relative;
        max-width: 800px;
        margin: 20px auto 0;
        padding-left: 24px;
    }
    .home-timeline-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 5px;
        width: 2px;
        background: rgba(139, 92, 246, 0.35);
    }
    .home-timeline-item {
        position: relative;
        margin-bottom: 20px;
        background: var(--bg-card, #0b1120);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 18px;
    }
    .home-timeline-item::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 22px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary, #8b5cf6);
        box-shadow: 0 0 8px #8b5cf6;
        border: 2px solid var(--bg-body, #0a0d14);
    }

    @media (max-width: 768px) {
        .fluid-card-grid { grid-template-columns: 1fr; gap: 16px; margin-top: 16px; }
        .home-custom-split, .home-custom-split.img-right { grid-template-columns: 1fr; gap: 24px; }
        .home-custom-split.img-right .home-custom-media-box { order: 0; }
    }
</style>

<section class="hero" id="home">
    <div class="hero-text fade-in">
        <p class="eyebrow" data-i18n="hero.eyebrow">Halo, Saya</p>
        <h1 data-i18n="hero.name">Ibe Zisokhi Laia</h1>
        <p data-i18n="hero.subtitle">Mahasiswa Informatika · Calon Full-Stack &amp; Web3 Developer</p>
        <p style="margin-top:10px;" data-i18n="hero.paragraph">
            Bersemangat dalam rekayasa perangkat lunak, eksplorasi teknologi Web3, dan pembangunan sistem digital yang bermanfaat. Ruang digital ini mendokumentasikan proses belajar, karya proyek, dan perkembangan keahlian saya.
        </p>

        <div class="cta">
            <a href="journey.php"><button data-i18n="hero.btn.journey">Perjalanan</button></a>
            <a href="contact.php"><button data-i18n="hero.btn.contact">Kontak</button></a>
        </div>
    </div>

    <div class="hero-image slide-in">
        <div class="box-img">
            <img src="assets/img/hero-1.jpg.png" alt="Ibe Laia">
        </div>
    </div>
</section>

<section class="about reveal" id="about">
    <div class="about-container">
        <div class="about-image">
            <div class="img-box">
                <img src="assets/img/about.jpg.png" alt="Tentang Ibe Laia">
            </div>
        </div>

        <div class="about-text">
            <p class="eyebrow" data-i18n="index.about.eyebrow">Tentang</p>
            <h2 class="section-heading" data-i18n="index.about.title">Siapa Saya</h2>

            <p data-i18n="index.about.p1">
                Saya adalah mahasiswa Informatika yang memiliki minat besar terhadap teknologi dan pembelajaran berkelanjutan.
            </p>

            <p data-i18n="index.about.p2">
                Saat ini saya sedang memperdalam pengetahuan di bidang frontend development, backend development, software engineering, dan teknologi Web3 melalui berbagai proyek praktik serta pembelajaran mandiri.
            </p>

            <p data-i18n="index.about.p3">
                Website ini menjadi tempat untuk mendokumentasikan perjalanan, pengalaman, pemikiran, dan perkembangan diri saya dalam perjalanan menjadi seorang software developer profesional.
            </p>

            <p style="margin-top:16px;">
                <a href="about.php" class="btn outline" data-i18n="index.about.button">
                    Pelajari Lebih Lanjut Tentang Saya →
                </a>
            </p>
        </div>
    </div>
</section>

<section class="reveal" id="journey">
    <p class="eyebrow" data-i18n="index.journey.eyebrow">Perjalanan Saya</p>
    <h2 class="section-heading" data-i18n="index.journey.title">Perjalanan Pertumbuhan Saya</h2>

    <div class="grid gap-lg">
        <div class="card">
            <span class="card-year">2024</span>
            <h3 data-i18n="index.journey.2024.title">🌱 Menemukan Dunia Pemrograman</h3>
            <p data-i18n="index.journey.2024.desc">
                Perjalanan saya dimulai dengan mempelajari dasar-dasar pemrograman menggunakan C, C++, dan Python. Pada tahap ini saya mengembangkan kemampuan berpikir logis dan fondasi ilmu komputer.
            </p>
            <div class="takeaway">
                <strong data-i18n="index.journey.takeaway">Pelajaran Utama:</strong>
                <span data-i18n="index.journey.2024.takeaway">Setiap ahli memulai dari dasar.</span>
            </div>
        </div>

        <div class="card">
            <span class="card-year">2025</span>
            <h3 data-i18n="index.journey.2025.title">🚀 Membangun Pengetahuan Melalui Proyek</h3>
            <p data-i18n="index.journey.2025.desc">
                Setelah memahami dasar-dasarnya, saya mulai menerapkan pengetahuan dengan mengembangkan proyek nyata seperti UI/UX dan Sistem Manajemen Perpustakaan.
            </p>
            <div class="takeaway">
                <strong data-i18n="index.journey.takeaway">Pelajaran Utama:</strong>
                <span data-i18n="index.journey.2025.takeaway">Belajar menjadi lebih bermakna ketika diterapkan melalui proyek nyata.</span>
            </div>
        </div>

        <div class="card">
            <span class="card-year">2026</span>
            <h3 data-i18n="index.journey.2026.title">💡 Menciptakan Solusi Dunia Nyata</h3>
            <p data-i18n="index.journey.2026.desc">
                Fase akselerasi di mana saya mengembangkan sistem yang lebih kompleks, website pribadi, aplikasi e-commerce, hingga proyek untuk klien nyata.
            </p>
            <div class="takeaway">
                <strong data-i18n="index.journey.takeaway">Pelajaran Utama:</strong>
                <span data-i18n="index.journey.2026.takeaway">Pertumbuhan yang sesungguhnya dimulai ketika kita mampu menyelesaikan masalah nyata bagi orang lain.</span>
            </div>
        </div>
    </div>

    <a href="journey.php" class="btn outline" style="margin-top:24px; display:inline-block;" data-i18n="index.journey.button">
        Jelajahi Perjalanan Saya →
    </a>
</section>

<section class="reveal">
    <p class="eyebrow" data-i18n="index.edu.eyebrow">Jejak Akademik</p>
    <h2 class="section-heading" data-i18n="index.edu.title">Pendidikan</h2>
    <p data-i18n="index.edu.desc">Membangun fondasi pengetahuan formal di setiap tahapan akademik.</p>

    <div class="grid cols-4" style="margin-top:20px;">
        <div class="card">
            <h3>🏫 <span data-i18n="index.edu.sd.title">Sekolah Dasar</span></h3>
            <p class="meta" data-i18n="index.edu.sd.desc">
                SD Methodist 5 Medan &amp; SD Bintang Laut
            </p>
        </div>

        <div class="card">
            <h3>🏫 <span data-i18n="index.edu.smp.title">Sekolah Menengah Pertama</span></h3>
            <p class="meta" data-i18n="index.edu.smp.desc">
                SMP Swasta Bintang Laut, Nias Selatan
            </p>
        </div>

        <div class="card">
            <h3>🏫 <span data-i18n="index.edu.sma.title">Sekolah Menengah Atas</span></h3>
            <p class="meta" data-i18n="index.edu.sma.desc">
                SMA Negeri 1 Teluk Dalam · Peraih Medali Olimpiade Sains
            </p>
        </div>

        <div class="card">
            <h3>🎓 <span data-i18n="index.edu.univ.title">Perguruan Tinggi</span></h3>
            <p class="meta" data-i18n="index.edu.univ.desc">
                UPN "Veteran" Jawa Timur · Sarjana Informatika
            </p>
        </div>
    </div>

    <a href="education.php" class="btn outline" style="margin-top:20px; display:inline-block;" data-i18n="index.edu.button">
        Jelajahi Pendidikan Saya →
    </a>
</section>

<section class="reveal">
    <p class="eyebrow" data-i18n="index.achievement.eyebrow">Prestasi</p>
    <h2 class="section-heading" data-i18n="index.achievement.title">Sekilas Perjalanan Saya</h2>
    <p data-i18n="index.achievement.desc">🏆 Sekilas pencapaian yang membentuk perjalanan akademik saya.</p>

    <div class="fluid-card-grid">
        <?php foreach ($homeAchCardsList as $item): 
            $hasImg = !empty($item['image']) && file_exists($item['image']);
        ?>
            <div class="ach-preview-card">
                <div class="card-thumb-banner">
                    <?php if ($hasImg): ?>
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title_en'] ?? '') ?>" loading="lazy">
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                            <span class="card-thumb-icon"><?= htmlspecialchars($item['icon'] ?? '🏆') ?></span>
                            <span style="font-size: 9px; letter-spacing: 0.8px; text-transform: uppercase; color: rgba(255,255,255,0.45); font-weight: 700;">Verified Award</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-text-container">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span class="ach-dyn-text" data-id="<?= htmlspecialchars($item['badge_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($item['badge_en']) ? $item['badge_en'] : ($item['badge_id'] ?? '')) ?>" style="background: rgba(139,92,246,0.12); color: #c4b5fd; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; text-transform: uppercase;">
                            <?= htmlspecialchars($item['badge_en'] ?? $item['badge_id'] ?? '') ?>
                        </span>
                        <span style="background: rgba(255,255,255,0.04); color: var(--text-muted, #94a3b8); font-size: 10.5px; font-weight: 700; padding: 2px 7px; border-radius: 4px;">
                            <?= htmlspecialchars($item['year'] ?? '') ?>
                        </span>
                    </div>

                    <span class="ach-dyn-text" data-id="<?= htmlspecialchars($item['issuer_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($item['issuer_en']) ? $item['issuer_en'] : ($item['issuer_id'] ?? '')) ?>" style="font-size: 10.5px; font-weight: 700; color: #38bdf8; text-transform: uppercase; margin-bottom: 4px; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?= htmlspecialchars($item['issuer_en'] ?? $item['issuer_id'] ?? '') ?>
                    </span>
                    <h3 class="ach-dyn-text" data-id="<?= htmlspecialchars($item['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($item['title_en']) ? $item['title_en'] : ($item['title_id'] ?? '')) ?>" style="font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 6px; line-height: 1.35;">
                        <?= htmlspecialchars($item['title_en'] ?? $item['title_id'] ?? '') ?>
                    </h3>
                    <p class="ach-dyn-text" data-id="<?= htmlspecialchars($item['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($item['desc_en']) ? $item['desc_en'] : ($item['desc_id'] ?? '')) ?>" style="font-size: 12px; color: var(--text-muted, #94a3b8); line-height: 1.5; margin-bottom: 12px; flex: 1;">
                        <?= htmlspecialchars($item['desc_en'] ?? $item['desc_id'] ?? '') ?>
                    </p>

                    <div style="margin-top: auto; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.05);">
                        <a href="achievement.php" style="color: #c4b5fd; font-size: 11.5px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                            <span>Lihat Detail</span> <span>→</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="achievement.php" class="btn outline" style="margin-top:24px; display:inline-block;" data-i18n="index.achievement.button">
        Lihat Semua Prestasi →
    </a>
</section>

<!-- BAGIAN "WHAT I'VE BUILT" (TERHUBUNG KE DETAIL STUDI KASUS PORTFOLIO) -->
<section class="reveal" id="built">
    <p class="eyebrow" data-i18n="index.projects.eyebrow">Karya &amp; Portofolio</p>
    <h2 class="section-heading" data-i18n="index.projects.title">Apa yang Telah Saya Bangun ⭐</h2>
    <p data-i18n="index.projects.desc">Setiap proyek berawal dari rasa ingin tahu. Berikut beberapa hal yang telah saya bangun selama perjalanan belajar saya.</p>

    <div class="fluid-card-grid">
        <?php foreach ($homeProjectsList as $proj): 
            $projThumb = !empty($proj['thumbnail']) ? $proj['thumbnail'] : '';
            $projTitle = $proj['title'];
            $projCat   = $proj['category'];
            $projDesc  = $proj['overview'];
            $projSlug  = $proj['slug'] ?? '';
            $projType  = $proj['type'] ?? 'single';
            
            // Mengarahkan tombol Detail Studi Kasus ke halaman detail project Next.js yang sesuai
            $targetUrl = ($projType === 'multi') ? "projects/collection/$projSlug" : "projects/$projSlug";
            $portfolioBaseUrl = defined('PORTFOLIO_URL') ? PORTFOLIO_URL : 'http://localhost:3000/';
            $fullProjectUrl = rtrim($portfolioBaseUrl, '/') . '/' . ltrim($targetUrl, '/');
        ?>
            <div class="project-card-custom" style="border-color: rgba(245, 158, 11, 0.35); box-shadow: 0 4px 20px rgba(245, 158, 11, 0.08);">
                <div class="card-thumb-banner">
                    <?php if (!empty($projThumb)): ?>
                        <img src="<?= htmlspecialchars($projThumb) ?>" alt="<?= htmlspecialchars($projTitle) ?>" loading="lazy">
                    <?php else: ?>
                        <span class="card-thumb-icon">⭐</span>
                    <?php endif; ?>
                    <span style="position: absolute; top: 10px; right: 10px; background: rgba(245, 158, 11, 0.9); color: #0a0d14; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">★ Featured</span>
                </div>
                <div class="card-text-container">
                    <h3 style="font-size:16px; margin-bottom:4px; color:#fff; font-weight:700;"><?= htmlspecialchars($projTitle) ?></h3>
                    <p class="meta" style="margin-bottom:8px; font-size:12px; color:#38bdf8; font-family:monospace;"><?= htmlspecialchars($projCat) ?></p>
                    <p style="font-size:12.5px; color:var(--text-muted); line-height:1.55; margin-bottom:12px; flex:1;"><?= htmlspecialchars($projDesc) ?></p>
                    <div style="margin-top:auto; padding-top:8px;">
                        <a href="<?= htmlspecialchars($fullProjectUrl) ?>" target="_blank" class="btn outline" style="padding:6px 16px; font-size:12px; border-color: rgba(139,92,246,0.6); color: #c4b5fd;">
                            Detail Studi Kasus →
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <p style="margin-top:24px;" data-i18n="index.portfolio.cta">Tertarik dengan proyek yang telah saya buat?</p>
    <a href="<?= rtrim((defined('PORTFOLIO_URL') ? PORTFOLIO_URL : 'http://localhost:3000/'), '/') ?>/projects" target="_blank" rel="noopener noreferrer" class="btn outline" style="display:inline-block;" data-i18n="index.portfolio.button">
        Jelajahi Portofolio Saya →
    </a>
</section>

<?php if (!empty($homeOnlySections)): ?>
    <?php foreach ($homeOnlySections as $cSec): ?>
        <section class="reveal" id="<?= htmlspecialchars($cSec['slug'] ?? 'section') ?>" style="margin-top: 50px; margin-bottom: 50px;">
            <?php if (!empty($cSec['eyebrow_id'])): ?>
                <p class="eyebrow ach-dyn-text" data-id="<?= htmlspecialchars($cSec['eyebrow_id']) ?>" data-en="<?= htmlspecialchars(!empty($cSec['eyebrow_en']) ? $cSec['eyebrow_en'] : $cSec['eyebrow_id']) ?>">
                    <?= htmlspecialchars($cSec['eyebrow_id']) ?>
                </p>
            <?php endif; ?>

            <h2 class="section-heading ach-dyn-text" data-id="<?= htmlspecialchars($cSec['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($cSec['title_en']) ? $cSec['title_en'] : ($cSec['title_id'] ?? '')) ?>">
                <?= htmlspecialchars($cSec['title_id'] ?? '') ?>
            </h2>

            <?php if (!empty($cSec['teaser_id'])): ?>
                <p class="ach-dyn-text" data-id="<?= htmlspecialchars($cSec['teaser_id']) ?>" data-en="<?= htmlspecialchars(!empty($cSec['teaser_en']) ? $cSec['teaser_en'] : $cSec['teaser_id']) ?>" style="color:var(--text-muted); max-width:700px; line-height:1.6; margin-bottom: 24px;">
                    <?= htmlspecialchars($cSec['teaser_id']) ?>
                </p>
            <?php endif; ?>

            <?php 
            if (!empty($cSec['blocks']) && is_array($cSec['blocks'])) {
                $firstBlock = $cSec['blocks'][0];
                $bType = $firstBlock['type'] ?? '';

                if ($bType === 'story') {
                    $hasImg = !empty($firstBlock['image']);
                    $imgPos = $firstBlock['image_position'] ?? 'left';
            ?>
                    <div class="home-custom-split <?= $imgPos === 'right' ? 'img-right' : '' ?>" style="<?= !$hasImg ? 'grid-template-columns: 1fr;' : '' ?>">
                        <?php if ($hasImg): ?>
                            <div class="home-custom-media-box"><img src="<?= htmlspecialchars($firstBlock['image']) ?>" alt="Visual"></div>
                        <?php endif; ?>
                        <div>
                            <div class="ach-dyn-text" data-id="<?= nl2br(htmlspecialchars($firstBlock['body_id'] ?? '')) ?>" data-en="<?= nl2br(htmlspecialchars(!empty($firstBlock['body_en']) ? $firstBlock['body_en'] : ($firstBlock['body_id'] ?? ''))) ?>" style="line-height: 1.8; color: var(--text-muted); font-size: 15px;">
                                <?= nl2br(htmlspecialchars($firstBlock['body_id'] ?? '')) ?>
                            </div>
                        </div>
                    </div>
            <?php 
                } elseif ($bType === 'cards' && !empty($firstBlock['items'])) {
            ?>
                    <div class="fluid-card-grid">
                        <?php foreach ($firstBlock['items'] as $cItem): ?>
                            <div class="ach-preview-card" style="padding: 22px;">
                                <span style="font-size: 28px; display: block; margin-bottom: 10px;"><?= htmlspecialchars($cItem['icon'] ?? '💡') ?></span>
                                <h3 class="ach-dyn-text" data-id="<?= htmlspecialchars($cItem['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($cItem['title_en']) ? $cItem['title_en'] : ($cItem['title_id'] ?? '')) ?>" style="color:#fff; font-size:16px; margin-bottom:8px; font-weight:700;">
                                    <?= htmlspecialchars($cItem['title_id'] ?? '') ?>
                                </h3>
                                <p class="ach-dyn-text" data-id="<?= htmlspecialchars($cItem['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($cItem['desc_en']) ? $cItem['desc_en'] : ($cItem['desc_id'] ?? '')) ?>" style="color:var(--text-muted); font-size:13px; line-height:1.6; margin:0;">
                                    <?= htmlspecialchars($cItem['desc_id'] ?? '') ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
            <?php 
                } elseif ($bType === 'timeline' && !empty($firstBlock['items'])) {
            ?>
                    <div class="home-timeline-wrapper">
                        <?php foreach ($firstBlock['items'] as $tItem): ?>
                            <div class="home-timeline-item">
                                <span style="background: rgba(139,92,246,0.15); color: #c4b5fd; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 6px; display:inline-block; margin-bottom:8px;">📅 <?= htmlspecialchars($tItem['year']) ?></span>
                                <h4 class="ach-dyn-text" data-id="<?= htmlspecialchars($tItem['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($tItem['title_en']) ? $tItem['title_en'] : ($tItem['title_id'] ?? '')) ?>" style="color:#fff; font-size:16px; margin-bottom:6px; font-weight:700;">
                                    <?= htmlspecialchars($tItem['title_id'] ?? '') ?>
                                </h4>
                                <p class="ach-dyn-text" data-id="<?= htmlspecialchars($tItem['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($tItem['desc_en']) ? $tItem['desc_en'] : ($tItem['desc_id'] ?? '')) ?>" style="color:var(--text-muted); font-size:13.5px; line-height:1.6; margin:0;">
                                    <?= htmlspecialchars($tItem['desc_id'] ?? '') ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
            <?php 
                } elseif ($bType === 'article') {
            ?>
                    <div class="card" style="padding: 24px; margin-top: 10px;">
                        <h4 class="ach-dyn-text" data-id="<?= htmlspecialchars($firstBlock['heading_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($firstBlock['heading_en']) ? $firstBlock['heading_en'] : ($firstBlock['heading_id'] ?? '')) ?>" style="color:#fff; font-size:17px; margin-bottom:10px; font-weight:700;">
                            <?= htmlspecialchars($firstBlock['heading_id'] ?? '') ?>
                        </h4>
                        <p class="ach-dyn-text" data-id="<?= htmlspecialchars($firstBlock['content_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($firstBlock['content_en']) ? $firstBlock['content_en'] : ($firstBlock['content_id'] ?? '')) ?>" style="color:var(--text-muted); font-size:14px; line-height:1.7; margin:0;">
                            <?= htmlspecialchars($firstBlock['content_id'] ?? '') ?>
                        </p>
                    </div>
            <?php 
                }
            } 
            ?>

            <a href="explore.php?slug=<?= urlencode($cSec['slug'] ?? '') ?>" class="btn outline ach-dyn-text" data-id="<?= htmlspecialchars($cSec['btn_text_id'] ?? 'Pelajari Lebih Lanjut →') ?>" data-en="<?= htmlspecialchars($cSec['btn_text_en'] ?? 'Learn More →') ?>" style="margin-top:24px; display:inline-block;">
                <?= htmlspecialchars($cSec['btn_text_id'] ?? 'Pelajari Lebih Lanjut →') ?>
            </a>
        </section>
    <?php endforeach; ?>
<?php endif; ?>

<section class="reveal" style="margin-top: 50px; margin-bottom: 40px;">
    <div style="background: linear-gradient(135deg, rgba(30, 27, 75, 0.35), rgba(15, 23, 42, 0.7)); border: 1px solid rgba(139, 92, 246, 0.3); border-radius: 16px; padding: 30px 26px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div style="max-width: 620px;">
            <p class="eyebrow" style="margin-bottom: 6px;">ARSIP INISIATIF</p>
            <h3 style="font-size: 21px; color: #fff; margin-bottom: 6px; font-weight: 700;">
                Eksplorasi &amp; Catatan Lainnya
            </h3>
            <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6; margin: 0;">
                Menelusuri seluruh dokumentasi proyek, riset, dan ide masa lalu yang telah diarsipkan.
            </p>
        </div>
        <div>
            <a href="archive.php" class="btn outline" style="padding: 10px 22px; font-size: 13px; border-color: rgba(139,92,246,0.5); color:#c4b5fd;">
                📂 Buka Halaman Arsip (<?= $archiveCount ?>) →
            </a>
        </div>
    </div>
</section>

<section class="reveal" style="text-align:center;">
    <p class="eyebrow" style="text-align:center;">Hubungi Saya</p>
    <h2 class="section-heading">Mari Terhubung &amp; Berkolaborasi</h2>
    <p style="max-width:600px; margin:0 auto; color:var(--text-muted); line-height:1.6;">
        Terima kasih telah meluangkan waktu mengunjungi website saya. Jika Anda memiliki ide proyek, diskusi teknologi, atau tawaran kolaborasi, pintu selalu terbuka untuk Anda.
    </p>
    <div class="cta" style="justify-content:center; margin-top:20px;">
        <a href="about.php"><button>Tentang Saya</button></a>
        <a href="contact.php"><button>Hubungi Saya</button></a>
    </div>
</section>

<script>
function syncHomeDynLanguages() {
    const currentLang = localStorage.getItem('lang') || localStorage.getItem('preferred_lang') || 'id';
    document.querySelectorAll('.ach-dyn-text').forEach(el => {
        const text = (currentLang === 'en') ? el.getAttribute('data-en') : el.getAttribute('data-id');
        if (text) {
            el.textContent = text;
        }
    });
}

document.addEventListener('DOMContentLoaded', syncHomeDynLanguages);
syncHomeDynLanguages();

document.addEventListener('click', function(e) {
    if (e.target.closest('[data-lang]') || e.target.closest('.lang-btn') || e.target.closest('.lang-option')) {
        setTimeout(syncHomeDynLanguages, 80);
    }
});
</script>

<?php include 'partials/footer.php'; ?>