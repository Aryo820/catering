<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = 'Daftar Invoice - LSP COACHPRO INDONESIA';
require_once '../../config.php';

/** @var mysqli $conn */
global $conn;

require_login();

// ============================================================
// FUNGSI HELPER (Hanya yang belum ada di config.php)
// ============================================================

function invoice_status_badge($status) {
    $colors = [
        'draft' => ['bg' => '#f1f5f9', 'color' => '#64748b'],
        'sent' => ['bg' => '#eef1ff', 'color' => '#1f2462'],
        'paid' => ['bg' => '#d1fae5', 'color' => '#065f46'],
        'overdue' => ['bg' => '#fee2e2', 'color' => '#991b1b']
    ];
    $color = $colors[$status] ?? ['bg' => '#f1f5f9', 'color' => '#64748b'];
    
    return '<span style="background: ' . $color['bg'] . '; color: ' . $color['color'] . '; padding: 2px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: capitalize;">' . ucfirst($status) . '</span>';
}

// ============================================================
// AMBIL SEMUA INVOICE
// ============================================================
$invoices = [];

// Periksa apakah tabel invoices ada
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'invoices'");
if (mysqli_num_rows($check_table) == 0) {
    die("Tabel 'invoices' belum ada di database. Silakan buat tabel terlebih dahulu.");
}

$stmt = mysqli_prepare($conn, "SELECT i.*, p.name as product_name 
                                FROM invoices i 
                                LEFT JOIN products p ON i.product_id = p.id 
                                ORDER BY i.id DESC");
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $invoices[] = $row;
}
mysqli_stmt_close($stmt);

// Statistik
$total_invoice = count($invoices);
$total_amount = 0;
$paid_count = 0;
$pending_count = 0;
foreach ($invoices as $inv) {
    $total_amount += $inv['total'];
    if ($inv['status'] == 'paid') $paid_count++;
    if (in_array($inv['status'], ['draft', 'sent'])) $pending_count++;
}

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.5rem;">
    <h2 style="font-size: 1.3rem; margin: 0; color: #1f2462;">
        <i class="fas fa-file-invoice" style="color: #e8b830;"></i> Daftar Invoice
    </h2>
    <a href="create.php" style="background: #1f2462; color: white; padding: 0.5rem 1.2rem; border-radius: 30px; text-decoration: none; display: inline-block;">
        <i class="fas fa-plus"></i> Invoice Baru
    </a>
</div>

<!-- Statistik Premium -->
<div class="stats">
    <div class="stat-card" style="border-top: 4px solid #1f2462;">
        <div class="stat-info">
            <h3>Total Invoice</h3>
            <div class="number" style="color: #1f2462;"><?= $total_invoice ?></div>
        </div>
        <div class="stat-icon" style="background: #eef1ff;"><i class="fas fa-file-invoice" style="color: #1f2462;"></i></div>
    </div>
    <div class="stat-card" style="border-top: 4px solid #e8b830;">
        <div class="stat-info">
            <h3>Total Pendapatan</h3>
            <div class="number" style="color: #e8b830;"><?= format_rupiah($total_amount) ?></div>
        </div>
        <div class="stat-icon" style="background: #fcf9f0;"><i class="fas fa-money-bill-wave" style="color: #e8b830;"></i></div>
    </div>
    <div class="stat-card" style="border-top: 4px solid #10b981;">
        <div class="stat-info">
            <h3>Lunas</h3>
            <div class="number" style="color: #10b981;"><?= $paid_count ?></div>
        </div>
        <div class="stat-icon" style="background: #d1fae5;"><i class="fas fa-check-circle" style="color: #10b981;"></i></div>
    </div>
    <div class="stat-card" style="border-top: 4px solid #f59e0b;">
        <div class="stat-info">
            <h3>Pending</h3>
            <div class="number" style="color: #f59e0b;"><?= $pending_count ?></div>
        </div>
        <div class="stat-icon" style="background: #fef3c7;"><i class="fas fa-clock" style="color: #f59e0b;"></i></div>
    </div>
</div>

<!-- Tabel -->
<div style="background: white; border-radius: 20px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(31, 36, 98, 0.05); overflow-x: auto;">
    <table class="recent-table" style="width: 100%;">
        <thead>
            <tr>
                <th>#</th>
                <th>No. Invoice</th>
                <th>Klien</th>
                <th>Produk/Layanan</th>
                <th>Total</th>
                <th>Status</th>
                <th>Jatuh Tempo</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($invoices)): ?>
                <tr><td colspan="8" style="text-align: center; color: #94a3b8; padding: 2rem;">Belum ada invoice.</td></tr>
            <?php else: ?>
                <?php foreach ($invoices as $i => $inv): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td><strong style="color: #1f2462;"><?= htmlspecialchars($inv['invoice_number']) ?></strong></td>
                    <td><?= htmlspecialchars($inv['client_name']) ?></td>
                    <td>
                        <?php if ($inv['product_id'] > 0 && !empty($inv['product_name'])): ?>
                            <span class="badge" style="background: #eef1ff; color: #1f2462;">
                                <?= htmlspecialchars($inv['product_name']) ?>
                            </span>
                        <?php else: ?>
                            <span style="color: #6c7a8a;"><?= htmlspecialchars($inv['service_name']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 700; color: #1f2462;"><?= format_rupiah($inv['total']) ?></td>
                    <td><?= invoice_status_badge($inv['status']) ?></td>
                    <td><?= date('d/m/Y', strtotime($inv['due_date'])) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $inv['id'] ?>" style="color: #e8b830; margin-right: 5px;" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="../../public/invoice/view.php?link=<?= $inv['unique_link'] ?>" target="_blank" style="color: #1f2462; margin-right: 5px;" title="Lihat"><i class="fas fa-eye"></i></a>
                        <a href="../../pdf/generate.php?link=<?= $inv['unique_link'] ?>" target="_blank" style="color: #10b981; margin-right: 5px;" title="PDF"><i class="fas fa-file-pdf"></i></a>
                        <a href="delete.php?id=<?= $inv['id'] ?>" style="color: #dc2626;" onclick="return confirm('Yakin hapus?')" title="Hapus"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>