<?php
$page_title = 'Kelola Produk';
require_once '../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

// --- PROSES FORM (TAMBAH / EDIT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_action'])) {
    $name = clean_input($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $desc = clean_input($_POST['description']);
    // Normalisasi harga ke angka bersih (varchar kosong = 0) agar number_format/getCartTotal konsisten
    $price = (string)normalize_price($_POST['price'] ?? '0');
    $demo_link = clean_input($_POST['demo_link']);
    $wa_number = clean_input($_POST['wa_number']);
    
    // Proses upload gambar
    // old_image dari hidden field ber-HTML entity (htmlspecialchars saat render) → decode dulu
    $image_url = isset($_POST['old_image']) ? htmlspecialchars_decode($_POST['old_image'], ENT_QUOTES) : '';
    
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        // === PERBAIKAN PATH UPLOAD ===
        $upload_dir = '';
        
        // Coba beberapa kemungkinan path
        if (file_exists(__DIR__ . '/../uploads/')) {
            $upload_dir = __DIR__ . '/../uploads/';
        } elseif (file_exists(__DIR__ . '/uploads/')) {
            $upload_dir = __DIR__ . '/uploads/';
        } else {
            $upload_dir = __DIR__ . '/../uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
        }
        
        // Set permission
        chmod($upload_dir, 0755);
        
        $file = $_FILES['product_image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $file_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
        $target_file = $upload_dir . $file_name;
        
        // Validasi tipe file
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($file['tmp_name']);
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if ($file['size'] > $max_size) {
            $_SESSION['error'] = "Ukuran file terlalu besar. Maksimal 5MB.";
        } elseif (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                // Hapus gambar lama jika ada (untuk edit)
                if (!empty($image_url) && strpos($image_url, 'uploads/') !== false) {
                    $old_file_path = __DIR__ . '/../' . $image_url;
                    if (file_exists($old_file_path)) {
                        unlink($old_file_path);
                    }
                }
                
                // Simpan path ke database
                $image_url = 'uploads/' . $file_name;
                
                // Debug: catat ke error log
                error_log("Upload success: " . $image_url);
                
            } else {
                $_SESSION['error'] = "Gagal mengupload gambar. Periksa permission folder.";
            }
        } else {
            $_SESSION['error'] = "Tipe file tidak diizinkan. Gunakan JPG, PNG, GIF, atau WEBP.";
        }
    }
    
    // Validasi
    $errors = [];
    if (empty($name)) $errors[] = "Nama produk wajib diisi";
    if ($category_id <= 0) $errors[] = "Kategori wajib dipilih";
    
    if (empty($errors)) {
        if ($_POST['product_action'] === 'add') {
            if (empty($image_url)) {
                $image_url = 'https://placehold.co/400x300/2c3e50/white?text=No+Image';
            }
            $stmt = mysqli_prepare($conn, "INSERT INTO products (name, category_id, description, price, demo_link, image_url, wa_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sisssss", $name, $category_id, $desc, $price, $demo_link, $image_url, $wa_number);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Produk berhasil ditambahkan!";
            } else {
                $_SESSION['error'] = "Gagal menambah: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } elseif ($_POST['product_action'] === 'edit') {
            $id = (int)$_POST['id'];
            $stmt = mysqli_prepare($conn, "UPDATE products SET name=?, category_id=?, description=?, price=?, demo_link=?, image_url=?, wa_number=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sisssssi", $name, $category_id, $desc, $price, $demo_link, $image_url, $wa_number, $id);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = "Produk berhasil diupdate!";
            } else {
                $_SESSION['error'] = "Gagal mengupdate: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
        header('Location: products.php');
        exit;
    } else {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        header('Location: products.php' . ($_POST['product_action'] === 'edit' ? '?edit=' . $_POST['id'] : '?add'));
        exit;
    }
}

// --- HAPUS PRODUK ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['product_action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        // Ambil gambar untuk dihapus
        $stmt_img = mysqli_prepare($conn, "SELECT image_url FROM products WHERE id = ?");
        mysqli_stmt_bind_param($stmt_img, "i", $id);
        mysqli_stmt_execute($stmt_img);
        $res_img = mysqli_stmt_get_result($stmt_img);
        $img_data = mysqli_fetch_assoc($res_img);
        mysqli_stmt_close($stmt_img);

        if ($img_data && !empty($img_data['image_url']) && strpos($img_data['image_url'], 'uploads/') !== false) {
            $file_to_delete = __DIR__ . '/../' . $img_data['image_url'];
            if (file_exists($file_to_delete)) {
                unlink($file_to_delete);
            }
        }

        // Hapus produk
        $stmt_del = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
        mysqli_stmt_bind_param($stmt_del, "i", $id);
        mysqli_stmt_execute($stmt_del);
        mysqli_stmt_close($stmt_del);

        $_SESSION['success'] = "Produk berhasil dihapus!";
    }
    header('Location: products.php');
    exit;
}

