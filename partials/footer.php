<!-- FOOTER RESMI & ARSIP HIDUP -->
    <footer class="footer" style="padding: 40px 20px 30px; border-top: 1px solid var(--border-color, rgba(255, 255, 255, 0.08)); margin-top: 50px; background: rgba(10, 13, 20, 0.5);">
        <div style="max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; align-items: center; gap: 18px; text-align: center;">
            
            <!-- Tautan Arsip Perjalanan, Belajar, dan Tujuan -->
            <div style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 10px 18px; font-size: 13px; font-weight: 500;">
                <a href="learning.php" style="color: var(--text-muted, #94a3b8); text-decoration: none;" onmouseover="this.style.color='#c4b5fd'" onmouseout="this.style.color='var(--text-muted, #94a3b8)'">
                    ⚡ <span data-i18n="nav.learning">Perjalanan Belajar</span>
                </a>
                <span style="color: rgba(255,255,255,0.2);">•</span>
                <a href="goals.php" style="color: var(--text-muted, #94a3b8); text-decoration: none;" onmouseover="this.style.color='#c4b5fd'" onmouseout="this.style.color='var(--text-muted, #94a3b8)'">
                    🎯 <span data-i18n="nav.goals">Target Masa Depan</span>
                </a>
                <span style="color: rgba(255,255,255,0.2);">•</span>
                <a href="achievement.php" style="color: var(--text-muted, #94a3b8); text-decoration: none;" onmouseover="this.style.color='#c4b5fd'" onmouseout="this.style.color='var(--text-muted, #94a3b8)'">
                    🏆 <span data-i18n="nav.achievements">Prestasi &amp; Sertifikat</span>
                </a>
            </div>

            <!-- Hak Cipta & Info Pembuat -->
            <div style="color: var(--text-muted, #94a3b8); font-size: 12px; line-height: 1.6;">
                &copy; <?= date('Y') ?> <strong>Ibe Laia</strong>. All Rights Reserved.<br>
                <span style="font-size: 11px; opacity: 0.75;">Dirancang &amp; Dikembangkan oleh Ibe Laia · Dibangun dengan PHP, HTML, CSS &amp; JavaScript.</span>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>