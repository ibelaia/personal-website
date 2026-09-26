<?php
$page  = 'education';
$title = 'Education';
include 'partials/header.php';

$eduPath = __DIR__ . '/data/education_cards.json';
$eduData = [];
if (file_exists($eduPath)) {
    $eduData = json_decode(file_get_contents($eduPath), true) ?: [];
}

$schools = $eduData['schools'] ?? [];
$higher  = $eduData['higher_education'] ?? [];

if (empty($schools)) {
    $schools = [
        [
            'period'     => '2012–2017',
            'level_id'   => 'Sekolah Dasar',
            'level_en'   => 'Primary School',
            'school_id'  => 'SD Methodist 5 Medan',
            'school_en'  => 'SD Methodist 5 Medan',
            'desc_id'    => 'Di sinilah saya menghabiskan sebagian besar masa kecil. Selain membangun pengetahuan dasar, saya mengembangkan rasa ingin tahu, disiplin, dan rasa percaya diri yang menjadi bekal penting dalam perjalanan akademik saya.',
            'desc_en'    => 'This was where I spent most of my childhood. Besides gaining foundational knowledge, I developed curiosity, discipline, and confidence that later became important throughout my academic journey.',
            'highlights' => 'Curiosity, Discipline, Confidence'
        ],
        [
            'period'     => '2017–2018',
            'level_id'   => 'Sekolah Dasar — Awal yang Baru',
            'level_en'   => 'Primary School — A New Beginning',
            'school_id'  => 'SD Bintang Laut, Nias Selatan',
            'school_en'  => 'SD Bintang Laut, South Nias',
            'desc_id'    => 'Setelah pindah ke Nias Selatan, saya menyelesaikan tahun terakhir SD di lingkungan yang sama sekali baru. Beradaptasi dengan teman sekelas dan guru baru mengajarkan saya ketangguhan dan pentingnya menyambut perubahan.',
            'desc_en'    => 'After moving to South Nias, I completed my final year of elementary school in a completely new environment. Adapting to new classmates, teachers, and surroundings taught me resilience and the importance of embracing change.',
            'highlights' => 'Adaptability, Resilience'
        ],
        [
            'period'     => '2018–2021',
            'level_id'   => 'Sekolah Menengah Pertama',
            'level_en'   => 'Junior High School',
            'school_id'  => 'SMP Swasta Bintang Laut, Nias Selatan',
            'school_en'  => 'SMP Swasta Bintang Laut, South Nias',
            'desc_id'    => 'Melanjutkan ke jenjang SMP di sekolah yang sama membuat saya beradaptasi lebih nyaman. Minat saya pada matematika dan sains terus bertumbuh, meletakkan dasar bagi ketertarikan pada bidang teknologi.',
            'desc_en'    => 'Continuing at the same school into junior high allowed me to adapt more comfortably to my new surroundings. During these years, my interest in mathematics and science continued to grow, laying the foundation for my future studies in technology.',
            'highlights' => 'Mathematics, Science, Logical Thinking'
        ],
        [
            'period'     => '2021–2024',
            'level_id'   => 'Sekolah Menengah Atas',
            'level_en'   => 'Senior High School',
            'school_id'  => 'SMA Negeri 1 Teluk Dalam',
            'school_en'  => 'SMA Negeri 1 Teluk Dalam',
            'desc_id'    => 'Masa SMA menjadi titik balik perjalanan akademik saya. Saya aktif mengikuti berbagai kompetisi sains yang memperkuat penalaran analitis, kepercayaan diri, dan memantapkan langkah saya berkarier di dunia rekayasa perangkat lunak.',
            'desc_en'    => 'High school became a turning point in my academic life. I actively participated in competitions, earning medals in national-level mathematics and physics competitions that strengthened my analytical thinking, boosted my confidence, and inspired me to pursue a career in technology.',
            'highlights' => '🏅 National Mathematics Competition — Bronze Medal, 🥇 National Physics Competition — Gold Medal, 🔬 Science Olympiad Participant'
        ]
    ];
}

if (empty($higher)) {
    $higher = [
        [
            'period'       => '2024–Present',
            'title_id'     => 'Universitas',
            'title_en'     => 'University',
            'school_id'    => 'UPN "Veteran" Jawa Timur — Sarjana Informatika',
            'school_en'    => 'UPN "Veteran" Jawa Timur — Bachelor of Informatics',
            'desc_id'      => 'Perkuliahan memperkenalkan saya pada berbagai cabang ilmu komputer, termasuk rekayasa perangkat lunak, algoritma, basis data, pengembangan web, dan banyak bidang lain yang terus memperkokoh fondasi teknis saya.',
            'desc_en'      => 'University has introduced me to various fields of computer science, including software engineering, algorithms, databases, web development, and many other areas that continue to shape my technical foundation.',
            'highlights'   => 'Full-Stack Development, Web Development, Web3 Development, UI/UX Design, Software Engineering',
            'projects'     => 'Personal Website, Portfolio Website, Library Management System, Client-Based Web Projects, UI/UX Design, Full-Stack Development, Web3 Development',
            'summary_id'   => 'Bagi saya, universitas bukan hanya tempat meraih gelar—tetapi tempat mengubah ilmu menjadi pengalaman nyata, menghadapi tantangan baru, dan mempersiapkan diri berkarier di industri teknologi.',
            'summary_en'   => 'Today, university is more than just earning a degree—it is where I transform knowledge into practical experience, embrace new challenges, and prepare myself for a career in the technology industry.'
        ]
    ];
}
?>

