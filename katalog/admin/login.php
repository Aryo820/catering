<?php
// Mulai session HANYA jika belum aktif (config.php juga memulai session)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

/** @var mysqli $conn */
global $conn;

if (is_admin()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];

    // --- PERBAIKAN KEAMANAN: Gunakan Prepared Statement ---
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dashboard - Dapur Nusantara</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #FFF8F0;
            /* Warna Cream */
            min-height: 100vh;
            display: flex;
        }

        /* ==========================================
           LEFT SIDE - IMAGE BRANDING
           ========================================== */
        .login-left {
            flex: 1;
            background-image: linear-gradient(rgba(61, 64, 91, 0.85), rgba(61, 64, 91, 0.9)),
                url('https://images.unsplash.com/photo-1555244162-803834f70033?q=80&w=1470&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 40px;
            color: white;
        }

        .left-logo {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .left-logo span {
            color: #E07A5F;
        }

        .left-logo i {
            color: #E07A5F;
            font-size: 24px;
        }

        .left-content {
            max-width: 400px;
        }

        .left-content h1 {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .left-content p {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.9;
        }

        .left-footer {
            font-size: 13px;
            opacity: 0.7;
        }

        /* ==========================================
           RIGHT SIDE - LOGIN FORM
           ========================================== */
        .login-right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .login-box {
            width: 100%;
            max-width: 380px;
        }

        .mobile-logo {
            display: none;
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: #3D405B;
            text-align: center;
            margin-bottom: 30px;
        }

        .mobile-logo span {
            color: #E07A5F;
        }

        .login-header {
            margin-bottom: 30px;
        }

        .login-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #3D405B;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #8a8f9c;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #3D405B;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #aab0bc;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            color: #3D405B;
            background: #fff;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #E07A5F;
            box-shadow: 0 0 0 4px rgba(224, 122, 95, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #E07A5F;
            /* Warna Terracotta */
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            font-size: 15px;
            font-family: 'Outfit', sans-serif;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: #c8674d;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(224, 122, 95, 0.3);
        }

        .error-msg {
            background: #FFF0EE;
            color: #E07A5F;
            border: 1px solid #FFD3CC;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 30px;
            color: #8a8f9c;
            font-size: 13px;
            text-decoration: none;
            transition: 0.3s;
        }

        .back-link:hover {
            color: #3D405B;
        }

        /* ==========================================
           RESPONSIVE - MOBILE
           ========================================== */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .login-left {
                display: none;
                /* Sembunyikan gambar di mobile */
            }

            .mobile-logo {
                display: block;
                /* Munculkan logo di mobile */
            }

            .login-right {
                padding: 20px;
                min-height: 100vh;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <!-- LEFT SIDE (Branding/Image) -->
    <div class="login-left">
        <div class="left-logo">
            <i class="fa-solid fa-utensils"></i>
            Dapur Nusantara<span>Market</span>
        </div>

        <div class="left-content">
            <h1>Selamat Datang Kembali, Dapur Nusantaraer!</h1>
            <p>Masuk ke dashboard untuk mengelola menu Dapur Nusantara Anda, melihat pesanan masuk, dan mengembangkan bisnis kuliner Anda bersama Dapur Nusantara.</p>
        </div>

        <div class="left-footer">
            &copy; 2024 Dapur Nusantara Indonesia. Semua hak dilindungi.
        </div>
    </div>

    <!-- RIGHT SIDE (Login Form) -->
    <div class="login-right">
        <div class="login-box">
            <div class="mobile-logo">
                Dapur Nusantara<span>Market</span>
            </div>

            <div class="login-header">
                <h2>Masuk Dashboard</h2>
                <p>Silakan masukkan kredensial Anda untuk melanjutkan.</p>
            </div>

            <?php if ($error): ?>
                <div class="error-msg">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="username" placeholder="Masukkan username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Masuk ke Dashboard <i class="fa-solid fa-arrow-right" style="margin-left: 5px;"></i>
                </button>
            </form>

            <a href="<?= SITE_URL ?>index.php" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

</body>

</html>