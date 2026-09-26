</div> <!-- .content-area -->

        <footer style="padding: 20px 32px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: var(--text-sub); flex-wrap: wrap; gap: 8px;">
            <div>&copy; <?= date('Y') ?> IbeLaia.Dev &mdash; All rights reserved.</div>
            <div>Dashboard Version 2.0</div>
        </footer>
    </main>

    <script>
        // Logika Drawer Sidebar Mobile (Gambar 4)
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeDrawerBtn = document.getElementById('closeDrawerBtn');
        const adminSidebar = document.getElementById('adminSidebar');
        const drawerBackdrop = document.getElementById('drawerBackdrop');

        function openDrawer() {
            adminSidebar.classList.add('open');
            drawerBackdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            adminSidebar.classList.remove('open');
            drawerBackdrop.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openDrawer);
        if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
        if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeDrawer);
    </script>

</body>
</html>