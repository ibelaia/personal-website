<?php
require_once __DIR__ . '/../config.php';

if (!isset($page))  { $page = ''; }
if (!isset($title)) { $title = 'Ibe Laia'; }

function navActive($current, $target) {
    return $current === $target ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — Ibe Laia</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="navbar-wrap">
      <nav class="navbar">
        <a href="index.php" class="logo" style="text-decoration:none; color:inherit;">Ibe Laia</a>

        <!-- MENU UTAMA DENGAN DUKUNGAN DWIBAHASA PENUH -->
        <ul class="menu">
            <li><a href="index.php" data-i18n="nav.home" class="<?= navActive($page, 'home') ?>">Home</a></li>
            <li><a href="about.php" data-i18n="nav.about" class="<?= navActive($page, 'about') ?>">About</a></li>
            <li><a href="journey.php" data-i18n="nav.journey" class="<?= navActive($page, 'journey') ?>">Journey</a></li>
            <li><a href="education.php" data-i18n="nav.education" class="<?= navActive($page, 'education') ?>">Education</a></li>
            
            <!-- MENU ARSIP DWIBAHASA (ID: Arsip | EN: Archive) -->
            <li>
                <a href="archive.php" 
                   data-i18n="nav.archive" 
                   class="nav-archive-link <?= navActive($page, 'archive') ?>">
                   Arsip
                </a>
            </li>
            
            <!-- Link Portofolio -->
            <li>
                <a href="<?= defined('PORTFOLIO_URL') ? PORTFOLIO_URL : '/portfolio-ibelaia/' ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="portfolio-link"
                   data-i18n="nav.mywork">
                     Portofolio ↗
                </a>
            </li>
        </ul>

        <div class="nav-actions">
            <a href="contact.php" class="btn-contact-pill">
                <span data-i18n="nav.contactBtn">Contact Me</span> 📞
            </a>

            <button id="hamburger" class="hamburger" aria-label="Buka menu" aria-expanded="false">Menu</button>

            <div class="menu-trigger-wrap">
                <button id="menuTrigger" class="menu-trigger" aria-label="Buka opsi lain" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>

                <div id="dropdownPanel" class="dropdown-panel">
                    <!-- LOGIN ADMIN -->
                    <a href="admin/login.php" class="dropdown-item">
                        👤 <span data-i18n="dropdown.login">Login</span>
                    </a>

                    <!-- DOWNLOAD CV -->
                    <a href="assets/cv.pdf" download class="dropdown-item">
                        📄 <span data-i18n="dropdown.download">Download CV</span>
                    </a>

                    <div class="dropdown-divider"></div>
                    <div class="dropdown-label" data-i18n="dropdown.language">Language</div>
                    <button type="button" class="dropdown-item lang-option" data-lang="id">
                        🇮🇩 Bahasa Indonesia <span class="check">✓</span>
                    </button>
                    <button type="button" class="dropdown-item lang-option" data-lang="en">
                        🇬🇧 English <span class="check">✓</span>
                    </button>

                    <div class="dropdown-divider"></div>
                    <div class="dropdown-label" data-i18n="dropdown.theme">Theme</div>
                    <button type="button" class="dropdown-item theme-option" data-theme="light">
                        ☀️ <span data-i18n="dropdown.white">White</span> <span class="check">✓</span>
                    </button>
                    <button type="button" class="dropdown-item theme-option" data-theme="dark">
                        🌙 <span data-i18n="dropdown.dark">Dark</span> <span class="check">✓</span>
                    </button>
                </div>
            </div>
        </div>
      </nav>
    </div>

    <!-- SCRIPT OTOMATIS PENYESUAIAN TEKS ARSIP/ARCHIVE -->
    <script>
    (function() {
        function syncNavArchiveText() {
            var currentLang = localStorage.getItem('lang') || localStorage.getItem('preferred_lang') || 'id';
            var archiveLinks = document.querySelectorAll('.nav-archive-link');
            archiveLinks.forEach(function(el) {
                el.textContent = (currentLang === 'en') ? 'Archive' : 'Arsip';
            });
        }
        document.addEventListener('DOMContentLoaded', syncNavArchiveText);
        document.addEventListener('click', function(e) {
            if (e.target.closest('[data-lang]')) {
                setTimeout(syncNavArchiveText, 60);
            }
        });
        syncNavArchiveText();
    })();
    </script>