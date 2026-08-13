<?php
include 'config.php';

/** @var mysqli $conn */
global $conn;

// --- GUNAKAN FUNGSI HELPER DARI CONFIG.PHP ---
$wa_default = get_wa_number(); // Ambil nomor WA
$products   = get_all_products(); // Ambil semua produk
$categories = get_categories(); // Ambil kategori

// --- AMBIL JUMLAH CART SAAT INI (UNTUK BADGE) ---
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    $cart_count = count($_SESSION['cart']);
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<!-- STYLE KUSTOM TEMA WARM ARTISAN -->
<style>
    :root {
        --color-cream: #FFF8F0;
        --color-olive: #3D405B;
        --color-terracotta: #E07A5F;
        --color-terracotta-dark: #c8674d;
        --color-mute: #8a8f9c;
        --color-border: #f0f0f0;
    }

    .main-content {
        background-color: var(--color-cream);
        padding: 40px;
        font-family: 'Outfit', sans-serif;
    }

    /* Header Katalog */
    .products-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .products-header h2 {
        font-family: 'Playfair Display', serif;
        font-size: 36px;
        color: var(--color-olive);
        margin-bottom: 8px;
    }

    .products-header p {
        color: var(--color-mute);
        font-size: 16px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        background: #fff;
        padding: 60px 20px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        max-width: 500px;
        margin: 0 auto;
    }

    .empty-state i {
        font-size: 50px;
        color: var(--color-terracotta);
        margin-bottom: 20px;
        opacity: 0.8;
    }

    .empty-state h3 {
        font-family: 'Playfair Display', serif;
        font-size: 24px;
        color: var(--color-olive);
        margin-bottom: 10px;
    }

    .empty-state p {
        color: var(--color-mute);
        margin-bottom: 20px;
    }

    /* Grid Produk */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 30px;
    }

    /* Card Produk */
    .product-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        border: 1px solid var(--color-border);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(224, 122, 95, 0.15);
        border-color: rgba(224, 122, 95, 0.3);
    }

    .product-img-wrapper {
        position: relative;
        height: 220px;
        overflow: hidden;
    }

    .product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-img {
        transform: scale(1.05);
    }

    .product-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--color-terracotta);
        color: white;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(224, 122, 95, 0.4);
    }

    .product-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .product-category {
        font-size: 12px;
        color: var(--color-terracotta);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .product-title {
        font-family: 'Playfair Display', serif;
        font-size: 20px;
        color: var(--color-olive);
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .product-desc {
        font-size: 14px;
        color: var(--color-mute);
        margin-bottom: 16px;
        line-height: 1.5;
        flex-grow: 1;
    }

    .product-price {
        font-family: 'Playfair Display', serif;
        font-size: 24px;
        font-weight: 700;
        color: var(--color-olive);
        margin-bottom: 20px;
    }

    /* Tombol */
    .product-buttons {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn-detail,
    .btn-cart,
    .btn-wa {
        padding: 12px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        border: none;
    }

    .btn-detail {
        background: transparent;
        border: 1.5px solid #e2e8f0;
        color: var(--color-olive);
    }

    .btn-detail:hover {
        border-color: var(--color-olive);
        background: var(--color-olive);
        color: #fff;
    }

    .btn-cart {
        background: var(--color-terracotta);
        color: #fff;
    }

    .btn-cart:hover {
        background: var(--color-terracotta-dark);
    }

    .btn-wa {
        background: #25D366;
        color: white;
    }

    .btn-wa:hover {
        background: #1da851;
    }

    .btn-cart:disabled {
        background: #eef2f0;
        color: #8ba0ae;
        cursor: not-allowed;
        border: none;
    }

    /* Toast Notification */
    .toast-notification {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: var(--color-olive);
        color: #fff;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
</style>

<main class="main-content">
    <div class="products-header">
        <h2>Pilihan Menu Dapur Nusantara Premium</h2>
        <p>Temukan paket Dapur Nusantara favorit untuk acara spesial Anda</p>
    </div>

    <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="fas fa-utensils"></i>
            <h3>Menu Belum Tersedia</h3>
            <p>Saat ini belum ada menu Dapur Nusantara yang tersedia di katalog.</p>
            <p>Silakan hubungi Dapur Nusantaraer kami untuk informasi lebih lanjut.</p>
            <a href="https://wa.me/<?= $wa_default ?>" class="btn-wa" target="_blank" style="display: inline-flex; padding: 12px 24px;">
                <i class="fab fa-whatsapp"></i> Hubungi Admin
            </a>
        </div>
    <?php else: ?>
        <div class="products-grid" id="productsGrid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-category="<?= htmlspecialchars($product['category_name']) ?>" data-product-id="<?= $product['id'] ?>">
                    <div class="product-img-wrapper">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img"
                            onerror="this.src='https://placehold.co/400x200/3D405B/white?text=<?= rawurlencode(substr($product['name'], 0, 20)) ?>'">
                        <span class="product-badge">Menu Favorit</span>
                    </div>
                    <div class="product-body">
                        <span class="product-category"><?= htmlspecialchars($product['category_name']) ?></span>
                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-desc"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                        <div class="product-price">Rp <?= number_format(htmlspecialchars($product['price']), 0, ',', '.') ?></div>
                        <div class="product-buttons">
                            <a href="detail/index.php?id=<?= $product['id'] ?>" class="btn-detail">
                                <i class="fas fa-info-circle"></i> Lihat Detail
                            </a>

                            <!-- TOMBOL SIMPAN / PESAN -->
                            <button class="btn-cart"
                                onclick='addToCart(<?= (int)$product['id'] ?>, <?= json_encode($product['name']) ?>, <?= json_encode($product['price']) ?>)'>
                                <i class="fas fa-shopping-cart"></i> Pesan Sekarang
                            </button>

                            <a href="https://wa.me/<?= $wa_default ?>?text=Halo%2C%20saya%20tertarik%20dengan%20menu%20<?= rawurlencode($product['name']) ?>%20di%20Dapur Nusantara.%20Mohon%20informasi%20lebih%20lanjut%20mengenai%20pemesanan%20dan%20stok%20porsi.%20Terima%20kasih."
                                class="btn-wa" target="_blank">
                                <i class="fab fa-whatsapp"></i> Tanya Dapur Nusantara
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>

<!-- ============================================================ -->
<!-- JAVASCRIPT CART (DITEMPATKAN DI SINI AGAR PASTI TERBACA)     -->
<!-- ============================================================ -->
<script>
    // =============================================
    // 1. FUNGSI ADD TO CART (Menggunakan AJAX)
    // =============================================
    window.addToCart = function(productId, productName, productPrice) {
        // Jika tombol disable, jangan lakukan apa-apa
        const button = event ? event.currentTarget : document.querySelector(`.btn-cart[onclick*="addToCart(${productId},"]`);
        if (button && button.disabled) {
            showToast('Menu sudah ada di keranjang!', true);
            return;
        }

        // Ubah status tombol menjadi loading
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        }

        // Kirim request AJAX ke file cart_functions (bukan cart.php)
        fetch('ajax/cart_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `action=add&product_id=${productId}&product_name=${encodeURIComponent(productName)}&product_price=${encodeURIComponent(productPrice)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge di header
                    const badge = document.getElementById('cartCount');
                    if (badge) {
                        badge.textContent = data.count;
                        badge.style.display = 'inline-block';
                    }
                    showToast(`${productName} ditambahkan ke keranjang!`);

                    // Ubah status tombol menjadi "Dipesan"
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<i class="fas fa-check"></i> Ditambahkan';
                        button.style.background = '#eef2f0';
                        button.style.color = '#8ba0ae';
                        button.style.borderColor = '#eef2f0';
                    }
                } else if (data.already_exists) {
                    showToast(`${productName} sudah ada di keranjang!`, true);
                    if (button) {
                        button.disabled = true;
                        button.innerHTML = '<i class="fas fa-check"></i> Ditambahkan';
                    }
                } else {
                    showToast('Gagal menambahkan ke cart', true);
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-shopping-cart"></i> Pesan Sekarang';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Terjadi kesalahan sistem', true);
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-shopping-cart"></i> Pesan Sekarang';
                }
            });
    };

    // =============================================
    // 2. FUNGSI TOAST NOTIFICATION
    // =============================================
    function showToast(message, isError = false) {
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        // Ubah warna toast sesuai tema (Terracotta untuk error, Olive untuk sukses)
        toast.style.background = isError ? '#E07A5F' : '#3D405B';
        toast.innerHTML = '<i class="fas ' + (isError ? 'fa-exclamation-circle' : 'fa-check-circle') + '" style="color: #FFF8F0;"></i> ' + message;
        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast && toast.remove) toast.remove();
        }, 2500);
    }
</script>