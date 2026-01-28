<?php
// checkout.php - CHECKOUT VIEW & PROCESSOR
session_start();
require_once 'config.php';

// Cek Login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// --- PERBAIKAN UTAMA: HITUNG TOTAL DARI DATABASE ---
$subtotal_belanja = 0;
$total_qty = 0;

if ($koneksi) {
    $query_keranjang = "SELECT k.qty, p.nama_product, p.harga, p.gambar 
                        FROM tabel_keranjang k 
                        JOIN tabel_product p ON k.id_product = p.id_product 
                        WHERE k.id_user = '$id_user'";
    $result_keranjang = mysqli_query($koneksi, $query_keranjang);

    if ($result_keranjang) {
        while ($row = mysqli_fetch_assoc($result_keranjang)) {
            $subtotal_belanja += ($row['harga'] * $row['qty']);
            $total_qty += $row['qty'];
        }
    }
} else {
    // DB Error handling handled by alert below or redirect
    echo "<script>alert('Gagal terhubung ke database. Silakan coba lagi nanti.'); window.location='menu.php';</script>";
    exit();
}
// Default berat: 250g per item (karena kolom berat tidak ada di DB)
$total_berat = $total_qty * 250;
if ($total_berat == 0) $total_berat = 1000;

// Jika keranjang kosong, lempar balik ke menu
if ($subtotal_belanja == 0) {
    echo "<script>alert('Keranjang Anda kosong!'); window.location='menu.php';</script>";
    exit();
}

// AMBIL DATA USER UNTUK AUTO-FILL
$query_user = "SELECT username, email, phone FROM tabel_user WHERE id_user = '$id_user'";
$result_user = mysqli_query($koneksi, $query_user);
$user_data = mysqli_fetch_assoc($result_user);