<style>
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
    .timeline-phase-tag {
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
        margin: 24px 0 16px 0;
    }
    .timeline-phase-tag::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary, #8b5cf6);
        box-shadow: 0 0 8px #8b5cf6;
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
</style>

<div class="page-header" style="padding-bottom:0; text-align:center;">
    <h1 data-i18n="edu.header.title">Education</h1>
    <p data-i18n="edu.header.subtitle">Every place I studied became part of my journey, shaping not only my knowledge but also my character and ambitions.</p>
</div>

<section class="reveal" style="padding-top:10px;">
    <div style="max-width: 850px; margin: 0 auto;">
        
        <div class="timeline-phase-tag">
            <span>🏫</span>
            <span class="edu-dyn-text" data-id="Pendidikan Dasar &amp; Menengah" data-en="Primary &amp; Secondary Education">Primary &amp; Secondary Education</span>
        </div>

        <div class="edu-timeline-wrapper">
            <?php foreach ($schools as $s): 
                // Bersihkan teks periode dari emoji lama jika ada agar tidak ganda
                $cleanPeriod = preg_replace('/[📍📌🎓🏫🏆]/u', '', $s['period'] ?? '');
                $cleanPeriod = trim($cleanPeriod);
            ?>
                <div class="edu-timeline-item">
                    <span class="card-year-badge">📍 <?= htmlspecialchars($cleanPeriod) ?></span>
                    <h3 class="edu-dyn-text" style="color:#fff; font-size:18px; margin-bottom:6px; font-weight:700;" data-id="<?= htmlspecialchars($s['level_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($s['level_en']) ? $s['level_en'] : ($s['level_id'] ?? '')) ?>"><?= htmlspecialchars($s['level_en'] ?? $s['level_id'] ?? '') ?></h3>
                    <p class="meta" style="margin-bottom:10px;">
                        <strong class="edu-dyn-text" style="color:#38bdf8; font-size:14px;" data-id="<?= htmlspecialchars($s['school_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($s['school_en']) ? $s['school_en'] : ($s['school_id'] ?? '')) ?>"><?= htmlspecialchars($s['school_en'] ?? $s['school_id'] ?? '') ?></strong>
                    </p>
                    <p class="edu-dyn-text" style="color:var(--text-muted, #94a3b8); font-size:14px; line-height:1.7; margin-bottom:14px;" data-id="<?= htmlspecialchars($s['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($s['desc_en']) ? $s['desc_en'] : ($s['desc_id'] ?? '')) ?>"><?= htmlspecialchars($s['desc_en'] ?? $s['desc_id'] ?? '') ?></p>

                    <?php if (!empty($s['highlights'])): ?>
                        <p class="meta" style="margin-bottom:6px;"><strong data-i18n="edu.common.highlights" style="color:#fff; font-size:13px;">Key Highlights</strong></p>
                        <ul style="color:var(--text-muted, #94a3b8); font-size:13.5px; line-height:1.8; padding-left:18px; margin:0;">
                            <?php 
                            $hlList = explode(',', $s['highlights']);
                            foreach ($hlList as $hlItem):
                                $hlTrim = trim($hlItem);
                                if ($hlTrim !== ''):
                            ?>
                                <li><?= htmlspecialchars($hlTrim) ?></li>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="timeline-phase-tag" style="margin-top: 30px;">
            <span>🎓</span>
            <span class="edu-dyn-text" data-id="Pendidikan Tinggi &amp; Karier" data-en="Higher Education &amp; Career">Higher Education &amp; Career</span>
        </div>

        <div class="edu-timeline-wrapper">
            <?php foreach ($higher as $h): 
                $cleanPeriodH = preg_replace('/[📍📌🎓🏫🏆]/u', '', $h['period'] ?? '');
                $cleanPeriodH = trim($cleanPeriodH);
            ?>
                <div class="edu-timeline-item">
                    <span class="card-year-badge">📍 <?= htmlspecialchars($cleanPeriodH) ?></span>
                    <h3 class="edu-dyn-text" style="color:#fff; font-size:18px; margin-bottom:6px; font-weight:700;" data-id="<?= htmlspecialchars($h['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($h['title_en']) ? $h['title_en'] : ($h['title_id'] ?? '')) ?>"><?= htmlspecialchars($h['title_en'] ?? $h['title_id'] ?? '') ?></h3>
                    <p class="meta" style="margin-bottom:10px;">
                        <strong class="edu-dyn-text" style="color:#38bdf8; font-size:14px;" data-id="<?= htmlspecialchars($h['school_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($h['school_en']) ? $h['school_en'] : ($h['school_id'] ?? '')) ?>"><?= htmlspecialchars($h['school_en'] ?? $h['school_id'] ?? '') ?></strong>
                    </p>
                    <p class="edu-dyn-text" style="color:var(--text-muted, #94a3b8); font-size:14px; line-height:1.7; margin-bottom:14px;" data-id="<?= htmlspecialchars($h['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($h['desc_en']) ? $h['desc_en'] : ($h['desc_id'] ?? '')) ?>"><?= htmlspecialchars($h['desc_en'] ?? $h['desc_id'] ?? '') ?></p>

                    <?php if (!empty($h['highlights'])): ?>
                        <p class="meta" style="margin-bottom:6px;"><strong data-i18n="edu.common.highlights" style="color:#fff; font-size:13px;">Key Highlights</strong></p>
                        <ul style="color:var(--text-muted, #94a3b8); font-size:13.5px; line-height:1.8; padding-left:18px; margin:0;">
                            <?php 
                            $hlList = explode(',', $h['highlights']);
                            foreach ($hlList as $hlItem):
                                $hlTrim = trim($hlItem);
                                if ($hlTrim !== ''):
                            ?>
                                <li><?= htmlspecialchars($hlTrim) ?></li>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </ul>
                    <?php endif; ?>

                    <?php if (!empty($h['projects'])): ?>
                        <p class="meta" style="margin-top:14px; margin-bottom:6px;"><strong style="color:#fff; font-size:13px;">Projects &amp; Practical Experience</strong></p>
                        <ul style="color:var(--text-muted, #94a3b8); font-size:13.5px; line-height:1.8; padding-left:18px; margin:0;">
                            <?php 
                            $prList = explode(',', $h['projects']);
                            foreach ($prList as $prItem):
                                $prTrim = trim($prItem);
                                if ($prTrim !== ''):
                            ?>
                                <li><?= htmlspecialchars($prTrim) ?></li>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </ul>
                    <?php endif; ?>

                    <?php if (!empty($h['summary_id']) || !empty($h['summary_en'])): ?>
                        <p style="margin-top:14px; color:var(--text-muted, #94a3b8); font-size:14px; line-height:1.7;" class="edu-dyn-text" data-id="<?= htmlspecialchars($h['summary_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($h['summary_en']) ? $h['summary_en'] : ($h['summary_id'] ?? '')) ?>">
                            <?= htmlspecialchars($h['summary_en'] ?? $h['summary_id'] ?? '') ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<section class="reveal" style="text-align:center; max-width:750px; margin: 40px auto 0;">
    <p class="eyebrow" data-i18n="edu.beyond.eyebrow">Beyond the Classroom</p>
    <h2 class="section-heading" data-i18n="edu.beyond.heading">Continuing My Learning</h2>
    <p data-i18n="edu.beyond.p1" style="color:var(--text-muted, #94a3b8); line-height:1.7;">I believe education does not end in the classroom.</p>
    <p data-i18n="edu.beyond.p2" style="color:var(--text-muted, #94a3b8); line-height:1.7; margin-top:10px;">
        Beyond university, I dedicate time to learning through online courses, technical documentation,
        open-source communities, and personal projects. Every project I build is an opportunity to
        improve my skills and deepen my understanding of software development.
    </p>

    <blockquote class="quote-block" style="margin:24px 0;">
        <span data-i18n="edu.beyond.quote">"Education is not only about earning degrees—it is about building the mindset to keep learning for a lifetime."</span>
        <br>— <span data-i18n="hero.name">Ibe Zisokhi Laia</span>
    </blockquote>

    <a href="learning.php" class="btn outline" style="margin-top:10px; display:inline-block;" data-i18n="edu.beyond.btn">Discover What I'm Learning →</a>
</section>

<script>
function syncEduLanguages() {
    const currentLang = localStorage.getItem('lang') || localStorage.getItem('preferred_lang') || 'id';
    document.querySelectorAll('.edu-dyn-text').forEach(el => {
        const text = (currentLang === 'en') ? el.getAttribute('data-en') : el.getAttribute('data-id');
        if (text) {
            el.textContent = text;
        }
    });
}

document.addEventListener('DOMContentLoaded', syncEduLanguages);
syncEduLanguages();

document.addEventListener('click', function(e) {
    if (e.target.closest('[data-lang]') || e.target.closest('.lang-btn') || e.target.closest('.lang-option')) {
        setTimeout(syncEduLanguages, 80);
    }
});
</script>

<?php include 'partials/footer.php'; ?>