<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>LSP COACHPRO INDONESIA - Lembaga Sertifikasi & Pelatihan Kompetensi</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        /* ==========================================
           RESET & BASE - FULL HEIGHT
           ========================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            background: #f7f9fc;
            color: #20243c;
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

        /* ==========================================
           HERO - FULL VIEWPORT
           ========================================== */
        .hero {
            position: relative;
            width: 100%;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, .95), rgba(245, 247, 255, .95)),
                repeating-linear-gradient(90deg, rgba(69, 93, 172, .07) 0px, rgba(69, 93, 172, .07) 1px, transparent 1px, transparent 80px),
                repeating-linear-gradient(180deg, rgba(69, 93, 172, .07) 0px, rgba(69, 93, 172, .07) 1px, transparent 1px, transparent 80px);
            display: flex;
            align-items: center;
        }

        .hero::before {
            content: "";
            position: absolute;
            right: -260px;
            top: -220px;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(232, 184, 48, .15), transparent 70%);
            filter: blur(40px);
            pointer-events: none;
        }

        .container {
            width: 1200px;
            max-width: 95%;
            margin: 0 auto;
            position: relative;
            z-index: 5;
        }

        /* ==========================================
           NAVBAR - Compact
           ========================================== */
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 0 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            flex-wrap: wrap;
            gap: 8px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e8b830, #ffd54f);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a1a2e;
            font-size: 15px;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.05;
        }

        .logo-text span:nth-child(1) {
            font-size: 17px;
            font-weight: 800;
            color: #1f2462;
        }

        .logo-text span:nth-child(2) {
            font-size: 9px;
            letter-spacing: 2px;
            font-weight: 700;
            color: #e8b830;
            text-transform: uppercase;
        }

        .menu {
            display: flex;
            gap: 28px;
            list-style: none;
        }

        .menu a {
            font-size: 13px;
            font-weight: 500;
            color: #53617c;
            transition: .3s;
        }

        .menu a:hover {
            color: #e8b830;
        }

        .nav-action {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }

        .btn-login {
            height: 38px;
            padding: 0 22px;
            border-radius: 10px;
            background: #1f2462;
            color: #fff;
            font-weight: 600;
            font-size: 13px;
            transition: .35s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(31, 36, 98, .25);
        }

        .btn-outline {
            height: 38px;
            padding: 0 20px;
            border-radius: 10px;
            border: 2px solid #e8b830;
            background: #fff;
            color: #e8b830;
            font-weight: 600;
            font-size: 13px;
            transition: .35s;
        }

        .btn-outline:hover {
            background: #e8b830;
            color: #fff;
        }

        /* ==========================================
           HERO WRAPPER - Flex
           ========================================== */
        .hero-wrapper {
            display: flex;
            align-items: center;
            gap: 50px;
            padding: 12px 0 20px 0;
            height: calc(100vh - 80px);
            height: calc(100dvh - 80px);
        }

        .hero-left {
            flex: 1;
            min-width: 0;
        }

        .hero-right {
            flex: 0 0 340px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-right::before {
            content: "";
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(232, 184, 48, .12), transparent 70%);
            filter: blur(25px);
            animation: floatGlow 7s ease-in-out infinite;
        }

        /* ==========================================
           HERO LEFT CONTENT
           ========================================== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 18px;
            border-radius: 100px;
            background: #eef1ff;
            border: 1px solid #d7defd;
            margin-bottom: 20px;
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e8b830;
        }

        .badge span {
            font-size: 12px;
            font-weight: 600;
            color: #31398d;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 54px;
            line-height: 1.05;
            font-weight: 900;
            letter-spacing: -1.5px;
            color: #202340;
            margin-bottom: 16px;
        }

        .hero h1 strong {
            color: #e8b830;
            font-weight: 900;
        }

        .hero p {
            font-size: 16px;
            line-height: 1.7;
            color: #6b7790;
            max-width: 550px;
            margin-bottom: 28px;
        }

        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-primary {
            height: 48px;
            padding: 0 28px;
            border-radius: 12px;
            background: #1f2462;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            transition: .35s;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(31, 36, 98, .25);
        }

        .btn-secondary {
            height: 48px;
            padding: 0 28px;
            border-radius: 12px;
            background: #fff;
            border: 2px solid #d9def5;
            font-size: 14px;
            font-weight: 700;
            color: #1d2149;
            transition: .35s;
        }

        .btn-secondary:hover {
            background: #f7f8fd;
        }

        /* ==========================================
           FEATURES MINI - Inline di hero
           ========================================== */
        .features-mini {
            display: flex;
            gap: 30px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .feature-mini {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .feature-mini .icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #e8b830, #ffd54f);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a1a2e;
            font-size: 15px;
            flex-shrink: 0;
        }

        .feature-mini .text {
            font-size: 12px;
            color: #4a5a6a;
            line-height: 1.3;
        }

        .feature-mini .text strong {
            display: block;
            color: #1a1a2e;
            font-size: 14px;
        }

        /* ==========================================
           CERTIFICATE - Compact
           ========================================== */
        .certificate {
            position: relative;
            width: 320px;
            padding: 24px 26px;
            border-radius: 24px;
            background: linear-gradient(160deg, #1f2462 0%, #2a2f7a 45%, #3a2f7a 100%);
            overflow: hidden;
            box-shadow: 0 30px 70px rgba(31, 36, 98, .3);
            z-index: 2;
            animation: floatingCard 6s ease-in-out infinite;
        }

        .certificate::before {
            content: "";
            position: absolute;
            top: -80px;
            right: -60px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .06);
        }

        .certificate::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -60px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .04);
        }

        .cert-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .cert-logo {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #1f2462;
        }

        .cert-title {
            display: flex;
            flex-direction: column;
        }

        .cert-title small {
            color: rgba(255, 255, 255, .6);
            font-size: 10px;
            letter-spacing: .5px;
        }

        .cert-title h3 {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            margin-top: 2px;
        }

        .cert-line {
            height: 1px;
            background: rgba(255, 255, 255, .1);
            margin: 14px 0;
        }

        .cert-label {
            font-size: 11px;
            color: rgba(255, 255, 255, .6);
            margin-bottom: 6px;
        }

        .cert-name {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-style: italic;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }

        .cert-job {
            color: rgba(255, 255, 255, .8);
            font-size: 12px;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .cert-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 14px;
            border-radius: 100px;
            background: #e8b830;
            color: #1a1a2e;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .cert-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .cert-stamp {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px dashed #20e0b8;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #20e0b8;
            font-size: 9px;
            text-align: center;
            transform: rotate(-15deg);
        }

        .cert-info {
            text-align: right;
        }

        .cert-info small {
            display: block;
            color: rgba(255, 255, 255, .5);
            font-size: 9px;
        }

        .cert-info strong {
            display: block;
            font-size: 14px;
            color: #fff;
            margin: 4px 0 8px;
        }

        .cert-info span {
            display: block;
            color: #e8b830;
            font-weight: 700;
            font-size: 13px;
        }

        .hero-decoration {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffffff, rgba(255, 255, 255, .15));
            backdrop-filter: blur(20px);
        }

        .dec1 {
            width: 50px;
            height: 50px;
            top: 20px;
            right: 10px;
            opacity: .3;
            animation: float 5s ease-in-out infinite;
        }

        .dec2 {
            width: 28px;
            height: 28px;
            left: 0px;
            bottom: 50px;
            opacity: .25;
            animation: float 6s ease-in-out infinite;
        }

        .dec3 {
            width: 12px;
            height: 12px;
            right: 80px;
            bottom: 20px;
            background: #e8b830;
            opacity: .6;
        }

        /* ==========================================
           ANIMATIONS
           ========================================== */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-14px); }
            100% { transform: translateY(0); }
        }

        @keyframes floatGlow {
            0% { transform: scale(1); }
            50% { transform: scale(1.06); }
            100% { transform: scale(1); }
        }

        @keyframes floatingCard {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }

        /* ==========================================
           RESPONSIVE - MOBILE FIRST
           ========================================== */

        /* Large Desktop */
        @media (min-width: 1200px) {
            .hero h1 {
                font-size: 62px;
            }
            .certificate {
                width: 360px;
                padding: 30px 32px;
            }
            .cert-name {
                font-size: 38px;
            }
        }

        /* Desktop */
        @media (max-width: 1100px) {
            .hero-wrapper {
                flex-direction: column;
                text-align: center;
                height: auto;
                padding: 20px 0;
                gap: 30px;
                overflow-y: auto;
            }

            .hero-left {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .hero h1 {
                font-size: 44px;
                max-width: 100%;
            }

            .hero p {
                max-width: 100%;
            }

            .hero-buttons {
                justify-content: center;
            }

            .features-mini {
                justify-content: center;
            }

            .hero-right {
                flex: 0 0 auto;
                margin-top: 0;
            }

            .hero-right::before {
                width: 300px;
                height: 300px;
            }

            .certificate {
                width: 280px;
                padding: 20px 22px;
            }

            .cert-name {
                font-size: 26px;
            }
        }

        /* Tablet */
        @media (max-width: 768px) {
            .hero {
                height: 100vh;
                height: 100dvh;
                overflow-y: auto;
                align-items: flex-start;
                padding-top: 10px;
            }

            .hero-wrapper {
                height: auto;
                min-height: calc(100vh - 70px);
                min-height: calc(100dvh - 70px);
                justify-content: center;
                gap: 20px;
                padding: 10px 0 20px;
                overflow-y: auto;
            }

            .navbar {
                padding: 12px 0 10px 0;
                gap: 6px;
            }

            .menu {
                display: none;
            }

            .logo-text span:nth-child(1) {
                font-size: 15px;
            }

            .logo-icon {
                width: 30px;
                height: 30px;
                font-size: 13px;
            }

            .nav-action .btn-outline {
                display: none;
            }

            .btn-login {
                height: 34px;
                padding: 0 16px;
                font-size: 12px;
            }

            .hero h1 {
                font-size: 32px;
                letter-spacing: -0.5px;
                margin-bottom: 10px;
            }

            .hero p {
                font-size: 14px;
                line-height: 1.6;
                margin-bottom: 18px;
            }

            .badge {
                padding: 5px 14px;
                margin-bottom: 14px;
            }

            .badge span {
                font-size: 10px;
            }

            .badge-dot {
                width: 6px;
                height: 6px;
            }

            .btn-primary,
            .btn-secondary {
                height: 40px;
                padding: 0 20px;
                font-size: 12px;
            }

            .features-mini {
                gap: 16px;
                margin-top: 18px;
            }

            .feature-mini .icon {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }

            .feature-mini .text {
                font-size: 10px;
            }

            .feature-mini .text strong {
                font-size: 12px;
            }

            .hero-right {
                flex: 0 0 auto;
            }

            .hero-right::before {
                width: 220px;
                height: 220px;
            }

            .certificate {
                width: 240px;
                padding: 16px 18px;
                border-radius: 18px;
            }

            .cert-header {
                margin-bottom: 12px;
                gap: 10px;
            }

            .cert-logo {
                width: 32px;
                height: 32px;
                font-size: 14px;
                border-radius: 10px;
            }

            .cert-title small {
                font-size: 8px;
            }

            .cert-title h3 {
                font-size: 12px;
            }

            .cert-line {
                margin: 10px 0;
            }

            .cert-label {
                font-size: 9px;
            }

            .cert-name {
                font-size: 22px;
                margin-bottom: 4px;
            }

            .cert-job {
                font-size: 10px;
                margin-bottom: 10px;
            }

            .cert-badge {
                font-size: 9px;
                padding: 4px 12px;
                margin-bottom: 10px;
            }

            .cert-footer {
                margin-top: 0;
            }

            .cert-stamp {
                width: 44px;
                height: 44px;
                font-size: 7px;
            }

            .cert-info strong {
                font-size: 11px;
                margin: 2px 0 4px;
            }

            .cert-info span {
                font-size: 11px;
            }

            .dec1,
            .dec2,
            .dec3 {
                display: none;
            }

            .hero-wrapper {
                gap: 16px;
            }
        }

        /* Small Mobile */
        @media (max-width: 400px) {
            .hero h1 {
                font-size: 26px;
            }

            .hero p {
                font-size: 12px;
            }

            .hero-buttons {
                gap: 8px;
            }

            .btn-primary,
            .btn-secondary {
                height: 36px;
                padding: 0 14px;
                font-size: 11px;
            }

            .certificate {
                width: 200px;
                padding: 14px 14px;
            }

            .cert-name {
                font-size: 18px;
            }

            .cert-job {
                font-size: 9px;
            }

            .features-mini {
                gap: 10px;
            }

            .feature-mini .text {
                font-size: 9px;
            }

            .feature-mini .text strong {
                font-size: 10px;
            }

            .feature-mini .icon {
                width: 24px;
                height: 24px;
                font-size: 10px;
            }
        }

        /* ==========================================
           SCROLLBAR HIDE
           ========================================== */
        .hero-wrapper::-webkit-scrollbar {
            display: none;
        }

        .hero-wrapper {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body>

    <!-- ==========================================
    HERO - FULL VIEWPORT
    ========================================== -->
    <section class="hero">
        <div class="container">

            <!-- ===== NAVBAR ===== -->
            <nav class="navbar">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div class="logo-text">
                        <span>LSP COACHPRO INDONESIA</span>
                        <span>Sertifikasi & Pelatihan</span>
                    </div>
                </div>

                <ul class="menu">
                    <li><a href="#program">Program</a></li>
                    <li><a href="#sertifikat">Sertifikat</a></li>
                    <li><a href="#kontak">Kontak</a></li>
                </ul>

                <div class="nav-action">
                    <button class="btn-login" onclick="location.href='katalog/admin/login.php'">
                        <i class="fa-solid fa-key"></i> Masuk
                    </button>
                    <button class="btn-outline" onclick="location.href='katalog/'">
                        <i class="fa-solid fa-folder-open"></i> Katalog
                    </button>
                </div>
            </nav>

            <!-- ===== HERO WRAPPER ===== -->
            <div class="hero-wrapper">

                <!-- ===== LEFT CONTENT ===== -->
                <div class="hero-left">
                    <div class="badge">
                        <div class="badge-dot"></div>
                        <span>Lembaga Sertifikasi Profesi</span>
                    </div>

                    <h1>
                        Raih Sertifikasi
                        <strong>Kompetensi</strong>
                        Profesional Anda
                    </h1>

                    <p>
                        LSP COACHPRO INDONESIA hadir untuk mengakui, menilai, dan merekomendasikan kompetensi
                        tenaga pelatihan &amp; kursus nasional secara kredibel dan terpercaya.
                    </p>

                    <div class="hero-buttons">
                        <button class="btn-secondary" onclick="location.href='#program'">
                            <i class="fa-solid fa-list"></i> Lihat Program
                        </button>
                        <button class="btn-primary" onclick="location.href='katalog/'">
                            <i class="fa-solid fa-folder-open"></i> Buka Katalog
                        </button>
                    </div>

                    <!-- ===== FEATURES MINI ===== -->
                    <div class="features-mini">
                        <div class="feature-mini">
                            <div class="icon"><i class="fa-solid fa-medal"></i></div>
                            <div class="text">
                                <strong>Sertifikasi Terpercaya</strong>
                                Standar mutu profesional
                            </div>
                        </div>
                        <div class="feature-mini">
                            <div class="icon"><i class="fa-solid fa-user-check"></i></div>
                            <div class="text">
                                <strong>Asesor Profesional</strong>
                                Berpengalaman & objektif
                            </div>
                        </div>
                        <div class="feature-mini">
                            <div class="icon"><i class="fa-solid fa-laptop-file"></i></div>
                            <div class="text">
                                <strong>Sistem Digital</strong>
                                Efisien & terintegrasi
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== RIGHT - CERTIFICATE ===== -->
                <div class="hero-right">
                    <div class="hero-decoration dec1"></div>
                    <div class="hero-decoration dec2"></div>
                    <div class="hero-decoration dec3"></div>

                    <div class="certificate">
                        <div class="cert-header">
                            <div class="cert-logo">
                                <i class="fa-solid fa-award"></i>
                            </div>
                            <div class="cert-title">
                                <small>LEMBAGA SERTIFIKASI</small>
                                <h3>Sertifikat Kompetensi</h3>
                            </div>
                        </div>

                        <div class="cert-line"></div>

                        <div class="cert-label">Diberikan kepada</div>
                        <div class="cert-name">John Doe</div>
                        <div class="cert-job">Kepala Lembaga Pelatihan / Kursus Nasional</div>

                        <div class="cert-badge">Level VI KKNI</div>

                        <div class="cert-line"></div>

                        <div class="cert-footer">
                            <div class="cert-stamp">KOMPETEN</div>
                            <div class="cert-info">
                                <small>NO. REGISTRASI</small>
                                <strong>MET.123.XXXXXX</strong>
                                <small>MASA BERLAKU</small>
                                <span>3 Tahun</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /hero-wrapper -->
        </div><!-- /container -->
    </section>

    <!-- ==========================================
    JAVASCRIPT
    ========================================== -->
    <script>
        // Smooth scroll untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener("click", function(e) {
                e.preventDefault();
                // Just show alert for demo - no actual scroll
                alert('Halaman ini adalah hero section satu tampilan. Semua konten sudah ada di sini!');
            });
        });

        // Button handlers dengan feedback
        document.querySelectorAll('.btn-login, .btn-outline, .btn-primary, .btn-secondary').forEach(btn => {
            btn.addEventListener('click', function(e) {
                // Ripple effect
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);

                ripple.style.cssText = `
                            width: ${size}px;
                            height: ${size}px;
                            left: ${e.clientX - rect.left - size/2}px;
                            top: ${e.clientY - rect.top - size/2}px;
                            position: absolute;
                            border-radius: 50%;
                            background: rgba(255,255,255,.3);
                            transform: scale(0);
                            animation: ripple .6s linear;
                            pointer-events: none;
                        `;

                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);

                setTimeout(() => ripple.remove(), 600);
            });
        });

        // Ripple keyframes
        const style = document.createElement('style');
        style.innerHTML = `
                    @keyframes ripple {
                        to { transform: scale(4); opacity: 0; }
                    }
                `;
        document.head.appendChild(style);
    </script>

</body>
</html>