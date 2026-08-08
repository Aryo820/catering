<?php
// ============================================================
// FOOTER - LSP COACHPRO INDONESIA (DENGAN SMART CTA DATABASE)
// ============================================================

/** @var mysqli $conn */
global $conn;

// --- DATA DEFAULTS (FALLBACK JIKA DATABASE KOSONG) ---
$footer_copyright   = 'LSP COACHPRO INDONESIA – Lembaga Sertifikasi Profesi Terakreditasi';
$footer_credit_left = '<i class="fas fa-mobile-alt"></i> Mobile Friendly';
$footer_credit_right = '<i class="fas fa-tachometer-alt"></i> Kinerja Cepat';
$wa_default_promo   = '6281383796300';

// --- DATA DEFAULT UNTUK CTA (SMART PROMO) ---
$cta_defaults = [
    'cta_title'       => '💡 Konsultasi Gratis!',
    'cta_message'     => 'Butuh bantuan memilih program sertifikasi yang tepat untuk karir Anda? Tim kami siap membantu 24/7!',
    'cta_emoji'       => '🎁',
    'cta_btn_text'    => 'Hubungi Sekarang →',
    'cta_wa_text'     => 'Halo, saya butuh konsultasi mengenai program sertifikasi LSP COACHPRO.',
    'cta_footer_text' => '⭐ 500+ klien puas | ⚡ Respon cepat | 🎯 Garansi 30 hari'
];

$cta_data = $cta_defaults;

// --- AMBIL DATA DARI DATABASE (AMAN DENGAN PREPARED STATEMENT) ---
if (isset($conn)) {
    // 1. Ambil data Footer & WA
    $stmt_footer = mysqli_prepare($conn, "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('footer_copyright', 'footer_credit_left', 'footer_credit_right', 'wa_number')");
    mysqli_stmt_execute($stmt_footer);
    $result_footer = mysqli_stmt_get_result($stmt_footer);
    
    while ($row = mysqli_fetch_assoc($result_footer)) {
        switch ($row['setting_key']) {
            case 'footer_copyright':  $footer_copyright = $row['setting_value']; break;
            case 'footer_credit_left': $footer_credit_left = $row['setting_value']; break;
            case 'footer_credit_right': $footer_credit_right = $row['setting_value']; break;
            case 'wa_number': $wa_default_promo = $row['setting_value']; break;
        }
    }
    mysqli_stmt_close($stmt_footer);

    // 2. Ambil data CTA (Smart Promo)
    $keys_cta = array_keys($cta_defaults);
    $placeholders = implode(',', array_fill(0, count($keys_cta), '?'));
    $types = str_repeat('s', count($keys_cta));

    $stmt_cta = mysqli_prepare($conn, "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ($placeholders)");
    mysqli_stmt_bind_param($stmt_cta, $types, ...$keys_cta);
    mysqli_stmt_execute($stmt_cta);
    $result_cta = mysqli_stmt_get_result($stmt_cta);

    while ($row = mysqli_fetch_assoc($result_cta)) {
        $cta_data[$row['setting_key']] = $row['setting_value'];
    }
    mysqli_stmt_close($stmt_cta);
}
?>
    </div> <!-- .layout -->
    <footer class="main-footer">
        <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($footer_copyright) ?></p>
        <p>
            <?= $footer_credit_left ?> 
            <?php if (!empty($footer_credit_left) && !empty($footer_credit_right)): ?>
                |
            <?php endif; ?>
            <?= $footer_credit_right ?>
        </p>
    </footer>
</div> <!-- .app -->

