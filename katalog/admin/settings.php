<?php
$page_title = 'Pengaturan Website';
require_once '../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

// --- PROSES SIMPAN SETTING (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    
    // Ambil input text
    $site_name = clean_input($_POST['site_name']);
    $wa_number = clean_input($_POST['wa_number']);
    $footer_copyright = clean_input($_POST['footer_copyright']);
    // footer_credit_* sengaja boleh berisi HTML (icon) → simpan mentah, admin trusted
    $footer_credit_left = trim($_POST['footer_credit_left'] ?? '');
    $footer_credit_right = trim($_POST['footer_credit_right'] ?? '');
    
    // Ambil input CTA (Smart Promo)
    $cta_title = clean_input($_POST['cta_title']);
    $cta_message = clean_input($_POST['cta_message']);
    $cta_emoji = clean_input($_POST['cta_emoji']);
    $cta_btn_text = clean_input($_POST['cta_btn_text']);
    $cta_wa_text = clean_input($_POST['cta_wa_text']);
    $cta_footer_text = clean_input($_POST['cta_footer_text']);
    
    // Validasi Nomor WA
    if (!preg_match('/^[0-9]{10,15}$/', $wa_number)) {
        $_SESSION['error'] = "Nomor WhatsApp tidak valid! Harus angka 10-15 digit.";
        header('Location: settings.php');
        exit;
    }

    // --- PROSES UPLOAD LOGO ---
    $logo_path = null;
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_image($_FILES['site_logo'], '../uploads/');
        if ($upload_result['success']) {
            // Simpan path URL relatif dari folder admin ke folder public
            $logo_path = 'uploads/' . basename($upload_result['path']);
        } else {
            $_SESSION['error'] = "Gagal upload logo: " . $upload_result['message'];
            header('Location: settings.php');
            exit;
        }
    }

    // --- UPDATE KE DATABASE (Menggunakan Prepared Statement) ---
    $updates = [
        'site_name' => $site_name,
        'wa_number' => $wa_number,
        'footer_copyright' => $footer_copyright,
        'footer_credit_left' => $footer_credit_left,
        'footer_credit_right' => $footer_credit_right,
        // Tambahkan data CTA
        'cta_title' => $cta_title,
        'cta_message' => $cta_message,
        'cta_emoji' => $cta_emoji,
        'cta_btn_text' => $cta_btn_text,
        'cta_wa_text' => $cta_wa_text,
        'cta_footer_text' => $cta_footer_text
    ];

    // Jika ada logo baru, tambahkan ke update
    if ($logo_path !== null) {
        $updates['site_logo'] = $logo_path;
    }

    foreach ($updates as $key => $value) {
        $stmt_update = mysqli_prepare($conn, "UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        mysqli_stmt_bind_param($stmt_update, "ss", $value, $key);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
    }

    $_SESSION['success'] = "Semua pengaturan website berhasil disimpan!";
    header('Location: settings.php');
    exit;
}

// --- AMBIL DATA SETTING SAAT INI ---
$current_settings = [];
$keys_to_fetch = [
    'site_logo',
    'site_name',
    'wa_number',
    'footer_copyright',
    'footer_credit_left',
    'footer_credit_right',
    // Keys untuk CTA
    'cta_title',
    'cta_message',
    'cta_emoji',
    'cta_btn_text',
    'cta_wa_text',
    'cta_footer_text'
];

foreach ($keys_to_fetch as $key) {
    $stmt_get = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ?");
    mysqli_stmt_bind_param($stmt_get, "s", $key);
    mysqli_stmt_execute($stmt_get);
    $res_get = mysqli_stmt_get_result($stmt_get);
    $row = mysqli_fetch_assoc($res_get);
    
    // Jika key belum ada di database, beri nilai default
    if ($row) {
        $current_settings[$key] = $row['setting_value'];
    } else {
        // Default values jika belum ada
        $defaults = [
            'site_logo' => '',
            'site_name' => 'LSP COACHPRO INDONESIA',
            'wa_number' => '6281383796300',
            'footer_copyright' => 'LSP COACHPRO INDONESIA – Lembaga Sertifikasi Profesi Terakreditasi',
            'footer_credit_left' => '<i class="fas fa-mobile-alt"></i> Mobile Friendly',
            'footer_credit_right' => '<i class="fas fa-tachometer-alt"></i> Kinerja Cepat',
            // Default CTA
            'cta_title' => '💡 Konsultasi Gratis!',
            'cta_message' => 'Butuh bantuan memilih program sertifikasi yang tepat untuk karir Anda? Tim kami siap membantu 24/7!',
            'cta_emoji' => '🎁',
            'cta_btn_text' => 'Hubungi Sekarang →',
            'cta_wa_text' => 'Halo, saya butuh konsultasi mengenai program sertifikasi LSP COACHPRO.',
            'cta_footer_text' => '⭐ 500+ klien puas | ⚡ Respon cepat | 🎯 Garansi 30 hari'
        ];
        $current_settings[$key] = $defaults[$key];
    }
    mysqli_stmt_close($stmt_get);
}

// --- AMBIL PESAN SESSION ---
$success_msg = $_SESSION['success'] ?? null;
$error_msg = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

include 'includes/header.php';
?>

