<?php
// ============================================================
// LOGOUT - LSP COACHPRO INDONESIA
// ============================================================

// Pastikan session dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus semua data session
$_SESSION = [];

// Hancurkan session
session_destroy();

// Redirect ke halaman login admin
header('Location: admin/login.php');
exit;
?>