// Handle phone number formatting (if int)
$user_phone = isset($user_data['phone']) ? $user_data['phone'] : '';
if ($user_phone != '' && substr($user_phone, 0, 1) != '0') {
    $user_phone = '0' . $user_phone;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Coffee Blend | Checkout</title>
    <meta charset="utf-8">
    <meta name="referrer" content="no-referrer-when-downgrade">
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

    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="Mid-client-I0dOWZ5M_S3_2LdF"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Styling Fixes */
        select.form-control {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #fff !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
        }

        select.form-control:disabled {
            background: rgba(255, 255, 255, 0.02) !important;
            color: #666 !important;
            cursor: not-allowed;
        }

        select.form-control option {
            background: #343a40;
            color: white;
        }

        .shipping-service {
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.05);
        }

        .shipping-service:hover {
            background: rgba(249, 109, 0, 0.1);
            border-color: #F96D00;
            transform: translateX(5px);
        }

        .shipping-service.selected {
            background: rgba(249, 109, 0, 0.2);
            border-color: #F96D00;
            border-width: 2px;
        }

        .loading-spinner {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            border: 2px solid #F96D00;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: none;
            z-index: 5;
        }

        @keyframes spin {
            0% {
                transform: translateY(-50%) rotate(0deg);
            }

            100% {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        /* Layout Adjustment & Premium Navbar */
        body {
            padding-top: 0;
            background-color: #0f0f0f;
        }

        /* Glassmorphism Navbar */
        .ftco_navbar {
            background: rgba(18, 18, 18, 0.95) !important;
            /* Very dark semi-transparent */
            backdrop-filter: blur(10px);
            /* Blur effect */
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(196, 155, 99, 0.3);
            /* Subtle Gold Border */
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
            /* Gold color for 'Blend' */
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
            opacity: 1 !important;
        }

        .ftco_navbar .nav-item.active .nav-link {
            color: #c49b63 !important;
            /* Active Gold */
        }

        .ftco_navbar .nav-item:hover .nav-link {
            color: #c49b63 !important;
        }

        .billing-form,
        .cart-detail {
            background: rgba(30, 30, 30, 0.6) !important;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(196, 155, 99, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .billing-heading {
            color: #c49b63 !important;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(196, 155, 99, 0.2);
            padding-bottom: 15px;
        }

        .form-group label {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: #c49b63 !important;
            box-shadow: 0 0 0 0.2rem rgba(196, 155, 99, 0.25);
        }

        .btn-primary {
            background: #c49b63 !important;
            border: 1px solid #c49b63 !important;
            color: #000 !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
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

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 ftco-animate">
                    <form id="checkoutForm" action="#" class="billing-form">
                        <h3 class="mb-4 billing-heading">Billing Details</h3>
                        <div class="row align-items-end">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama">Nama Penerima</label>
                                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama Lengkap" value="<?= isset($user_data['username']) ? htmlspecialchars($user_data['username']) : '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">No. Telepon</label>
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="08xxxxxxxx" value="<?= isset($user_phone) ? htmlspecialchars($user_phone) : '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email Anda" value="<?= isset($user_data['email']) ? htmlspecialchars($user_data['email']) : '' ?>" required>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group position-relative">
                                    <label for="provinsi">Provinsi</label>
                                    <div class="loading-spinner" id="loadingProvinsi"></div>
                                    <div class="select-wrap">
                                        <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                                        <select name="province_id" id="provinsi" class="form-control" required>
                                            <option value="">-- Pilih Provinsi --</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="provinsi_nama" id="provinsi_nama">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group position-relative">
                                    <label for="kota">Kota/Kabupaten</label>
                                    <div class="loading-spinner" id="loadingKota"></div>
                                    <div class="select-wrap">
                                        <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                                        <select name="city_id" id="kota" class="form-control" disabled required>
                                            <option value="">-- Pilih Provinsi Dulu --</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="kota_nama" id="kota_nama">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group position-relative">
                                    <label for="kecamatan">Kecamatan</label>
                                    <div class="loading-spinner" id="loadingKecamatan"></div>
                                    <div class="select-wrap">
                                        <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                                        <select name="district_id" id="kecamatan" class="form-control" disabled required>
                                            <option value="">-- Pilih Kota Dulu --</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="kecamatan_nama" id="kecamatan_nama">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kodepos">Kode Pos</label>
                                    <input type="text" name="kodepos" id="kodepos" class="form-control" placeholder="Contoh: 12345" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="alamat">Alamat Lengkap</label>
                                    <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Nama Jalan, No. Rumah, RT/RW, Patokan" required></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="weight">Berat Paket (gram)</label>
                                    <input type="number" name="weight" id="weight" class="form-control" value="<?= $total_berat ?>" min="100" step="100" readonly required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping">Pilih Kurir</label>
                                    <div class="select-wrap">
                                        <div class="icon"><span class="ion-ios-arrow-down"></span></div>
                                        <select name="courier" id="shipping" class="form-control" required>
                                            <option value="">-- Pilih Kurir --</option>
                                            <option value="jne">JNE</option>
                                            <option value="jnt">J&T</option>
                                            <option value="sicepat">SiCepat</option>
                                            <option value="pos">POS</option>
                                            <option value="tiki">TIKI</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <button type="button" id="btnCekOngkir" class="btn btn-outline-light btn-block py-3" disabled>
                                    <span class="btn-text">Cek Ongkos Kirim</span>
                                    <span class="spinner-border spinner-border-sm d-none ml-2"></span>
                                </button>
                                <small id="ongkirHelp" class="d-block text-center mt-2 text-muted">*Lengkapi Kecamatan, Berat, dan Kurir untuk cek ongkir</small>
                            </div>

                            <div class="col-md-12 mt-3">
                                <div id="shippingServices"></div>
                            </div>

                            <input type="hidden" name="shipping_service" id="shipping_service">
                            <input type="hidden" name="shipping_cost" id="shipping_cost" value="0">
                            <input type="hidden" name="shipping_etd" id="shipping_etd">

                        </div>
                    </form>
                </div>

                <div class="col-xl-4 sidebar ftco-animate">

                    <div class="cart-detail mb-3">
                        <h3 class="billing-heading mb-4">Ringkasan Pesanan</h3>
                        <?php
                        mysqli_data_seek($result_keranjang, 0);
                        while ($row = mysqli_fetch_assoc($result_keranjang)):
                        ?>
                            <div class="d-flex mb-3 align-items-center">
                                <div class="img mr-3" style="width: 60px; height: 60px; background-image: url(images/<?= $row['gambar'] ?>); background-size: cover; background-position: center; border-radius: 5px;"></div>
                                <div class="text">
                                    <h6 style="color: #fff; margin-bottom: 0; font-size: 14px;"><?= $row['nama_product'] ?></h6>
                                    <span style="color: #c49b63; font-size: 13px;"><?= $row['qty'] ?> x Rp <?= number_format($row['harga'], 0, ',', '.') ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="cart-detail cart-total mb-3">
                        <h3 class="billing-heading mb-4">Cart Total</h3>

                        <p class="d-flex">
                            <span>Subtotal</span>
                            <span id="subtotalDisplay">Rp <?= number_format($subtotal_belanja, 0, ',', '.') ?></span>
                        </p>
                        <p class="d-flex">
                            <span>Ongkos Kirim</span>
                            <span id="deliveryDisplay">Rp 0</span>
                        </p>
                        <hr>
                        <p class="d-flex total-price">
                            <span>Total</span>
                            <span id="totalDisplay">Rp <?= number_format($subtotal_belanja, 0, ',', '.') ?></span>
                        </p>

                        <input type="hidden" id="subtotalValue" value="<?= $subtotal_belanja ?>">
                        <input type="hidden" id="grandTotal" value="<?= $subtotal_belanja ?>">
                    </div>

                    <div class="cart-detail">
                        <h3 class="billing-heading mb-4">Pembayaran</h3>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="radio">
                                    <label><input type="radio" name="payment" class="mr-2" checked> QRIS / Transfer (Midtrans)</label>
                                </div>
                            </div>
                        </div>
                        <p>
                            <button type="button" id="btnPay" class="btn btn-primary py-3 px-4 btn-block">Buat Pesanan (Pay)</button>
                        </p>
                    </div>

                </div>
            </div>
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
        $(document).ready(function() {

            // =========================
            // CONFIG
            // =========================
            const ORIGIN_CITY_ID = "70";
            const DEBUG = true; // Set ke false untuk production

            // =========================
            // INIT
            // =========================
            loadProvinces();

            // =========================
            // EVENTS
            // =========================
            $('#provinsi').on('change', function() {
                resetResult();
                resetDropdown('#kota', 'Pilih Provinsi Dulu');
                resetDropdown('#kecamatan', 'Pilih Kota Dulu');

                let pid = $(this).val();
                $('#provinsi_nama').val($(this).find('option:selected').text());

                if (pid) loadCities(pid);
            });

            $('#kota').on('change', function() {
                resetResult();
                resetDropdown('#kecamatan', 'Pilih Kota Dulu');

                let cid = $(this).val();
                $('#kota_nama').val($(this).find('option:selected').text());

                if (cid) loadDistricts(cid);
            });

            $('#kecamatan').on('change', function() {
                $('#kecamatan_nama').val($(this).find('option:selected').text());
                checkButtonState();
            });

            $('#shipping, #weight').on('change', function() {
                resetResult();
                checkButtonState();
            });

            $('#btnCekOngkir').on('click', function() {
                hitungOngkir();
            });

            $('#btnPay').on('click', function() {
                prosesCheckout();
            });

            // =========================
            // LOAD PROvinces - OPTIMIZED (CACHE)
            // =========================
            function loadProvinces() {
                // Check Cache
                const cached = localStorage.getItem('provinces_cache');
                if (cached) {
                    if (DEBUG) console.log('Loaded Provinces from Cache');
                    $('#provinsi').html(cached);
                    return;
                }

                $('#loadingProvinsi').show();
                $.ajax({
                    url: 'ongkir.php?action=get_provinces',
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        let h = '<option value="">-- Pilih Provinsi --</option>';
                        if (res && res.success && res.data) {
                            res.data.forEach(p => {
                                h += `<option value="${p.province_id}">${p.province}</option>`;
                            });
                            // Save to Cache
                            localStorage.setItem('provinces_cache', h);
                        }
                        $('#provinsi').html(h);
                        $('#loadingProvinsi').hide();
                    },
                    error: function() {
                        $('#loadingProvinsi').hide();
                    }
                });
            }

            // =========================
            // LOAD CITIES - OPTIMIZED (CACHE)
            // =========================
            function loadCities(pid) {
                // Check Cache
                const cacheKey = 'cities_' + pid;
                const cached = localStorage.getItem(cacheKey);
                if (cached) {
                    if (DEBUG) console.log('Loaded Cities from Cache');
                    $('#kota').html(cached).prop('disabled', false);
                    return;
                }

                $('#loadingKota').show();
                $('#kota').prop('disabled', true).html('<option>Loading...</option>');

                $.ajax({
                    url: 'ongkir.php?action=get_cities&province_id=' + pid,
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        let html = '<option value="">-- Pilih Kota --</option>';
                        if (res.success && res.data) {
                            res.data.forEach(c => {
                                html += `<option value="${c.city_id}">${c.city_name}</option>`;
                            });
                            localStorage.setItem(cacheKey, html); // Cache it
                            $('#kota').html(html).prop('disabled', false);
                        }
                        $('#loadingKota').hide();
                    },
                    error: function() {
                        $('#kota').html('<option>Error Load</option>');
                        $('#loadingKota').hide();
                    }
                });
            }

            // =========================
            // LOAD DISTRICTS - OPTIMIZED (CACHE)
            // =========================
            function loadDistricts(cid) {
                // Check Cache
                const cacheKey = 'districts_' + cid;
                const cached = localStorage.getItem(cacheKey);
                if (cached) {
                    if (DEBUG) console.log('Loaded Districts from Cache');
                    $('#kecamatan').html(cached).prop('disabled', false);
                    return;
                }

                $('#loadingKecamatan').show();
                $('#kecamatan').prop('disabled', true).html('<option>Loading...</option>');

                $.ajax({
                    url: 'ongkir.php?action=get_districts&city_id=' + cid,
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        let html = '<option value="">-- Pilih Kecamatan --</option>';
                        if (res.success && res.data) {
                            res.data.forEach(d => {
                                html += `<option value="${d.district_id}">${d.district_name}</option>`;
                            });
                            localStorage.setItem(cacheKey, html); // Cache it
                            $('#kecamatan').html(html).prop('disabled', false);
                        }
                        $('#loadingKecamatan').hide();
                    },
                    error: function() {
                        $('#kecamatan').html('<option>Error Load</option>');
                        $('#loadingKecamatan').hide();
                    }
                });
            }

            // =========================
            // HITUNG ONGKIR
            // =========================
            function hitungOngkir() {
                let dest = $('#kecamatan').val(); // Use District ID for accurate cost
                if (!dest) dest = $('#kota').val(); // Fallback if no district selected (though validation prevents this)
                let w = $('#weight').val();
                let cour = $('#shipping').val();

                if (!dest || !w || !cour) {
                    Swal.fire('Perhatian', 'Pilih kota, berat, dan kurir terlebih dahulu', 'warning');
                    return;
                }

                $('#btnCekOngkir').prop('disabled', true);
                let spinner = $('#btnCekOngkir').find('.spinner-border');
                spinner.removeClass('d-none');

                $.ajax({
                    url: 'ongkir.php?action=get_cost&_=' + new Date().getTime(),
                    type: 'POST',
                    dataType: 'json',
                    timeout: 15000,
                    data: {
                        destination: dest,
                        weight: w,
                        courier: cour
                    },
                    success: function(res) {
                        let h = '';
                        if (res.success && res.data && res.data.length > 0) {
                            for (let i = 0; i < res.data.length; i++) {
                                let s = res.data[i];
                                h += `<div class="shipping-service" onclick="selectService(this, ${s.price}, '${s.service}', '${s.etd || '-'}')">
                                    <b>${s.courier} ${s.service}</b>
                                    <div>Rp ${s.price.toLocaleString('id-ID')}</div>
                                    <small style="color: #c49b63;">Est: ${s.etd || '-'}</small>
                                </div>`;
                            }
                        } else {
                            h = '<div class="alert alert-warning">Ongkir tidak tersedia untuk kurir ini</div>';
                        }
                        $('#shippingServices').html(h);
                        spinner.addClass('d-none');
                        $('#btnCekOngkir').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error calculating shipping:', error);
                        $('#shippingServices').html('<div class="alert alert-danger">Error menghitung ongkir</div>');
                        spinner.addClass('d-none');
                        $('#btnCekOngkir').prop('disabled', false);
                    }
                });
            }

            // =========================
            // SELECT SERVICE
            // =========================
            window.selectService = function(el, cost, name, etd) {
                $('.shipping-service').removeClass('selected');
                $(el).addClass('selected');

                $('#shipping_cost').val(cost);
                $('#shipping_service').val(name);
                $('#shipping_etd').val(etd || '-');

                updateTotal();
            };

            // =========================
            // TOTAL UPDATE
            // =========================
            function updateTotal() {
                let sub = parseFloat($('#subtotalValue').val());
                let ship = parseFloat($('#shipping_cost').val()) || 0;
                let total = sub + ship;

                $('#deliveryDisplay').text('Rp ' + ship.toLocaleString('id-ID'));
                $('#totalDisplay').text('Rp ' + total.toLocaleString('id-ID'));
                $('#grandTotal').val(total);
            }

            // =========================
            // PROCESS CHECKOUT
            // =========================
            function prosesCheckout() {
                // Validasi form
                // Validasi form detil
                let emptyFields = [];
                if (!$('#nama').val()) emptyFields.push('Nama Penerima');
                if (!$('#phone').val()) emptyFields.push('No. Telepon');
                if (!$('#email').val()) emptyFields.push('Email');
                if (!$('#provinsi').val()) emptyFields.push('Provinsi');
                if (!$('#kota').val()) emptyFields.push('Kota');
                if (!$('#kecamatan').val()) emptyFields.push('Kecamatan');
                if (!$('#kodepos').val()) emptyFields.push('Kode Pos');
                if (!$('#alamat').val()) emptyFields.push('Alamat');

                // Debug values to console
                console.log('Form Values:', {
                    nama: $('#nama').val(),
                    phone: $('#phone').val(),
                    email: $('#email').val(),
                    provinsi: $('#provinsi').val(),
                    kota: $('#kota').val(),
                    kecamatan: $('#kecamatan').val(),
                    kodepos: $('#kodepos').val(),
                    alamat: $('#alamat').val(),
                    shipping_cost: $('#shipping_cost').val()
                });

                if (emptyFields.length > 0) {
                    Swal.fire('Perhatian', 'Mohon lengkapi: ' + emptyFields.join(', '), 'warning');
                    return;
                }

                if (!$('#shipping_cost').val() || $('#shipping_cost').val() == 0) {
                    Swal.fire('Perhatian', 'Silakan pilih layanan pengiriman (klik salah satu opsi ongkir)', 'warning');
                    return;
                }

                if ($('#shipping_cost').val() == 0) {
                    Swal.fire('Perhatian', 'Pilih layanan pengiriman terlebih dahulu', 'warning');
                    return;
                }

                // Siapkan data
                // Prepare data for midtrans_token.php (Expects JSON)
                let payload = {
                    amount: parseInt($('#grandTotal').val()),
                    first_name: $('#nama').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    address: $('#alamat').val(),
                    city: $('#kota_nama').val(),
                    postal_code: $('#kodepos').val(),
                    shipping_cost: parseInt($('#shipping_cost').val()),
                    shipping_service: $('#shipping_service').val() || 'Courier'
                };

                // Kirim ke server untuk proses pembayaran
                $.ajax({
                    url: 'midtrans_token.php',
                    type: 'POST',
                    contentType: 'application/json', // Important: midtrans_token.php reads php://input
                    dataType: 'json',
                    data: JSON.stringify(payload),
                    success: function(res) {
                        if (res.token) {
                            // Trigger Midtrans Snap
                            snap.pay(res.token, {
                                onSuccess: function(result) {
                                    Swal.fire('Sukses', 'Pembayaran berhasil', 'success');
                                    window.location = 'confirmation.php?order_id=' + (res.order_id || 'UNKNOWN') + '&status=success';
                                },
                                onPending: function(result) {
                                    Swal.fire('Pending', 'Pembayaran sedang diproses', 'info');
                                },
                                onError: function(result) {
                                    Swal.fire('Error', 'Pembayaran gagal', 'error');
                                },
                                onClose: function() {
                                    Swal.fire('Dibatalkan', 'Anda menutup popup pembayaran', 'info');
                                }
                            });
                        } else {
                            Swal.fire('Error', res.message || 'Terjadi kesalahan', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghubungi server', 'error');
                    }
                });
            }

            // =========================
            // HELPERS
            // =========================
            function resetDropdown(sel, txt) {
                $(sel).html('<option value="">-- ' + txt + ' --</option>').prop('disabled', true);
            }

            function resetResult() {
                $('#shippingServices').empty();
                $('#shipping_cost').val(0);
                $('#shipping_service').val('');
                $('#shipping_etd').val('');
                updateTotal();
            }

            function checkButtonState() {
                if ($('#kecamatan').val() && $('#shipping').val()) {
                    $('#btnCekOngkir').prop('disabled', false);
                    $('#ongkirHelp').hide();
                } else {
                    $('#btnCekOngkir').prop('disabled', true);
                    $('#ongkirHelp').show();
                }
            }

        });
    </script>
</body>

</html>