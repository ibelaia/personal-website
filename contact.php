<?php
session_start();
require_once __DIR__ . '/supabase.php';

$tujuan_email = "ibezlaia@gmail.com";
$max_pesan_per_jam = 5;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Deteksi bahasa aktif (default 'en' atau sesuai session/cookie website Anda)
$lang = $_SESSION['lang'] ?? $_GET['lang'] ?? 'en';
if (!in_array($lang, ['en', 'id'])) {
    $lang = 'en';
}

// Baca konten dari file JSON
$contactDataPath = __DIR__ . '/data/contact_content.json';
$contact = file_exists($contactDataPath) ? json_decode(file_get_contents($contactDataPath), true) : [];

// Ambil teks berdasarkan bahasa aktif, dengan fallback aman
$badge      = $contact[$lang]['badge'] ?? ($contact['en']['badge'] ?? 'GET IN TOUCH');
$title_text = $contact[$lang]['title'] ?? ($contact['en']['title'] ?? "Let's Work Together.");
$desc_text  = $contact[$lang]['desc'] ?? ($contact['en']['desc'] ?? "Have a project in mind or want to collaborate? I'd love to hear from you.");

$email_val  = $contact['email'] ?? 'ibezlaia@gmail.com';
$email_sub  = $contact[$lang]['email_sub'] ?? 'Drop me a message';
$wa_val     = $contact['whatsapp'] ?? '+62 812-3015-6692';
$wa_link    = $contact['wa_link'] ?? 'https://wa.me/6281230156692';
$wa_sub     = $contact[$lang]['wa_sub'] ?? "Let's chat";
$loc_val    = $contact['location'] ?? 'Surabaya, Indonesia';
$loc_sub    = $contact[$lang]['loc_sub'] ?? 'WIB (GMT +7)';
$avail_val  = $contact['availability'] ?? 'Open for Work';
$avail_sub  = $contact[$lang]['avail_sub'] ?? 'Internship & Projects';

/* ================= HANDLE POST (submit form) ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    function jsonResponse($success, $message) {
        echo json_encode(["success" => $success, "message" => $message]);
        exit;
    }

    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        http_response_code(403);
        jsonResponse(false, "Sesi tidak valid, silakan muat ulang halaman.");
    }

    if (!empty($_POST['website'])) {
        jsonResponse(true, "Pesan terkirim.");
    }

    if (!isset($_SESSION['contact_attempts'])) {
        $_SESSION['contact_attempts'] = [];
    }
    $_SESSION['contact_attempts'] = array_filter(
        $_SESSION['contact_attempts'],
        fn($t) => $t > time() - 3600
    );
    if (count($_SESSION['contact_attempts']) >= $max_pesan_per_jam) {
        http_response_code(429);
        jsonResponse(false, "Terlalu banyak percobaan. Silakan coba lagi nanti.");
    }

    $nama   = trim($_POST['nama'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $subjek = trim($_POST['subjek'] ?? '');
    $pesan  = trim($_POST['pesan'] ?? '');

    if ($nama === '' || $email === '' || $subjek === '' || $pesan === '') {
        jsonResponse(false, "Semua kolom wajib diisi.");
    }
    if (strlen($nama) > 100 || strlen($subjek) > 150 || strlen($pesan) > 2000) {
        jsonResponse(false, "Input terlalu panjang.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, "Format email tidak valid.");
    }

    $nama_aman   = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
    $email_aman  = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $subjek_aman = htmlspecialchars($subjek, ENT_QUOTES, 'UTF-8');
    $pesan_aman  = htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8');
    $ip_address  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    // 1. Simpan ke Supabase
    $payload = [
        'nama'       => $nama_aman,
        'email'      => $email_aman,
        'subjek'     => $subjek_aman,
        'pesan'      => $pesan_aman,
        'ip_address' => $ip_address,
        'is_replied' => 0,
        'created_at' => date('c')
    ];
    $resSupabase = supabase('messages', 'POST', [$payload]);

    $newId = time();
    if (!empty($resSupabase) && is_array($resSupabase) && isset($resSupabase[0]['id'])) {
        $newId = $resSupabase[0]['id'];
    }

    // 2. Backup JSON
    $logFile = __DIR__ . '/data/messages.json';
    $entry = [
        "id"         => $newId,
        "nama"       => $nama_aman,
        "email"      => $email_aman,
        "subjek"     => $subjek_aman,
        "pesan"      => $pesan_aman,
        "waktu"      => date('Y-m-d H:i:s'),
        "ip"         => $ip_address,
        "is_replied" => 0
    ];

    $existing = [];
    if (file_exists($logFile)) {
        $decoded = json_decode(file_get_contents($logFile), true);
        if (is_array($decoded)) { $existing = $decoded; }
    }
    $existing[] = $entry;
    @file_put_contents($logFile, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // 3. Email Notifikasi
    $subject = "[Website] $subjek_aman — from $nama_aman";
    $body    = "Nama: $nama_aman\nEmail: $email_aman\nSubject: $subjek_aman\n\nPesan:\n$pesan_aman";
    $headers = "From: no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n";
    $headers .= "Reply-To: " . $email_aman . "\r\n";
    @mail($tujuan_email, $subject, $body, $headers);

    $_SESSION['contact_attempts'][] = time();
    jsonResponse(true, "Terima kasih, pesan Anda sudah terkirim!");
}

/* ================= RENDER HALAMAN (GET) ================= */
$page  = 'contact';
$title = 'Contact';
include 'partials/header.php';
?>