// --- AMBIL DATA PRODUK UNTUK EDIT ---
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt_edit = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt_edit, "i", $edit_id);
    mysqli_stmt_execute($stmt_edit);
    $res_edit = mysqli_stmt_get_result($stmt_edit);
    $edit_product = mysqli_fetch_assoc($res_edit);
    mysqli_stmt_close($stmt_edit);
    
    if (!$edit_product) {
        $_SESSION['error'] = "Produk tidak ditemukan!";
        header('Location: products.php');
        exit;
    }
}

// --- QUERY PRODUK DENGAN FILTER PENCARIAN ---
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';
$products = [];

if (!empty($search)) {
    $like_search = "%" . $search . "%";
    $stmt_search = mysqli_prepare($conn, "SELECT p.*, c.name as category_name 
                                          FROM products p 
                                          JOIN categories c ON p.category_id = c.id 
                                          WHERE p.name LIKE ? OR c.name LIKE ? 
                                          ORDER BY p.id DESC");
    mysqli_stmt_bind_param($stmt_search, "ss", $like_search, $like_search);
    mysqli_stmt_execute($stmt_search);
    $products = mysqli_stmt_get_result($stmt_search);
} else {
    $stmt_all = mysqli_prepare($conn, "SELECT p.*, c.name as category_name 
                                       FROM products p 
                                       JOIN categories c ON p.category_id = c.id 
                                       ORDER BY p.id DESC");
    mysqli_stmt_execute($stmt_all);
    $products = mysqli_stmt_get_result($stmt_all);
}

// --- AMBIL KATEGORI ---
$stmt_cat = mysqli_prepare($conn, "SELECT * FROM categories ORDER BY name");
mysqli_stmt_execute($stmt_cat);
$categories = mysqli_stmt_get_result($stmt_cat);

// --- TAMPILKAN PESAN SESSION ---
$success_msg = $_SESSION['success'] ?? null;
$error_msg = $_SESSION['error'] ?? null;
$upload_success = $_SESSION['upload_success'] ?? null;
$errors = $_SESSION['errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['upload_success'], $_SESSION['errors'], $_SESSION['old_data']);

include 'includes/header.php';
?>

<style>
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border-left: 4px solid #10b981;
    }
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border-left: 4px solid #ef4444;
    }
    .form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .form-group input, .form-group select, .form-group textarea {
        padding: 0.5rem;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 1rem;
    }
    .form-group label {
        font-weight: 600;
        font-size: 0.9rem;
    }
    .form-group small {
        color: #64748b;
        font-size: 0.8rem;
    }
    .btn-primary {
        background: #3b82f6;
        color: white;
        padding: 0.5rem 1.5rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .btn-primary:hover {
        background: #2563eb;
    }
    .btn-success {
        background: #10b981;
        color: white;
        padding: 0.5rem 1.5rem;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
    .btn-success:hover {
        background: #059669;
    }
    .btn-danger {
        background: #ef4444;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
    }
    .btn-danger:hover {
        background: #dc2626;
    }
    .btn-warning {
        background: #f59e0b;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
    }
    .btn-warning:hover {
        background: #d97706;
    }
    .btn-back {
        background: #64748b;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
    }
    .btn-back:hover {
        background: #475569;
    }
    .table-container {
        overflow-x: auto;
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    thead {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    th {
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
    }
    td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
    }
    .header-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .search-box {
        display: flex;
        gap: 0.5rem;
    }
    .search-box input {
        padding: 0.5rem 1rem;
        border-radius: 30px;
        border: 1px solid #cbd5e1;
        width: 250px;
    }
    .search-box button {
        padding: 0.5rem 1rem;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 30px;
        cursor: pointer;
    }
    .preview-image {
        margin-top: 0.5rem;
        max-width: 150px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 4px;
        display: none;
    }
    .current-image {
        margin-top: 0.5rem;
        padding: 0.5rem;
        background: #f8fafc;
        border-radius: 8px;
    }
    .current-image img {
        max-width: 150px;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
    }
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .header-actions {
            flex-direction: column;
            align-items: stretch;
        }
        .search-box input {
            width: 100%;
        }
        .action-buttons {
            flex-direction: column;
        }
    }
</style>

<!-- Header Actions -->
<div class="header-actions">
    <div>
        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="Cari produk..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fas fa-search"></i> Cari</button>
            <?php if (!empty($search)): ?>
                <a href="products.php" style="background: #64748b; color: white; padding: 0.5rem 1rem; border-radius: 30px; text-decoration: none;">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    <?php if (!$edit_product): ?>
        <a href="?add" class="btn-success">
            <i class="fas fa-plus"></i> Tambah Produk
        </a>
    <?php endif; ?>
</div>

<!-- Alert Messages -->
<?php if ($success_msg): ?>
    <div class="alert-success">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?>
    </div>
<?php endif; ?>
<?php if ($upload_success): ?>
    <div class="alert-success">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($upload_success) ?>
    </div>
<?php endif; ?>
<?php if ($error_msg): ?>
    <div class="alert-error">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_msg) ?>
    </div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="alert-error">
        <ul style="margin-left: 1rem;">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Form Tambah/Edit Produk -->
<?php if (isset($_GET['add']) || $edit_product): ?>
<div class="form-card">
    <h3 style="margin-bottom: 1rem;"><?= $edit_product ? '✏️ Edit Produk' : '➕ Tambah Produk Baru' ?></h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_action" value="<?= $edit_product ? 'edit' : 'add' ?>">
        <?php if ($edit_product): ?>
            <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
            <input type="hidden" name="old_image" value="<?= htmlspecialchars($edit_product['image_url'] ?? '') ?>">
        <?php endif; ?>
        
        <div class="form-grid">
            <div class="form-group">
                <label>Nama Produk <span style="color:red">*</span></label>
                <input type="text" name="name" value="<?= htmlspecialchars($old_data['name'] ?? $edit_product['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Kategori <span style="color:red">*</span></label>
                <select name="category_id" required>
                    <option value="">Pilih Kategori</option>
                    <?php mysqli_data_seek($categories, 0); while($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?= $cat['id'] ?>" 
                            <?= (($edit_product && $edit_product['category_id'] == $cat['id']) || ($old_data['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="text" name="price" value="<?= htmlspecialchars($old_data['price'] ?? $edit_product['price'] ?? '') ?>" placeholder="Contoh: Rp 2.500.000">
            </div>
            <div class="form-group">
                <label>Nomor WhatsApp (optional)</label>
                <input type="text" name="wa_number" value="<?= htmlspecialchars($old_data['wa_number'] ?? $edit_product['wa_number'] ?? '') ?>" placeholder="6281234567890">
                <small>Kosongkan untuk menggunakan nomor default</small>
            </div>
            <div class="form-group">
                <label>Link Demo</label>
                <input type="text" name="demo_link" value="<?= htmlspecialchars($old_data['demo_link'] ?? $edit_product['demo_link'] ?? '') ?>" placeholder="Contoh: demo/1/">
            </div>
            <div class="form-group">
                <label>Gambar Produk</label>
                <input type="file" name="product_image" id="product_image" accept="image/*">
                <small>Upload gambar (JPG, PNG, GIF, WEBP) max 5MB</small>
                
                <!-- Preview untuk upload baru -->
                <img id="preview" class="preview-image" style="display: none;">
                
                <!-- Tampilkan gambar saat ini -->
                <?php if ($edit_product && !empty($edit_product['image_url'])): ?>
                    <div class="current-image">
                        <strong style="display: block; margin-bottom: 0.25rem;">Gambar saat ini:</strong>
                        <?php 
                        $image_path = $edit_product['image_url'];
                        // Cek jika path adalah uploads/
                        if (strpos($image_path, 'uploads/') === 0) {
                            $full_path = __DIR__ . '/../' . $image_path;
                            if (file_exists($full_path)) {
                                echo '<img src="' . htmlspecialchars($image_path) . '" alt="Current Image">';
                            } else {
                                echo '<img src="https://placehold.co/150x150/cccccc/white?text=File+Not+Found">';
                                echo '<br><small style="color:red;">File tidak ditemukan di: ' . htmlspecialchars($full_path) . '</small>';
                            }
                        } else {
                            echo '<img src="' . htmlspecialchars($image_path) . '" alt="Current Image" onerror="this.src=\'https://placehold.co/150x150/cccccc/white?text=Error\'">';
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Deskripsi</label>
                <textarea name="description" rows="4"><?= htmlspecialchars($old_data['description'] ?? $edit_product['description'] ?? '') ?></textarea>
            </div>
        </div>
        <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
            <a href="products.php" class="btn-back">Batal</a>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Tabel Produk -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>WA</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($products) > 0): ?>
                <?php while($p = mysqli_fetch_assoc($products)): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td>
                        <?php 
                        // ===== PERBAIKAN TOTAL TAMPILAN GAMBAR =====
                        $image_path = $p['image_url'] ?? '';
                        $image_display = false;
                        $image_src = '';
                        
                        // Log untuk debugging
                        error_log("Product ID: " . $p['id'] . " - Image Path: " . $image_path);
                        
                        // CASE 1: Path dimulai dengan 'uploads/'
                        if (strpos($image_path, 'uploads/') === 0) {
                            // Cek di beberapa lokasi
                            $possible_paths = [
                                __DIR__ . '/../' . $image_path,  // ../uploads/nama.jpg
                                __DIR__ . '/' . $image_path,      // admin/uploads/nama.jpg
                                $_SERVER['DOCUMENT_ROOT'] . '/' . $image_path // /var/www/html/uploads/nama.jpg
                            ];
                            
                            foreach ($possible_paths as $path) {
                                if (file_exists($path)) {
                                    $image_display = true;
                                    $image_src = $image_path; // Gunakan path relatif
                                    error_log("Image found at: " . $path);
                                    break;
                                }
                            }
                            
                            // Jika tidak ditemukan, coba cari file dengan nama yang sama di folder uploads
                            if (!$image_display) {
                                $filename = basename($image_path);
                                $upload_dir = __DIR__ . '/../uploads/';
                                if (file_exists($upload_dir . $filename)) {
                                    $image_display = true;
                                    $image_src = 'uploads/' . $filename;
                                    error_log("Image found as: " . $upload_dir . $filename);
                                }
                            }
                        }
                        // CASE 2: Path adalah URL lengkap
                        elseif (filter_var($image_path, FILTER_VALIDATE_URL)) {
                            $image_display = true;
                            $image_src = $image_path;
                        }
                        // CASE 3: Path kosong atau tidak valid
                        else {
                            $image_display = false;
                            $image_src = 'https://placehold.co/50x50/cccccc/white?text=No+Img';
                        }
                        
                        // Tampilkan gambar
                        if ($image_display && !empty($image_src)) {
                            echo '<img src="' . htmlspecialchars($image_src) . '" class="product-image" alt="' . htmlspecialchars($p['name']) . '" onerror="this.src=\'https://placehold.co/50x50/cccccc/white?text=Error\'">';
                        } else {
                            echo '<img src="https://placehold.co/50x50/cccccc/white?text=No+Img" class="product-image" alt="No Image">';
                        }
                        
                        // DEBUG: Tampilkan path (HAPUS setelah berhasil)
                        // echo '<br><small style="font-size:9px;color:#999;">' . htmlspecialchars($image_path) . '</small>';
                        ?>
                    </td>
                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                    <td><?= htmlspecialchars($p['category_name']) ?></td>
                    <td><?= htmlspecialchars($p['price']) ?></td>
                    <td><?= !empty($p['wa_number']) ? htmlspecialchars($p['wa_number']) : '-' ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="?edit=<?= $p['id'] ?>" class="btn-warning" style="font-size: 0.9rem;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus produk ini?')">
                                <input type="hidden" name="product_action" value="delete">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn-danger" style="font-size: 0.9rem;">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="padding: 2rem; text-align: center; color: #64748b;">
                        <i class="fas fa-inbox" style="font-size: 2rem;"></i>
                        <p>Belum ada produk. <?= empty($search) ? 'Silakan tambah produk terlebih dahulu.' : 'Tidak ada produk yang cocok dengan pencarian.' ?></p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Preview gambar
    const fileInput = document.getElementById('product_image');
    const preview = document.getElementById('preview');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });
    }
</script>

<?php 
// --- MENUTUP RESOURCE STATEMENT YANG TERBUKA ---
if (isset($stmt_cat)) mysqli_stmt_close($stmt_cat);
if (isset($stmt_all)) mysqli_stmt_close($stmt_all);
if (isset($stmt_search)) mysqli_stmt_close($stmt_search);

include 'includes/footer.php'; 
?>