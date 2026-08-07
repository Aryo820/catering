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

<main class="main-content">
    <div class="products-header">
        <h2>Program Sertifikasi & Pelatihan Kompetensi</h2>
        <p>Dapatkan pengakuan kompetensi profesional Anda melalui LSP COACHPRO INDONESIA</p>
    </div>
    
    <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="fas fa-certificate"></i>
            <h3>Belum Ada Program</h3>
            <p>Belum ada program sertifikasi yang tersedia saat ini.</p>
            <p>Silakan hubungi admin untuk informasi lebih lanjut.</p>
            <a href="https://wa.me/<?= $wa_default ?>" class="btn-wa" target="_blank">
                <i class="fab fa-whatsapp"></i> Hubungi Admin
            </a>
        </div>
    <?php else: ?>
        <div class="products-grid" id="productsGrid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-category="<?= htmlspecialchars($product['category_name']) ?>" data-product-id="<?= $product['id'] ?>">
                    <div class="product-img-wrapper">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img"
                             onerror="this.src='https://placehold.co/400x200/1f2462/white?text=<?= rawurlencode(substr($product['name'], 0, 20)) ?>'">
                        <span class="product-badge">Sertifikasi Resmi</span>
                    </div>
                    <div class="product-body">
                        <span class="product-category"><?= htmlspecialchars($product['category_name']) ?></span>
                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-desc"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                        <div class="product-price"><?= htmlspecialchars($product['price']) ?></div>
                        <div class="product-buttons">
                            <a href="detail/index.php?id=<?= $product['id'] ?>" class="btn-detail">
                                <i class="fas fa-info-circle"></i> Detail
                            </a>
                            
                            <!-- TOMBOL SIMPAN -->
                            <button class="btn-cart" 
                                    onclick="addToCart(<?= $product['id'] ?>, '<?= addslashes(htmlspecialchars($product['name'])) ?>', '<?= addslashes(htmlspecialchars($product['price'])) ?>')">
                                <i class="fas fa-shopping-cart"></i> Simpan
                            </button>
                            
                            <a href="https://wa.me/<?= $wa_default ?>?text=Halo%2C%20saya%20tertarik%20dengan%20program%20<?= rawurlencode($product['name']) ?>%20di%20LSP%20COACHPRO%20INDONESIA.%20Mohon%20informasi%20lebih%20lanjut%20mengenai%20pendaftaran%20dan%20biaya%20sertifikasi.%20Terima%20kasih." 
                               class="btn-wa" target="_blank">
                                <i class="fab fa-whatsapp"></i> Konsultasi
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
            showToast('Produk sudah ada di keranjang!', true);
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
                
                // Ubah status tombol menjadi "Tersimpan"
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-check"></i> Tersimpan';
                    button.style.background = '#eef2f0';
                    button.style.color = '#8ba0ae';
                    button.style.borderColor = '#eef2f0';
                }
            } else if (data.already_exists) {
                showToast(`${productName} sudah ada di keranjang!`, true);
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-check"></i> Tersimpan';
                }
            } else {
                showToast('Gagal menambahkan ke cart', true);
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-shopping-cart"></i> Simpan';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan sistem', true);
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-shopping-cart"></i> Simpan';
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
        toast.style.background = isError ? '#ef4444' : '#1f2462';
        toast.innerHTML = '<i class="fas ' + (isError ? 'fa-exclamation-circle' : 'fa-check-circle') + '" style="color: #e8b830;"></i> ' + message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast && toast.remove) toast.remove();
        }, 2500);
    }
</script>