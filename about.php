<?php
session_start();
require_once 'config.php';

$isLoggedIn = !empty($_SESSION['id_user']);
$id_user = $isLoggedIn ? $_SESSION['id_user'] : null;

// Fetch username and cart count if logged in
if ($isLoggedIn) {
  $query_user = "SELECT username FROM tabel_user WHERE id_user = '$id_user'";
  $result_user = mysqli_query($koneksi, $query_user);
  $user_data = mysqli_fetch_assoc($result_user);
  $username = $user_data['username'];

  $q_cart = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tabel_keranjang WHERE id_user = '$id_user'");
  $cart_count = mysqli_fetch_assoc($q_cart)['total'];
} else {
  $cart_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>BrewBeans - About Us</title>
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
</head>

<body>
  <!-- Navbar -->
  <?php include 'partials/navbar.php'; ?>
  <!-- END nav -->

  <!-- Hapus Hero Section Tanpa Gambar -->
  <!-- <section class="home">
      <div class="slider-item d-flex align-items-center justify-content-center" style="background: #111; height: 300px; position: relative;">
        <div class="overlay" style="background: rgba(0,0,0,0.4); position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
        <div class="container text-center" style="position: relative; z-index: 2;">
        </div>
      </div>
    </section> -->

  <!-- About Section -->
  <section class="ftco-about d-md-flex">
    <div class="one-half img" style="background-image: url(images/about.jpg);"></div>
    <div class="one-half ftco-animate">
      <div class="overlap p-5">
        <div class="heading-section ftco-animate">
          <span class="subheading">Discover</span>
          <h2 class="mb-4">Our Story</h2>
        </div>
        <p>
          BrewBeans hadir untuk menghubungkan petani kopi lokal Indonesia dengan coffee shop di seluruh negeri.
          Kami percaya pada keaslian rasa, transparansi pasokan, dan keberlanjutan pertanian lokal.
          Dari biji pilihan hingga cangkir kopi terbaik — semua dimulai dari tangan-tangan petani kita sendiri.
        </p>
      </div>
    </div>
  </section>

  <!-- Counter Section -->
  <section class="ftco-counter ftco-bg-dark img" id="section-counter" style="background-image: url(images/bg_2.jpg);" data-stellar-background-ratio="0.5">
    <div class="overlay"></div>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="row">
            <div class="col-md-6 col-lg-3 d-flex justify-content-center counter-wrap ftco-animate">
              <div class="block-18 text-center">
                <div class="text">
                  <div class="icon"><span class="flaticon-coffee-cup"></span></div>
                  <strong class="number" data-number="100">0</strong>
                  <span>Coffee Branches</span>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex justify-content-center counter-wrap ftco-animate">
              <div class="block-18 text-center">
                <div class="text">
                  <div class="icon"><span class="flaticon-award"></span></div>
                  <strong class="number" data-number="85">0</strong>
                  <span>Awards</span>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex justify-content-center counter-wrap ftco-animate">
              <div class="block-18 text-center">
                <div class="text">
                  <div class="icon"><span class="flaticon-smile"></span></div>
                  <strong class="number" data-number="10567">0</strong>
                  <span>Happy Customers</span>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-lg-3 d-flex justify-content-center counter-wrap ftco-animate">
              <div class="block-18 text-center">
                <div class="text">
                  <div class="icon"><span class="flaticon-employee"></span></div>
                  <strong class="number" data-number="900">0</strong>
                  <span>Staff</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="ftco-footer ftco-section img">
    <div class="overlay"></div>
    <div class="container">
      <div class="row mb-5">
        <div class="col-lg-3 col-md-6">
          <div class="ftco-footer-widget mb-4">
            <h2 class="ftco-heading-2">About Us</h2>
            <p>BrewBeans adalah jembatan antara petani kopi lokal dan pecinta kopi di seluruh Indonesia.</p>
            <ul class="ftco-footer-social list-unstyled mt-5">
              <li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
              <li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
              <li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="ftco-footer-widget mb-4">
            <h2 class="ftco-heading-2">Recent Blog</h2>
            <div class="block-21 mb-4 d-flex">
              <a class="blog-img mr-4" style="background-image: url(images/image_1.jpg);"></a>
              <div class="text">
                <h3 class="heading"><a href="#">The Art of Local Coffee</a></h3>
                <div class="meta">
                  <div><a href="#"><span class="icon-calendar"></span> Oct 1, 2025</a></div>
                  <div><a href="#"><span class="icon-person"></span> Admin</a></div>
                </div>
              </div>
            </div>
            <div class="block-21 d-flex">
              <a class="blog-img mr-4" style="background-image: url(images/image_2.jpg);"></a>
              <div class="text">
                <h3 class="heading"><a href="#">Empowering Local Farmers</a></h3>
                <div class="meta">
                  <div><a href="#"><span class="icon-calendar"></span> Oct 8, 2025</a></div>
                  <div><a href="#"><span class="icon-person"></span> Admin</a></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-2 col-md-6">
          <div class="ftco-footer-widget mb-4 ml-md-4">
            <h2 class="ftco-heading-2">Services</h2>
            <ul class="list-unstyled">
              <li><a href="#" class="py-2 d-block">Roastery</a></li>
              <li><a href="#" class="py-2 d-block">Delivery</a></li>
              <li><a href="#" class="py-2 d-block">Wholesale</a></li>
              <li><a href="#" class="py-2 d-block">Quality Control</a></li>
            </ul>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="ftco-footer-widget mb-4">
            <h2 class="ftco-heading-2">Have a Question?</h2>
            <div class="block-23 mb-3">
              <ul>
                <li><span class="icon icon-map-marker"></span><span class="text">Jl. Braga No.10, Bandung, Indonesia</span></li>
                <li><a href="#"><span class="icon icon-phone"></span><span class="text">+62 812 3456 7890</span></a></li>
                <li><a href="#"><span class="icon icon-envelope"></span><span class="text">support@brewbeans.com</span></a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12 text-center">
          <p>Copyright &copy;
            <script>
              document.write(new Date().getFullYear());
            </script>
            BrewBeans | Made with <i class="icon-heart"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
          </p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Loader & Scripts -->
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
</body>

</html>