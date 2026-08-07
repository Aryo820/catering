<?php
$page_title = 'Kelola Kategori';
require_once '../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

// --- PROSES FORM (TAMBAH / EDIT) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_action'])) {
    $cat_name = clean_input($_POST['cat_name']);
    
    if (empty($cat_name)) {
        $_SESSION['error'] = "Nama kategori wajib diisi!";
    } else {
        if ($_POST['category_action'] === 'add') {
            // --- PERBAIKAN KEAMANAN: Cek duplikat dengan Prepared Statement ---
            $stmt_check = mysqli_prepare($conn, "SELECT id FROM categories WHERE name = ?");
            mysqli_stmt_bind_param($stmt_check, "s", $cat_name);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);
            
            if (mysqli_num_rows($result_check) > 0) {
                $_SESSION['error'] = "Kategori '$cat_name' sudah ada!";
            } else {
                // --- PERBAIKAN KEAMANAN: Insert dengan Prepared Statement ---
                $stmt_insert = mysqli_prepare($conn, "INSERT INTO categories (name) VALUES (?)");
                mysqli_stmt_bind_param($stmt_insert, "s", $cat_name);
                mysqli_stmt_execute($stmt_insert);
                mysqli_stmt_close($stmt_insert);
                $_SESSION['success'] = "Kategori berhasil ditambahkan!";
            }
            mysqli_stmt_close($stmt_check);
            
        } elseif ($_POST['category_action'] === 'edit') {
            $cat_id = (int)$_POST['cat_id'];
            
            // --- PERBAIKAN KEAMANAN: Cek duplikat dengan Prepared Statement ---
            $stmt_check = mysqli_prepare($conn, "SELECT id FROM categories WHERE name = ? AND id != ?");
            mysqli_stmt_bind_param($stmt_check, "si", $cat_name, $cat_id);
            mysqli_stmt_execute($stmt_check);
            $result_check = mysqli_stmt_get_result($stmt_check);
            
            if (mysqli_num_rows($result_check) > 0) {
                $_SESSION['error'] = "Kategori '$cat_name' sudah ada!";
            } else {
                // --- PERBAIKAN KEAMANAN: Update dengan Prepared Statement ---
                $stmt_update = mysqli_prepare($conn, "UPDATE categories SET name = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt_update, "si", $cat_name, $cat_id);
                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);
                $_SESSION['success'] = "Kategori berhasil diupdate!";
            }
            mysqli_stmt_close($stmt_check);
        }
    }
    header('Location: categories.php');
    exit;
}

// --- HAPUS KATEGORI ---
if (isset($_GET['delete'])) {
    $cat_id = (int)$_GET['delete'];
    
    // --- PERBAIKAN KEAMANAN: Cek apakah kategori memiliki produk ---
    $stmt_check_prod = mysqli_prepare($conn, "SELECT id FROM products WHERE category_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt_check_prod, "i", $cat_id);
    mysqli_stmt_execute($stmt_check_prod);
    $result_check_prod = mysqli_stmt_get_result($stmt_check_prod);
    
    if (mysqli_num_rows($result_check_prod) > 0) {
        $_SESSION['error'] = "Kategori tidak bisa dihapus karena masih memiliki produk!";
        mysqli_stmt_close($stmt_check_prod);
    } else {
        mysqli_stmt_close($stmt_check_prod);
        // --- PERBAIKAN KEAMANAN: Delete dengan Prepared Statement ---
        $stmt_delete = mysqli_prepare($conn, "DELETE FROM categories WHERE id = ?");
        mysqli_stmt_bind_param($stmt_delete, "i", $cat_id);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);
        $_SESSION['success'] = "Kategori berhasil dihapus!";
    }
    header('Location: categories.php');
    exit;
}

// --- AMBIL DATA KATEGORI UNTUK EDIT ---
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    
    // --- PERBAIKAN KEAMANAN: Select dengan Prepared Statement ---
    $stmt_edit = mysqli_prepare($conn, "SELECT * FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt_edit, "i", $edit_id);
    mysqli_stmt_execute($stmt_edit);
    $result_edit = mysqli_stmt_get_result($stmt_edit);
    $edit_category = mysqli_fetch_assoc($result_edit);
    mysqli_stmt_close($stmt_edit);
    
    if (!$edit_category) {
        $_SESSION['error'] = "Kategori tidak ditemukan!";
        header('Location: categories.php');
        exit;
    }
}

