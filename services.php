<?php
session_start();
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Coffee - Free Bootstrap 4 Template by Colorlib</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Great+Vibes" rel="stylesheet">

  <!-- CSS Files -->
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
  <?php include 'partials/navbar.php'; ?>
  <!-- END nav -->

  <section class="home-slider owl-carousel">
    <div class="slider-item" style="background-image: url('images/bg_3.jpg');" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row slider-text justify-content-center align-items-center">
          <div class="col-md-7 col-sm-12 text-center ftco-animate">
            <h1 class="mb-3 mt-5 bread">Services</h1>
            <p class="breadcrumbs"><span class="mr-2"><a href="utama.php">Home</a></span> <span>Services</span></p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="ftco-section ftco-services">
    <div class="container">
      <div class="row">
        <?php
        $services = [
          ["icon" => "flaticon-choices", "title" => "Easy to Order", "desc" => "Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic."],
          ["icon" => "flaticon-delivery-truck", "title" => "Fastest Delivery", "desc" => "Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic."],
          ["icon" => "flaticon-coffee-bean", "title" => "Quality Coffee", "desc" => "Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic."]
        ];

        foreach ($services as $s) {
          echo '
              <div class="col-md-4 ftco-animate">
                <div class="media d-block text-center block-6 services">
                  <div class="icon d-flex justify-content-center align-items-center mb-5">
                  	<span class="' . $s['icon'] . '"></span>
                  </div>
                  <div class="media-body">
                    <h3 class="heading">' . $s['title'] . '</h3>
                    <p>' . $s['desc'] . '</p>
                  </div>
                </div>
              </div>';
        }
        ?>
      </div>
    </div>
  </section>


  <!-- Loader -->
  <div id="ftco-loader" class="show fullscreen">
    <svg class="circular" width="48px" height="48px">
      <circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee" />
      <circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00" />
    </svg>
  </div>

  <!-- JS Files -->
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
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
  <script src="js/google-map.js"></script>
  <script src="js/main.js"></script>
</body>

</html>