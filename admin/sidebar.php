<?php
// sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* CSS to make sidebar fixed */
    #accordionSidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 1000;
        overflow-y: auto;
        width: 224px;
        /* Standard SB Admin 2 sidebar width */
    }

    /* Adjust content wrapper to not be hidden behind the fixed sidebar */
    #content-wrapper {
        margin-left: 224px;
        width: calc(100% - 224px);
    }

    /* Handle toggled state if applicable */
    .sidebar.toggled {
        width: 104px !important;
    }

    .sidebar.toggled+#content-wrapper {
        margin-left: 104px;
        width: calc(100% - 104px);
    }

    @media (max-width: 768px) {
        #accordionSidebar {
            position: relative;
            height: auto;
            width: 100% !important;
        }

        #content-wrapper {
            margin-left: 0;
            width: 100%;
        }
    }
</style>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
            ☕
        </div>
        <div class="sidebar-brand-text mx-3">Admin Panel</div>
    </a>

    <hr class="sidebar-divider">

    <li class="nav-item <?= ($current_page == 'index.php') ? 'active' : '' ?>">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Data Management</div>

    <li class="nav-item <?= ($current_page == 'data_user.php') ? 'active' : '' ?>">
        <a class="nav-link" href="data_user.php">
            <i class="fas fa-fw fa-users"></i>
            <span>Data user</span>
        </a>
    </li>

    <li class="nav-item <?= ($current_page == 'tabel_penjual.php') ? 'active' : '' ?>">
        <a class="nav-link" href="tabel_penjual.php">
            <i class="fas fa-fw fa-store"></i>
            <span>Tabel Penjual</span></a>
    </li>

    <li class="nav-item <?= ($current_page == 'daftar-product.php') ? 'active' : '' ?>">
        <a class="nav-link" href="daftar-product.php">
            <i class="fas fa-fw fa-coffee"></i>
            <span>Menu Produk</span></a>
    </li>

    <li class="nav-item <?= ($current_page == 'reviews.php') ? 'active' : '' ?>">
        <a class="nav-link" href="reviews.php">
            <i class="fas fa-fw fa-star"></i>
            <span>Ulasan Produk</span></a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Transactions</div>

    <li class="nav-item <?= ($current_page == 'tabel_keranjang.php') ? 'active' : '' ?>">
        <a class="nav-link" href="tabel_keranjang.php">
            <i class="fas fa-fw fa-shopping-basket"></i>
            <span>Tabel Keranjang</span></a>
    </li>

    <li class="nav-item <?= ($current_page == 'tabel_checkout.php') ? 'active' : '' ?>">
        <a class="nav-link" href="tabel_checkout.php">
            <i class="fas fa-fw fa-shopping-cart"></i>
            <span>Tabel Checkout</span></a>
    </li>

    <li class="nav-item <?= ($current_page == 'riwayat_transaksi.php') ? 'active' : '' ?>">
        <a class="nav-link" href="riwayat_transaksi.php">
            <i class="fas fa-fw fa-history"></i>
            <span>Riwayat Transaksi</span></a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Analytics</div>

    <li class="nav-item <?= ($current_page == 'charts.php') ? 'active' : '' ?>">
        <a class="nav-link" href="charts.php">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Grafik</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>