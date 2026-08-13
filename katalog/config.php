<?php
// ==========================================
// KONFIGURASI DATABASE - LSP COACHPRO INDONESIA
// ==========================================

// Mulai session HANYA jika belum aktif (hindari notice double session_start)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi Database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'lsp';  // Ganti dengan nama database LSP COACHPRO

// Koneksi Database
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// ============================================================
// FUNGSI HELPER UMUM
// ============================================================

/**
 * Sanitasi input untuk keamanan (hanya escape DB; HTML escape dilakukan saat output)
 */
function clean_input($data)
{
    global $conn;
    $data = trim($data);
    // strip_tags tetap dilakukan di sisi input untuk buang tag berbahaya,
    // tapi htmlspecialchars TIDAK — itu tanggung jawab saat menampilkan (sanitize_output)
    return mysqli_real_escape_string($conn, strip_tags($data));
}

/**
 * Cek apakah user sudah login sebagai admin
 */
function is_admin()
{
    return isset($_SESSION['admin_id']);
}

/**
 * Redirect ke login jika belum login
 */
function require_login()
{
    if (!is_admin()) {
        // Redirect absolut agar benar dari folder mana pun (termasuk admin/invoice/)
        header('Location: ' . SITE_URL . 'admin/login.php');
        exit;
    }
}

/**
 * Format Rupiah
 */
function format_rupiah($amount)
{
    // Cast ke string agar aman menerima int dari DB (PHP 8 strpos menolak int)
    $amount = (string)$amount;

    // Jika sudah ada format Rp, langsung return
    if ($amount === '' || $amount === '0' || $amount === '0.00') {
        return 'Rp 0';
    }
    if (strpos($amount, 'Rp') !== false) {
        return $amount;
    }
    $number = preg_replace('/[^0-9]/', '', $amount);
    if (is_numeric($number) && $number > 0) {
        return 'Rp ' . number_format((int)$number, 0, ',', '.');
    }
    // Fallback: bukan angka valid → tampilkan nol agar tidak kosong/blank
    return is_numeric($amount) ? 'Rp ' . number_format((float)$amount, 0, ',', '.') : 'Rp 0';
}

/**
 * Ambil setting dari database (AMAN: Menggunakan Prepared Statement)
 */
function get_setting($key, $default = null)
{
    global $conn;

    // Menggunakan prepared statement untuk mencegah SQL Injection
    $stmt = mysqli_prepare($conn, "SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $key);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row['setting_value'];
    }

    mysqli_stmt_close($stmt);
    return $default;
}

/**
 * Ambil nomor WhatsApp dari setting
 */
function get_wa_number()
{
    $wa = get_setting('wa_number');
    return $wa ? $wa : '6281383796300';
}

/**
 * Ambil semua kategori (AMAN: Menggunakan Prepared Statement)
 */
function get_categories()
{
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT * FROM categories ORDER BY name ASC");
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $categories;
}

/**
 * Ambil semua produk (AMAN: Menggunakan Prepared Statement)
 */
function get_all_products()
{
    global $conn;
    $query = "SELECT p.*, c.name as category_name 
              FROM products p 
              JOIN categories c ON p.category_id = c.id 
              ORDER BY p.id DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $products;
}

/**
 * Ambil produk berdasarkan ID (AMAN: Binding integer)
 */
function get_product_by_id($id)
{
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name 
                                   FROM products p 
                                   JOIN categories c ON p.category_id = c.id 
                                   WHERE p.id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $data;
}

/**
 * Ambil produk berdasarkan kategori (AMAN: Binding integer)
 */
function get_products_by_category($category_id)
{
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT p.*, c.name as category_name 
                                   FROM products p 
                                   JOIN categories c ON p.category_id = c.id 
                                   WHERE p.category_id = ? 
                                   ORDER BY p.id DESC");
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $products;
}

/**
 * Ambil kategori berdasarkan ID (AMAN: Binding integer)
 */
function get_category_by_id($id)
{
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT * FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $data;
}

/**
 * Redirect ke URL
 */
function redirect($url)
{
    header("Location: $url");
    exit;
}

/**
 * Sanitasi output HTML
 */
function sanitize_output($input)
{
    return htmlspecialchars((string)$input, ENT_QUOTES, 'UTF-8');
}

/**
 * Normalisasi harga dari berbagai format ("Rp 2.500.000", "2500000", "") ke integer
 */
function normalize_price($value)
{
    $value = is_string($value) ? preg_replace('/[^0-9]/', '', $value) : (string)(int)$value;
    return (int)$value;
}

/**
 * Upload gambar produk
 */
function upload_image($file, $target_dir = 'uploads/')
{
    // Buat folder jika belum ada
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $file_name = time() . '_' . basename($file['name']);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validasi tipe file
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($file_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak didukung'];
    }

    // Validasi ukuran (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 5MB)'];
    }

    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'path' => $target_file, 'url' => $target_file];
    }

    return ['success' => false, 'message' => 'Gagal upload file'];
}

/**
 * Fungsi untuk mendapatkan link katalog dengan base URL yang benar
 */
function get_katalog_url()
{
    // SELALU folder katalog (dari lokasi file config.php), tidak bergantung halaman yang dibuka
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])), '/') : '';
    $site_dir = str_replace('\\', '/', __DIR__);

    if ($doc_root && strpos($site_dir, $doc_root) === 0) {
        return $protocol . $host . substr($site_dir, strlen($doc_root)) . '/';
    }

    // Fallback: relatif terhadap script saat ini
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . $host . $script_dir . (strpos($script_dir, '/katalog') === false ? '/katalog/' : '/');
}

/**
 * Fungsi untuk mendapatkan link admin
 */
function get_admin_url()
{
    return get_katalog_url() . 'admin/';
}

// ============================================================
// KONFIGURASI WEBSITE
// ============================================================

// Konfigurasi dasar
define('SITE_NAME', 'Dapur Nusantara');

// Deteksi URL otomatis: SELALU folder katalog, tidak bergantung halaman yang dibuka
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])), '/') : '';
$site_dir = str_replace('\\', '/', __DIR__);

if ($doc_root && strpos($site_dir, $doc_root) === 0) {
    $base_url = $protocol . $host . substr($site_dir, strlen($doc_root)) . '/';
} else {
    // Fallback: relatif terhadap script saat ini
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    $base_url = $protocol . $host . $script_dir . (strpos($script_dir, '/katalog') === false ? '/katalog/' : '/');
}

define('SITE_URL', $base_url);
define('ROOT_URL', str_replace('/katalog/', '/', SITE_URL));
define('ADMIN_URL', SITE_URL . 'admin/');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . 'uploads/');

// Konfigurasi WhatsApp Default
define('WA_NUMBER', '6281383796300');

// Konfigurasi Email
define('ADMIN_EMAIL', 'info@lspcoachpro.com');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// ============================================================
// ERROR REPORTING (Nonaktifkan di production)
// ============================================================
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
