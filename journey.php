<?php
$page  = 'journey';
$title = 'My Journey';
include 'partials/header.php';

// 1. Baca Data Status Dinamis
$statusPath = __DIR__ . '/data/journey_status.json';
$statusCards = [];
if (file_exists($statusPath)) {
    $statusCards = json_decode(file_get_contents($statusPath), true) ?: [];
}
if (empty($statusCards)) {
    $statusCards = [
        ['icon' => '🖥', 'label_id' => 'Mulai Coding', 'label_en' => 'Started Coding', 'val_id' => '2024', 'val_en' => '2024'],
        ['icon' => '📚', 'label_id' => 'Status Saat Ini', 'label_en' => 'Current Status', 'val_id' => 'Mahasiswa Informatika', 'val_en' => 'Informatics Student'],
        ['icon' => '🚀', 'label_id' => 'Fokus Saat Ini', 'label_en' => 'Current Focus', 'val_id' => 'Full-Stack & Web3', 'val_en' => 'Full-Stack & Web3'],
        ['icon' => '🎯', 'label_id' => 'Target Masa Depan', 'label_en' => 'Future Goal', 'val_id' => 'Software Engineer', 'val_en' => 'Software Engineer']
    ];
}

// 2. Baca Data Timeline Dinamis
$timelinePath = __DIR__ . '/data/journey_timeline.json';
$timelineChapters = [];
if (file_exists($timelinePath)) {
    $timelineChapters = json_decode(file_get_contents($timelinePath), true) ?: [];
}
if (empty($timelineChapters)) {
    $timelineChapters = [
        [
            'badge'    => '2024',
            'title_id' => 'Menemukan Minat Saya',
            'title_en' => 'Discovering My Passion',
            'desc_id'  => 'Pada tahun 2024, saya memulai perjalanan sebagai mahasiswa Informatika di UPN "Veteran" Jawa Timur. Selama tahun ini, saya mempelajari dasar-dasar pemrograman melalui C, C++, dan Python sambil membangun pemahaman tentang algoritma, pemikiran logis, dan pemecahan masalah.',
            'desc_en'  => 'In 2024, I began my journey as an Informatics student at UPN "Veteran" Jawa Timur. During this year, I learned the fundamentals of programming through C, C++, and Python while building my understanding of algorithms, logical thinking, and problem-solving.',
            'learn_id' => 'Setiap ahli memulai langkahnya dengan menguasai hal mendasar.',
            'learn_en' => 'Every expert begins by mastering the fundamentals.'
        ],
        [
            'badge'    => '2025',
            'title_id' => 'Membangun Fondasi yang Kuat',
            'title_en' => 'Building Strong Foundations',
            'desc_id'  => 'Seiring bertambahnya pemahaman, saya mulai menerapkan apa yang telah saya pelajari melalui proyek akademik dan pribadi. Saya mengeksplorasi desain UI/UX, mengembangkan Sistem Manajemen Perpustakaan menggunakan Java, dan memperkuat pemahaman saya tentang software development.',
            'desc_en'  => 'As my understanding grew, I started applying what I had learned through academic and personal projects. I explored UI/UX design, developed a Library Management System using Java, and strengthened my understanding of software development through practical experience.',
            'learn_id' => 'Kemajuan datang dari latihan nyata, bukan dari kesempurnaan instan.',
            'learn_en' => 'Progress comes from practice, not perfection.'
        ],
        [
            'badge'    => '2026',
            'title_id' => 'Menciptakan Solusi Dunia Nyata',
            'title_en' => 'Creating Real-World Solutions',
            'desc_id'  => 'Pada tahun 2026, saya menantang diri dengan proyek-proyek yang lebih kompleks. Saya mulai membangun situs web pribadi dan portofolio saya sambil mengerjakan proyek berbasis klien seperti platform e-commerce dan sistem silsilah keluarga digital.',
            'desc_en'  => 'In 2026, I challenged myself with more advanced projects. I started building my personal website and portfolio website while also working on client-based projects such as an e-commerce platform and a digital family tree system.',
            'learn_id' => 'Teknologi memberi dampak terbesar ketika mampu menyelesaikan masalah nyata.',
            'learn_en' => 'Technology creates the greatest impact when it solves real problems.'
        ],
        [
            'badge'    => 'Present',
            'title_id' => 'Terus Bertumbuh',
            'title_en' => 'Still Growing',
            'desc_id'  => 'Saat ini, saya terus memperluas pengetahuan di bidang Full-Stack Development, Web3, Desain UI/UX, dan rekayasa perangkat lunak.',
            'desc_en'  => 'Today, I continue expanding my knowledge in Full-Stack Development, Web3, UI/UX Design, and software engineering. Every new project becomes another opportunity to improve.',
            'learn_id' => 'Pertumbuhan adalah perjalanan yang berkelanjutan, bukan sekadar garis akhir.',
            'learn_en' => 'Growth is a continuous journey, not a destination.'
        ]
    ];
}
?>

