<!-- Sidebar Admin -->
<div class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <h3><i class="fas fa-certificate" style="color: #e8b830;"></i> LSP <span style="color: #e8b830;">COACHPRO</span></h3>
        <p>Admin Panel</p>
    </div>

    <ul class="sidebar-menu">
        <?php
        // Gunakan variabel helper untuk menentukan menu aktif
        $active_page = basename($_SERVER['PHP_SELF']);
        $active_dir = basename(dirname($_SERVER['PHP_SELF']));
        ?>
        <li class="<?= ($active_page == 'index.php') ? 'active' : '' ?>">
            <a href="index.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?= ($active_page == 'products.php') ? 'active' : '' ?>">
            <a href="products.php">
                <i class="fas fa-boxes"></i>
                <span>Kelola Produk</span>
            </a>
        </li>
        <li class="<?= ($active_page == 'categories.php') ? 'active' : '' ?>">
            <a href="categories.php">
                <i class="fas fa-tags"></i>
                <span>Kelola Kategori</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-divider"></div>

    <ul class="sidebar-menu">
        <li class="<?= ($active_page == 'settings.php') ? 'active' : '' ?>">
            <a href="settings.php">
                <i class="fas fa-sliders-h"></i>
                <span>Pengaturan Website</span>
            </a>
        </li>
        <li class="<?= ($active_dir == 'invoice') ? 'active' : '' ?>">
            <a href="invoice/index.php">
                <i class="fas fa-file-invoice" style="color: #e8b830;"></i>
                <span>Invoice</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <p>&copy; <?= date('Y') ?> <strong>LSP COACHPRO INDONESIA</strong></p>
        <p>Version 1.0</p>
    </div>
</div>

<script>
    (function() {
        function initSidebar() {
            const mobileToggle = document.getElementById('mobileToggle');
            const adminSidebar = document.getElementById('adminSidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (!mobileToggle || !adminSidebar) return;

            function openSidebar() {
                adminSidebar.classList.add('open');
                if (sidebarOverlay) sidebarOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                adminSidebar.classList.remove('open');
                if (sidebarOverlay) sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            mobileToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (adminSidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            const sidebarLinks = adminSidebar.querySelectorAll('a');
            sidebarLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        setTimeout(closeSidebar, 200);
                    }
                });
            });

            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if (window.innerWidth > 768) {
                        closeSidebar();
                    }
                }, 250);
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSidebar);
        } else {
            initSidebar();
        }
    })();
</script>