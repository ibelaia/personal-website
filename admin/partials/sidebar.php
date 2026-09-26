<!-- Bagian Menu Custom Sections dengan Dropdown Panah -->
<li class="nav-item has-submenu <?= (basename($_SERVER['PHP_SELF']) == 'custom-sections.php' || basename($_SERVER['PHP_SELF']) == 'custom-sections-list.php') ? 'active open' : '' ?>">
    <a href="javascript:void(0)" class="nav-link" onclick="toggleCustomSubmenu(this)" style="display:flex; justify-content:space-between; align-items:center;">
        <span style="display:flex; align-items:center; gap:10px;">
            <span>🧩</span>
            <span>Custom Sections</span>
        </span>
        <span class="sub-arrow" style="font-size:11px; transition:transform 0.2s;">▾</span>
    </a>
    <ul class="submenu" style="list-style:none; padding-left:28px; margin-top:6px; display: <?= (basename($_SERVER['PHP_SELF']) == 'custom-sections.php' || basename($_SERVER['PHP_SELF']) == 'custom-sections-list.php') ? 'block' : 'none' ?>;">
        <li style="margin-bottom:6px;">
            <a href="custom-sections.php" style="color: <?= basename($_SERVER['PHP_SELF']) == 'custom-sections.php' ? 'var(--primary, #a78bfa)' : 'var(--text-muted, #94a3b8)' ?>; text-decoration:none; font-size:12.5px; display:flex; align-items:center; gap:6px;">
                <span>+</span> Buat Halaman Baru
            </a>
        </li>
        <li>
            <a href="custom-sections-list.php" style="color: <?= basename($_SERVER['PHP_SELF']) == 'custom-sections-list.php' ? 'var(--primary, #a78bfa)' : 'var(--text-muted, #94a3b8)' ?>; text-decoration:none; font-size:12.5px; display:flex; align-items:center; gap:6px;">
                <span>📋</span> Kelola &amp; Daftar
            </a>
        </li>
    </ul>
</li>

<script>
function toggleCustomSubmenu(el) {
    var sub = el.nextElementSibling;
    var arrow = el.querySelector('.sub-arrow');
    if (sub.style.display === 'none' || sub.style.display === '') {
        sub.style.display = 'block';
        if (arrow) arrow.style.transform = 'rotate(180deg)';
    } else {
        sub.style.display = 'none';
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    }
}
</script>