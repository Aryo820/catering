<?php
$page_title = 'Edit Invoice';
require_once '../../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['error'] = 'Invoice tidak ditemukan!';
    header('Location: index.php');
    exit;
}

// Ambil data invoice
$stmt = mysqli_prepare($conn, "SELECT * FROM invoices WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$invoice = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$invoice) {
    $_SESSION['error'] = 'Invoice tidak ditemukan!';
    header('Location: index.php');
    exit;
}

// Ambil produk
$products = [];
$stmt_prod = mysqli_prepare($conn, "SELECT id, name, price, description FROM products ORDER BY name");
mysqli_stmt_execute($stmt_prod);
$prod_result = mysqli_stmt_get_result($stmt_prod);
while ($row = mysqli_fetch_assoc($prod_result)) {
    $products[] = $row;
}
mysqli_stmt_close($stmt_prod);

// Proses update
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = trim($_POST['client_name'] ?? '');
    $client_email = trim($_POST['client_email'] ?? '');
    $client_phone = trim($_POST['client_phone'] ?? '');
    $client_address = trim($_POST['client_address'] ?? '');
    $service_name = trim($_POST['service_name'] ?? '');
    $service_description = trim($_POST['service_description'] ?? '');
    $guide_content = trim($_POST['guide_content'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $product_id = (int)($_POST['product_id'] ?? 0);

    $price_raw = $_POST['price'] ?? '0';
    $amount = (float)preg_replace('/[^0-9]/', '', $price_raw);
    $tax = isset($_POST['tax']) ? (float)$_POST['tax'] : 0;
    $discount = isset($_POST['discount']) ? (float)$_POST['discount'] : 0;
    $issue_date = $_POST['issue_date'] ?? $invoice['issue_date'];
    $due_date = $_POST['due_date'] ?? $invoice['due_date'];
    $notes = trim($_POST['notes'] ?? '');
    $status = $_POST['status'] ?? $invoice['status'];

    $errors = [];
    if ($client_name === '') $errors[] = 'Nama klien wajib diisi!';
    if ($service_name === '') $errors[] = 'Nama layanan wajib diisi!';
    if ($amount <= 0) $errors[] = 'Jumlah wajib diisi dan harus lebih dari 0!';

    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    } else {
        $total = $amount + $tax - $discount;
        if ($total < 0) $total = 0;

        $stmt_update = mysqli_prepare($conn, "UPDATE invoices SET
            client_name = ?, client_email = ?, client_phone = ?, client_address = ?,
            service_name = ?, service_description = ?, guide_content = ?, schedule = ?,
            amount = ?, tax = ?, discount = ?, total = ?,
            status = ?, issue_date = ?, due_date = ?, notes = ?, product_id = ?
            WHERE id = ?");
        mysqli_stmt_bind_param(
            $stmt_update,
            "sssssssssddddssssi",
            $client_name, $client_email, $client_phone, $client_address,
            $service_name, $service_description, $guide_content, $schedule,
            $amount, $tax, $discount, $total,
            $status, $issue_date, $due_date, $notes, $product_id,
            $id
        );
        if (mysqli_stmt_execute($stmt_update)) {
            $success = 'Invoice berhasil diupdate!';
            mysqli_stmt_close($stmt_update);
            header("refresh:2;url=index.php");
            exit;
        } else {
            $error = 'Gagal mengupdate invoice: ' . mysqli_error($conn);
            mysqli_stmt_close($stmt_update);
        }
    }
}

include '../includes/header.php';
?>

<!-- Form Edit (sama seperti create, tapi data diisi dari $invoice) -->

<?php include '../includes/footer.php'; ?>