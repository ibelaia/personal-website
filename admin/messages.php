<?php
$pageTitle = 'Messages & Contact Hub';
$topbarTitle = 'Overview / Messages & Contact Hub';

require_once __DIR__ . '/../supabase.php';
require_once __DIR__ . '/partials/header.php';

$contactDataPath = __DIR__ . '/../data/contact_content.json';

// Default values lengkap untuk EN & ID
$defaultContact = [
    'en' => [
        'badge'     => 'GET IN TOUCH',
        'title'     => "Let's Work Together.",
        'desc'      => "Have a project in mind or want to collaborate? I'd love to hear from you. Let's create something amazing together!",
        'email_sub' => 'Drop me a message',
        'wa_sub'    => "Let's chat",
        'loc_sub'   => 'WIB (GMT +7)',
        'avail_sub' => 'Internship & Projects'
    ],
    'id' => [
        'badge'     => 'HUBUNGI SAYA',
        'title'     => "Mari Bekerja Bersama.",
        'desc'      => "Punya proyek atau ingin berkolaborasi? Saya akan sangat senang mendengarnya. Mari buat sesuatu yang hebat bersama!",
        'email_sub' => 'Kirimkan pesan',
        'wa_sub'    => 'Mari mengobrol',
        'loc_sub'   => 'WIB (GMT +7)',
        'avail_sub' => 'Magang & Proyek'
    ],
    'email'        => 'ibezlaia@gmail.com',
    'whatsapp'     => '+62 812-3015-6692',
    'wa_link'      => 'https://wa.me/6281230156692',
    'location'     => 'Surabaya, Indonesia',
    'availability' => 'Open for Work'
];

