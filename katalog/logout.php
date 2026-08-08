<?php
// ============================================================
// LOGOUT - LSP COACHPRO INDONESIA
// ============================================================

// Load config (memulai session + definisikan SITE_URL)
require_once __DIR__ . '/config.php';

// Hapus semua data session
$_SESSION = [];

// Hancurkan session
session_destroy();

// Redirect absolut ke login admin (benar dari folder mana pun)
header('Location: ' . SITE_URL . 'admin/login.php');
exit;
