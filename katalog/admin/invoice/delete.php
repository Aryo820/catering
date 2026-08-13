<?php
require_once '../../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

// Hanya menerima POST (anti-CSRF: GET link tidak bisa memicu hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM invoices WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['success'] = 'Invoice berhasil dihapus!';
    } else {
        $_SESSION['error'] = 'ID invoice tidak valid!';
    }
}

header('Location: index.php');
exit;