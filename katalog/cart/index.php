<?php
include '../config.php';
// PERBAIKAN 1: Ganti include ke file functions yang baru
include '../includes/cart_functions.php';

// Ambil semua item dari cart
$cart_items = getCartItems();
$total_price = getCartTotal();

// PERBAIKAN 2: Gunakan fungsi getWaNumber() yang sudah aman (Prepared Statement)
$wa_default = getWaNumber();

// Buat pesan untuk WhatsApp
$wa_message = "Halo, saya tertarik dengan menu catering berikut:%0A%0A";
$no = 1;
foreach ($cart_items as $item) {
    $wa_message .= $no . ". " . $item['name'] . " - " . $item['price'] . "%0A";
    $no++;
}
$wa_message .= "%0ATotal: Rp " . number_format($total_price, 0, ',', '.');
$wa_message .= "%0A%0ATerima kasih.";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Keranjang Pesanan - Dapur Nusantara</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        :root {
            --color-cream: #FFF8F0;
            --color-olive: #3D405B;
            --color-terracotta: #E07A5F;
            --color-terracotta-light: #fce7e1;
            --color-mute: #8a8f9c;
            --color-border: #f0f0f0;
            --color-wa: #25D366;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--color-cream);
            padding: 2rem;
            min-height: 100vh;
            color: var(--color-olive);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(61, 64, 91, 0.08);
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .cart-header {
            padding: 1.5rem 2rem;
            background: #ffffff;
            border-bottom: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .cart-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--color-olive);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .cart-header h1 i {
            color: var(--color-terracotta);
        }

        .btn-back {
            background: var(--color-cream);
            color: var(--color-olive);
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s;
            border: 1px solid var(--color-border);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-back:hover {
            background: var(--color-terracotta-light);
            border-color: var(--color-terracotta);
            color: var(--color-terracotta);
            transform: translateY(-2px);
        }

        /* Empty State */
        .cart-empty {
            text-align: center;
            padding: 5rem 2rem;
        }

        .cart-empty i {
            font-size: 4rem;
            color: var(--color-terracotta);
            margin-bottom: 1.5rem;
            opacity: 0.8;
        }

        .cart-empty h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--color-olive);
        }

        .cart-empty p {
            color: var(--color-mute);
            margin-bottom: 2rem;
        }

        .btn-shop {
            background: var(--color-terracotta);
            color: #ffffff;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(224, 122, 95, 0.2);
        }

        .btn-shop:hover {
            background: #c8674d;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(224, 122, 95, 0.3);
        }

        /* Cart Items */
        .cart-items {
            padding: 1rem 2rem;
        }

        .cart-items-header {
            display: grid;
            grid-template-columns: 2.5fr 1fr 0.8fr 1fr 0.5fr;
            padding: 0.8rem 0;
            border-bottom: 2px solid var(--color-border);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--color-mute);
        }

        .cart-item {
            display: grid;
            grid-template-columns: 2.5fr 1fr 0.8fr 1fr 0.5fr;
            align-items: center;
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--color-border);
            gap: 1rem;
        }

        .cart-item-info {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .cart-item-name {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--color-olive);
            font-size: 1.1rem;
        }

        .cart-item-category {
            font-size: 0.7rem;
            color: var(--color-terracotta);
            background: var(--color-terracotta-light);
            display: inline-block;
            padding: 3px 10px;
            border-radius: 50px;
            width: fit-content;
            font-weight: 600;
        }

        .cart-item-price {
            font-weight: 500;
            color: var(--color-mute);
            font-size: 0.95rem;
        }

        .cart-item-quantity {
            text-align: center;
        }

        .quantity-badge {
            background: var(--color-cream);
            color: var(--color-olive);
            padding: 0.3rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .quantity-badge i {
            font-size: 0.7rem;
            color: var(--color-terracotta);
        }

        .cart-item-total {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--color-olive);
            font-size: 1.1rem;
        }

        .cart-item-remove {
            background: none;
            border: none;
            color: #e63946;
            cursor: pointer;
            font-size: 1.1rem;
            transition: all 0.2s;
            text-align: center;
            width: 36px;
            height: 36px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-item-remove:hover {
            background: #ffe6e6;
            color: #d62828;
            transform: scale(1.05);
        }

        /* Summary */
        .cart-summary {
            background: var(--color-cream);
            padding: 1.5rem 2rem;
            border-top: 1px solid var(--color-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .cart-total {
            font-size: 1rem;
            color: var(--color-mute);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        .cart-total span {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--color-terracotta);
            font-size: 1.8rem;
            margin-left: 0.5rem;
            text-transform: none;
        }

        .btn-checkout {
            background: var(--color-wa);
            color: white;
            padding: 0.9rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.2);
        }

        .btn-checkout:hover {
            background: #1da85e;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.3);
        }

        .btn-clear {
            background: transparent;
            border: 1px solid #e63946;
            color: #e63946;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: 'Outfit', sans-serif;
            font-size: 0.85rem;
        }

        .btn-clear:hover {
            background: #e63946;
            color: white;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 0;
                background: #fff;
            }

            .container {
                border-radius: 0;
            }

            .cart-header {
                padding: 1rem 1.2rem;
            }

            .cart-header h1 {
                font-size: 1.4rem;
            }

            .cart-items {
                padding: 0 1.2rem;
            }

            .cart-items-header {
                display: none;
            }

            .cart-item {
                grid-template-columns: 1fr;
                gap: 0.5rem;
                padding: 1.5rem 0;
                position: relative;
                padding-right: 40px;
            }

            .cart-item-info {
                gap: 0.4rem;
                margin-bottom: 0.5rem;
            }

            .cart-item-price {
                font-size: 0.9rem;
            }

            .cart-item-total {
                font-size: 1rem;
            }

            .cart-item-remove {
                position: absolute;
                top: 1.5rem;
                right: 0;
            }

            .cart-summary {
                flex-direction: column;
                align-items: stretch;
                padding: 1.2rem;
                text-align: center;
            }

            .cart-total span {
                display: block;
                margin-top: 0.5rem;
                margin-left: 0;
                font-size: 1.5rem;
            }

            .btn-checkout,
            .btn-clear {
                justify-content: center;
            }
        }

        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--color-olive);
            color: white;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            z-index: 1000;
            box-shadow: 0 8px 25px rgba(61, 64, 91, 0.3);
            animation: slideIn 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast-notification i {
            color: var(--color-terracotta);
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
</head>

<body>
    <div class="container">
        <div class="cart-header">
            <h1><i class="fas fa-shopping-bag"></i> Keranjang Pesanan</h1>
            <a href="../index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali Belanja</a>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="cart-empty">
                <i class="fas fa-box-open"></i>
                <h3>Keranjang Masih Kosong</h3>
                <p>Belum ada menu catering yang kamu simpan. Yuk, cari menu favoritmu!</p>
                <a href="../index.php" class="btn-shop"><i class="fas fa-utensils"></i> Jelajahi Menu</a>
            </div>
        <?php else: ?>
            <div class="cart-items-header">
                <span>Detail Menu</span>
                <span>Harga</span>
                <span>Porsi</span>
                <span>Subtotal</span>
                <span></span>
            </div>

            <div class="cart-items" id="cartItemsContainer">
                <?php foreach ($cart_items as $id => $item): ?>
                    <div class="cart-item" data-id="<?= $id ?>">
                        <div class="cart-item-info">
                            <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <span class="cart-item-category"><i class="fas fa-tag"></i> Menu Catering</span>
                        </div>
                        <div class="cart-item-price" id="price-<?= $id ?>">
                            Rp <?= number_format(htmlspecialchars($item['price']), 0, ',', '.') ?>
                        </div>
                        <div class="cart-item-quantity">
                            <span class="quantity-badge">
                                <i class="fas fa-check-circle"></i> 1 Porsi
                            </span>
                        </div>
                        <div class="cart-item-total" id="total-<?= $id ?>">
                            Rp <?= number_format(htmlspecialchars($item['price']), 0, ',', '.') ?>
                        </div>
                        <button class="cart-item-remove" onclick="removeFromCart(<?= $id ?>)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <button class="btn-clear" onclick="clearCart()">
                    <i class="fas fa-trash-alt"></i> Kosongkan
                </button>
                <div class="cart-total">
                    Total Pesanan: <span id="cartTotal">Rp <?= number_format($total_price, 0, ',', '.') ?></span>
                </div>
                <a href="https://wa.me/<?= $wa_default ?>?text=<?= rawurlencode($wa_message) ?>" class="btn-checkout" target="_blank">
                    <i class="fab fa-whatsapp"></i> Checkout via WhatsApp
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function showToast(message, isError = false) {
            const existingToast = document.querySelector('.toast-notification');
            if (existingToast) existingToast.remove();

            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            // Ubah warna toast sesuai tema
            toast.style.background = isError ? '#E07A5F' : '#3D405B';
            toast.innerHTML = '<i class="fas ' + (isError ? 'fa-exclamation-circle' : 'fa-check-circle') + '" style="color: #FFF8F0;"></i> ' + message;
            document.body.appendChild(toast);

            setTimeout(() => {
                if (toast && toast.remove) toast.remove();
            }, 2500);
        }

        function updateCartDisplay() {
            // PERBAIKAN 3: Ubah URL AJAX ke file ajax/cart_ajax.php yang baru
            fetch('../ajax/cart_ajax.php', {
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
                        if (window.opener && window.opener.updateCartBadge) {
                            window.opener.updateCartBadge();
                        }
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function removeFromCart(productId) {
            if (confirm('Hapus menu ini dari keranjang?')) {
                // PERBAIKAN 3: Ubah URL AJAX ke file ajax/cart_ajax.php yang baru
                fetch('../ajax/cart_ajax.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `action=remove&product_id=${productId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Menu berhasil dihapus dari keranjang');
                            updateCartDisplay();
                        } else {
                            showToast('Gagal menghapus menu', true);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan', true);
                    });
            }
        }

        function clearCart() {
            if (confirm('Kosongkan semua menu di keranjang?')) {
                // PERBAIKAN 3: Ubah URL AJAX ke file ajax/cart_ajax.php yang baru
                fetch('../ajax/cart_ajax.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: 'action=clear'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast('Keranjang berhasil dikosongkan');
                            updateCartDisplay();
                        } else {
                            showToast('Gagal mengosongkan keranjang', true);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan', true);
                    });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (window.opener && window.opener.updateCartBadge) {
                window.opener.updateCartBadge();
            }
        });
    </script>
</body>

</html>