<style>
    .contact-wrapper-pro {
        max-width: 1240px;
        margin: 130px auto 80px;
        padding: 0 20px;
        width: 100%;
        box-sizing: border-box;
    }
    .contact-grid-pro {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 32px;
        align-items: start;
        width: 100%;
    }
    .hero-badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #2dd4bf;
        margin-bottom: 10px;
    }
    .hero-badge-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #2dd4bf;
    }
    .contact-headline {
        font-size: clamp(28px, 3.5vw, 40px);
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -0.02em;
        color: #fff;
        margin-bottom: 12px;
    }
    .contact-headline span {
        color: #38bdf8;
    }
    .contact-subdesc {
        color: var(--color-text-muted, #94a3b8);
        font-size: 14.5px;
        line-height: 1.6;
        margin-bottom: 24px;
        max-width: 500px;
    }

    /* BANNER VISUAL */
    .contact-top-banner {
        background: #0b1120;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: 0 16px 32px rgba(0,0,0,0.2);
        width: 100%;
        box-sizing: border-box;
    }
    .banner-mobile { display: none; margin-bottom: 20px; }
    .banner-desktop { display: block; }

    .banner-top-bar {
        background: #06090f;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        font-size: 11px;
        color: #94a3b8;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .banner-dots { display: flex; gap: 6px; }
    .banner-dot { width: 9px; height: 9px; border-radius: 50%; }
    .banner-img-wrap {
        width: 100%;
        height: 170px;
        overflow: hidden;
        background: linear-gradient(135deg, #1e1b4b, #0f172a);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .banner-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.85;
        display: block;
    }

    /* FORMULIR CARD */
    .form-card-pro {
        background: #0b1120;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 16px 32px rgba(0,0,0,0.2);
        width: 100%;
        box-sizing: border-box;
    }
    .field-group-pro {
        position: relative;
        margin-bottom: 16px;
    }
    .field-icon-pro {
        position: absolute;
        left: 15px;
        top: 14px;
        color: #64748b;
        font-size: 14px;
        pointer-events: none;
    }
    .input-pro {
        width: 100%;
        padding: 13px 14px 13px 42px;
        background: #06090f;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        color: #fff;
        font-size: 13.5px;
        font-family: inherit;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .input-pro:focus {
        border-color: #2dd4bf;
        outline: none;
        box-shadow: 0 0 12px rgba(45, 212, 191, 0.15);
    }
    textarea.input-pro {
        min-height: 120px;
        resize: vertical;
        padding-top: 13px;
    }
    .btn-send-pro {
        width: 100%;
        background: linear-gradient(135deg, #a78bfa 0%, #2dd4bf 100%);
        color: #030712;
        border: none;
        padding: 13px 20px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.2s, transform 0.1s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 6px;
        box-sizing: border-box;
    }
    .btn-send-pro:hover { opacity: 0.95; }
    .btn-send-pro:active { transform: scale(0.99); }
    .hp-field { display: none !important; }

    /* KARTU KONTAK INFORMASI (GRID 2x2 KOMPAK) */
    .info-cards-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }
    .contact-box-item {
        background: #0b1120;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: border-color 0.2s, transform 0.2s;
        box-sizing: border-box;
    }
    .contact-box-item:hover {
        border-color: rgba(45, 212, 191, 0.35);
        transform: translateY(-2px);
    }
    .box-icon-circle {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: rgba(45, 212, 191, 0.1);
        color: #2dd4bf;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .box-content { display: flex; flex-direction: column; overflow: hidden; width: 100%; }
    .box-label { font-size: 9.5px; color: #94a3b8; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.8px; font-weight: 700; }
    .box-val { font-size: 11.5px; font-weight: 600; color: #2dd4bf; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .box-val a { color: inherit; text-decoration: none; }
    .box-sub { font-size: 9.5px; color: #64748b; }

    .divider-text-pro {
        display: flex; align-items: center; gap: 12px; color: #64748b;
        font-size: 11px; font-weight: 700; letter-spacing: 1.5px; margin: 24px 0 16px;
    }
    .divider-text-pro::before, .divider-text-pro::after {
        content: ''; flex: 1; height: 1px; background: rgba(255, 255, 255, 0.08);
    }
    .social-connect-card {
        background: #0b1120; border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px; padding: 16px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; box-sizing: border-box;
    }
    .social-btn-item {
        display: flex; flex-direction: column; align-items: center; gap: 6px;
        padding: 10px 4px; border-radius: 8px; color: #94a3b8; text-decoration: none; font-size: 11.5px; font-weight: 600; transition: all 0.2s;
    }
    .social-btn-item:hover { background: rgba(255, 255, 255, 0.05); color: #fff; }
    .social-btn-item span.icon { font-size: 16px; }

    .form-status { margin-top: 12px; font-size: 13px; border-radius: 8px; text-align: center; }
    .form-status.success { color: #34d399; padding: 8px; background: rgba(52, 211, 153, 0.1); }
    .form-status.error { color: #f87171; padding: 8px; background: rgba(248, 113, 113, 0.1); }

    /* ================= RESPONSIVE DESIGN (TABLET & MOBILE) ================= */
    @media (max-width: 992px) {
        .contact-grid-pro {
            grid-template-columns: 1fr;
            gap: 28px;
        }
        .contact-wrapper-pro {
            margin: 90px auto 50px;
            padding: 0 16px;
        }
        .banner-desktop {
            display: none;
        }
        .banner-mobile {
            display: block;
            margin-top: 14px;
            margin-bottom: 20px;
        }
    }

    @media (max-width: 480px) {
        .contact-wrapper-pro {
            padding: 0 12px;
            margin: 80px auto 40px;
        }
        .form-card-pro {
            padding: 16px;
            border-radius: 14px;
        }
        .info-cards-grid {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .contact-box-item {
            padding: 12px;
            gap: 12px;
        }
        .box-icon-circle {
            width: 36px;
            height: 36px;
            font-size: 16px;
            border-radius: 8px;
        }
        .box-val {
            font-size: 12px;
        }
        .social-connect-card {
            grid-template-columns: repeat(2, 1fr);
            padding: 12px;
        }
        .contact-headline {
            font-size: 26px;
        }
    }
</style>

<div class="contact-wrapper-pro">
    <div class="contact-grid-pro">
        <!-- KOLOM KIRI: DESKRIPSI, BANNER MOBILE, & FORMULIR -->
        <div>
            <div class="hero-badge-tag">
                <span class="hero-badge-dot"></span>
                <span><?= htmlspecialchars($badge) ?></span>
                <span class="hero-badge-dot"></span>
            </div>
            <h1 class="contact-headline">
                <span><?= htmlspecialchars($title_text) ?></span>
            </h1>
            <p class="contact-subdesc">
                <?= htmlspecialchars($desc_text) ?>
            </p>

            <!-- BANNER KHUSUS MOBILE (MUNCUL TEPAT DI BAWAH DESKRIPSI DI HP) -->
            <div class="contact-top-banner banner-mobile">
                <div class="banner-top-bar">
                    <span>TERMINAL — READY</span>
                    <div class="banner-dots">
                        <div class="banner-dot" style="background:#f87171;"></div>
                        <div class="banner-dot" style="background:#fbbf24;"></div>
                        <div class="banner-dot" style="background:#34d399;"></div>
                    </div>
                </div>
                <div class="banner-img-wrap">
                    <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=800&q=80" alt="Tech Workspace">
                </div>
            </div>

            <div class="form-card-pro">
                <form class="contact-form" method="POST" action="contact.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="text" name="website" class="hp-field" tabindex="-1" autocomplete="off">

                    <div class="field-group-pro">
                        <span class="field-icon-pro">👤</span>
                        <input type="text" name="nama" class="input-pro" placeholder="<?= $lang === 'id' ? 'Nama Anda' : 'Your Name' ?>" required maxlength="100">
                    </div>

                    <div class="field-group-pro">
                        <span class="field-icon-pro">✉️</span>
                        <input type="email" name="email" class="input-pro" placeholder="<?= $lang === 'id' ? 'Email Anda' : 'Your Email' ?>" required>
                    </div>

                    <div class="field-group-pro">
                        <span class="field-icon-pro">💬</span>
                        <input type="text" name="subjek" class="input-pro" placeholder="<?= $lang === 'id' ? 'Subjek Pesan' : 'Subject' ?>" required maxlength="150">
                    </div>

                    <div class="field-group-pro">
                        <span class="field-icon-pro">✏️</span>
                        <textarea name="pesan" class="input-pro" placeholder="<?= $lang === 'id' ? 'Tulis pesan Anda di sini...' : 'Your Message' ?>" required maxlength="2000"></textarea>
                    </div>

                    <button type="submit" class="btn-send-pro">
                        <span><?= $lang === 'id' ? 'Kirim Pesan' : 'Send Message' ?></span> →
                    </button>
                    <div class="form-status"></div>
                </form>
            </div>
        </div>

        <!-- KOLOM KANAN: BANNER DESKTOP & KARTU KONTAK -->
        <div>
            <!-- Banner Visual Khusus Desktop di Kanan Atas -->
            <div class="contact-top-banner banner-desktop">
                <div class="banner-top-bar">
                    <span>TERMINAL — READY</span>
                    <div class="banner-dots">
                        <div class="banner-dot" style="background:#f87171;"></div>
                        <div class="banner-dot" style="background:#fbbf24;"></div>
                        <div class="banner-dot" style="background:#34d399;"></div>
                    </div>
                </div>
                <div class="banner-img-wrap">
                    <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=800&q=80" alt="Tech Workspace">
                </div>
            </div>

            <div class="hero-badge-tag" style="color:#38bdf8; margin-bottom:12px;">
                <span class="hero-badge-dot" style="background:#38bdf8;"></span>
                <span><?= $lang === 'id' ? 'INFORMASI KONTAK' : 'CONTACT INFO' ?></span>
            </div>

            <!-- Grid Kartu Info Kontak Dinamis -->
            <div class="info-cards-grid">
                <div class="contact-box-item">
                    <div class="box-icon-circle">✉️</div>
                    <div class="box-content">
                        <div class="box-label">Email</div>
                        <div class="box-val"><a href="mailto:<?= htmlspecialchars($email_val) ?>"><?= htmlspecialchars($email_val) ?></a></div>
                        <div class="box-sub"><?= htmlspecialchars($email_sub) ?></div>
                    </div>
                </div>

                <div class="contact-box-item">
                    <div class="box-icon-circle" style="background:rgba(52,211,153,0.1); color:#34d399;">💬</div>
                    <div class="box-content">
                        <div class="box-label">WhatsApp</div>
                        <div class="box-val"><a href="<?= htmlspecialchars($wa_link) ?>" target="_blank"><?= htmlspecialchars($wa_val) ?></a></div>
                        <div class="box-sub"><?= htmlspecialchars($wa_sub) ?></div>
                    </div>
                </div>

                <div class="contact-box-item">
                    <div class="box-icon-circle" style="background:rgba(168,85,247,0.1); color:#c084fc;">📍</div>
                    <div class="box-content">
                        <div class="box-label"><?= $lang === 'id' ? 'Lokasi' : 'Location' ?></div>
                        <div class="box-val"><?= htmlspecialchars($loc_val) ?></div>
                        <div class="box-sub"><?= htmlspecialchars($loc_sub) ?></div>
                    </div>
                </div>

                <div class="contact-box-item">
                    <div class="box-icon-circle" style="background:rgba(251,191,36,0.1); color:#fbbf24;">🟢</div>
                    <div class="box-content">
                        <div class="box-label"><?= $lang === 'id' ? 'Ketersediaan' : 'Availability' ?></div>
                        <div class="box-val"><?= htmlspecialchars($avail_val) ?></div>
                        <div class="box-sub"><?= htmlspecialchars($avail_sub) ?></div>
                    </div>
                </div>
            </div>

            <div class="divider-text-pro"><?= $lang === 'id' ? 'ATAU HUBUNGI LANGSUNG' : 'OR REACH ME DIRECTLY' ?></div>

            <div class="hero-badge-tag" style="color:#a78bfa; margin-bottom:10px;">
                <span class="hero-badge-dot" style="background:#a78bfa;"></span>
                <span><?= $lang === 'id' ? 'MARI TERHUBUNG' : "LET'S CONNECT" ?></span>
                <span class="hero-badge-dot"></span>
            </div>

            <div class="social-connect-card">
                <a href="https://github.com" target="_blank" class="social-btn-item">
                    <span class="icon">&lt;/&gt;</span>
                    <span>GitHub</span>
                </a>
                <a href="https://linkedin.com" target="_blank" class="social-btn-item">
                    <span class="icon">💼</span>
                    <span>LinkedIn</span>
                </a>
                <a href="https://instagram.com/ibe_z._laia02" target="_blank" class="social-btn-item">
                    <span class="icon">📷</span>
                    <span>Instagram</span>
                </a>
                <a href="/portfolio-ibelaia/" target="_blank" class="social-btn-item">
                    <span class="icon">🌐</span>
                    <span>Portfolio</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>