<script>
    // =============================================
    // 1. TOGGLE SIDEBAR MOBILE
    // =============================================
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('active');
        });
        
        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            });
        }
    }

    // =============================================
    // 2. SEARCH TOGGLE & AJAX
    // =============================================
    const searchIcon = document.getElementById('searchIcon');
    const searchInput = document.getElementById('searchInput');
    const searchKeyword = document.getElementById('searchKeyword');
    const searchResults = document.getElementById('searchResults');

    if (searchIcon && searchInput) {
        searchIcon.addEventListener('click', function() {
            searchInput.classList.toggle('active');
            if (searchInput.classList.contains('active')) {
                searchKeyword.focus();
            }
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && e.target !== searchIcon) {
                searchInput.classList.remove('active');
            }
        });
    }

    if (searchKeyword && searchResults) {
        let debounceTimer;
        searchKeyword.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const keyword = this.value.trim();
            
            if (keyword.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            // Track perilaku mencari untuk promo
            if (keyword.length >= 3) {
                trackUserBehavior('search', keyword);
            }

            debounceTimer = setTimeout(() => {
                fetch('<?= SITE_URL ?>search.php?q=' + encodeURIComponent(keyword))
                .then(response => response.json())
                .then(data => {
                    if (data.results.length > 0) {
                        let html = '';
                        data.results.forEach(item => {
                            html += `
                                <a href="<?= SITE_URL ?>detail/index.php?id=${item.id}" class="search-result-item" onclick="trackUserBehavior('view', '${item.name.replace(/'/g, "\\'")}')">
                                    <img src="${item.image_url}" alt="${item.name}" class="search-result-img">
                                    <div class="search-result-info">
                                        <div class="search-result-name">${escapeHtml(item.name)}</div>
                                        <div class="search-result-price">${item.price}</div>
                                    </div>
                                </a>
                            `;
                        });
                        searchResults.innerHTML = html;
                    } else {
                        searchResults.innerHTML = '<div class="search-no-result">Produk tidak ditemukan.</div>';
                    }
                })
                .catch(error => console.error('Error:', error));
            }, 300);
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // =============================================
    // 3. CART BADGE UPDATE & BUTTON STATUS
    // =============================================
    function updateCartBadge() {
        const badge = document.getElementById('cartCount');
        if (!badge) return;

        fetch('<?= SITE_URL ?>ajax/cart_ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'action=get'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.textContent = '0';
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error updating cart:', error));
    }

    function updateButtonStatus() {
        fetch('<?= SITE_URL ?>ajax/cart_ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'action=get'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.items) {
                const cartItemIds = Object.keys(data.items);
                
                document.querySelectorAll('.btn-cart').forEach(button => {
                    const onclickAttr = button.getAttribute('onclick');
                    let productId = null;
                    
                    if (onclickAttr) {
                        const match = onclickAttr.match(/addToCart\((\d+),/);
                        if (match) productId = match[1];
                    }
                    
                    if (productId && cartItemIds.includes(productId)) {
                        button.innerHTML = '<i class="fas fa-check"></i> Tersimpan';
                        button.disabled = true;
                        button.style.background = '#eef2f0';
                        button.style.color = '#8ba0ae';
                        button.style.borderColor = '#eef2f0';
                        button.style.cursor = 'not-allowed';
                    } else {
                        button.innerHTML = '<i class="fas fa-shopping-cart"></i> Simpan';
                        button.disabled = false;
                        button.style.background = '#f8faf8';
                        button.style.color = '#1a2a3a';
                        button.style.borderColor = '#eef2f0';
                        button.style.cursor = 'pointer';
                    }
                });
            }
        })
        .catch(error => console.error('Error update button status:', error));
    }

    // =============================================
    // 4. ADD TO CART (Fungsi Global)
    // =============================================
    window.addToCart = function(productId, productName, productPrice) {
        const button = event ? event.currentTarget : document.querySelector(`.btn-cart[onclick*="addToCart(${productId},"]`);
        if (button && button.disabled) {
            showToast('Produk sudah ada di keranjang!', true);
            return;
        }
        
        if (button) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        }
        
        // Track perilaku menyimpan produk untuk promo
        trackUserBehavior('save', productName);
        
        fetch('<?= SITE_URL ?>ajax/cart_ajax.php', {
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
                updateCartBadge();  // <--- INI YANG MEMBUAT BADGE BERUBAH
                showToast(`${productName} ditambahkan ke keranjang!`);
                updateButtonStatus();
                // Trigger promo setelah menyimpan
                if (!promoTriggered) {
                    promoTriggered = true;
                    setTimeout(() => showSmartPromo(), 1000);
                }
            } else if (data.already_exists) {
                showToast(`${productName} sudah ada di keranjang!`, true);
                updateButtonStatus();
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
            showToast('Terjadi kesalahan', true);
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-shopping-cart"></i> Simpan';
            }
        });
    };

    // =============================================
    // 5. TOAST NOTIFICATION
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

    // =============================================
    // 6. SMART PROMO FLOATING CARD (DENGAN DETEKSI HISTORY USER)
    // =============================================
    
    // Data untuk tracking perilaku user (history)
    let userBehavior = {
        lastViewedProduct: null,
        searchedKeywords: [],
        savedProducts: [],
        lastActivity: null
    };
    
    if (sessionStorage.getItem('userBehavior')) {
        try {
            userBehavior = JSON.parse(sessionStorage.getItem('userBehavior'));
        } catch(e) {}
    }
    
    // Fungsi untuk menyimpan perilaku user ke sessionStorage
    window.trackUserBehavior = function(type, data) {
        switch(type) {
            case 'view':
                userBehavior.lastViewedProduct = data;
                userBehavior.lastActivity = new Date().toISOString();
                break;
            case 'search':
                if (!userBehavior.searchedKeywords.includes(data)) {
                    userBehavior.searchedKeywords.unshift(data);
                    userBehavior.searchedKeywords = userBehavior.searchedKeywords.slice(0, 5);
                }
                userBehavior.lastActivity = new Date().toISOString();
                break;
            case 'save':
                if (!userBehavior.savedProducts.includes(data)) {
                    userBehavior.savedProducts.push(data);
                }
                userBehavior.lastActivity = new Date().toISOString();
                break;
        }
        sessionStorage.setItem('userBehavior', JSON.stringify(userBehavior));
        triggerPromoAfterBehavior();
    };
    
    // Track ketika user melihat produk (hover)
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const productName = this.querySelector('.product-title')?.textContent;
            if (productName && !userBehavior.lastViewedProduct) {
                trackUserBehavior('view', productName);
            }
        });
    });
    
    // Fungsi untuk menentukan pesan promosi berdasarkan history user
    function getSmartPromoMessage() {
        if (userBehavior.savedProducts.length > 0) {
            const savedProduct = userBehavior.savedProducts[userBehavior.savedProducts.length - 1];
            return {
                title: "🎯 Produk Sudah Disimpan!",
                message: `"${savedProduct.substring(0, 35)}" siap untuk Anda. Hubungi kami sekarang untuk konsultasi GRATIS!`,
                emoji: "💎",
                cta: "Konsultasi Sekarang →",
                waMessage: `Halo, saya sudah menyimpan produk "${savedProduct}" dan tertarik untuk membeli. Apakah bisa konsultasi lebih lanjut?`
            };
        }
        
        if (userBehavior.lastViewedProduct) {
            const viewedProduct = userBehavior.lastViewedProduct;
            return {
                title: "👀 Masih Ragu?",
                message: `Anda tertarik dengan "${viewedProduct.substring(0, 35)}"? Dapatkan penawaran spesial untuk pembelian hari ini!`,
                emoji: "⚡",
                cta: "Ambil Penawaran →",
                waMessage: `Halo, saya tertarik dengan "${viewedProduct}". Apakah ada promo spesial saat ini?`
            };
        }
        
        if (userBehavior.searchedKeywords.length >= 2) {
            const lastSearch = userBehavior.searchedKeywords[0];
            return {
                title: "🔍 Lagi Cari-cari?",
                message: `Anda mencari "${lastSearch}". Kami punya solusi terbaik untuk kebutuhan Anda. Chat sekarang untuk rekomendasi!`,
                emoji: "🚀",
                cta: "Tanya Rekomendasi →",
                waMessage: `Halo, saya sedang mencari "${lastSearch}". Bantu rekomendasikan produk yang sesuai dong.`
            };
        }
        
        // Default promosi (dari database)
        return {
            title: "<?= htmlspecialchars($cta_data['cta_title']) ?>",
            message: "<?= htmlspecialchars($cta_data['cta_message']) ?>",
            emoji: "<?= htmlspecialchars($cta_data['cta_emoji']) ?>",
            cta: "<?= htmlspecialchars($cta_data['cta_btn_text']) ?>",
            waMessage: "<?= htmlspecialchars($cta_data['cta_wa_text']) ?>"
        };
    }
    
    let promoCard = null;
    let promoTimeout = null;
    let promoTriggered = false;
    let behaviorTimeout = null;
    
    function showSmartPromo() {
        if (promoCard) return;
        
        const promo = getSmartPromoMessage();
        const waNumber = '<?= $wa_default_promo ?>';
        const footerText = '<?= htmlspecialchars($cta_data['cta_footer_text']) ?>';
        
        promoCard = document.createElement('div');
        promoCard.className = 'smart-promo-card';
        promoCard.innerHTML = `
            <div class="smart-promo-header">
                <span class="smart-promo-emoji">${promo.emoji}</span>
                <span class="smart-promo-title">${promo.title}</span>
                <button class="smart-promo-close">&times;</button>
            </div>
            <div class="smart-promo-body">
                <p>${promo.message}</p>
                <a href="https://wa.me/${waNumber}?text=${encodeURIComponent(promo.waMessage)}" 
                   class="smart-promo-btn" target="_blank">
                    ${promo.cta} <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="smart-promo-footer">
                ${footerText}
            </div>
        `;
        
        document.body.appendChild(promoCard);
        
        const closeBtn = promoCard.querySelector('.smart-promo-close');
        closeBtn.addEventListener('click', () => {
            promoCard.remove();
            promoCard = null;
            localStorage.setItem('promoLastClosed', Date.now());
        });
        
        promoTimeout = setTimeout(() => {
            if (promoCard) {
                promoCard.style.animation = 'slideUp 0.3s reverse';
                setTimeout(() => {
                    if (promoCard) {
                        promoCard.remove();
                        promoCard = null;
                    }
                }, 300);
            }
        }, 20000);
    }
    
    function shouldShowPromo() {
        const lastClosed = localStorage.getItem('promoLastClosed');
        if (lastClosed && (Date.now() - lastClosed) < 30 * 60 * 1000) {
            return false;
        }
        return true;
    }
    
    function triggerPromoAfterBehavior() {
        if (promoTriggered) return;
        if (behaviorTimeout) clearTimeout(behaviorTimeout);
        
        behaviorTimeout = setTimeout(() => {
            if (shouldShowPromo() && !promoTriggered) {
                promoTriggered = true;
                showSmartPromo();
            }
        }, 3000);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        updateCartBadge();  // Update badge saat halaman dimuat
        updateButtonStatus();
        
        setTimeout(() => {
            if (shouldShowPromo() && !promoTriggered && 
                (userBehavior.lastViewedProduct || userBehavior.searchedKeywords.length > 0)) {
                promoTriggered = true;
                showSmartPromo();
            }
        }, 5000);
        
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (!promoTriggered && window.scrollY > 400) {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    if (!promoTriggered && shouldShowPromo()) {
                        promoTriggered = true;
                        showSmartPromo();
                    }
                }, 1500);
            }
        });
    });

    // =============================================
    // 7. CSS TAMBAHAN (SMART PROMO & TOAST)
    // =============================================
    if (!document.querySelector('#footer-extras-style')) {
        const style = document.createElement('style');
        style.id = 'footer-extras-style';
        style.textContent = `
            .smart-promo-card {
                position: fixed;
                bottom: 30px;
                left: 30px;
                width: 360px;
                max-width: calc(100vw - 60px);
                background: white;
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.15), 0 0 0 1px rgba(31, 36, 98, 0.2);
                z-index: 1001;
                animation: slideUp 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
                overflow: hidden;
            }
            @keyframes slideUp {
                from { opacity: 0; transform: translateY(50px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .smart-promo-header {
                background: linear-gradient(135deg, #1f2462, #2a2f7a);
                color: white;
                padding: 12px 16px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .smart-promo-emoji { font-size: 1.3rem; }
            .smart-promo-title { flex: 1; font-weight: 700; font-size: 0.9rem; }
            .smart-promo-close {
                background: rgba(255,255,255,0.2);
                border: none;
                color: white;
                width: 28px;
                height: 28px;
                border-radius: 30px;
                cursor: pointer;
                font-size: 1.2rem;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
            }
            .smart-promo-close:hover { background: rgba(255,255,255,0.3); transform: scale(1.05); }
            .smart-promo-body { padding: 16px; }
            .smart-promo-body p { font-size: 0.85rem; color: #1a2a3a; line-height: 1.5; margin-bottom: 16px; }
            .smart-promo-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                background: linear-gradient(135deg, #1f2462, #2a2f7a);
                color: white;
                padding: 10px 20px;
                border-radius: 40px;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.85rem;
                transition: all 0.2s;
                width: 100%;
            }
            .smart-promo-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(31, 36, 98, 0.3);
            }
            .smart-promo-footer {
                background: #f8faf8;
                padding: 8px 16px;
                font-size: 0.7rem;
                color: #8ba0ae;
                text-align: center;
                border-top: 1px solid #eef2f0;
            }
            @media (max-width: 640px) {
                .smart-promo-card {
                    left: 16px;
                    right: 16px;
                    width: auto;
                    bottom: 16px;
                }
            }
            
            .toast-notification {
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: #1f2462;
                color: white;
                padding: 12px 24px;
                border-radius: 50px;
                font-size: 0.85rem;
                font-weight: 500;
                z-index: 1000;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                animation: slideIn 0.3s ease;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
</script>
</body>
</html>