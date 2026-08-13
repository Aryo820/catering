<?php
$page_title = 'Buat Invoice Baru';
require_once '../../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

// ============================================================
// FUNGSI HELPER
// ============================================================

function generate_invoice_number()
{
    return 'INV-LSP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
}

function generate_invoice_link($length = 12)
{
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
}



// ============================================================
// AMBIL PRODUK UNTUK DROPDOWN
// ============================================================
$products = [];
$stmt_prod = mysqli_prepare($conn, "SELECT id, name, price, description FROM products ORDER BY name");
mysqli_stmt_execute($stmt_prod);
$prod_result = mysqli_stmt_get_result($stmt_prod);
while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}
mysqli_stmt_close($stmt_prod);

// ============================================================
// PROSES SIMPAN
// ============================================================
$error = '';
$success = '';
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $client_name = trim($_POST['client_name'] ?? '');
    $client_email = trim($_POST['client_email'] ?? '');
    $client_phone = trim($_POST['client_phone'] ?? '');
    $client_address = trim($_POST['client_address'] ?? '');
    $service_name = trim($_POST['service_name'] ?? '');
    $service_description = trim($_POST['service_description'] ?? '');
    $guide_content = trim($_POST['guide_content'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');

    $price_raw = $_POST['price'] ?? '0';
    $price_clean = preg_replace('/[^0-9]/', '', $price_raw);
    $amount = floatval($price_clean);

    $tax = isset($_POST['tax']) ? floatval($_POST['tax']) : 0;
    $discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0;
    $issue_date = $_POST['issue_date'] ?? date('Y-m-d');
    $due_date = $_POST['due_date'] ?? date('Y-m-d', strtotime('+30 days'));
    $notes = trim($_POST['notes'] ?? '');
    $status = $_POST['status'] ?? 'draft';

    $form_data = [
        'product_id' => $product_id,
        'client_name' => $client_name,
        'client_email' => $client_email,
        'client_phone' => $client_phone,
        'client_address' => $client_address,
        'service_name' => $service_name,
        'service_description' => $service_description,
        'guide_content' => $guide_content,
        'schedule' => $schedule,
        'amount' => $amount,
        'tax' => $tax,
        'discount' => $discount,
        'issue_date' => $issue_date,
        'due_date' => $due_date,
        'notes' => $notes,
        'status' => $status
    ];

    // Validasi
    $errors = [];
    if (!($client_name)) $errors[] = 'Nama klien wajib diisi!';
    if (!($service_name)) $errors[] = 'Nama layanan wajib diisi!';
    if ($amount <= 0) $errors[] = 'Jumlah wajib diisi dan harus lebih dari 0!';

    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    } else {
        $total = $amount + $tax - $discount;
        if ($total < 0) $total = 0;

        $invoice_number = generate_invoice_number();

        // Cek unik, maks 10 percobaan agar tidak hang jika tabrakan tak terduga
        $unique_link = generate_invoice_link(12);
        $link_ok = false;
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $stmt_cek = mysqli_prepare($conn, "SELECT id FROM invoices WHERE unique_link = ?");
            mysqli_stmt_bind_param($stmt_cek, "s", $unique_link);
            mysqli_stmt_execute($stmt_cek);
            $result_cek = mysqli_stmt_get_result($stmt_cek);
            $is_unique = mysqli_num_rows($result_cek) == 0;
            mysqli_stmt_close($stmt_cek);
            if ($is_unique) { $link_ok = true; break; }
            $unique_link = generate_invoice_link(12);
        }

        if (!$link_ok) {
            $error = 'Gagal membuat link unik invoice, coba lagi.';
        } else {
            $stmt_insert = mysqli_prepare($conn, "INSERT INTO invoices (
                        invoice_number, client_name, client_email, client_phone, client_address,
                        service_name, service_description, guide_content, schedule,
                        amount, tax, discount, total,
                        status, issue_date, due_date, notes, unique_link, product_id
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            mysqli_stmt_bind_param(
                $stmt_insert,
                "sssssssssddddsssssi",
                $invoice_number,
                $client_name,
                $client_email,
                $client_phone,
                $client_address,
                $service_name,
                $service_description,
                $guide_content,
                $schedule,
                $amount,
                $tax,
                $discount,
                $total,
                $status,
                $issue_date,
                $due_date,
                $notes,
                $unique_link,
                $product_id
            );

            if (mysqli_stmt_execute($stmt_insert)) {
                $success = 'Invoice berhasil dibuat!';
                mysqli_stmt_close($stmt_insert);
                $form_data = [];
                header("refresh:2;url=index.php");
            } else {
                $error = 'Gagal menyimpan invoice: ' . mysqli_error($conn);
                mysqli_stmt_close($stmt_insert);
            }
        }
    }
}


include '../includes/header.php';
?>