<style>
    .journey-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-top: 28px;
    }
    /* STYLING LINIMASA VERTIKAL UTUH (PERSIS HALAMAN EDUCATION) */
    .edu-timeline-wrapper {
        position: relative;
        max-width: 850px;
        margin: 30px auto;
        padding-left: 30px;
    }
    .edu-timeline-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 7px;
        width: 2px;
        background: rgba(139, 92, 246, 0.35);
    }
    .edu-timeline-item {
        position: relative;
        margin-bottom: 30px;
        background: var(--bg-card, #0b1120);
        border: 1px solid var(--border-color, rgba(255, 255, 255, 0.08));
        border-radius: 14px;
        padding: 24px;
        box-sizing: border-box;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .edu-timeline-item:hover {
        transform: translateY(-2px);
        border-color: rgba(139, 92, 246, 0.4);
    }
    .edu-timeline-item::before {
        content: '';
        position: absolute;
        left: -30px;
        top: 28px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--primary, #8b5cf6);
        box-shadow: 0 0 10px #8b5cf6;
        border: 2px solid var(--bg-body, #0a0d14);
    }
    .card-year-badge {
        display: inline-block;
        background: rgba(139, 92, 246, 0.15);
        color: #c4b5fd;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 6px;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (max-width: 860px) {
        .journey-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
    }
</style>

<div class="page-header" style="padding-bottom:0;">
    <h1 data-i18n="journey.header.title">My Journey</h1>
    <p data-i18n="journey.header.subtitle">The moments, experiences, and decisions that have shaped the person I am today.</p>
</div>

<!-- INTRODUCTION + STATUS DINAMIS -->
<section class="reveal" style="padding-top:20px;">
    <p data-i18n="journey.header.desc">
        Every journey begins with a single step, and mine started with curiosity. From growing up in
        two different cities to discovering my passion for technology, every experience has shaped the
        way I think, learn, and grow. This journey is not only about building technical skills, but
        also about becoming a person who embraces challenges, values lifelong learning, and strives to
        create meaningful solutions through technology.
    </p>

    <div class="journey-stats-grid">
        <?php foreach ($statusCards as $st): ?>
            <div class="card" style="text-align:center;">
                <p class="meta">
                    <span><?= htmlspecialchars($st['icon'] ?? '🚀') ?></span>
                    <span class="journey-dyn-text" data-id="<?= htmlspecialchars($st['label_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($st['label_en']) ? $st['label_en'] : ($st['label_id'] ?? '')) ?>"><?= htmlspecialchars($st['label_en'] ?? $st['label_id'] ?? '') ?></span>
                </p>
                <h3 class="journey-dyn-text" data-id="<?= htmlspecialchars($st['val_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($st['val_en']) ? $st['val_en'] : ($st['val_id'] ?? '')) ?>"><?= htmlspecialchars($st['val_en'] ?? $st['val_id'] ?? '') ?></h3>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- TIMELINE BERGARIS WAKTU VERTIKAL (HANYA BAGIAN INI YANG DIUBAH) -->
<section class="reveal" style="padding-top:20px;">
    <p class="eyebrow" data-i18n="journey.timeline.eyebrow">Journey Timeline</p>
    <h2 class="section-heading" data-i18n="journey.timeline.heading">Every Chapter Has a Story</h2>

    <div class="edu-timeline-wrapper">
        <?php foreach ($timelineChapters as $chap): 
            $cleanBadge = preg_replace('/[📍📌🎓🏫🏆🌱🚀💡]/u', '', $chap['badge'] ?? '');
            $cleanBadge = trim($cleanBadge);
        ?>
            <div class="edu-timeline-item">
                <span class="card-year-badge">🚀 <?= htmlspecialchars($cleanBadge) ?></span>
                <h3 class="journey-dyn-text" style="color:#fff; font-size:18px; margin-bottom:6px; font-weight:700;" data-id="<?= htmlspecialchars($chap['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($chap['title_en']) ? $chap['title_en'] : ($chap['title_id'] ?? '')) ?>">
                    <?= htmlspecialchars($chap['title_en'] ?? $chap['title_id'] ?? '') ?>
                </h3>
                <p class="journey-dyn-text" style="color:var(--text-muted, #94a3b8); font-size:14px; line-height:1.7; margin-bottom:14px;" data-id="<?= htmlspecialchars($chap['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($chap['desc_en']) ? $chap['desc_en'] : ($chap['desc_id'] ?? '')) ?>">
                    <?= htmlspecialchars($chap['desc_en'] ?? $chap['desc_id'] ?? '') ?>
                </p>
                <?php if (!empty($chap['learn_id']) || !empty($chap['learn_en'])): ?>
                    <div class="takeaway" style="margin-top:12px;">
                        <strong>What I Learned:</strong> 
                        <span class="journey-dyn-text" data-id="<?= htmlspecialchars($chap['learn_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($chap['learn_en']) ? $chap['learn_en'] : ($chap['learn_id'] ?? '')) ?>">
                            <?= htmlspecialchars($chap['learn_en'] ?? $chap['learn_id'] ?? '') ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- TURNING POINTS -->
<section class="reveal">
    <p class="eyebrow" data-i18n="journey.tp.eyebrow">Turning Points</p>
    <h2 class="section-heading" data-i18n="journey.tp.heading">The Moments That Changed My Direction</h2>

    <div class="grid cols-2">
        <div class="card">
            <h3 data-i18n="journey.tp.card1.title">Discovering My Direction</h3>
            <p class="meta" style="margin-top:10px;"><strong>The Moment</strong></p>
            <p data-i18n="journey.tp.card1.desc">
                Winning mathematics and physics competitions gave me the confidence to pursue technology
                as my future career.
            </p>
            <p class="meta"><strong>Why It Mattered</strong></p>
            <p data-i18n="journey.tp.card1.why">
                Those achievements helped me realize that I enjoyed solving problems and thinking
                logically, which eventually led me to choose Informatics as my field of study.
            </p>
        </div>
        <div class="card">
            <h3 data-i18n="journey.tp.card2.title">Building Beyond the Classroom</h3>
            <p class="meta" style="margin-top:10px;"><strong>The Moment</strong></p>
            <p data-i18n="journey.tp.card2.desc">
                Working on real-world projects introduced me to challenges that could not be learned
                from textbooks alone.
            </p>
            <p class="meta"><strong>Why It Mattered</strong></p>
            <p data-i18n="journey.tp.card2.why">
                I learned that software development is not only about writing code but also about
                communication, teamwork, and understanding people's needs.
            </p>
        </div>
    </div>
</section>

<!-- LOOKING AHEAD -->
<section class="reveal">
    <p class="eyebrow" data-i18n="journey.next.eyebrow">Looking Ahead</p>
    <h2 class="section-heading" data-i18n="journey.next.heading">The Next Chapter</h2>
    <p data-i18n="journey.next.desc">
        Every experience has prepared me for the next chapter of my journey. As I continue learning and
        building new projects, I hope to contribute to meaningful technology, collaborate with
        inspiring people, and create solutions that make a positive difference.
    </p>
</section>

<!-- CLOSING REFLECTION -->
<section class="reveal">
    <p class="eyebrow" data-i18n="journey.closing.eyebrow">Closing Reflection</p>
    <p data-i18n="journey.closing.desc">
        Looking back, every challenge, every success, and every failure has contributed to the person I
        am today. My journey is still far from over, and I believe the best chapters are yet to come. I
        will continue learning, building, and growing as I work toward becoming a Software Engineer and
        Web3 Developer who creates technology that makes a positive impact on people's lives.
    </p>

    <blockquote class="quote-block">
        <span data-i18n="journey.closing.quote">"The best chapters are still waiting to be written."</span>
        <br>— <span data-i18n="hero.name">Ibe Zisokhi Laia</span>
    </blockquote>

    <a href="goals.php" class="btn outline" style="margin-top:24px; display:inline-block;" data-i18n="journey.closing.btn">See What's Next →</a>
</section>

<script>
function syncJourneyLanguages() {
    const currentLang = localStorage.getItem('lang') || localStorage.getItem('preferred_lang') || 'id';
    document.querySelectorAll('.journey-dyn-text').forEach(el => {
        const text = (currentLang === 'en') ? el.getAttribute('data-en') : el.getAttribute('data-id');
        if (text) {
            el.textContent = text;
        }
    });
}

document.addEventListener('DOMContentLoaded', syncJourneyLanguages);
syncJourneyLanguages();

document.addEventListener('click', function(e) {
    if (e.target.closest('[data-lang]') || e.target.closest('.lang-btn') || e.target.closest('.lang-option')) {
        setTimeout(syncJourneyLanguages, 80);
    }
});
</script>

<?php include 'partials/footer.php'; ?>