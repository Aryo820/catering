<?php
include '../config.php';

/** @var mysqli $conn */
global $conn;

// Ambil ID produk dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- KEAMANAN: Ambil data produk dengan Prepared Statement ---
$stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name 
                               FROM products p 
                               JOIN categories c ON p.category_id = c.id 
                               WHERE p.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Jika produk tidak ditemukan
if (!$product) {
    header('Location: ../index.php');
    exit;
}

// --- KEAMANAN: Ambil nomor WhatsApp default dari database ---
$wa_default = '6281383796300';
$stmt_wa = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
$key_wa = 'wa_number';
mysqli_stmt_bind_param($stmt_wa, "s", $key_wa);
mysqli_stmt_execute($stmt_wa);
$result_wa = mysqli_stmt_get_result($stmt_wa);
if ($result_wa && mysqli_num_rows($result_wa) > 0) {
    $wa_default = mysqli_fetch_assoc($result_wa)['setting_value'];
}
mysqli_stmt_close($stmt_wa);

// Gunakan nomor WA khusus produk jika ada, jika tidak pakai default
$wa_number = !empty($product['wa_number']) ? $product['wa_number'] : $wa_default;

// ========== PERBAIKAN PATH GAMBAR ==========
$image_url = $product['image_url'];
$valid_image = false;

if (!empty($image_url)) {
    // Cek apakah ini path lokal (uploads/...)
    if (strpos($image_url, 'uploads/') === 0) {
        // Path lengkap ke file gambar dari root
        $full_path = dirname(__DIR__) . '/' . $image_url;

        // Cek apakah file benar-benar ada
        if (file_exists($full_path)) {
            $valid_image = true;
            // Gunakan path relatif dari folder detail ke uploads
            $image_src = '../' . $image_url;
        } else {
            // File tidak ditemukan, cek ekstensi case-sensitive
            $full_path_lower = strtolower($full_path);
            if (file_exists($full_path_lower)) {
                $valid_image = true;
                $image_src = '../' . $image_url;
            } else {
                $valid_image = false;
            }
        }
    }
    // Cek apakah ini URL lengkap (http:// atau https://)
    elseif (strpos($image_url, 'http') === 0) {
        $valid_image = true;
        $image_src = $image_url;
    }
}