<div style="max-width: 900px; margin: 0 auto;">
    
    <?php if ($success_msg): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <!-- FORM UTAMA PENGATURAN -->
    <div class="form-card">
        <h3 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-cogs" style="color: #3b82f6;"></i> Pengaturan Website &amp; Promo
        </h3>
        
        <form method="POST" enctype="multipart/form-data">
            
            <!-- ================== BAGIAN LOGO & BRANDING ================== -->
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
                <h4 style="color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-image" style="color: #3b82f6;"></i> Logo &amp; Branding
                </h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Logo Website</label>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <?php if (!empty($current_settings['site_logo'])): ?>
                                <img src="../<?= htmlspecialchars($current_settings['site_logo']) ?>" alt="Logo" style="height: 60px; width: auto; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px; background: white;">
                            <?php else: ?>
                                <div style="height: 60px; width: 60px; border: 2px dashed #cbd5e1; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 0.7rem;">Belum ada</div>
                            <?php endif; ?>
                            <input type="file" name="site_logo" accept="image/png,image/jpeg,image/webp">
                        </div>
                        <small>Upload logo (format PNG, JPG, WebP. Maks 5MB)</small>
                    </div>
                    <div class="form-group">
                        <label>Nama Website</label>
                        <input type="text" name="site_name" value="<?= htmlspecialchars($current_settings['site_name']) ?>" placeholder="LSP COACHPRO INDONESIA" required>
                    </div>
                </div>
            </div>

            <!-- ================== BAGIAN WHATSAPP ================== -->
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
                <h4 style="color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fab fa-whatsapp" style="color: #25D366;"></i> Kontak WhatsApp
                </h4>
                <div class="form-group">
                    <label>Nomor WhatsApp Default</label>
                    <input type="text" name="wa_number" value="<?= htmlspecialchars($current_settings['wa_number']) ?>" placeholder="6281234567890" required>
                    <small>Gunakan format 628xxxxx (tanpa + atau spasi).</small>
                </div>
            </div>

            <!-- ================== BAGIAN FOOTER ================== -->
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
                <h4 style="color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-edit" style="color: #f59e0b;"></i> Konten Footer
                </h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Copyright Footer</label>
                        <input type="text" name="footer_copyright" value="<?= htmlspecialchars($current_settings['footer_copyright']) ?>" placeholder="LSP COACHPRO INDONESIA – Lembaga Sertifikasi Profesi Terakreditasi">
                    </div>
                    <div class="form-group">
                        <label>Kredit Kiri (Text/HTML)</label>
                        <input type="text" name="footer_credit_left" value="<?= htmlspecialchars($current_settings['footer_credit_left']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Kredit Kanan (Text/HTML)</label>
                        <input type="text" name="footer_credit_right" value="<?= htmlspecialchars($current_settings['footer_credit_right']) ?>">
                    </div>
                </div>
                <small>Anda boleh menggunakan kode HTML seperti <code>&lt;i&gt;</code> untuk icon di bagian kredit.</small>
            </div>

            <!-- ================== BAGIAN SMART PROMO CTA ================== -->
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
                <h4 style="color: #0f172a; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-bullhorn" style="color: #e8b830;"></i> Smart Promo CTA (Floating Card)
                </h4>
                <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 1rem;">
                    <i class="fas fa-info-circle"></i> 
                    Konten ini akan muncul sebagai kartu promosi melayang di pojok kiri bawah website berdasarkan perilaku pengunjung.
                </p>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Judul CTA</label>
                        <input type="text" name="cta_title" value="<?= htmlspecialchars($current_settings['cta_title']) ?>" placeholder="💡 Konsultasi Gratis!">
                    </div>
                    <div class="form-group">
                        <label>Emoji / Ikon</label>
                        <input type="text" name="cta_emoji" value="<?= htmlspecialchars($current_settings['cta_emoji']) ?>" placeholder="🎁">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Pesan Utama</label>
                        <textarea name="cta_message" rows="3" placeholder="Butuh bantuan memilih program sertifikasi yang tepat?"><?= htmlspecialchars($current_settings['cta_message']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Teks Tombol</label>
                        <input type="text" name="cta_btn_text" value="<?= htmlspecialchars($current_settings['cta_btn_text']) ?>" placeholder="Hubungi Sekarang →">
                    </div>
                    <div class="form-group">
                        <label>Pesan WhatsApp (Otomatis)</label>
                        <input type="text" name="cta_wa_text" value="<?= htmlspecialchars($current_settings['cta_wa_text']) ?>" placeholder="Halo, saya butuh konsultasi...">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Footer Kecil di Bawah CTA</label>
                        <input type="text" name="cta_footer_text" value="<?= htmlspecialchars($current_settings['cta_footer_text']) ?>" placeholder="⭐ 500+ klien puas | ⚡ Respon cepat">
                    </div>
                </div>
            </div>

            <!-- ================== TOMBOL SIMPAN ================== -->
            <button type="submit" name="save_settings" style="width: 100%; padding: 0.8rem; font-size: 1rem; margin-top: 0.5rem;">
                <i class="fas fa-save"></i> Simpan Semua Pengaturan
            </button>
            
        </form>
    </div>
    
</div>

<?php include 'includes/footer.php'; ?>