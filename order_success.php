<?php
session_start();
include 'partials/navbar.php';

$order_id = isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Order Success - Coffee Shop</title>
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
        .success-container {
            padding: 100px 0;
            text-align: center;
        }

        .icon-success {
            font-size: 80px;
            color: #c49b63;
            margin-bottom: 20px;
        }

        .order-box {
            background: rgba(0, 0, 0, 0.4);
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
            margin-top: 20px;
            border: 1px solid rgba(196, 155, 99, 0.3);
        }

        .btn-primary {
            background: #c49b63;
            border: 1px solid #c49b63;
            color: #000;
        }

        .btn-primary:hover {
            background: transparent;
            color: #c49b63;
        }
    </style>
</head>

<body>

    <section class="home-slider owl-carousel">
        <div class="slider-item" style="background-image: url(images/bg_3.jpg);" data-stellar-background-ratio="0.5">
            <div class="overlay"></div>
            <div class="container">
                <div class="row slider-text justify-content-center align-items-center">
                    <div class="col-md-7 col-sm-12 text-center ftco-animate">
                        <h1 class="mb-3 mt-5 bread">Terima Kasih!</h1>
                        <p class="breadcrumbs"><span class="mr-2"><a href="index.php">Home</a></span> <span>Success</span></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section success-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 ftco-animate">
                    <span class="icon-success ion-ios-checkmark-circle-outline"></span>
                    <h2 class="mb-4">Pesanan Anda Berhasil Dibuat!</h2>

                    <?php if ($order_id): ?>
                        <div class="order-box">
                            <h4 class="mb-0" style="color: #fff;">Order ID: <span style="color: #c49b63;"><?= $order_id ?></span></h4>
                            <p class="mt-3 mb-0" style="color: #ccc;">Silakan selesaikan pembayaran Anda (jika belum).</p>
                        </div>
                    <?php endif; ?>

                    <p class="mt-5">
                        <a href="index.php" class="btn btn-primary py-3 px-4">Kembali ke Beranda</a>
                        <a href="cart.php" class="btn btn-white btn-outline-white py-3 px-4">Lihat Pesanan Saya</a>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'partials/footer.php'; ?>

    <!-- loader -->
    <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00" />
        </svg></div>

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

</body>

</html>