// Jika file belum ada atau strukturnya lama, buat file dengan default lengkap
if (!file_exists($contactDataPath)) {
    file_put_contents($contactDataPath, json_encode($defaultContact, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$successMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_contact_content'])) {
    $updatedData = [
        'en' => [
            'badge'     => trim($_POST['badge_en'] ?? ''),
            'title'     => trim($_POST['title_en'] ?? ''),
            'desc'      => trim($_POST['desc_en'] ?? ''),
            'email_sub' => trim($_POST['email_sub_en'] ?? ''),
            'wa_sub'    => trim($_POST['wa_sub_en'] ?? ''),
            'loc_sub'   => trim($_POST['loc_sub_en'] ?? ''),
            'avail_sub' => trim($_POST['avail_sub_en'] ?? '')
        ],
        'id' => [
            'badge'     => trim($_POST['badge_id'] ?? ''),
            'title'     => trim($_POST['title_id'] ?? ''),
            'desc'      => trim($_POST['desc_id'] ?? ''),
            'email_sub' => trim($_POST['email_sub_id'] ?? ''),
            'wa_sub'    => trim($_POST['wa_sub_id'] ?? ''),
            'loc_sub'   => trim($_POST['loc_sub_id'] ?? ''),
            'avail_sub' => trim($_POST['avail_sub_id'] ?? '')
        ],
        'email'        => trim($_POST['email'] ?? ''),
        'whatsapp'     => trim($_POST['whatsapp'] ?? ''),
        'wa_link'      => trim($_POST['wa_link'] ?? ''),
        'location'     => trim($_POST['location'] ?? ''),
        'availability' => trim($_POST['availability'] ?? '')
    ];

    file_put_contents($contactDataPath, json_encode($updatedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $successMsg = 'Konten & Informasi Kontak berhasil diperbarui untuk Bahasa Inggris & Indonesia!';
}

$contact = json_decode(file_get_contents($contactDataPath), true);
// Fallback aman jika struktur json lama
if (!is_array($contact) || !isset($contact['en'])) {
    $contact = $defaultContact;
}

// Handle Toggle Status Balasan Pesan
if (isset($_GET['toggle_reply']) && isset($_GET['id'])) {
    $targetId = $_GET['id'];
    $newStatus = (int)$_GET['toggle_reply'];

    supabase('messages?id=eq.' . $targetId, 'PATCH', ['is_replied' => $newStatus]);

    $jsonPath = __DIR__ . '/../data/messages.json';
    if (file_exists($jsonPath)) {
        $messagesJson = json_decode(file_get_contents($jsonPath), true) ?: [];
        foreach ($messagesJson as &$m) {
            if ((string)($m['id'] ?? '') === (string)$targetId) {
                $m['is_replied'] = $newStatus;
                break;
            }
        }
        @file_put_contents($jsonPath, json_encode($messagesJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    header('Location: messages.php?view=inbox&msg=updated');
    exit;
}

$messages = [];
$res = supabase('messages?select=*&order=created_at.desc');
if (!empty($res) && is_array($res) && !isset($res['error'])) {
    $messages = $res;
} else {
    $jsonPath = __DIR__ . '/../data/messages.json';
    if (file_exists($jsonPath)) {
        $messages = json_decode(file_get_contents($jsonPath), true) ?: [];
        usort($messages, fn($a, $b) => strtotime($b['waktu'] ?? 0) <=> strtotime($a['waktu'] ?? 0));
    }
}

$unreadCount = 0;
foreach ($messages as $m) {
    if (empty($m['is_replied'])) {
        $unreadCount++;
    }
}

$view = $_GET['view'] ?? 'hub';
?>

<style>
    .hub-wrapper { max-width: 1100px; margin: 0 auto; }
    
    .hub-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-top: 24px;
    }
    .hub-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 32px 28px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 260px;
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        text-decoration: none;
        color: inherit;
        position: relative;
        overflow: hidden;
    }
    .hub-card:hover {
        transform: translateY(-4px);
        border-color: rgba(139, 92, 246, 0.45);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }
    .hub-card-icon-wrap {
        width: 52px; height: 52px; border-radius: 12px;
        background: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center;
        font-size: 24px; margin-bottom: 20px; border: 1px solid rgba(139, 92, 246, 0.2);
    }
    .hub-card h3 { font-size: 19px; color: #fff; margin-bottom: 8px; font-weight: 700; letter-spacing: -0.3px; }
    .hub-card p { font-size: 13.5px; color: var(--text-muted); line-height: 1.6; margin-bottom: 24px; flex: 1; }
    .hub-card-action { color: #c4b5fd; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; }
    .notification-pill {
        position: absolute; top: 24px; right: 24px;
        background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3);
        padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
    }

    .admin-card-box {
        background: var(--bg-card); border: 1px solid var(--border-color);
        border-radius: 16px; padding: 28px; margin-top: 10px;
    }
    .card-box-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 24px; padding-bottom: 14px; border-bottom: 1px solid var(--border-color);
    }
    .btn-back-hub {
        background: rgba(255,255,255,0.05); border: 1px solid var(--border-color);
        color: #cbd5e1; padding: 7px 16px; border-radius: 8px; font-size: 13px;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; transition: background 0.2s;
    }
    .btn-back-hub:hover { background: rgba(255,255,255,0.1); color: #fff; }
    
    .field-item { margin-bottom: 14px; }
    .field-item label { display: block; font-size: 12px; font-weight: 600; color: #cbd5e1; margin-bottom: 5px; }
    .clean-input {
        width: 100%; padding: 10px 14px; background: #05080e;
        border: 1px solid var(--border-color); border-radius: 8px; color: #fff; font-size: 13.5px; font-family: inherit; box-sizing: border-box;
    }
    .clean-input:focus { border-color: var(--primary); outline: none; }
    textarea.clean-input { min-height: 75px; resize: vertical; }
    
    .grid-2-lang { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; background: rgba(255,255,255,0.015); border: 1px solid var(--border-color); padding: 16px; border-radius: 12px; margin-bottom: 16px; }
    .lang-column h5 { font-size: 12px; text-transform: uppercase; letter-spacing: 0.8px; color: #c4b5fd; margin-bottom: 10px; font-weight: 700; }

    .sub-section-title { font-size: 14px; font-weight: 700; color: #c4b5fd; margin: 26px 0 14px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color); padding-bottom: 6px; }
    
    .contact-item-card {
        background: #090c13;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 16px;
    }
    .contact-item-card-title {
        font-size: 13.5px; font-weight: 700; color: #fff; margin-bottom: 12px;
        display: flex; align-items: center; gap: 8px;
    }

    .btn-save-contact {
        background: var(--primary); color: #fff; border: none; padding: 12px 24px;
        border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: opacity 0.2s;
    }
    .btn-save-contact:hover { opacity: 0.9; }
    .alert-ok { background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; font-weight: 500; }

    /* INBOX ITEMS */
    .msg-card-item {
        background: #090c13; border: 1px solid var(--border-color);
        border-radius: 12px; padding: 20px; margin-bottom: 16px;
    }
    .msg-card-item.replied { border-left: 4px solid #34d399; background: rgba(52, 211, 153, 0.02); }
    .msg-top-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; gap: 12px; }
    .sender-info-box { display: flex; align-items: center; gap: 12px; }
    .sender-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: linear-gradient(135deg, #4c1d95, #2dd4bf);
        color: #fff; display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 13.5px; flex-shrink: 0;
    }
    .sender-name { font-size: 14.5px; font-weight: 700; color: #fff; margin-bottom: 2px; }
    .sender-email { font-size: 12px; color: var(--text-muted); }
    .msg-time-badge {
        font-size: 11.5px; color: #64748b; background: #05080e;
        padding: 5px 10px; border-radius: 6px; border: 1px solid var(--border-color); white-space: nowrap;
    }
    .msg-subject-box { font-size: 13.5px; font-weight: 600; color: #c4b5fd; margin: 12px 0 8px; }
    .msg-body-box {
        font-size: 13.5px; line-height: 1.6; color: var(--text-main);
        background: #05080e; padding: 14px 16px; border-radius: 8px;
        border: 1px solid var(--border-color); white-space: pre-wrap; word-break: break-word;
    }
    .msg-actions-row { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; flex-wrap: wrap; gap: 10px; }
    .btn-reply-mail {
        background: rgba(139, 92, 246, 0.15); border: 1px solid rgba(139, 92, 246, 0.3);
        color: #c4b5fd; padding: 6px 14px; border-radius: 6px; text-decoration: none;
        font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;
    }
    .btn-reply-mail:hover { background: var(--primary); color: #fff; }
    .status-badge-toggle {
        padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }
    .status-badge-toggle.unreplied { background: rgba(245, 158, 11, 0.12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.25); }
    .status-badge-toggle.replied { background: rgba(52, 211, 153, 0.12); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.25); }
    .empty-inbox-panel { text-align: center; padding: 50px 20px; color: var(--text-muted); font-size: 13.5px; }

    @media (max-width: 768px) {
        .hub-grid { grid-template-columns: 1fr; gap: 16px; }
        .grid-2-lang { grid-template-columns: 1fr; gap: 12px; }
    }
</style>

<div class="hub-wrapper">

    <?php if ($view === 'hub'): ?>
        <!-- ================= HALAMAN UTAMA HUB (2 CARD) ================= -->
        <div style="margin-bottom: 6px;">
            <h2 style="font-size: 22px; color: #fff; margin-bottom: 6px; font-weight: 700;">Pusat Kontrol Kontak &amp; Pesan</h2>
            <p style="font-size: 13.5px; color: var(--text-muted);">Pilih menu di bawah untuk mengedit konten halaman kontak atau mengelola pesan masuk.</p>
        </div>

        <div class="hub-grid">
            <!-- Card 1: Edit Contact Content -->
            <a href="messages.php?view=edit" class="hub-card">
                <div>
                    <div class="hub-card-icon-wrap">✏️</div>
                    <h3>Edit Contact Content</h3>
                    <p>Ubah teks utama (Let's Work Together), nomor WhatsApp, email, lokasi, dan status ketersediaan kerja (Multi-Bahasa EN/ID).</p>
                </div>
                <div class="hub-card-action">Buka Editor Kontak →</div>
            </a>

            <!-- Card 2: Messages Inbox -->
            <a href="messages.php?view=inbox" class="hub-card">
                <?php if ($unreadCount > 0): ?>
                    <div class="notification-pill">🔔 <?= $unreadCount ?> Pesan Baru</div>
                <?php endif; ?>
                <div>
                    <div class="hub-card-icon-wrap" style="background: rgba(45, 212, 191, 0.1); border-color: rgba(45, 212, 191, 0.2);">📥</div>
                    <h3>Messages Inbox</h3>
                    <p>Lihat daftar pesan masuk dari pengunjung website, tandai sudah dibalas, dan balas langsung via email.</p>
                </div>
                <div class="hub-card-action" style="color: #2dd4bf;">Buka Kotak Masuk (<?= count($messages) ?>) →</div>
            </a>
        </div>

    <?php elseif ($view === 'edit'): ?>
        <!-- ================= HALAMAN 1: FORM EDIT KONTAK MULTI-BAHASA ================= -->
        <div class="admin-card-box">
            <div class="card-box-header">
                <div>
                    <h3 style="font-size: 17px; color: #fff; margin-bottom: 3px;">✏️ Edit Konten Halaman Kontak (EN / ID)</h3>
                    <p style="font-size: 12.5px; color: var(--text-muted);">Perubahan langsung terupdate secara real-time di halaman publik.</p>
                </div>
                <a href="messages.php" class="btn-back-hub">← Kembali ke Hub</a>
            </div>

            <?php if ($successMsg): ?>
                <div class="alert-ok">✓ <?= htmlspecialchars($successMsg) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="update_contact_content" value="1">

                <div class="sub-section-title">1. Header Utama (Judul &amp; Deskripsi)</div>
                <div class="grid-2-lang">
                    <div class="lang-column">
                        <h5>🇬🇧 English</h5>
                        <div class="field-item">
                            <label>Badge Atas</label>
                            <input type="text" name="badge_en" class="clean-input" value="<?= htmlspecialchars($contact['en']['badge'] ?? '') ?>" required>
                        </div>
                        <div class="field-item">
                            <label>Judul Utama</label>
                            <input type="text" name="title_en" class="clean-input" value="<?= htmlspecialchars($contact['en']['title'] ?? '') ?>" required>
                        </div>
                        <div class="field-item">
                            <label>Deskripsi Pengantar</label>
                            <textarea name="desc_en" class="clean-input"><?= htmlspecialchars($contact['en']['desc'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="lang-column">
                        <h5>🇮🇩 Indonesia</h5>
                        <div class="field-item">
                            <label>Badge Atas</label>
                            <input type="text" name="badge_id" class="clean-input" value="<?= htmlspecialchars($contact['id']['badge'] ?? '') ?>" required>
                        </div>
                        <div class="field-item">
                            <label>Judul Utama</label>
                            <input type="text" name="title_id" class="clean-input" value="<?= htmlspecialchars($contact['id']['title'] ?? '') ?>" required>
                        </div>
                        <div class="field-item">
                            <label>Deskripsi Pengantar</label>
                            <textarea name="desc_id" class="clean-input"><?= htmlspecialchars($contact['id']['desc'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="sub-section-title">2. Kartu Informasi Kontak (Project Cards Terstruktur)</div>

                <!-- Card A: Email -->
                <div class="contact-item-card">
                    <div class="contact-item-card-title">✉️ Kartu Email</div>
                    <div class="field-item">
                        <label>Alamat Email (Global)</label>
                        <input type="email" name="email" class="clean-input" value="<?= htmlspecialchars($contact['email'] ?? '') ?>" required>
                    </div>
                    <div class="grid-2-lang" style="margin-bottom:0;">
                        <div class="lang-column">
                            <div class="field-item" style="margin-bottom:0;">
                                <label>Sub-teks (EN)</label>
                                <input type="text" name="email_sub_en" class="clean-input" value="<?= htmlspecialchars($contact['en']['email_sub'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="lang-column">
                            <div class="field-item" style="margin-bottom:0;">
                                <label>Sub-teks (ID)</label>
                                <input type="text" name="email_sub_id" class="clean-input" value="<?= htmlspecialchars($contact['id']['email_sub'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card B: WhatsApp -->
                <div class="contact-item-card">
                    <div class="contact-item-card-title">💬 Kartu WhatsApp</div>
                    <div class="grid-2-lang">
                        <div class="lang-column" style="margin-bottom:0;">
                            <div class="field-item">
                                <label>Nomor WhatsApp</label>
                                <input type="text" name="whatsapp" class="clean-input" value="<?= htmlspecialchars($contact['whatsapp'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="lang-column" style="margin-bottom:0;">
                            <div class="field-item">
                                <label>Link URL (https://wa.me/...)</label>
                                <input type="text" name="wa_link" class="clean-input" value="<?= htmlspecialchars($contact['wa_link'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="grid-2-lang" style="margin-bottom:0;">
                        <div class="lang-column">
                            <div class="field-item" style="margin-bottom:0;">
                                <label>Sub-teks (EN)</label>
                                <input type="text" name="wa_sub_en" class="clean-input" value="<?= htmlspecialchars($contact['en']['wa_sub'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="lang-column">
                            <div class="field-item" style="margin-bottom:0;">
                                <label>Sub-teks (ID)</label>
                                <input type="text" name="wa_sub_id" class="clean-input" value="<?= htmlspecialchars($contact['id']['wa_sub'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card C: Location -->
                <div class="contact-item-card">
                    <div class="contact-item-card-title">📍 Kartu Lokasi</div>
                    <div class="field-item">
                        <label>Lokasi / Kota (Global)</label>
                        <input type="text" name="location" class="clean-input" value="<?= htmlspecialchars($contact['location'] ?? '') ?>" required>
                    </div>
                    <div class="grid-2-lang" style="margin-bottom:0;">
                        <div class="lang-column">
                            <div class="field-item" style="margin-bottom:0;">
                                <label>Sub-teks / Zona Waktu (EN)</label>
                                <input type="text" name="loc_sub_en" class="clean-input" value="<?= htmlspecialchars($contact['en']['loc_sub'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="lang-column">
                            <div class="field-item" style="margin-bottom:0;">
                                <label>Sub-teks / Zona Waktu (ID)</label>
                                <input type="text" name="loc_sub_id" class="clean-input" value="<?= htmlspecialchars($contact['id']['loc_sub'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card D: Availability -->
                <div class="contact-item-card">
                    <div class="contact-item-card-title">🟢 Kartu Status Ketersediaan</div>
                    <div class="field-item">
                        <label>Status Ketersediaan (Global)</label>
                        <input type="text" name="availability" class="clean-input" value="<?= htmlspecialchars($contact['availability'] ?? '') ?>" required>
                    </div>
                    <div class="grid-2-lang" style="margin-bottom:0;">
                        <div class="lang-column">
                            <div class="field-item" style="margin-bottom:0;">
                                <label>Sub-teks (EN)</label>
                                <input type="text" name="avail_sub_en" class="clean-input" value="<?= htmlspecialchars($contact['en']['avail_sub'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="lang-column">
                            <div class="field-item" style="margin-bottom:0;">
                                <label>Sub-teks (ID)</label>
                                <input type="text" name="avail_sub_id" class="clean-input" value="<?= htmlspecialchars($contact['id']['avail_sub'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-save-contact">💾 Simpan Perubahan Kontak</button>
                </div>
            </form>
        </div>

    <?php elseif ($view === 'inbox'): ?>
        <!-- ================= HALAMAN 2: MESSAGES INBOX ================= -->
        <div class="admin-card-box">
            <div class="card-box-header">
                <div>
                    <h3 style="font-size: 17px; color: #fff; margin-bottom: 3px;">📥 Messages Inbox (Pesan Masuk)</h3>
                    <p style="font-size: 12.5px; color: var(--text-muted);">Daftar pesan dari pengunjung form kontak.</p>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <?php if ($unreadCount > 0): ?>
                        <span class="notification-pill" style="position:static;"><?= $unreadCount ?> Pesan Baru</span>
                    <?php endif; ?>
                    <a href="messages.php" class="btn-back-hub">← Kembali ke Hub</a>
                </div>
            </div>

            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $msg): 
                    $msgId   = $msg['id'] ?? '';
                    $nama    = htmlspecialchars($msg['nama'] ?? 'Pengunjung');
                    $email   = htmlspecialchars($msg['email'] ?? '-');
                    $subjek  = htmlspecialchars($msg['subjek'] ?? 'Tanpa Subjek');
                    $pesan   = htmlspecialchars($msg['pesan'] ?? '');
                    $waktu   = htmlspecialchars($msg['created_at'] ?? $msg['waktu'] ?? date('Y-m-d H:i'));
                    $isReplied = !empty($msg['is_replied']) ? 1 : 0;
                    $initial = strtoupper(substr($nama, 0, 2));
                ?>
                    <div class="msg-card-item <?= $isReplied ? 'replied' : '' ?>">
                        <div class="msg-top-row">
                            <div class="sender-info-box">
                                <div class="sender-avatar"><?= $initial ?></div>
                                <div>
                                    <div class="sender-name"><?= $nama ?></div>
                                    <div class="sender-email"><?= $email ?></div>
                                </div>
                            </div>
                            <div class="msg-time-badge">🕒 <?= date('d M Y, H:i', strtotime($waktu)) ?></div>
                        </div>

                        <div class="msg-subject-box">📌 <?= $subjek ?></div>
                        <div class="msg-body-box"><?= $pesan ?></div>

                        <div class="msg-actions-row">
                            <a href="messages.php?view=inbox&toggle_reply=<?= $isReplied ? 0 : 1 ?>&id=<?= urlencode($msgId) ?>" class="status-badge-toggle <?= $isReplied ? 'replied' : 'unreplied' ?>">
                                <span><?= $isReplied ? '✅ Sudah Dibalas' : '⏳ Belum Dibalas (Tandai Selesai)' ?></span>
                            </a>

                            <a href="mailto:<?= $email ?>?subject=Re: <?= rawurlencode($subjek) ?>" class="btn-reply-mail" target="_blank">
                                <span>✉️</span> Balas via Email
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-inbox-panel">
                    <span style="font-size:36px; display:block; margin-bottom:10px;">📭</span>
                    <p>Belum ada pesan yang masuk dari pengunjung.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>