<div class="dashboard-card" style="border-top: 4px solid #e8b830;">
    <h3 style="color: #1f2462; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-plus-circle" style="color: #e8b830;"></i> Buat Invoice Baru
    </h3>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <!-- Kolom Kiri -->
            <div>
                <h4 style="color: #1f2462; border-bottom: 2px solid #1f2462; padding-bottom: 0.5rem;">
                    <i class="fas fa-user" style="color: #e8b830;"></i> Data Klien
                </h4>
                <div class="form-group">
                    <label>Nama Klien <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="client_name" required value="<?= htmlspecialchars($form_data['client_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="client_email" value="<?= htmlspecialchars($form_data['client_email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Telepon</label>
                    <input type="text" name="client_phone" value="<?= htmlspecialchars($form_data['client_phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="client_address" rows="3"><?= htmlspecialchars($form_data['client_address'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div>
                <h4 style="color: #1f2462; border-bottom: 2px solid #e8b830; padding-bottom: 0.5rem;">
                    <i class="fas fa-briefcase" style="color: #e8b830;"></i> Data Layanan & Jadwal
                </h4>
                <div class="form-group">
                    <label>Pilih Produk (Opsional)</label>
                    <select name="product_id" id="productSelect" onchange="fillProductData(this)">
                        <option value="0">-- Pilih Produk --</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Layanan <span style="color: #dc2626;">*</span></label>
                    <input type="text" name="service_name" id="serviceName" required value="<?= htmlspecialchars($form_data['service_name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Deskripsi Layanan</label>
                    <textarea name="service_description" id="serviceDesc" rows="3"><?= htmlspecialchars($form_data['service_description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Jadwal Program (Opsional)</label>
                    <textarea name="schedule" id="scheduleField" rows="2"><?= htmlspecialchars($form_data['schedule'] ?? '') ?></textarea>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Jumlah (Rp) <span style="color: #dc2626;">*</span></label>
                        <input type="text" name="price" id="servicePrice" required value="<?= htmlspecialchars($form_data['amount'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Pajak (Rp)</label>
                        <input type="number" name="tax" step="1000" value="<?= $form_data['tax'] ?? 0 ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Diskon (Rp)</label>
                    <input type="number" name="discount" step="1000" value="<?= $form_data['discount'] ?? 0 ?>">
                </div>
            </div>
        </div>

        <hr style="border-color: #e8b830;">
        <h4 style="color: #1f2462;">
            <i class="fas fa-book" style="color: #e8b830;"></i> Panduan Penggunaan
        </h4>
        <div class="form-group">
            <textarea name="guide_content" id="guideContent" rows="8"><?= htmlspecialchars($form_data['guide_content'] ?? '') ?></textarea>
        </div>
        <div style="margin-bottom: 1rem;">
            <button type="button" onclick="insertTemplate1()">Template 1</button>
            <button type="button" onclick="insertTemplate2()">Template 2</button>
            <button type="button" onclick="insertTemplate3()">Template 3</button>
            <button type="button" onclick="clearGuide()">Kosongkan</button>
        </div>

        <hr style="border-color: #e8b830;">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label>Tanggal Terbit</label>
                <input type="date" name="issue_date" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>Tanggal Jatuh Tempo</label>
                <input type="date" name="due_date" value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                </select>
            </div>
            <div class="form-group">
                <label>Catatan</label>
                <input type="text" name="notes" value="<?= htmlspecialchars($form_data['notes'] ?? '') ?>">
            </div>
        </div>

        <div style="text-align: right; margin-top: 1.5rem;">
            <a href="index.php" class="btn-back">Batal</a>
            <button type="submit" style="background: #1f2462; color: white; padding: 10px 25px; border-radius: 30px;">Simpan</button>
        </div>
    </form>
</div>

<script>
    function fillProductData(select) {
        const products = <?= json_encode($products) ?>;
        const id = parseInt(select.value);
        if (id === 0) {
            document.getElementById('serviceName').value = '';
            document.getElementById('serviceDesc').value = '';
            document.getElementById('servicePrice').value = '';
            document.getElementById('scheduleField').value = '';
            return;
        }
        const product = products.find(p => p.id === id);
        if (product) {
            document.getElementById('serviceName').value = product.name;
            document.getElementById('servicePrice').value = product.price.replace(/[^0-9]/g, '');
            document.getElementById('serviceDesc').value = product.description ?? '';
            const now = new Date();
            const start = new Date(now);
            start.setDate(now.getDate() + 14);
            const end = new Date(start);
            end.setDate(start.getDate() + 3);
            const opt = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('scheduleField').value =
                `Pelaksanaan Program: ${start.toLocaleDateString('id-ID', opt)} - ${end.toLocaleDateString('id-ID', opt)}
Waktu: 08.00 - 16.00 WIB
Lokasi: LSP COACHPRO INDONESIA (Online / Offline)`;
        }
    }

    function insertTemplate1() {
        document.getElementById('guideContent').value = `... (sama seperti sebelumnya) ...`;
    }

    function insertTemplate2() {
        document.getElementById('guideContent').value = `... (sama seperti sebelumnya) ...`;
    }

    function insertTemplate3() {
        document.getElementById('guideContent').value = `... (sama seperti sebelumnya) ...`;
    }

    function clearGuide() {
        if (confirm('Kosongkan konten panduan?')) document.getElementById('guideContent').value = '';
    }
</script>

<?php include '../includes/footer.php'; ?>