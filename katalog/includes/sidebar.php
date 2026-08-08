<!-- Sidebar Kategori Menu Dapur Nusantara -->
<?php
/** @var mysqli $conn */
global $conn;
?>

<!-- STYLE KUSTOM TEMA WARM ARTISAN -->
<style>
    :root {
        --color-cream: #FFF8F0;
        --color-olive: #3D405B;
        --color-terracotta: #E07A5F;
        --color-terracotta-light: #fce7e1;
        --color-mute: #8a8f9c;
        --color-border: #f0f0f0;
    }

    .sidebar {
        background: #ffffff;
        width: 300px;
        max-height: calc(100vh - 80px);
        position: sticky;
        top: 80px;
        padding: 30px 20px;
        border-right: 1px solid var(--color-border);
        display: flex;
        flex-direction: column;
        font-family: 'Outfit', sans-serif;
        overflow-y: auto;
        z-index: 50;
    }

    .sidebar-header {
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--color-olive);
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--color-cream);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sidebar-header i {
        color: var(--color-terracotta);
    }

    .category-list {
        list-style: none;
        flex-grow: 1;
    }

    .category-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 12px;
        cursor: pointer;
        margin-bottom: 8px;
        transition: all 0.3s ease;
        font-size: 14px;
        color: var(--color-olive);
        font-weight: 500;
    }

    .category-item:hover {
        background: var(--color-cream);
        color: var(--color-terracotta);
        transform: translateX(5px);
    }

    .category-item.active {
        background: var(--color-terracotta-light);
        color: var(--color-terracotta);
        font-weight: 700;
    }

    .category-item i {
        width: 20px;
        text-align: center;
        font-size: 15px;
    }

    .count {
        margin-left: auto;
        font-size: 11px;
        background: #f0f0f0;
        color: var(--color-mute);
        padding: 3px 10px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .category-item.active .count {
        background: var(--color-terracotta);
        color: #fff;
    }

    /* Info Box */
    .sidebar-info-box {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--color-border);
    }

    .info-card {
        background: var(--color-cream);
        border-radius: 16px;
        padding: 18px;
        border-left: 4px solid var(--color-terracotta);
    }

    .info-card h4 {
        font-family: 'Playfair Display', serif;
        font-size: 16px;
        color: var(--color-olive);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-card h4 i {
        color: var(--color-terracotta);
    }

    .info-card p {
        font-size: 12px;
        color: var(--color-mute);
        line-height: 1.5;
    }

    .sidebar-footer {
        margin-top: 20px;
        text-align: center;
    }

    .sidebar-footer small {
        font-size: 12px;
        color: var(--color-mute);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .sidebar-footer a {
        color: var(--color-olive);
        font-weight: 700;
        text-decoration: none;
        transition: 0.3s;
    }

    .sidebar-footer a:hover {
        color: var(--color-terracotta);
    }

    /* Mobile Overlay */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(61, 64, 91, 0.5);
        z-index: 999;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .sidebar-overlay.active {
        display: block;
        opacity: 1;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .sidebar {
            position: fixed;
            left: -300px;
            top: 0;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease;
        }

        .sidebar.open {
            left: 0;
        }
    }
</style>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-utensils"></i> Kategori Menu
    </div>

    <ul class="category-list" id="categoryList">
        <?php
        // --- KEAMANAN: Query kategori dengan Prepared Statement ---
        $query_cat = "SELECT c.id, c.name, COUNT(p.id) as total 
                      FROM categories c 
                      LEFT JOIN products p ON c.id = p.category_id 
                      GROUP BY c.id ORDER BY c.name";

        $stmt_cat = mysqli_prepare($conn, $query_cat);
        mysqli_stmt_execute($stmt_cat);
        $result_cat = mysqli_stmt_get_result($stmt_cat);

        // --- KEAMANAN: Ambil total produk dengan Prepared Statement ---
        $stmt_total = mysqli_prepare($conn, "SELECT COUNT(*) as total FROM products");
        mysqli_stmt_execute($stmt_total);
        $total_result = mysqli_stmt_get_result($stmt_total);
        $total_products = 0;
        if ($total_result) {
            $total_row = mysqli_fetch_assoc($total_result);
            $total_products = $total_row['total'];
        }
        mysqli_stmt_close($stmt_total);
        ?>
        <li class="category-item active" data-category="Semua" onclick="filterCategory('Semua')">
            <i class="fas fa-th-large"></i>
            <span>Semua Menu</span>
            <span class="count"><?= $total_products ?></span>
        </li>
        <?php while ($cat = mysqli_fetch_assoc($result_cat)): ?>
            <li class="category-item" data-category="<?= htmlspecialchars($cat['name']) ?>" onclick="filterCategory('<?= htmlspecialchars($cat['name']) ?>')">
                <i class="fas fa-tag"></i>
                <span><?= htmlspecialchars($cat['name']) ?></span>
                <span class="count"><?= $cat['total'] ?></span>
            </li>
        <?php endwhile;
        mysqli_stmt_close($stmt_cat);
        ?>
    </ul>

    <!-- Info Dapur Nusantara -->
    <div class="sidebar-info-box">
        <div class="info-card">
            <h4><i class="fas fa-award"></i> Dapur Nusantara</h4>
            <p>Marketplace Dapur Nusantara terpercaya. Temukan Dapur Nusantaraer & menu terbaik untuk setiap acara spesial Anda.</p>
        </div>
    </div>

    <div class="sidebar-footer">
        <small>
            <i class="fas fa-headset" style="color: var(--color-terracotta);"></i>
            Butuh bantuan?
            <?php
            // --- KEAMANAN: Ambil nomor WA dengan Prepared Statement ---
            $wa_number = '6281383796300'; // Default
            if (isset($conn)) {
                $stmt_wa = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
                $key_wa = 'wa_number';
                mysqli_stmt_bind_param($stmt_wa, "s", $key_wa);
                mysqli_stmt_execute($stmt_wa);
                $result_wa = mysqli_stmt_get_result($stmt_wa);
                if ($result_wa && mysqli_num_rows($result_wa) > 0) {
                    $row_wa = mysqli_fetch_assoc($result_wa);
                    $wa_number = $row_wa['setting_value'];
                }
                mysqli_stmt_close($stmt_wa);
            }
            ?>
            <a href="https://wa.me/<?= $wa_number ?>" target="_blank">Hubungi Kami</a>
        </small>
    </div>
</aside>

<script>
    // Fungsi filter kategori (untuk mobile dan desktop)
    function filterCategory(categoryName) {
        // Update active state
        document.querySelectorAll('.category-item').forEach(item => {
            item.classList.remove('active');
            if (item.dataset.category === categoryName) {
                item.classList.add('active');
            }
        });

        // Filter produk
        const cards = document.querySelectorAll('.product-card');
        const headerTitle = document.querySelector('.products-header h2');

        if (categoryName === 'Semua') {
            cards.forEach(card => {
                card.style.display = 'flex'; // Gunakan flex karena card pakai flex column
            });
            if (headerTitle) {
                headerTitle.textContent = 'Semua Menu Dapur Nusantara';
            }
        } else {
            cards.forEach(card => {
                const cat = card.dataset.category;
                if (cat === categoryName) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
            if (headerTitle) {
                headerTitle.textContent = 'Kategori: ' + categoryName;
            }
        }

        // Tutup sidebar di mobile
        const sidebar = document.getElementById('sidebar');
        if (sidebar && window.innerWidth <= 992) {
            sidebar.classList.remove('open');
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) overlay.classList.remove('active');
        }
    }

    // --- LOGIKA JAVASCRIPT SAAT HALAMAN DIMUAT ---
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Set active category berdasarkan URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');
        if (category) {
            document.querySelectorAll('.category-item').forEach(item => {
                if (item.dataset.category === category) {
                    item.click();
                }
            });
        }

        // 2. Buat overlay untuk mobile sidebar (jika belum ada)
        if (!document.getElementById('sidebarOverlay')) {
            const overlay = document.createElement('div');
            overlay.id = 'sidebarOverlay';
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }

        // 3. Logika toggle mobile sidebar
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (mobileBtn && sidebar) {
            mobileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('open');
                if (overlay) {
                    overlay.classList.toggle('active');
                }
            });
        }

        if (overlay && sidebar) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }
    });
</script>