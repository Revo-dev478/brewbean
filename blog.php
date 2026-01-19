<?php
// blog.php
// Contoh: Halaman blog template Coffee Blend versi PHP
session_start();
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Coffee Blend | Blog</title>
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


    <section class="ftco-section">
        <div class="container">
            <div class="row d-flex">
                <?php
                // Contoh data blog bisa diganti dengan database
                $blogs = [
                    ["image" => "images/image_1.jpg", "date" => "Sept 28, 2018", "title" => "Coffee Testing Day", "desc" => "A small river named Duden flows by their place and supplies it with the necessary regelialia."],
                    ["image" => "images/image_2.jpg", "date" => "Sept 28, 2018", "title" => "Latte Art Competition", "desc" => "Discover how baristas express art through every cup of coffee."],
                    ["image" => "images/image_3.jpg", "date" => "Oct 02, 2018", "title" => "Coffee Origins", "desc" => "Explore the stories behind every coffee bean sourced from the highlands."],
                    ["image" => "images/image_4.jpg", "date" => "Oct 10, 2018", "title" => "Roasting Workshop", "desc" => "Learn how roasting brings out the best flavor profiles of coffee beans."],
                    ["image" => "images/image_5.jpg", "date" => "Oct 12, 2018", "title" => "Espresso Masterclass", "desc" => "A deep dive into espresso techniques and machine calibration."],
                    ["image" => "images/image_6.jpg", "date" => "Oct 15, 2018", "title" => "Coffee & Sustainability", "desc" => "How sustainable sourcing helps local farmers and the planet."]
                ];

                foreach ($blogs as $b) : ?>
                    <div class="col-md-4 d-flex ftco-animate">
                        <div class="blog-entry align-self-stretch">
                            <a href="blog-single.php" class="block-20" style="background-image: url('<?php echo $b['image']; ?>');"></a>
                            <div class="text py-4 d-block">
                                <div class="meta">
                                    <div><a href="#"><?php echo $b['date']; ?></a></div>
                                    <div><a href="#">Admin</a></div>
                                    <div><a href="#" class="meta-chat"><span class="icon-chat"></span> 3</a></div>
                                </div>
                                <h3 class="heading mt-2"><a href="blog-single.php"><?php echo $b['title']; ?></a></h3>
                                <p><?php echo $b['desc']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="row mt-5">
                <div class="col text-center">
                    <div class="block-27">
                        <ul>
                            <li><a href="#">&lt;</a></li>
                            <li class="active"><span>1</span></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">4</a></li>
                            <li><a href="#">5</a></li>
                            <li><a href="#">&gt;</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'partials/footer.php'; ?>
    <!-- File footer dipisahkan juga agar lebih rapi -->

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
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE&sensor=false"></script>
    <script src="js/google-map.js"></script>
    <script src="js/main.js"></script>

</body>

</html>