<?php
session_start();
require_once 'config.php';

// VALIDASI PARAMETER ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: menu.php");
    exit();
}

$id_product = (int) $_GET['id'];

// Fetch product details
$query = "SELECT p.*, s.nama as nama_penjual 
          FROM tabel_product p 
          LEFT JOIN tabel_penjual s ON p.id_penjual = s.id 
          WHERE p.id_product = $id_product";
$data = mysqli_query($koneksi, $query);

if (!$data || mysqli_num_rows($data) === 0) {
    header("Location: menu.php");
    exit();
}

$product = mysqli_fetch_assoc($data);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?php echo $product['nama_product']; ?> | Coffee Blend</title>
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #0f0f0f;
            color: #ddd;
            font-family: 'Poppins', sans-serif;
        }

        .ftco_navbar {
            background: transparent !important;
            border-bottom: 1px solid rgba(196, 155, 99, 0.3);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            color: #fff !important;
            font-weight: 800;
            text-transform: uppercase;
        }

        .navbar-brand small {
            color: #c49b63 !important;
            display: block;
            font-size: 14px;
            letter-spacing: 2px;
            text-transform: none;
            margin-top: -5px;
        }

        .ftco_navbar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 14px;
            padding-top: 0.8rem;
            padding-bottom: 0.8rem;
            padding-left: 20px;
            padding-right: 20px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .ftco_navbar .nav-item.active .nav-link {
            color: #c49b63 !important;
        }

        @media (max-width: 991.98px) {
            .ftco_navbar {
                background: #000 !important;
            }
        }

        /* Product Detail Styling */
        .product-detail-card {
            background: rgba(30, 30, 30, 0.6) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(196, 155, 99, 0.2);
            border-radius: 20px;
            overflow: hidden;
            margin-top: 120px;
            margin-bottom: 80px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .product-img-wrap {
            padding: 20px;
        }

        .product-img-wrap img {
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .product-content {
            padding: 40px;
        }

        .product-title {
            font-size: 42px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .product-price {
            font-size: 32px;
            color: #c49b63;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .product-desc {
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .meta-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        .meta-item i {
            width: 25px;
            color: #c49b63;
            font-size: 16px;
        }

        .meta-item strong {
            margin-left: 5px;
            color: #fff;
        }

        /* Quantity & Add to Cart */
        .order-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
        }

        .qty-box {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .qty-box button {
            background: transparent;
            border: none;
            color: #c49b63;
            width: 50px;
            height: 50px;
            font-size: 20px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .qty-box button:hover {
            background: rgba(196, 155, 99, 0.2);
        }

        .qty-box input {
            width: 60px;
            background: transparent;
            border: none;
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            text-align: center;
            font-weight: 600;
            font-size: 18px;
        }

        .btn-cart {
            flex: 1;
            min-width: 200px;
            background: #c49b63 !important;
            border: 1px solid #c49b63 !important;
            color: #000 !important;
            height: 50px;
            border-radius: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cart i {
            margin-right: 10px;
        }

        .btn-cart:hover {
            background: transparent !important;
            color: #c49b63 !important;
        }

        .qty-label {
            display: block;
            margin-bottom: 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.4);
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <?php include 'partials/navbar.php'; ?>
    <!-- END NAVBAR -->

    <!-- CONTENT PRODUCT -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="product-detail-card">
                    <div class="row no-gutters">
                        <!-- LEFT IMAGE -->
                        <div class="col-lg-5">
                            <div class="product-img-wrap ftco-animate">
                                <img src="images/<?php echo $product['gambar']; ?>" alt="<?php echo $product['nama_product']; ?>">
                            </div>
                        </div>

                        <!-- RIGHT DETAILS -->
                        <div class="col-lg-7">
                            <div class="product-content ftco-animate">
                                <h1 class="product-title"><?php echo $product['nama_product']; ?></h1>
                                <p class="product-price">Rp <?php echo number_format($product['harga'], 0, ',', '.'); ?></p>

                                <p class="product-desc"><?php echo $product['deskripsi']; ?></p>

                                <div class="meta-info">
                                    <div class="meta-item">
                                        <i class="fas fa-store"></i> <span>Penjual: <strong><?php echo isset($product['nama_penjual']) ? htmlspecialchars($product['nama_penjual']) : 'Official Store'; ?></strong></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-box"></i> <span>Stok tersedia: <strong><?php echo $product['qty']; ?> pcs</strong></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-weight-hanging"></i> <span>Berat: <strong><?php echo $product['berat']; ?>g</strong></span>
                                    </div>
                                </div>

                                <form method="POST" action="keranjang_add.php">
                                    <input type="hidden" name="id_produk" value="<?php echo $product['id_product']; ?>">

                                    <div class="qty-label">Jumlah Pesanan</div>
                                    <div class="order-actions">
                                        <div class="qty-box">
                                            <button type="button" onclick="minus()">−</button>
                                            <input id="qty" name="qty" type="number" value="1" min="1" readonly>
                                            <button type="button" onclick="plus()">+</button>
                                        </div>

                                        <button class="btn btn-cart" type="submit">
                                            <i class="fas fa-shopping-cart"></i> Tambah ke Keranjang
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
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
    <script src="js/main.js"></script>

    <script>
        function plus() {
            let qty = document.getElementById('qty');
            qty.value = parseInt(qty.value) + 1;
        }

        function minus() {
            let qty = document.getElementById('qty');
            if (qty.value > 1) qty.value = parseInt(qty.value) - 1;
        }
    </script>

</body>

</html>