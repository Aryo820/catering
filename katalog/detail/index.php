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
    // Buat warna random berdasarkan nama produk (tema Deep Navy)
    $colors = ['1f2462', '2a2f7a', '141844', '0f172a'];
    $color = $colors[abs(crc32($product['name'])) % count($colors)];
    $image_src = 'https://placehold.co/800x400/' . $color . '/white?text=' . rawurlencode(substr($product['name'], 0, 30));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($product['name']) ?> - LSP COACHPRO INDONESIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
            padding: 24px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
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
            background: linear-gradient(135deg, #f0fdfa 0%, #e6f4f0 100%);
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .product-image {
            width: 100%;
            height: 420px;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }
        
        .product-image-container:hover .product-image {
            transform: scale(1.02);
        }
        
        /* Product Info */
        .product-info {
            padding: 2.5rem;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            margin-bottom: 1.2rem;
            font-size: 0.85rem;
            color: #8ba0ae;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .breadcrumb a {
            color: #1f2462;
            text-decoration: none;
            transition: color 0.2s;
            font-weight: 500;
        }
        
        .breadcrumb a:hover {
            color: #e8b830;
            text-decoration: underline;
        }
        
        .breadcrumb i {
            font-size: 0.7rem;
            color: #cbd5e1;
        }
        
        .breadcrumb span {
            color: #5a6e7a;
        }
        
        /* Category Badge */
        .product-category {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #eef1ff;
            color: #1f2462;
            padding: 0.35rem 1rem;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 1.2rem;
            letter-spacing: 0.3px;
        }
        
        .product-category i {
            font-size: 0.7rem;
            color: #e8b830;
        }
        
        /* Title */
        .product-title {
            font-size: 2.2rem;
            margin-bottom: 0.75rem;
            color: #1a2a3a;
            font-weight: 700;
            line-height: 1.3;
            letter-spacing: -0.02em;
        }
        
        /* Price */
        .product-price {
            font-size: 1.8rem;
            font-weight: 800;
            color: #e8b830;
            margin-bottom: 1.5rem;
            display: inline-block;
            background: #eef1ff;
            padding: 0.35rem 1.2rem;
            border-radius: 60px;
        }
        
        /* Description */
        .product-description {
            color: #475569;
            line-height: 1.8;
            margin-bottom: 2rem;
            font-size: 1rem;
        }
        
        /* Button Group */
        .button-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }
        
        /* Button Detail / Demo */
        .btn-demo {
            background: #1a2a3a;
            color: white;
            padding: 0.85rem 1.8rem;
            border-radius: 60px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .btn-demo i {
            font-size: 0.9rem;
        }
        
        .btn-demo:hover {
            background: #1f2462;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(31, 36, 98, 0.25);
        }
        
        /* Button Simpan ke Cart */
        .btn-cart {
            background: #f8faf8;
            color: #1a2a3a;
            padding: 0.85rem 1.8rem;
            border-radius: 60px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            border: 1px solid #eef2f0;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .btn-cart i {
            font-size: 0.9rem;
        }
        
        .btn-cart:hover {
            background: #eef1ff;
            border-color: #e8b830;
            color: #1f2462;
            transform: translateY(-2px);
        }
        
        /* Button WhatsApp */
        .btn-wa {
            background: linear-gradient(135deg, #25D366, #1da85e);
            color: white;
            padding: 0.85rem 1.8rem;
            border-radius: 60px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.2);
            font-size: 0.9rem;
        }
        
        .btn-wa i {
            font-size: 0.9rem;
        }
        
        .btn-wa:hover {
            background: linear-gradient(135deg, #1da85e, #158a4d);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 211, 102, 0.3);
        }
        
        /* Divider */
        .divider {
            margin: 1.5rem 0;
            border: none;
            height: 1px;
            background: linear-gradient(90deg, #eef2f0, #e8b830, #eef2f0);
        }
        
        /* Back Button */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            color: #1f2462;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        
        .btn-back:hover {
            gap: 1rem;
            color: #e8b830;
        }
        
        /* Feature List (Opsional - jika ingin menambah fitur) */
        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
            padding: 1rem 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #475569;
            font-size: 0.85rem;
        }
        
        .feature-item i {
            color: #e8b830;
            font-size: 1rem;
            width: 24px;
        }
        
        /* ========== RESPONSIVE ========== */
        
        @media (max-width: 768px) {
            body {
                padding: 16px;
            }
            
            .product-image {
                height: 260px;
            }
            
            .product-info {
                padding: 1.5rem;
            }
            
            .product-title {
                font-size: 1.6rem;
            }
            
            .product-price {
                font-size: 1.3rem;
                padding: 0.25rem 1rem;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn-demo, .btn-cart, .btn-wa {
                text-align: center;
                justify-content: center;
                padding: 0.7rem 1.5rem;
            }
            
            .feature-list {
                grid-template-columns: 1fr;
                gap: 0.8rem;
            }
        }
        
        @media (max-width: 480px) {
            body {
                padding: 12px;
            }
            
            .product-image {
                height: 200px;
            }
            
            .product-info {
                padding: 1.2rem;
            }
            
            .product-title {
                font-size: 1.3rem;
            }
            
            .product-price {
                font-size: 1.1rem;
            }
            
            .product-description {
                font-size: 0.9rem;
                line-height: 1.6;
            }
            
            .breadcrumb {
                font-size: 0.7rem;
            }
        }
        
        /* Loading animation untuk gambar */
        .product-image {
            opacity: 0;
            transition: opacity 0.4s ease;
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
                 onerror="this.onerror=null; this.src='https://placehold.co/800x400/1f2462/white?text=Gambar+Tidak+Tersedia';"
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
                <i class="fas fa-tag"></i> <?= htmlspecialchars($product['category_name']) ?>
            </div>
            
            <!-- Title -->
            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
            
            <!-- Price -->
            <div class="product-price"><?= htmlspecialchars($product['price']) ?></div>
            
            <!-- Description -->
            <div class="product-description">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>
            
            <!-- Feature List (Opsional - tampilkan jika ada fitur, bisa diisi dari database nanti) -->
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
                <?php if (!empty($product['demo_link'])): ?>
                    <a href="<?= htmlspecialchars($product['demo_link']) ?>" class="btn-demo" target="_blank">
                        <i class="fas fa-desktop"></i> Lihat Demo
                    </a>
                <?php endif; ?>
                
                <!-- Tombol Simpan ke Cart -->
                <button class="btn-cart" 
                        onclick="addToCart(<?= $product['id'] ?>, '<?= addslashes(htmlspecialchars($product['name'])) ?>', '<?= addslashes(htmlspecialchars($product['price'])) ?>')">
                    <i class="fas fa-shopping-cart"></i> Simpan ke Keranjang
                </button>

                <a href="https://wa.me/<?= $wa_number ?>?text=Halo%2C%20saya%20tertarik%20dengan%20paket%20<?= rawurlencode($product['name']) ?>%20di%20LSP%20COACHPRO%20INDONESIA.%20Apakah%20masih%20tersedia%3F" 
                   class="btn-wa" target="_blank">
                    <i class="fab fa-whatsapp"></i> Konsultasi via WhatsApp
                </a>
            </div>
            
            <!-- Divider -->
            <hr class="divider">
            
            <!-- Back Button -->
            <a href="../index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
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