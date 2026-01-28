<?php
session_start();
require_once 'config.php';

// Session check removed to allow guest access
// if (!isset($_SESSION['id_user'])) {
//     header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
//     exit();
// }

// Initialize counts
$count_robusta = 0;
$count_arabika = 0;
$count_tea = 0;

if ($koneksi) {
    // Query Robusta
    $query_robusta = "SELECT p.*, k.nama_kategori FROM tabel_product p JOIN tabel_kategori k ON p.id_kategori = k.id_kategori WHERE k.nama_kategori = 'Robusta' ORDER BY p.id_product DESC";
    $result_robusta = mysqli_query($koneksi, $query_robusta);
    if ($result_robusta) {
        $count_robusta = mysqli_num_rows($result_robusta);
    }

    // Query Arabika
    $query_arabika = "SELECT p.*, k.nama_kategori FROM tabel_product p JOIN tabel_kategori k ON p.id_kategori = k.id_kategori WHERE k.nama_kategori = 'Arabika' ORDER BY p.id_product DESC";
    $result_arabika = mysqli_query($koneksi, $query_arabika);
    if ($result_arabika) {
        $count_arabika = mysqli_num_rows($result_arabika);
    }

    // Query Tea
    $query_tea = "SELECT p.*, k.nama_kategori FROM tabel_product p JOIN tabel_kategori k ON p.id_kategori = k.id_kategori WHERE k.nama_kategori = 'Tea' ORDER BY p.id_product DESC";
    $result_tea = mysqli_query($koneksi, $query_tea);
    if ($result_tea) {
        $count_tea = mysqli_num_rows($result_tea);
    }
} else {
    // If DB is down, we just show empty menus (handled by the counts being 0)
    // Optionally set an error flag for UI
    $db_error = "Database offline";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Coffee - Menu</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Great+Vibes" rel="stylesheet">
    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="css/ionicons.min.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .menu-img {
            cursor: pointer !important;
            display: block !important;
            min-height: 200px;
            text-decoration: none;
        }

        .menu-img:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <?php include 'partials/navbar.php'; ?>

    <section class="home-slider owl-carousel d-none d-md-block">
        <div class="slider-item" style="background-image: url(images/bg_3.jpg);">
            <div class="overlay"></div>
            <div class="container">
                <div class="row slider-text justify-content-center align-items-center">
                    <div class="col-md-7 col-sm-12 text-center ftco-animate">
                        <h1 class="mb-3 mt-5 bread">Our Menu</h1>
                        <p class="breadcrumbs"><span class="mr-2"><a href="utama.php">Home</a></span> <span>Menu</span></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Hero Static -->
    <section class="mobile-hero d-md-none" style="background-image: url(images/bg_3.jpg); height: 300px; background-size: cover; background-position: center; position: relative;">
        <div class="overlay" style="position: absolute; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5);"></div>
        <div class="container text-center d-flex align-items-center justify-content-center" style="height: 100%; position: relative; z-index: 2;">
            <div>
                <h1 class="mb-3 mt-5 bread" style="color: #fff; font-weight: 800; font-size: 30px;">Our Menu</h1>
                <p class="breadcrumbs" style="color: rgba(255,255,255,0.8);"><span class="mr-2"><a href="utama.php" style="color: #fff;">Home</a></span> <span>Menu</span></p>
            </div>
        </div>
    </section>

    <section class="ftco-menu mb-5 pb-5">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-md-7 heading-section text-center ftco-animate">
                    <span class="subheading">Discover</span>
                    <h2 class="mb-4">Our Products</h2>
                    <p>Explore the finest coffee beans and blends from BrewBeans Bandung.</p>
                </div>
            </div>

            <div class="row d-md-flex">
                <div class="col-lg-12 ftco-animate p-md-5">
                    <div class="row">
                        <div class="col-md-12 nav-link-wrap mb-5">
                            <div class="nav ftco-animate nav-pills justify-content-center" id="v-pills-tab" role="tablist">
                                <a class="nav-link active" id="v-pills-1-tab" data-toggle="pill" href="#v-pills-1" role="tab">ROBUSTA</a>
                                <a class="nav-link" id="v-pills-2-tab" data-toggle="pill" href="#v-pills-2" role="tab">ARABIKA</a>
                                <a class="nav-link" id="v-pills-3-tab" data-toggle="pill" href="#v-pills-3" role="tab">TEA</a>
                            </div>
                        </div>

                        <div class="col-md-12 d-flex align-items-center">
                            <div class="tab-content ftco-animate w-100" id="v-pills-tabContent">

                                <!-- ROBUSTA -->
                                <div class="tab-pane fade show active" id="v-pills-1" role="tabpanel">
                                    <div class="row">
                                        <?php if ($count_robusta > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($result_robusta)): ?>
                                                <div class="col-md-4 text-center">
                                                    <div class="menu-wrap">
                                                        <div class="menu-img img mb-4" data-href="product-detail.php?id=<?php echo (int)$row['id_product']; ?>" style="background-image: url(images/<?php echo htmlspecialchars($row['gambar']); ?>); background-size: cover; background-position: center; height: 200px; display: block; cursor: pointer;"></div>
                                                        <div class="text">
                                                            <h3><a href="product-detail.php?id=<?php echo (int)$row['id_product']; ?>"><?php echo htmlspecialchars($row['nama_product']); ?></a></h3>
                                                            <p><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                                                            <p class="price"><span>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?> / 250gr</span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <div class="col-md-12 text-center">
                                                <p>Tidak ada produk Robusta tersedia</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- ARABIKA -->
                                <div class="tab-pane fade" id="v-pills-2" role="tabpanel">
                                    <div class="row">
                                        <?php if ($count_arabika > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($result_arabika)): ?>
                                                <div class="col-md-4 text-center">
                                                    <div class="menu-wrap">
                                                        <div class="menu-img img mb-4" data-href="product-detail.php?id=<?php echo (int)$row['id_product']; ?>" style="background-image: url(images/<?php echo htmlspecialchars($row['gambar']); ?>); background-size: cover; background-position: center; height: 200px; display: block; cursor: pointer;"></div>
                                                        <div class="text">
                                                            <h3><a href="product-detail.php?id=<?php echo (int)$row['id_product']; ?>"><?php echo htmlspecialchars($row['nama_product']); ?></a></h3>
                                                            <p><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                                                            <p class="price"><span>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?> / 250gr</span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <div class="col-md-12 text-center">
                                                <p>Tidak ada produk Arabika tersedia</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- TEA -->
                                <div class="tab-pane fade" id="v-pills-3" role="tabpanel">
                                    <div class="row">
                                        <?php if ($count_tea > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($result_tea)): ?>
                                                <div class="col-md-4 text-center">
                                                    <div class="menu-wrap">
                                                        <div class="menu-img img mb-4" data-href="product-detail.php?id=<?php echo (int)$row['id_product']; ?>" style="background-image: url(images/<?php echo htmlspecialchars($row['gambar']); ?>); background-size: cover; background-position: center; height: 200px; display: block; cursor: pointer;"></div>
                                                        <div class="text">
                                                            <h3><a href="product-detail.php?id=<?php echo (int)$row['id_product']; ?>"><?php echo htmlspecialchars($row['nama_product']); ?></a></h3>
                                                            <p><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                                                            <p class="price"><span>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?> / pack</span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <div class="col-md-12 text-center">
                                                <p>Tidak ada produk Tea tersedia</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-migrate-3.0.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/jquery.animateNumber.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/jquery.timepicker.min.js"></script>
    <script src="js/scrollax.min.js"></script>
    <!-- Jangan load main.js dulu -->
    <script src="js/menu-navigation.js"></script>
    <script src="js/main.js"></script>
</body>

</html>

<?php
// Tutup koneksi database
mysqli_close($koneksi);
?>