// --- AMBIL SEMUA KATEGORI (DENGAN PREPARED STATEMENT) ---
$stmt_cat = mysqli_prepare($conn, "SELECT c.*, COUNT(p.id) as product_count 
                                    FROM categories c 
                                    LEFT JOIN products p ON c.id = p.category_id 
                                    GROUP BY c.id 
                                    ORDER BY c.id DESC");
mysqli_stmt_execute($stmt_cat);
$categories = mysqli_stmt_get_result($stmt_cat);

$success_msg = $_SESSION['success'] ?? null;
$error_msg = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

include 'includes/header.php';
?>

<!-- Header Actions -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <h2 style="font-size: 1.3rem;"><i class="fas fa-tags"></i> Manajemen Kategori</h2>
    <?php if (!$edit_category): ?>
        <a href="?add" style="background: #10b981; color: white; padding: 0.5rem 1rem; border-radius: 30px; text-decoration: none;">
            <i class="fas fa-plus"></i> Tambah Kategori
        </a>
    <?php endif; ?>
</div>

<!-- Alert Messages -->
<?php if ($success_msg): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?></div>
<?php endif; ?>
<?php if ($error_msg): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_msg) ?></div>
<?php endif; ?>

<!-- Form Tambah/Edit Kategori -->
<?php if (isset($_GET['add']) || $edit_category): ?>
<div class="form-card">
    <h3 style="margin-bottom: 1rem;"><?= $edit_category ? '✏️ Edit Kategori' : '➕ Tambah Kategori Baru' ?></h3>
    <form method="POST">
        <input type="hidden" name="category_action" value="<?= $edit_category ? 'edit' : 'add' ?>">
        <?php if ($edit_category): ?>
            <input type="hidden" name="cat_id" value="<?= $edit_category['id'] ?>">
        <?php endif; ?>
        <div class="form-group">
            <label>Nama Kategori</label>
            <input type="text" name="cat_name" value="<?= htmlspecialchars($edit_category['name'] ?? '') ?>" required autofocus>
        </div>
        <button type="submit"><i class="fas fa-save"></i> Simpan</button>
        <a href="categories.php" class="btn-back">Batal</a>
    </form>
</div>
<?php endif; ?>

<!-- Tabel Kategori -->
<div style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8fafc;">
                <th style="padding: 0.75rem; text-align: left;">ID</th>
                <th style="padding: 0.75rem; text-align: left;">Nama Kategori</th>
                <th style="padding: 0.75rem; text-align: left;">Jumlah Produk</th>
                <th style="padding: 0.75rem; text-align: left;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($categories) > 0): ?>
                <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                <tr>
                    <td style="padding: 0.75rem; border-bottom: 1px solid #e2e8f0;"><?= $cat['id'] ?></td>
                    <td style="padding: 0.75rem; border-bottom: 1px solid #e2e8f0;"><?= htmlspecialchars($cat['name']) ?></td>
                    <td style="padding: 0.75rem; border-bottom: 1px solid #e2e8f0;">
                        <span style="background: #e2e8f0; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem;">
                            <?= $cat['product_count'] ?> produk
                        </span>
                    </td>
                    <td style="padding: 0.75rem; border-bottom: 1px solid #e2e8f0;">
                        <a href="?edit=<?= $cat['id'] ?>" style="color: #f59e0b; text-decoration: none; margin-right: 10px;"><i class="fas fa-edit"></i> Edit</a>
                        <?php if ($cat['product_count'] == 0): ?>
                            <a href="?delete=<?= $cat['id'] ?>" style="color: #ef4444; text-decoration: none;" onclick="return confirm('Yakin hapus kategori ini?')"><i class="fas fa-trash"></i> Hapus</a>
                        <?php else: ?>
                            <span style="color: #94a3b8; cursor: not-allowed;" title="Kategori memiliki produk, tidak bisa dihapus"><i class="fas fa-trash"></i> Hapus</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="padding: 2rem; text-align: center; color: #64748b;">
                        <i class="fas fa-inbox" style="font-size: 2rem;"></i>
                        <p>Belum ada kategori. Silakan tambah kategori terlebih dahulu.</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php 
// --- TUTUP STATEMENT YANG TERBUKA ---
mysqli_stmt_close($stmt_cat);
include 'includes/footer.php'; 
?>