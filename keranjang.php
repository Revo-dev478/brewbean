<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Ambil data user untuk navbar
$query_user = "SELECT username FROM tabel_user WHERE id_user = '$id_user'";

// Ambil data keranjang
if ($koneksi) {
    $query = "SELECT k.*, p.nama_product, p.gambar, p.harga 
              FROM tabel_keranjang k 
              JOIN tabel_product p ON k.id_product = p.id_product 
              WHERE k.id_user = $id_user
              ORDER BY k.id_keranjang DESC";
    $result = mysqli_query($koneksi, $query);
} else {
    $result = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Coffee Blend | Cart</title>
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #0f0f0f;
        }

        /* Glassmorphism Navbar */
        .ftco_navbar {
            background: rgba(18, 18, 18, 0.95) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
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

        /* Cart Styling */
        .cart-list {
            background: rgba(30, 30, 30, 0.6) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(196, 155, 99, 0.2);
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 40px;
        }

        .table thead th {
            border-bottom: 2px solid rgba(196, 155, 99, 0.3) !important;
            background: rgba(30, 30, 30, 0.8);
            color: #c49b63;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 2px;
        }

        .table tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.3s;
        }

        .table tbody tr:hover {
            background: rgba(196, 155, 99, 0.03);
        }

        .product-name h3 {
            font-size: 16px;
            color: #fff;
            margin-bottom: 0;
        }

        .price,
        .total {
            color: #c49b63;
            font-weight: 600;
        }

        .qty-box {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .qty-box button {
            background: transparent;
            border: none;
            color: #c49b63;
            width: 35px;
            height: 35px;
            font-size: 18px;
            transition: all 0.3s;
        }

        .qty-box button:hover {
            background: rgba(196, 155, 99, 0.2);
        }

        .qty-display {
            width: 40px;
            line-height: 35px;
            text-align: center;
            color: #fff;
            font-weight: 600;
        }

        .btn-remove {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
            padding: 8px 12px;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .btn-remove:hover {
            background: #dc3545;
            color: #fff;
        }

        .cart-total {
            background: rgba(30, 30, 30, 0.6) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(196, 155, 99, 0.2);
            border-radius: 15px;
            padding: 30px;
        }

        .cart-total h3 {
            color: #c49b63;
            text-transform: uppercase;
            font-size: 18px;
            letter-spacing: 1px;
            margin-bottom: 25px;
            border-bottom: 1px solid rgba(196, 155, 99, 0.2);
            padding-bottom: 15px;
        }

        .cart-total p span:first-child {
            color: rgba(255, 255, 255, 0.8);
        }

        .cart-total p span:last-child {
            color: #fff;
            font-weight: 700;
        }

        .btn-primary {
            background: #c49b63 !important;
            border: 1px solid #c49b63 !important;
            color: #000 !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px 30px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: transparent !important;
            color: #c49b63 !important;
        }
    </style>
</head>

<body>

    <?php include 'partials/navbar.php'; ?>


    <section class="ftco-section ftco-cart">
        <div class="container">
            <div class="row">
                <div class="col-md-12 ftco-animate">
                    <div class="cart-list">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <table class="table">
                                <thead class="thead-primary">
                                    <tr class="text-center">
                                        <th>&nbsp;</th>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $grand_total = 0;
                                    while ($item = mysqli_fetch_assoc($result)):
                                        $grand_total += $item['subtotal'];
                                    ?>
                                        <tr class="text-center" id="row-<?= $item['id_keranjang'] ?>">
                                            <td class="image-prod">
                                                <div class="img" style="background-image:url(images/<?= $item['gambar'] ?>); width: 80px; height: 80px; background-size: cover; border-radius: 8px; margin: 0 auto;"></div>
                                            </td>

                                            <td class="product-name">
                                                <h3><?= $item['nama_product'] ?></h3>
                                            </td>

                                            <td class="price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>

                                            <td class="quantity">
                                                <div class="input-group mb-2 d-flex justify-content-center">
                                                    <div class="qty-box">
                                                        <button type="button" onclick="ubahQty(<?= $item['id_keranjang'] ?>, 'kurang')">−</button>
                                                        <div class="qty-display mt-2" id="qty-<?= $item['id_keranjang'] ?>"><?= $item['qty'] ?></div>
                                                        <button type="button" onclick="ubahQty(<?= $item['id_keranjang'] ?>, 'tambah')">+</button>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="total">
                                                Rp <span id="subtotal-<?= $item['id_keranjang'] ?>"><?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                                                <input type="hidden" id="raw-subtotal-<?= $item['id_keranjang'] ?>" class="raw-subtotal" value="<?= $item['subtotal'] ?>">
                                            </td>

                                            <td class="product-remove">
                                                <button class="btn-remove px-4" onclick="hapusItem(<?= $item['id_keranjang'] ?>)">
                                                    <span class="ion-ios-close"></span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-cart px-5 py-5 text-center">
                                <span class="icon ion-ios-cart" style="font-size: 80px; color: #c49b63;"></span>
                                <h3 class="mt-4" style="color: #fff;">Keranjang Anda Kosong</h3>
                                <p class="text-muted">Sepertinya Anda belum menambahkan apapun ke keranjang.</p>
                                <a href="menu.php" class="btn btn-primary px-4 py-3">Lihat Menu Kami</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <div class="row">
                    <div class="col-md-12 cart-wrap ftco-animate">
                        <div class="cart-total">
                            <h3>Cart Totals</h3>
                            <p class="d-flex">
                                <span>Subtotal</span>
                                <span id="subtotal-display">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                            </p>
                            <hr>
                            <p class="d-flex total-price ">
                                <span>Grand Total</span>
                                <span id="grand-total">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                            </p>
                            <p class="text-center mt-4">
                                <a href="checkout.php" class="btn btn-primary py-3 px-4 btn-block">Checkout Sekarang</a>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div id="ftco-loader" class="show fullscreen">
        <svg class="circular" width="48px" height="48px">
            <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
            <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#F96D00" />
        </svg>
    </div>

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
        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function ubahQty(idKeranjang, aksi) {
            const qtyDisplay = document.getElementById('qty-' + idKeranjang);
            let currentQty = parseInt(qtyDisplay.innerText);

            if (aksi === 'tambah') {
                currentQty++;
            } else if (aksi === 'kurang') {
                if (currentQty > 1) {
                    currentQty--;
                } else {
                    return;
                }
            }

            qtyDisplay.style.opacity = '0.5';

            $.ajax({
                url: 'keranjang_update.php',
                type: 'POST',
                data: {
                    id_keranjang: idKeranjang,
                    qty: currentQty
                },
                dataType: 'json',
                success: function(data) {
                    qtyDisplay.style.opacity = '1';
                    if (data.success) {
                        qtyDisplay.innerText = currentQty;
                        document.getElementById('subtotal-' + idKeranjang).innerText = formatRupiah(data.new_subtotal);
                        document.getElementById('raw-subtotal-' + idKeranjang).value = data.new_subtotal;
                        hitungGrandTotal();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                },
                error: function() {
                    qtyDisplay.style.opacity = '1';
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                }
            });
        }

        function hitungGrandTotal() {
            let grandTotal = 0;
            $('.raw-subtotal').each(function() {
                grandTotal += parseInt($(this).val());
            });
            let formatted = 'Rp ' + formatRupiah(grandTotal);
            $('#grand-total').text(formatted);
            $('#subtotal-display').text(formatted);
        }

        function hapusItem(idKeranjang) {
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Produk ini akan dihapus dari keranjang Anda.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c49b63',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                background: '#1a1a1a',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'keranjang_hapus.php',
                        type: 'POST',
                        data: {
                            id_keranjang: idKeranjang
                        },
                        dataType: 'json',
                        success: function(data) {
                            if (data.success) {
                                $('#row-' + idKeranjang).fadeOut(300, function() {
                                    $(this).remove();
                                    hitungGrandTotal();
                                    if ($('.cart-item, tr[id^="row-"]').length === 0) {
                                        location.reload();
                                    }
                                });
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Produk berhasil dihapus.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    background: '#1a1a1a',
                                    color: '#fff'
                                });
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>