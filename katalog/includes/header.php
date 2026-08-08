<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dapur Nusantara - Premium Dapur Nusantara Marketplace</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Google Fonts: Outfit & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

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
            color: var(--color-olive);
        }

        .app {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ==========================================
           TOP HEADER
           ========================================== */
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
            border-bottom: 1px solid var(--color-border);
            box-shadow: 0 4px 20px rgba(61, 64, 91, 0.04);
        }

        /* --- LOGO AREA --- */
        .logo-area {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: var(--color-olive);
            text-decoration: none;
        }

        .logo-area .logo-img {
            width: 45px;
            height: 45px;
            object-fit: contain;
            border-radius: 12px;
            background: #fff;
            padding: 3px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .logo-area i {
            color: var(--color-terracotta);
            font-size: 1.6rem;
        }

        .logo-area .highlight {
            color: var(--color-terracotta);
        }

        /* --- MOBILE MENU BTN --- */
        .mobile-menu-btn {
            display: none;
            font-size: 1.4rem;
            cursor: pointer;
            background: var(--color-cream);
            padding: 0.5rem 1rem;
            border-radius: 12px;
            color: var(--color-olive);
            transition: all 0.2s;
        }

        .mobile-menu-btn:hover {
            background: var(--color-terracotta-light);
            color: var(--color-terracotta);
        }

        /* --- HEADER ACTIONS (SEARCH & CART) --- */
        .header-actions {
            display: flex;
            gap: 1.5rem;
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--color-olive);
            align-items: center;
        }

        .header-actions i {
            transition: all 0.2s;
        }

        .header-actions i:hover {
            color: var(--color-terracotta);
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
            margin-top: 15px;
            width: 320px;
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 16px;
            padding: 0.8rem;
            display: none;
            box-shadow: 0 15px 40px rgba(61, 64, 91, 0.1);
            z-index: 101;
        }

        .search-input.active {
            display: block;
        }

        .search-input input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1.5px solid var(--color-border);
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.2s;
            background: var(--color-cream);
        }

        .search-input input:focus {
            border-color: var(--color-terracotta);
            box-shadow: 0 0 0 4px rgba(224, 122, 95, 0.1);
            background: #fff;
        }

        .search-results {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 12px;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            text-decoration: none;
            color: var(--color-olive);
            border-radius: 12px;
            transition: background 0.2s;
        }

        .search-result-item:hover {
            background: var(--color-cream);
        }

        .search-result-img {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 10px;
            background: var(--color-border);
        }

        .search-result-info {
            flex: 1;
        }

        .search-result-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .search-result-price {
            font-size: 0.8rem;
            color: var(--color-terracotta);
            font-weight: 700;
        }

        .search-no-result {
            padding: 20px;
            text-align: center;
            color: var(--color-mute);
            font-size: 0.85rem;
        }

        /* Cart Button Link */
        .cart-link {
            position: relative;
            color: var(--color-olive);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .cart-link:hover {
            color: var(--color-terracotta);
            transform: translateY(-2px);
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -10px;
            background: var(--color-terracotta);
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 30px;
            min-width: 18px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(224, 122, 95, 0.4);
        }

        /* ==========================================
           LAYOUT GRID (Sidebar & Main Content)
           ========================================== */
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

        /* Sidebar & Products CSS mengikuti kode sebelumnya, 
           tapi dipastikan menggunakan variabel tema yang sama */
        .sidebar {
            background: white;
            border-radius: 20px;
            padding: 1.5rem 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            height: fit-content;
            position: sticky;
            top: 90px;
            border: 1px solid var(--color-border);
        }

        .main-content {
            background: transparent;
        }

        .products-header {
            margin-bottom: 2rem;
            text-align: left;
        }

        .products-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-olive);
            margin-bottom: 0.5rem;
        }

        .products-header p {
            color: var(--color-mute);
            font-size: 0.95rem;
        }

        /* ==========================================
           RESPONSIVE
           ========================================== */
        @media (min-width: 768px) and (max-width: 1024px) {
            .layout {
                grid-template-columns: 240px 1fr;
                gap: 1.5rem;
                padding: 1.5rem;
            }
        }

        @media (max-width: 767px) {
            .top-header {
                padding: 1rem;
            }

            .mobile-menu-btn {
                display: block;
            }

            .layout {
                grid-template-columns: 1fr;
                padding: 1rem;
                gap: 1rem;
            }

            .sidebar {
                position: fixed;
                top: 70px;
                left: -300px;
                width: 280px;
                height: calc(100% - 70px);
                z-index: 999;
                border-radius: 0 20px 20px 0;
                transition: left 0.3s ease;
                overflow-y: auto;
                box-shadow: 5px 0 25px rgba(0, 0, 0, 0.1);
            }

            .sidebar.open {
                left: 0;
            }

            .search-input {
                position: fixed;
                top: 65px;
                left: 0;
                right: 0;
                width: 100%;
                border-radius: 0;
                margin-top: 0;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }

            .products-header h2 {
                font-size: 1.5rem;
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
            background: rgba(61, 64, 91, 0.4);
            z-index: 998;
            backdrop-filter: blur(2px);
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
            // --- AMBIL DATA LOGO & NAMA DARI DATABASE ---
            $logo_url = '';
            $site_name = 'Dapur NusantaraMarket'; // Default teks logo

            // Cek apakah variabel $conn ada
            if (isset($conn)) {
                // Ambil Logo
                $stmt_logo = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ?");
                $key_logo = 'site_logo';
                mysqli_stmt_bind_param($stmt_logo, "s", $key_logo);
                mysqli_stmt_execute($stmt_logo);
                $result_logo = mysqli_stmt_get_result($stmt_logo);
                if ($result_logo && mysqli_num_rows($result_logo) > 0) {
                    $logo_url = mysqli_fetch_assoc($result_logo)['setting_value'];
                }
                mysqli_stmt_close($stmt_logo);

                // Ambil Nama Marketplace
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

            <a href="<?= SITE_URL ?? './' ?>" class="logo-area">
                <?php if (!empty($logo_url)): ?>
                    <img src="<?= htmlspecialchars($logo_url) ?>" alt="<?= htmlspecialchars($site_name) ?>" class="logo-img">
                <?php else: ?>
                    <i class="fas fa-utensils"></i>
                <?php endif; ?>
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
                        <input type="text" id="searchKeyword" placeholder="Cari menu Dapur Nusantara favorit...">
                        <div id="searchResults" class="search-results"></div>
                    </div>
                </div>

                <!-- Ikon Keranjang Belanja -->
                <a href="cart/index.php" class="cart-link" title="Keranjang Pesanan">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-badge" id="cartCount">0</span>
                </a>
            </div>
        </header>

        <div class="layout">