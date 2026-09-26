# Personal Website v1.0 — Ibe Laia

## Cara Menjalankan (via XAMPP)

1. Copy seluruh folder ini ke `C:\xampp\htdocs\personal-website` (atau nama folder lain sesuai selera)
2. Jalankan **Apache** dari XAMPP Control Panel (tidak perlu MySQL, karena belum pakai database)
3. Buka browser: `http://localhost/personal-website/index.php`

> Penting: file ini pakai `.php`, jadi HARUS dibuka lewat `http://localhost/...`, BUKAN dengan double-click file (`file:///...`). Kalau dibuka langsung dari file explorer, kode PHP (termasuk navbar & footer) tidak akan jalan karena butuh Apache untuk memprosesnya.

## Struktur Folder

```
personal-website/
├── index.php          → Home
├── about.php          → About Me
├── journey.php        → My Journey
├── education.php      → Education
├── learning.php       → Learning Journey
├── experience.php     → Experiences
├── achievement.php    → Achievements
├── gallery.php        → Gallery
├── blog.php           → Journal/Blog
├── goals.php          → Future Goals
├── contact.php        → Contact (form + handler PHP aman)
│
├── partials/
│   ├── header.php     → navbar (di-include semua halaman)
│   └── footer.php     → footer (di-include semua halaman)
│
├── css/
│   └── style.css      → semua styling (1 file, dipakai semua halaman)
│
├── js/
│   └── script.js       → dark mode, reveal-on-scroll, hamburger menu, filter, form handler
│
└── data/               → scaffold untuk v2.0 (belum dipakai halaman saat ini)
    ├── about.json
    ├── journey.json
    ├── education.json
    ├── learning.json
    ├── experience.json
    ├── achievement.json
    ├── gallery.json
    ├── blog.json
    ├── goals.json
    └── messages.json   → otomatis terisi tiap ada yang submit form contact
```

## Yang Perlu Anda Isi Sendiri

Cari teks yang ditandai class `placeholder` (border putus-putus abu-abu di browser) atau tulisan dalam kurung siku `[ISI DI SINI: ...]`. Semua ada di:
- `about.php` — biography, personality, values, interests
- `journey.php` — narasi & timeline cerita hidup
- `education.php` — nama sekolah, tahun, jurusan
- `learning.php`, `experience.php`, `achievement.php`, `gallery.php`, `blog.php`, `goals.php` — sesuai isi masing-masing

Untuk foto: siapkan folder `assets/` sendiri, lalu ganti `<div class="box-img">Foto</div>` dengan `<img src="assets/nama-file.jpg" alt="...">` di file terkait.

## Tentang Keamanan Form Contact

`contact.php` sudah dilengkapi:
- **CSRF token** — mencegah form disubmit dari luar situs Anda
- **Honeypot field** — jebakan bot spam (field tersembunyi, invisible ke manusia)
- **Rate limiting** — maksimal 5 pesan/jam per pengunjung (bisa diubah di `$max_pesan_per_jam`)
- **Validasi & sanitasi** semua input, termasuk proteksi email header injection
- Backup otomatis semua pesan masuk ke `data/messages.json`

**PENTING:** Ganti `$tujuan_email` di dalam `contact.php` (baris paling atas) dengan email Anda yang aktif. Fungsi `mail()` PHP butuh mail server aktif — biasanya otomatis jalan di hosting cPanel, tapi **tidak** otomatis jalan di XAMPP lokal tanpa setup SMTP tambahan (misal pakai PHPMailer + Gmail SMTP). Selama itu, pesan tetap aman tersimpan di `data/messages.json` sebagai jaring pengaman.

## Langkah Selanjutnya (opsional, untuk Anda diskusikan lagi kalau perlu)

- Tambahkan foto asli di semua halaman
- Isi seluruh konten placeholder dengan cerita Anda sendiri
- Kalau nanti siap ke v2.0: sambungkan halaman ke file-file di `data/*.json` via PHP (`json_decode(file_get_contents(...))`) supaya konten bisa diedit tanpa sentuh HTML
- Setup SMTP kalau ingin email contact form benar-benar terkirim ke inbox Anda
