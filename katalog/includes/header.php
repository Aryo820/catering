<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>LSP COACHPRO INDONESIA - Sertifikasi & Pelatihan Kompetensi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
            color: #1a2a3a;
        }
        .app {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .top-header {
            background: #ffffff;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 20px rgba(31, 36, 98, 0.05);
        }
        
        /* --- CSS LOGO --- */
        .logo-area {
            font-size: 1.6rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #1f2462 0%, #2a2f7a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
        }
        .logo-area .logo-img {
            width: 45px;
            height: 45px;
            object-fit: contain;
            border-radius: 10px;
            background: #fff;
            padding: 3px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            -webkit-text-fill-color: initial;
        }
        .logo-area i {
            background: none;
            -webkit-text-fill-color: #e8b830;
            color: #e8b830;
        }
        .logo-area .highlight { 
            color: #e8b830;
            -webkit-text-fill-color: #e8b830;
        }
        /* --- AKHIR CSS LOGO --- */

        .mobile-menu-btn {
            display: none;
            font-size: 1.4rem;
            cursor: pointer;
            background: #eef1ff;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            color: #1f2462;
            transition: all 0.2s;
        }
        .mobile-menu-btn:hover {
            background: #d7defd;
        }
        .header-actions {
            display: flex;
            gap: 1.5rem;
            font-size: 1.2rem;
            cursor: pointer;
            color: #6c7a8a;
            align-items: center;
        }
        .header-actions i {
            transition: all 0.2s;
        }
        .header-actions i:hover {
            color: #e8b830;
            transform: translateY(-2px);
        }
        
        /* Search Container */
        .search-container {
            position: relative;
        }
        .search-input {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 10px;
            width: 280px;
            background: white;
            border: 1px solid #eef2f0;
            border-radius: 16px;
            padding: 0.5rem 1rem;
            display: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 101;
        }
        .search-input.active {
            display: block;
        }
        .search-input input {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid #eef2f0;
            border-radius: 40px;
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            outline: none;
            transition: all 0.2s;
        }
        .search-input input:focus {
            border-color: #e8b830;
            box-shadow: 0 0 0 2px rgba(232, 184, 48, 0.15);
        }
        .search-results {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 8px;
            border-top: 1px solid #eef2f0;
        }
        .search-result-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            text-decoration: none;
            color: #1a2a3a;
            border-bottom: 1px solid #eef2f0;
            transition: background 0.2s;
        }
        .search-result-item:hover {
            background: #eef1ff;
        }
        .search-result-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 8px;
            background: #eef2f0;
        }
        .search-result-info {
            flex: 1;
        }
        .search-result-name {
            font-weight: 600;
            font-size: 0.85rem;
        }
        .search-result-price {
            font-size: 0.7rem;
            color: #e8b830;
            font-weight: 600;
        }
        .search-no-result {
            padding: 15px;
            text-align: center;
            color: #8ba0ae;
            font-size: 0.8rem;
        }
        
        /* Cart Button Link */
        .cart-link {
            position: relative;
            color: #6c7a8a;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }
        .cart-link:hover {
            color: #e8b830;
            transform: translateY(-2px);
        }
        .cart-badge {
            position: absolute;
            top: -10px;
            right: -14px;
            background: #e8b830;
            color: #1f2462;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 30px;
            min-width: 18px;
            text-align: center;
        }
        .layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2rem;
            padding: 2rem;
            flex: 1;
            max-width: 1440px;
            margin: 0 auto;
            width: 100%;
        }
        .sidebar {
            background: white;
            border-radius: 24px;
            padding: 1.5rem 0;
            box-shadow: 0 8px 24px rgba(31, 36, 98, 0.06);
            height: fit-content;
            position: sticky;
            top: 90px;
            border: 1px solid #eef2f0;
        }
        .sidebar-header {
            font-weight: 700;
            padding: 0 1.5rem 1rem;
            border-bottom: 2px solid #eef2f0;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1f2462;
        }
        .sidebar-header i {
            color: #e8b830;
            font-size: 1.1rem;
        }
        .category-list {
            list-style: none;
            padding: 0.5rem 0;
        }
        .category-item {
            padding: 0.7rem 1.5rem;
            margin: 0.2rem 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            color: #5a6e7a;
            transition: all 0.2s;
            position: relative;
        }
        .category-item a {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
        }
        .category-item i { 
            width: 24px; 
            font-size: 1rem;
            color: #8ba0ae;
        }
        .category-item.active {
            background: linear-gradient(90deg, #eef1ff 0%, transparent 100%);
            color: #1f2462;
        }
        .category-item.active i {
            color: #e8b830;
        }
        .category-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #e8b830;
            border-radius: 0 3px 3px 0;
        }
        .category-item:hover:not(.active) { 
            background: #f8f9fc;
            color: #1f2462;
        }
        .category-item:hover:not(.active) i {
            color: #e8b830;
        }
        .count {
            margin-left: auto;
            font-size: 0.7rem;
            background: #eef2f0;
            padding: 2px 8px;
            border-radius: 30px;
            color: #5a6e7a;
            font-weight: 500;
        }
        .sidebar-footer {
            margin-top: 2rem;
            padding: 1rem 1.5rem;
            border-top: 1px solid #eef2f0;
            font-size: 0.75rem;
            color: #8ba0ae;
        }
        .sidebar-footer strong {
            color: #e8b830;
        }
        .main-content { 
            background: transparent; 
        }
        .products-header { 
            margin-bottom: 2rem;
            text-align: left;
        }
        .products-header h2 { 
            font-size: 1.8rem; 
            font-weight: 700;
            color: #1f2462;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }
        .products-header p {
            color: #6c7a8a;
            font-size: 0.95rem;
        }
        
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.8rem;
        }
        
        /* Product Card */
        .product-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.2, 0, 0, 1);
            border: 1px solid #eef2f0;
            position: relative;
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #e8b830, #ffd54f);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }
        
        .product-card:hover::before {
            transform: scaleX(1);
        }
        
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 40px rgba(31, 36, 98, 0.10);
            border-color: rgba(232, 184, 48, 0.2);
        }
        
        .product-img-wrapper {
            overflow: hidden;
            position: relative;
        }
        
        .product-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-img {
            transform: scale(1.05);
        }
        
        .product-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #e8b830;
            color: #1f2462;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 700;
            z-index: 2;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .product-body { 
            padding: 1.2rem 1.2rem 1.2rem;
            position: relative;
        }
        
        .product-category {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #e8b830;
            background: #eef1ff;
            padding: 4px 12px;
            border-radius: 30px;
            margin-bottom: 0.8rem;
        }
        
        .product-title { 
            font-size: 1.15rem; 
            font-weight: 700; 
            margin-bottom: 0.6rem;
            color: #1a2a3a;
            line-height: 1.4;
            
            /* --- PERBAIKAN CSS LINE CLAMP --- */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            display: box;
            line-clamp: 2;
            box-orient: vertical;
            
            overflow: hidden;
            transition: color 0.2s;
        }
        
        .product-card:hover .product-title {
            color: #1f2462;
        }
        
        .product-desc { 
            font-size: 0.8rem; 
            color: #6c7a8a; 
            margin-bottom: 1rem; 
            line-height: 1.5;
            
            /* --- PERBAIKAN CSS LINE CLAMP --- */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            display: box;
            line-clamp: 2;
            box-orient: vertical;
            
            overflow: hidden;
        }
        
        .product-price { 
            font-weight: 800; 
            color: #1f2462; 
            font-size: 1.25rem; 
            margin-bottom: 1rem;
        }
        
        /* Tombol Container */
        .product-buttons {
            display: flex;
            gap: 0.8rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }
        
        /* Tombol Detail */
        .btn-detail {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #f8f9fc;
            color: #1a2a3a;
            padding: 0.6rem 0.8rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.2s;
            text-align: center;
            flex: 1;
            border: 1px solid #eef2f0;
        }
        
        .btn-detail i {
            font-size: 0.7rem;
        }
        
        .btn-detail:hover {
            background: #eef1ff;
            border-color: #e8b830;
            color: #1f2462;
            transform: translateY(-2px);
        }
        
        /* Tombol Simpan (Cart) */
        .btn-cart {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #f8f9fc;
            color: #1a2a3a;
            padding: 0.6rem 0.8rem;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.2s;
            text-align: center;
            flex: 1;
            border: 1px solid #eef2f0;
            cursor: pointer;
            font-family: inherit;
        }
        
        .btn-cart i {
            font-size: 0.7rem;
        }
        
        .btn-cart:hover:not(:disabled) {
            background: #eef1ff;
            border-color: #e8b830;
            color: #1f2462;
            transform: translateY(-2px);
        }
        
        .btn-cart:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        /* Tombol Konsultasi WhatsApp */
        .btn-wa {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: linear-gradient(135deg, #25D366, #1da85e);
            color: white;
            padding: 0.6rem 0.8rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.75rem;
            transition: all 0.2s;
            text-align: center;
            flex: 1;
            box-shadow: 0 2px 8px rgba(37, 211, 102, 0.2);
        }
        
        .btn-wa i {
            font-size: 0.7rem;
        }
        
        .btn-wa:hover {
            background: linear-gradient(135deg, #1da85e, #158a4d);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
        }
        
        /* Toast Notification */
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
        
        .toast-notification i {
            color: #e8b830;
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
        
        .main-footer {
            background: #1f2462;
            color: #8ba0ae;
            text-align: center;
            padding: 1.5rem;
            font-size: 0.8rem;
            margin-top: 2rem;
            border-top: 3px solid #e8b830;
        }
        
        .main-footer strong {
            color: #e8b830;
        }
        
        /* ========== RESPONSIVE ========== */
        
        @media (min-width: 768px) and (max-width: 1024px) {
            .layout { 
                grid-template-columns: 240px 1fr; 
                gap: 1.5rem;
                padding: 1.5rem;
            }
            .products-grid { 
                grid-template-columns: repeat(2, 1fr);
                gap: 1.2rem;
            }
        }
        
        @media (max-width: 767px) {
            .top-header {
                padding: 0.8rem 1rem;
            }
            .mobile-menu-btn { display: block; }
            .layout { 
                grid-template-columns: 1fr; 
                padding: 1rem; 
                gap: 1rem;
            }
            .sidebar {
                position: fixed;
                top: 65px;
                left: -300px;
                width: 280px;
                height: calc(100% - 65px);
                z-index: 999;
                border-radius: 0 20px 20px 0;
                transition: left 0.3s ease;
                overflow-y: auto;
                background: white;
                box-shadow: 5px 0 25px rgba(0,0,0,0.1);
            }
            .sidebar.open { left: 0; }
            
            .search-input {
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                width: 100%;
                border-radius: 0;
                margin-top: 0;
            }
            
            .products-grid { 
                grid-template-columns: repeat(2, 1fr);
                gap: 0.8rem;
            }
            
            .product-img {
                height: 130px;
            }
            .product-body {
                padding: 0.8rem;
            }
            .product-title {
                font-size: 0.85rem;
                margin-bottom: 0.3rem;
            }
            .product-desc {
                font-size: 0.65rem;
                margin-bottom: 0.5rem;
            }
            .product-price {
                font-size: 0.9rem;
                margin-bottom: 0.6rem;
            }
            .product-buttons {
                gap: 0.4rem;
            }
            .btn-detail, .btn-cart, .btn-wa {
                padding: 0.4rem 0.5rem;
                font-size: 0.6rem;
            }
            .btn-detail i, .btn-cart i, .btn-wa i {
                font-size: 0.55rem;
            }
            .products-header h2 {
                font-size: 1.4rem;
            }
        }
        
        @media (max-width: 480px) {
            .products-grid { 
                gap: 0.6rem;
            }
            .product-img {
                height: 110px;
            }
            .product-body {
                padding: 0.6rem;
            }
            .product-title {
                font-size: 0.75rem;
            }
            .product-price {
                font-size: 0.8rem;
            }
            .btn-detail, .btn-cart, .btn-wa {
                padding: 0.3rem 0.4rem;
                font-size: 0.55rem;
            }
        }
        
        @media (min-width: 1400px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (min-width: 1800px) {
            .products-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        /* Overlay untuk mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 998;
        }
        .sidebar-overlay.active {
            display: block;
        }
    </style>
</head>
<body>
<div class="app">
    <header class="top-header">
        <?php 
        // --- AMBIL DATA LOGO & NAMA LEMBAGA DARI DATABASE ---
        $logo_url = ''; 
        $site_name = 'LSP COACHPRO INDONESIA'; // Default jika belum ada di database
        
        // Cek apakah variabel $conn ada (karena header di-include ke dalam file yang sudah include config)
        if (isset($conn)) {
            // Query menggunakan prepared statement
            $stmt_logo = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ?");
            
            // Ambil Logo
            $key_logo = 'site_logo';
            mysqli_stmt_bind_param($stmt_logo, "s", $key_logo);
            mysqli_stmt_execute($stmt_logo);
            $result_logo = mysqli_stmt_get_result($stmt_logo);
            if ($result_logo && mysqli_num_rows($result_logo) > 0) {
                $row_logo = mysqli_fetch_assoc($result_logo);
                $logo_url = $row_logo['setting_value'];
            }
            mysqli_stmt_close($stmt_logo);

            // Ambil Nama Lembaga (Opsional)
            $stmt_name = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ?");
            $key_name = 'site_name';
            mysqli_stmt_bind_param($stmt_name, "s", $key_name);
            mysqli_stmt_execute($stmt_name);
            $result_name = mysqli_stmt_get_result($stmt_name);
            if ($result_name && mysqli_num_rows($result_name) > 0) {
                $row_name = mysqli_fetch_assoc($result_name);
                if (!empty($row_name['setting_value'])) {
                    $site_name = $row_name['setting_value'];
                }
            }
            mysqli_stmt_close($stmt_name);
        }
        ?>

        <!-- PERBAIKAN HTML LOGO -->
        <a href="<?= SITE_URL ?? './' ?>" class="logo-area">
            <?php if (!empty($logo_url)): ?>
                <!-- Jika ada logo di database -->
                <img src="<?= htmlspecialchars($logo_url) ?>" alt="<?= htmlspecialchars($site_name) ?>" class="logo-img">
            <?php else: ?>
                <!-- Jika belum upload logo, gunakan ikon default -->
                <i class="fas fa-certificate"></i>
            <?php endif; ?>
            
            <!-- Tampilkan Nama Lembaga dari Database -->
            <span><?= htmlspecialchars($site_name) ?></span>
        </a>

        <div class="mobile-menu-btn" id="mobileMenuBtn">
            <i class="fas fa-bars"></i>
        </div>
        <div class="header-actions">
            <!-- Ikon Search -->
            <div class="search-container">
                <i class="fas fa-search" id="searchIcon"></i>
                <div class="search-input" id="searchInput">
                    <input type="text" id="searchKeyword" placeholder="Cari program sertifikasi...">
                    <div id="searchResults" class="search-results"></div>
                </div>
            </div>
            
            <!-- Ikon Keranjang Belanja -->
            <a href="cart/index.php" class="cart-link" title="Keranjang Belanja">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge" id="cartCount">0</span>
            </a>
        </div>
    </header>
    <div class="layout">