// Jika tidak ada gambar valid, gunakan placeholder
if (!$valid_image) {
    // Buat warna random berdasarkan nama produk (tema Dark Olive)
    $colors = ['3D405B', 'E07A5F', '81B29A', 'F2CC8F'];
    $color = $colors[abs(crc32($product['name'])) % count($colors)];
    $image_src = 'https://placehold.co/800x500/' . $color . '/FFF8F0?text=' . rawurlencode(substr($product['name'], 0, 20));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($product['name']) ?> - Dapur Nusantara</title>

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
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--color-cream);
            padding: 40px 20px;
            min-height: 100vh;
            color: var(--color-olive);
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(61, 64, 91, 0.08);
            animation: fadeIn 0.5s ease;
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

        /* Product Image */
        .product-image-container {
            position: relative;
            background: var(--color-cream);
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 450px;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
            opacity: 0;
        }

        .product-image-container:hover .product-image {
            transform: scale(1.03);
        }

        /* Product Info */
        .product-info {
            padding: 3rem;
        }

        /* Breadcrumb */
        .breadcrumb {
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            color: var(--color-mute);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .breadcrumb a {
            color: var(--color-olive);
            text-decoration: none;
            transition: color 0.2s;
            font-weight: 500;
        }

        .breadcrumb a:hover {
            color: var(--color-terracotta);
        }

        .breadcrumb i {
            font-size: 0.65rem;
            color: #cbd5e1;
        }

        .breadcrumb span {
            color: var(--color-mute);
        }

        /* Category Badge */
        .product-category {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--color-terracotta-light);
            color: var(--color-terracotta);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Title */
        .product-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--color-olive);
            font-weight: 700;
            line-height: 1.2;
        }

        /* Price */
        .product-price {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-terracotta);
            margin-bottom: 2rem;
            display: block;
        }

        /* Description */
        .product-description {
            color: #555a6e;
            line-height: 1.8;
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        /* Button Group */
        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2.5rem;
        }

        /* Button Cart */
        .btn-cart {
            background: var(--color-terracotta);
            color: #ffffff;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            font-family: 'Outfit', sans-serif;
            box-shadow: 0 4px 15px rgba(224, 122, 95, 0.2);
        }

        .btn-cart:hover {
            background: #c8674d;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(224, 122, 95, 0.3);
        }

        /* Button WhatsApp */
        .btn-wa {
            background: #ffffff;
            color: #25D366;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            border: 1.5px solid #e2e8f0;
            font-size: 0.95rem;
        }

        .btn-wa:hover {
            background: #25D366;
            color: #fff;
            border-color: #25D366;
            transform: translateY(-2px);
        }

        /* Divider */
        .divider {
            margin: 2rem 0;
            border: none;
            height: 1px;
            background: var(--color-border);
        }

        /* Back Button */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            color: var(--color-mute);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .btn-back:hover {
            gap: 1rem;
            color: var(--color-olive);
        }

        /* Feature List */
        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0 2rem 0;
            padding: 1.5rem;
            background: var(--color-cream);
            border-radius: 16px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--color-olive);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .feature-item i {
            color: var(--color-terracotta);
            font-size: 1rem;
            width: 24px;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            body {
                padding: 0;
            }

            .container {
                border-radius: 0;
            }

            .product-image {
                height: 280px;
            }

            .product-info {
                padding: 1.5rem;
            }

            .product-title {
                font-size: 1.8rem;
            }

            .product-price {
                font-size: 1.5rem;
            }

            .button-group {
                flex-direction: column;
            }

            .btn-cart,
            .btn-wa {
                text-align: center;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Container Gambar -->
        <div class="product-image-container">
            <img src="<?= htmlspecialchars($image_src) ?>"
                alt="<?= htmlspecialchars($product['name']) ?>"
                class="product-image"
                onerror="this.onerror=null; this.src='https://placehold.co/800x500/3D405B/FFF8F0?text=Gambar+Tidak+Tersedia';"
                onload="this.style.opacity='1'">
        </div>

        <div class="product-info">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="../index.php"><i class="fas fa-home"></i> Beranda</a>
                <i class="fas fa-chevron-right"></i>
                <span><?= htmlspecialchars($product['name']) ?></span>
            </div>

            <!-- Category Badge -->
            <div class="product-category">
                <i class="fas fa-utensils"></i> <?= htmlspecialchars($product['category_name']) ?>
            </div>

            <!-- Title -->
            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>

            <!-- Price -->
            <div class="product-price">Rp <?= number_format(htmlspecialchars($product['price']), 0, ',', '.') ?></div>

            <!-- Description -->
            <div class="product-description">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>

            <!-- Feature List (Opsional) -->
            <?php if (!empty($product['features'])): ?>
                <div class="feature-list">
                    <?php
                    $features = explode(',', $product['features']);
                    foreach ($features as $feature):
                    ?>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span><?= htmlspecialchars(trim($feature)) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Button Group -->
            <div class="button-group">
                <!-- Tombol Simpan ke Cart -->
                <button class="btn-cart"
                    onclick='addToCart(<?= (int)$product['id'] ?>, <?= json_encode($product['name']) ?>, <?= json_encode($product['price']) ?>)'>
                    <i class="fas fa-shopping-bag"></i> Pesan Sekarang
                </button>

                <a href="https://wa.me/<?= $wa_number ?>?text=Halo%2C%20saya%20tertarik%20dengan%20menu%20<?= rawurlencode($product['name']) ?>%20di%20Dapur%20Nusantara.%20Apakah%20masih%20tersedia%3F"
                    class="btn-wa" target="_blank">
                    <i class="fab fa-whatsapp"></i> Tanya Caterer
                </a>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- Back Button -->
            <a href="../index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Katalog
            </a>
        </div>
    </div>

    <script>
        // Smooth loading untuk gambar
        const img = document.querySelector('.product-image');
        if (img) {
            if (img.complete) {
                img.style.opacity = '1';
            } else {
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                });
            }
        }
    </script>
</body>

</html>