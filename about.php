<?php
$page  = 'about';
$title = 'About Me';
include 'partials/header.php';

// Baca data kartu dinamis
$cardsPath = __DIR__ . '/data/about_cards.json';
$aboutCards = [];
if (file_exists($cardsPath)) {
    $aboutCards = json_decode(file_get_contents($cardsPath), true) ?: [];
}

$personalities = $aboutCards['personality'] ?? [];
$values        = $aboutCards['values'] ?? [];
$interests     = $aboutCards['interests'] ?? [];
?>

<style>
    section.about {
        padding: 40px 0 60px;
    }
    
    section.about .about-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 72px;
        max-width: 1120px;
        margin: 0 auto;
    }

    section.about .about-image {
        flex: 0 0 350px;
        display: flex;
        justify-content: center;
    }

    section.about .about-image .img-box {
        width: 100%;
        max-width: 320px;
        background: transparent;
    }

    section.about .about-image .img-box img {
        width: 100%;
        height: auto;
        aspect-ratio: 4 / 5;
        object-fit: cover;
        border-radius: 24px;
        display: block;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }

    section.about .about-text {
        flex: 1;
    }

    /* GRID FLEKSIBEL DINAMIS */
    .grid-flexible-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .grid-interests-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }

    /* Penyesuaian Responsif Mobile (< 860px) */
    @media (max-width: 860px) {
        section.about {
            padding: 16px 14px 36px;
        }

        section.about .about-container {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }

        section.about .about-image {
            flex: none;
            width: 100%;
        }

        section.about .about-image .img-box {
            max-width: 200px;
            margin: 0 auto;
        }

        section.about .about-image .img-box img {
            width: 100%;
            height: auto;
            aspect-ratio: 4 / 5;
            object-fit: cover;
            border-radius: 18px;
        }

        section.about .about-text {
            text-align: left;
            padding: 0 4px;
        }

        .grid-flexible-cards {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .grid-interests-cards {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
    }
</style>

<div class="page-header">
    <h1 data-i18n="about.header.title">About Me</h1>
    <p data-i18n="about.header.subtitle">More than just a profile — this is the story of who I am, where I come from, and the journey that continues to shape me.</p>
</div>

<!-- BIOGRAPHY -->
<section class="about reveal">
    <div class="about-container">
        <div class="about-image">
            <div class="img-box">
                <img src="assets/img/about.jpg.png?v=<?= file_exists('assets/img/about.jpg.png') ? filemtime('assets/img/about.jpg.png') : time() ?>" alt="About Ibe Laia">
            </div>
        </div>
        <div class="about-text">
            <p class="eyebrow" data-i18n="about.bio.eyebrow">Biography</p>
            <h2 class="section-heading" data-i18n="about.bio.heading">My Story</h2>

            <p data-i18n="about.bio.p1">
                My name is Ibe Zisokhi Laia, an Informatics student at UPN "Veteran" Jawa Timur
                with a strong passion for technology and software development.
            </p>
            <p data-i18n="about.bio.p2">
                I was born in Medan, where I spent my early childhood before moving to South Nias,
                the hometown of my parents. Growing up in two different places gave me valuable life
                experiences and taught me how to adapt to new environments and challenges.
            </p>
            <p data-i18n="about.bio.p3">
                My interest in technology grew from a curiosity about how software and digital
                systems work. Inspired by technology and motivated by my achievements in mathematics
                and physics competitions, I decided to pursue Informatics in university. Since then,
                I have been continuously learning and building my skills, one project at a time.
            </p>
        </div>
    </div>
</section>

<!-- MY PERSONALITY -->
<section class="reveal">
    <p class="eyebrow" data-i18n="about.personality.eyebrow">Personality</p>
    <h2 class="section-heading" data-i18n="about.personality.heading">Who I Am</h2>

    <div class="grid-flexible-cards">
        <?php foreach ($personalities as $p): ?>
            <div class="card">
                <h3 class="card-dyn-text" data-id="<?= htmlspecialchars($p['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($p['title_en']) ? $p['title_en'] : ($p['title_id'] ?? '')) ?>"><?= htmlspecialchars($p['title_id'] ?? '') ?></h3>
                <p class="card-dyn-text" data-id="<?= htmlspecialchars($p['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($p['desc_en']) ? $p['desc_en'] : ($p['desc_id'] ?? '')) ?>"><?= htmlspecialchars($p['desc_id'] ?? '') ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- MY VALUES -->
<section class="reveal">
    <p class="eyebrow" data-i18n="about.values.eyebrow">Values</p>
    <h2 class="section-heading" data-i18n="about.values.heading">What I Believe In</h2>

    <div class="grid-flexible-cards">
        <?php foreach ($values as $v): ?>
            <div class="card">
                <h3 class="card-dyn-text" data-id="<?= htmlspecialchars($v['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($v['title_en']) ? $v['title_en'] : ($v['title_id'] ?? '')) ?>"><?= htmlspecialchars($v['title_id'] ?? '') ?></h3>
                <p class="card-dyn-text" data-id="<?= htmlspecialchars($v['desc_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($v['desc_en']) ? $v['desc_en'] : ($v['desc_id'] ?? '')) ?>"><?= htmlspecialchars($v['desc_id'] ?? '') ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- MY INTERESTS -->
<section class="reveal">
    <p class="eyebrow" data-i18n="about.interests.eyebrow">Interests</p>
    <h2 class="section-heading" data-i18n="about.interests.heading">Things I Enjoy</h2>
    <p data-i18n="about.interests.intro">Outside of academics and programming, I enjoy spending my time exploring different interests that keep me inspired and motivated.</p>

    <div class="grid-interests-cards">
        <?php foreach ($interests as $int): ?>
            <div class="card" style="text-align:center;">
                <span class="icon" style="font-size:26px; display:block; margin-bottom:8px;"><?= htmlspecialchars($int['icon'] ?? '⚡') ?></span>
                <span class="card-dyn-text" data-id="<?= htmlspecialchars($int['title_id'] ?? '') ?>" data-en="<?= htmlspecialchars(!empty($int['title_en']) ? $int['title_en'] : ($int['title_id'] ?? '')) ?>"><?= htmlspecialchars($int['title_id'] ?? '') ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- A LITTLE MORE ABOUT ME -->
<section class="reveal">
    <p class="eyebrow" data-i18n="about.vision.eyebrow">Looking Ahead</p>
    <h2 class="section-heading" data-i18n="about.vision.heading">My Vision</h2>

    <p data-i18n="about.vision.p1">
        My long-term goal is to become a Software Engineer and Web3 Developer, contributing to
        impactful technology projects while continuously improving my technical and personal skills.
    </p>
    <p data-i18n="about.vision.p2">
        Beyond building successful products, I hope to make my parents proud, work with leading
        technology companies, and one day build innovative digital solutions that can grow into
        meaningful businesses.
    </p>

    <blockquote class="quote-block">
        <span data-i18n="about.vision.quote">"Success doesn't come from talent alone. It comes from clear goals, responsibility, and consistent effort."</span>
        <br>— <span data-i18n="hero.name">Ibe Zisokhi Laia</span>
    </blockquote>
</section>

<script>
function syncDynamicCardLanguages() {
    const currentLang = localStorage.getItem('lang') || localStorage.getItem('preferred_lang') || 'id';
    document.querySelectorAll('.card-dyn-text').forEach(el => {
        const text = (currentLang === 'en') ? el.getAttribute('data-en') : el.getAttribute('data-id');
        if (text) {
            el.textContent = text;
        }
    });
}

document.addEventListener('DOMContentLoaded', syncDynamicCardLanguages);
syncDynamicCardLanguages();

document.addEventListener('click', function(e) {
    if (e.target.closest('[data-lang]') || e.target.closest('.lang-btn') || e.target.closest('.lang-option')) {
        setTimeout(syncDynamicCardLanguages, 100);
    }
});
</script>

<?php include 'partials/footer.php'; ?>