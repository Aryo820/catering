<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dapur Nusantara - Marketplace Dapur Nusantara Terbaik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            font-family: 'Outfit', sans-serif;
            background: #FFF8F0;
            color: #3D405B;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            font-family: inherit;
            cursor: pointer;
            border: none;
            outline: none;
        }

        /* NAVBAR */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 5%;
            background: #FFF8F0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 700;
            color: #3D405B;
        }

        .logo span {
            color: #E07A5F;
        }

        .menu {
            display: flex;
            gap: 30px;
            list-style: none;
        }

        .menu a {
            font-weight: 500;
            color: #3D405B;
            transition: .3s;
        }

        .menu a:hover {
            color: #E07A5F;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cart-icon {
            position: relative;
            font-size: 18px;
            color: #3D405B;
            cursor: pointer;
        }

        .cart-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background: #E07A5F;
            color: white;
            font-size: 10px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .btn-login {
            padding: 10px 24px;
            background: #3D405B;
            color: white;
            border-radius: 8px;
            font-weight: 600;
            transition: .3s;
        }

        .btn-login:hover {
            background: #2b2e47;
            transform: translateY(-2px);
        }

        /* HERO SECTION */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 50px 5% 80px;
            gap: 40px;
        }

        .hero-left {
            flex: 1;
            max-width: 600px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffe9e2;
            color: #E07A5F;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 56px;
            line-height: 1.1;
            color: #3D405B;
            margin-bottom: 20px;
        }

        .hero h1 em {
            color: #E07A5F;
            font-style: italic;
        }

        .hero p {
            font-size: 16px;
            line-height: 1.7;
            color: #6b7790;
            margin-bottom: 32px;
            max-width: 500px;
        }

        /* SEARCH BAR MARKETPLACE */
        .search-bar {
            background: white;
            border-radius: 16px;
            padding: 10px;
            display: flex;
            gap: 10px;
            box-shadow: 0 15px 40px rgba(224, 122, 95, 0.15);
            margin-bottom: 40px;
        }

        .search-bar select,
        .search-bar input {
            border: none;
            outline: none;
            padding: 12px;
            font-family: 'Outfit';
            font-size: 14px;
            background: transparent;
            color: #3D405B;
        }

        .search-bar select {
            border-right: 1px solid #eee;
            cursor: pointer;
        }

        .search-bar input {
            flex: 1;
        }

        .btn-search {
            background: #E07A5F;
            color: white;
            padding: 0 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: .3s;
        }

        .btn-search:hover {
            background: #c8674d;
        }

        /* STATS */
        .stats {
            display: flex;
            gap: 40px;
        }

        .stat-item h3 {
            font-size: 24px;
            color: #3D405B;
        }

        .stat-item p {
            font-size: 13px;
            color: #8a8f9c;
        }

        /* HERO RIGHT - IMAGE & FLOATING CARD */
        .hero-right {
            flex: 1;
            position: relative;
            display: flex;
            justify-content: center;
        }

        .hero-img-main {
            width: 100%;
            max-width: 450px;
            height: 550px;
            object-fit: cover;
            border-radius: 200px 200px 20px 20px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        .floating-card {
            position: absolute;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 16px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: float 4s ease-in-out infinite;
        }

        .fc-img {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            object-fit: cover;
        }

        .fc-text h4 {
            font-size: 14px;
            color: #3D405B;
        }

        .fc-text p {
            font-size: 12px;
            color: #E07A5F;
            font-weight: 600;
        }

        .fc-top {
            top: 40px;
            left: -20px;
        }

        .fc-bottom {
            bottom: 60px;
            right: -20px;
            animation-delay: 1.5s;
        }

        .fc-bottom .rating {
            color: #ffc107;
            font-size: 12px;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .hero {
                flex-direction: column-reverse;
                text-align: center;
                padding-top: 20px;
            }

            .hero h1 {
                font-size: 36px;
            }

            .stats {
                justify-content: center;
            }

            .search-bar {
                flex-direction: column;
            }

            .search-bar select {
                border-right: none;
                border-bottom: 1px solid #eee;
            }

            .menu {
                display: none;
            }

            .hero-img-main {
                height: 350px;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="logo">Dapur Nusantara</div>
        <ul class="menu">
            <li><a href="katalog/index.php">Katalog Catering</a></li>
            <li><a href="#">Jadi Seller</a></li>
            <li><a href="#">Cek Pesanan</a></li>
        </ul>
        <div class="nav-actions">
            <div class="cart-icon">
                <i class="fa-solid fa-bag-shopping"></i>
                <div class="cart-badge">2</div>
            </div>
            <button class="btn-login" onclick="location.href='katalog/admin/login.php'">Masuk</button>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-left">
            <div class="badge">
                <i class="fa-solid fa-utensils"></i>
                Marketplace Catering #1 di Indonesia
            </div>
            <h1>Pesan Catering <em>Premium</em> & UMKM untuk Semua Acara</h1>
            <p>Temukan ratusan menu Catering pilihan, dari prasmanan mewah, kotakan harian, hingga snack acara. Pre-order mudah, bahan segar, diantar tepat waktu.</p>

            <!-- SEARCH BAR MARKETPLACE -->
            <div class="search-bar">
                <select>
                    <option>Semua Kategori</option>
                    <option>Prasmanan</option>
                    <option>Kotakan</option>
                    <option>Pernikahan</option>
                </select>
                <input type="text" placeholder="Cari menu Dapur Nusantara atau seller...">
                <button class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            </div>

            <!-- STATS -->
            <div class="stats">
                <div class="stat-item">
                    <h3>500+</h3>
                    <p>Dapur Nusantaraer Mitra</p>
                </div>
                <div class="stat-item">
                    <h3>10rb+</h3>
                    <p>Menu Tersedia</p>
                </div>
                <div class="stat-item">
                    <h3>4.9 ⭐</h3>
                    <p>Rating Pembeli</p>
                </div>
            </div>
        </div>

        <div class="hero-right">
            <img src="https://images.unsplash.com/photo-1555244162-803834f70033?q=80&w=1470&auto=format&fit=crop" alt="Dapur Nusantara Premium" class="hero-img-main">

            <div class="floating-card fc-top">
                <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?q=80&w=400&auto=format&fit=crop" class="fc-img" alt="Menu">
                <div class="fc-text">
                    <h4>Ayam Bakar Madu</h4>
                    <p>Rp 25.000 / porsi</p>
                </div>
            </div>

            <div class="floating-card fc-bottom">
                <img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?q=80&w=800&auto=format&fit=crop" class="fc-img" alt="Menu">
                <div class="fc-text">
                    <h4>Paket Prasmanan A</h4>
                    <div class="rating">⭐ 4.9 (120 Ulasan)</div>
                </div>
            </div>
        </div>
    </section>

</body>

</html>