<?php
// Logic for dynamic navbar
$isLoggedIn = !empty($_SESSION['id_user']);
$id_user = $isLoggedIn ? $_SESSION['id_user'] : null;

$username = '';
$cart_count = 0;

if ($isLoggedIn) {
    // Fetch username
    $nav_query_user = "SELECT username FROM tabel_user WHERE id_user = '$id_user'";
    $nav_result_user = mysqli_query($koneksi, $nav_query_user);
    if ($nav_result_user) {
        $nav_user_data = mysqli_fetch_assoc($nav_result_user);
        $username = $nav_user_data ? $nav_user_data['username'] : '';
    }

    // Fetch cart count
    $nav_q_cart = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tabel_keranjang WHERE id_user = '$id_user'");
    if ($nav_q_cart) {
        $nav_cart_data = mysqli_fetch_assoc($nav_q_cart);
        $cart_count = $nav_cart_data ? $nav_cart_data['total'] : 0;
    }
}

// Determine active page helper (optional but useful)
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
    <div class="container">
        <a class="navbar-brand" href="utama.php">Coffee<small>Blend</small></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="oi oi-menu"></span> Menu
        </button>
        <div class="collapse navbar-collapse" id="ftco-nav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item <?= ($current_page == 'utama.php') ? 'active' : '' ?>"><a href="utama.php" class="nav-link">Home</a></li>
                <li class="nav-item <?= ($current_page == 'menu.php') ? 'active' : '' ?>"><a href="menu.php" class="nav-link">Menu</a></li>
                <li class="nav-item <?= ($current_page == 'services.php') ? 'active' : '' ?>"><a href="services.php" class="nav-link">Services</a></li>
                <li class="nav-item <?= ($current_page == 'blog.php') ? 'active' : '' ?>"><a href="blog.php" class="nav-link">Blog</a></li>
                <li class="nav-item <?= ($current_page == 'about.php') ? 'active' : '' ?>"><a href="about.php" class="nav-link">About</a></li>
                <li class="nav-item <?= ($current_page == 'contact.php') ? 'active' : '' ?>"><a href="contact.php" class="nav-link">Contact</a></li>

                <?php if ($isLoggedIn): ?>
                    <li class="nav-item dropdown <?= (in_array($current_page, ['keranjang.php', 'riwayat_pemesanan.php'])) ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown04" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Shop</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown04">
                            <a class="dropdown-item" href="menu.php">Shop</a>
                            <a class="dropdown-item" href="keranjang.php">Cart</a>
                            <a class="dropdown-item" href="riwayat_pemesanan.php">Pesanan Saya</a>
                        </div>
                    </li>
                    <li class="nav-item"><span class="nav-link" style="color: #fff !important;">Hi, <?= htmlspecialchars($username) ?></span></li>
                    <li class="nav-item"><a href="logout.php?redirect=utama.php" class="nav-link">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="login.php?redirect=<?= urlencode($current_page) ?>" class="nav-link">Login</a></li>
                <?php endif; ?>

                <li class="nav-item cart <?= ($current_page == 'keranjang.php') ? 'active' : '' ?>">
                    <a href="keranjang.php" class="nav-link">
                        <span class="icon icon-shopping_cart"></span>
                        <?php if ($cart_count > 0): ?>
                            <span class="bag d-flex justify-content-center align-items-center"><small><?= $cart_count ?></small></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>