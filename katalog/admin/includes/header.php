<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= $page_title ?? 'Admin Panel' ?> - LSP COACHPRO INDONESIA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ========================================= */
        /* TEMA MERCHANT PREMIUM (GOLD & DEEP BLUE)   */
        /* ========================================= */
        :root {
            --primary: #1f2462;       /* Deep Navy dari LSP */
            --primary-light: #2a2f7a;
            --gold: #e8b830;          /* Warna Emas Sertifikasi */
            --gold-light: #f5d76e;
            --bg-body: #f4f6fb;
            --card-shadow: 0 12px 30px rgba(31, 36, 98, 0.08);
            --sidebar-width: 270px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Admin Layout */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            position: relative;
        }
        
        /* ========================================= */
        /* SIDEBAR MERCHANT PREMIUM                  */
        /* ========================================= */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(160deg, #0b0f22 0%, #1f2462 100%);
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            transform: translateX(-100%);
            box-shadow: 4px 0 20px rgba(0,0,0,0.2);
        }
        .admin-sidebar.open {
            transform: translateX(0);
        }
        @media (min-width: 769px) {
            .admin-sidebar { transform: translateX(0) !important; }
        }
        
        /* Sidebar Scrollbar */
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-track { background: #1a1f3a; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: var(--gold); border-radius: 4px; }
        
        /* Header Sidebar dengan Logo */
        .sidebar-header {
            padding: 1.8rem 1.5rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 1rem;
            text-align: center;
        }
        .sidebar-header .logo-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.6rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
        }
        .sidebar-header .logo-icon i {
            color: var(--gold);
            font-size: 1.8rem;
        }
        .sidebar-header .logo-icon span {
            background: linear-gradient(135deg, #fff, var(--gold-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .sidebar-header p {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.5);
            margin-top: 0.2rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        /* Mini Profile di Sidebar */
        .sidebar-profile {
            padding: 0.8rem 1.5rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .sidebar-profile .avatar {
            width: 48px;
            height: 48px;
            background: var(--gold);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            box-shadow: 0 4px 12px rgba(232, 184, 48, 0.3);
        }
        .sidebar-profile .info {
            flex: 1;
        }
        .sidebar-profile .info .name {
            font-weight: 600;
            font-size: 0.95rem;
            color: white;
        }
        .sidebar-profile .info .role {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.5);
        }
        
        /* Menu Sidebar */
        .sidebar-menu {
            list-style: none;
            padding: 0 0.8rem;
        }
        .sidebar-menu li {
            margin-bottom: 0.2rem;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.7rem 1rem;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            border-radius: 14px;
            transition: all 0.25s ease;
            font-weight: 500;
            font-size: 0.9rem;
            position: relative;
        }
        .sidebar-menu a i {
            width: 24px;
            font-size: 1.1rem;
            color: rgba(255,255,255,0.4);
            transition: color 0.25s;
        }
        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.07);
            color: white;
            transform: translateX(4px);
        }
        .sidebar-menu a:hover i {
            color: var(--gold);
        }
        .sidebar-menu li.active a {
            background: linear-gradient(135deg, rgba(232, 184, 48, 0.15), rgba(232, 184, 48, 0.05));
            color: white;
            box-shadow: inset 3px 0 0 var(--gold);
        }
        .sidebar-menu li.active a i {
            color: var(--gold);
        }
        
        .sidebar-divider {
            height: 1px;
            background: rgba(255,255,255,0.06);
            margin: 0.8rem 1.5rem;
        }
        
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin-top: 1rem;
            font-size: 0.65rem;
            color: rgba(255,255,255,0.3);
            text-align: center;
        }
        .sidebar-footer strong { color: rgba(255,255,255,0.6); }
        
        /* Overlay Mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 999;
            transition: all 0.3s ease;
        }
        .sidebar-overlay.active { display: block; }
        
        /* ========================================= */
        /* MAIN CONTENT - MERCHANT STYLE             */
        /* ========================================= */
        .admin-main {
            flex: 1;
            margin-left: 0;
            padding: 20px 30px;
            transition: margin-left 0.3s ease;
            width: 100%;
            background: var(--bg-body);
        }
        @media (min-width: 769px) {
            .admin-main { margin-left: var(--sidebar-width); }
        }
        
        /* TOP BAR - Glassmorphism */
        .top-bar {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 0.8rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(31, 36, 98, 0.04);
            border: 1px solid rgba(255,255,255,0.8);
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }
        .page-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .page-title i { color: var(--gold); }
        
        /* User Info di Top Bar */
        .user-info {
            display: flex;
            align-items: center;
            gap: 1.2rem;
        }
        .user-info .user-badge {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: var(--bg-body);
            padding: 0.3rem 1rem 0.3rem 0.5rem;
            border-radius: 40px;
            border: 1px solid rgba(255,255,255,0.5);
        }
        .user-info .user-badge i {
            color: var(--gold);
            font-size: 1.2rem;
        }
        .user-info .user-badge span {
            font-weight: 500;
            font-size: 0.85rem;
            color: var(--primary);
        }
        .btn-logout {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 0.3rem 1.2rem;
            border-radius: 40px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        
        .mobile-toggle {
            display: block;
            background: rgba(255,255,255,0.6);
            border: 1px solid rgba(255,255,255,0.8);
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--primary);
            padding: 0.5rem 0.7rem;
            border-radius: 12px;
            transition: 0.2s;
            backdrop-filter: blur(4px);
        }
        .mobile-toggle:hover { background: white; }
        @media (min-width: 769px) {
            .mobile-toggle { display: none; }
        }
        
        /* ========================================= */
        /* STATS CARDS - MERCHANT STYLE              */
        /* ========================================= */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.5);
        }
        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 35px -10px rgba(31, 36, 98, 0.15);
            border-color: rgba(232, 184, 48, 0.2);
        }
        .stat-info h3 { 
            font-size: 0.75rem; 
            color: #8ba0ae; 
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-info .number { 
            font-size: 2rem; 
            font-weight: 800; 
            color: var(--primary); 
            margin-top: 0.2rem;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #eef1ff 0%, #dbeafe 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-icon i { 
            font-size: 1.6rem; 
            color: var(--gold); 
        }
        
        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.8rem;
            margin-bottom: 2rem;
        }
        .dashboard-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255,255,255,0.5);
            transition: all 0.3s ease;
        }
        .dashboard-card:hover {
            box-shadow: 0 20px 35px -10px rgba(31, 36, 98, 0.1);
        }
        .dashboard-card h3 {
            font-size: 1rem;
            margin-bottom: 1.2rem;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .dashboard-card h3 i { color: var(--gold); }
        .chart-container {
            position: relative;
            height: 240px;
        }
        
        /* Table */
        .recent-table {
            width: 100%;
            border-collapse: collapse;
        }
        .recent-table th {
            padding: 0.6rem 0.8rem;
            text-align: left;
            color: #8ba0ae;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #f1f5f9;
        }
        .recent-table td {
            padding: 0.7rem 0.8rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
            color: #1e293b;
        }
        .recent-table tr:hover td { background: #fafbff; }
        .badge {
            background: #eef1ff;
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.7rem;
            color: var(--primary);
            font-weight: 600;
        }
        
        /* Menu Grid */
        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 0.5rem;
        }
        .menu-item {
            background: white;
            padding: 1.2rem;
            border-radius: 16px;
            text-align: center;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.25s ease;
            box-shadow: var(--card-shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.6rem;
            border: 1px solid rgba(255,255,255,0.5);
        }
        .menu-item i { 
            font-size: 1.8rem; 
            color: var(--gold);
        }
        .menu-item span {
            font-weight: 600;
            font-size: 0.85rem;
        }
        .menu-item:hover { 
            background: linear-gradient(135deg, #fcf9f0, #f5f0e0);
            transform: translateY(-4px);
            border-color: var(--gold);
            box-shadow: 0 12px 25px -8px rgba(232, 184, 48, 0.15);
        }
        
        /* Alert & Form */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }
        .alert-success {
            background: #e6f7ed;
            color: #0d6632;
            border: 1px solid #b8e6cc;
        }
        .alert-error {
            background: #fee9e9;
            color: #991b1b;
            border: 1px solid #fccaca;
        }
        
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(255,255,255,0.5);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.2rem;
        }
        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
            color: var(--primary);
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 0.9rem;
            transition: 0.2s;
            background: white;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(232, 184, 48, 0.1);
        }
        button {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 40px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.2s;
            box-shadow: 0 4px 12px rgba(31, 36, 98, 0.2);
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(31, 36, 98, 0.25);
        }
        .btn-back {
            background: #e2e8f0;
            color: #475569;
            margin-left: 0.5rem;
            text-decoration: none;
            padding: 0.6rem 1.5rem;
            border-radius: 40px;
            display: inline-block;
            font-weight: 600;
            transition: 0.2s;
        }
        .btn-back:hover { background: #cbd5e1; transform: translateY(-2px); }
        
        /* ========== RESPONSIVE ========== */
        @media (max-width: 992px) {
            .dashboard-grid { grid-template-columns: 1fr; gap: 1.2rem; }
            .admin-main { padding: 15px; }
        }
        @media (max-width: 768px) {
            .stats { gap: 0.8rem; }
            .stat-card { padding: 1rem; }
            .stat-icon { width: 40px; height: 40px; }
            .stat-icon i { font-size: 1.2rem; }
            .stat-info .number { font-size: 1.5rem; }
            .menu { grid-template-columns: 1fr; gap: 0.8rem; }
            .top-bar { flex-direction: column; align-items: stretch; gap: 0.8rem; }
            .user-info { justify-content: space-between; }
            .form-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 480px) {
            .admin-main { padding: 10px; }
            .dashboard-card { padding: 1rem; }
            .recent-table th, .recent-table td { padding: 0.4rem; font-size: 0.7rem; }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include 'sidebar.php'; ?>
    
    <!-- Overlay untuk mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="admin-main">
        <div class="top-bar">
            <div class="top-bar-left">
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="page-title">
                    <i class="fas fa-certificate"></i> <?= $page_title ?? 'Dashboard' ?>
                </div>
            </div>
            <div class="user-info">
                <div class="user-badge">
                    <i class="fas fa-user-circle"></i>
                    <span><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
                </div>
                <a href="../logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>