<?php
session_start();
require_once 'config.php';

$siteTitle = "Coffee - Brewbeans Coffee Bandung";
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title><?= htmlspecialchars($siteTitle) ?></title>
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
		/* Glassmorphism Navbar */
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
	</style>
</head>

<body>
	<?php include 'partials/navbar.php'; ?>


	<section class="home-slider owl-carousel d-none d-md-block">
		<div class="slider-item" style="background-image: url(images/bg_1.jpg);">
			<div class="overlay"></div>
			<div class="container">
				<div class="row slider-text justify-content-center align-items-center" data-scrollax-parent="true">
					<div class="col-md-8 col-sm-12 text-center ftco-animate">
						<span class="subheading">Welcome</span>
						<h1 class="mb-4">BREWBEANS COFFEE BANDUNG</h1>
						<p class="mb-4 mb-md-5">A small river named Duden flows by their place and supplies it with the necessary regelialia.</p>
					</div>
				</div>
			</div>
		</div>

		<div class="slider-item" style="background-image: url(images/bg_2.jpg);">
			<div class="overlay"></div>
			<div class="container">
				<div class="row slider-text justify-content-center align-items-center" data-scrollax-parent="true">
					<div class="col-md-8 col-sm-12 text-center ftco-animate">
						<span class="subheading">Welcome</span>
						<h1 class="mb-4">BREWBEANS COFFEE BANDUNG</h1>
						<p class="mb-4 mb-md-5">A small river named Duden flows by their place and supplies it with the necessary regelialia.</p>
					</div>
				</div>
			</div>
		</div>

		<div class="slider-item" style="background-image: url(images/bg_3.jpg);">
			<div class="overlay"></div>
			<div class="container">
				<div class="row slider-text justify-content-center align-items-center" data-scrollax-parent="true">
					<div class="col-md-8 col-sm-12 text-center ftco-animate">
						<span class="subheading">Welcome</span>
						<h1 class="mb-4">BREWBEANS COFFEE BANDUNG</h1>
						<p class="mb-4 mb-md-5">A small river named Duden flows by their place and supplies it with the necessary regelialia.</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Mobile Hero Static -->
	<section class="mobile-hero d-md-none" style="background-image: url(images/bg_1.jpg); height: 400px; background-size: cover; background-position: center; position: relative; margin-top: 0;">
		<div class="overlay" style="position: absolute; top:0; left:0; right:0; bottom:0; background: rgba(0,0,0,0.5);"></div>
		<div class="container text-center d-flex align-items-center justify-content-center" style="height: 100%; position: relative; z-index: 2;">
			<div>
				<span class="subheading" style="color: #c49b63; font-family: 'Great Vibes', cursive; font-size: 30px;">Welcome</span>
				<h1 class="mb-4" style="color: #fff; font-weight: 800; text-transform: uppercase; font-size: 30px;">BREWBEANS COFFEE</h1>
			</div>
		</div>
	</section>

	<section class="ftco-intro">
		<div class="container-wrap">
			<div class="wrap d-md-flex align-items-xl-end">
				<div class="info">
					<div class="row no-gutters">
						<div class="col-md-4 d-flex ftco-animate">
							<div class="icon"><span class="icon-phone"></span></div>
							<div class="text">
								<h3>000 (123) 456 7890</h3>
								<p>A small river named Duden flows by their place and supplies.</p>
							</div>
						</div>
						<div class="col-md-4 d-flex ftco-animate">
							<div class="icon"><span class="icon-my_location"></span></div>
							<div class="text">
								<h3>198 West 21th Street</h3>
								<p> 203 Fake St. Mountain View, San Francisco, California, USA</p>
							</div>
						</div>
						<div class="col-md-4 d-flex ftco-animate">
							<div class="icon"><span class="icon-clock-o"></span></div>
							<div class="text">
								<h3>Open Monday-Friday</h3>
								<p>8:00am - 9:00pm</p>
							</div>
						</div>
					</div>
				</div>
				<div class="book p-4">
					<h3>Book a Table</h3>
					<form action="#" class="appointment-form">
						<div class="d-md-flex">
							<div class="form-group">
								<input type="text" class="form-control" placeholder="First Name">
							</div>
							<div class="form-group ml-md-4">
								<input type="text" class="form-control" placeholder="Last Name">
							</div>
						</div>
						<div class="d-md-flex">
							<div class="form-group">
								<div class="input-wrap">
									<div class="icon"><span class="ion-md-calendar"></span></div>
									<input type="text" class="form-control appointment_date" placeholder="Date">
								</div>
							</div>
							<div class="form-group ml-md-4">
								<div class="input-wrap">
									<div class="icon"><span class="ion-ios-clock"></span></div>
									<input type="text" class="form-control appointment_time" placeholder="Time">
								</div>
							</div>
							<div class="form-group ml-md-4">
								<input type="text" class="form-control" placeholder="Phone">
							</div>
						</div>
						<div class="d-md-flex">
							<div class="form-group">
								<textarea name="" id="" cols="30" rows="2" class="form-control" placeholder="Message"></textarea>
							</div>
							<div class="form-group ml-md-4">
								<input type="submit" value="Appointment" class="btn btn-white py-3 px-4">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
	<section class="ftco-section img" id="ftco-testimony" style="background-image: url(images/bg_1.jpg);" data-stellar-background-ratio="0.5">
		<div class="overlay"></div>
		<div class="container">
			<div class="row justify-content-center mb-5">
				<div class="col-md-7 heading-section text-center ftco-animate">
					<span class="subheading">Testimony</span>
					<h2 class="mb-4">Customers Says</h2>
					<p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
				</div>
			</div>
		</div>
		<div class="container-wrap">
			<div class="row d-flex no-gutters">
				<div class="col-lg align-self-sm-end ftco-animate">
					<div class="testimony">
						<blockquote>
							<p>&ldquo;Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small.&rdquo;</p>
						</blockquote>
						<div class="author d-flex mt-4">
							<div class="image mr-3 align-self-center">
								<img src="images/person_1.jpg" alt="">
							</div>
							<div class="name align-self-center">Louise Kelly <span class="position">Illustrator Designer</span></div>
						</div>
					</div>
				</div>
				<div class="col-lg align-self-sm-end">
					<div class="testimony overlay">
						<blockquote>
							<p>&ldquo;Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line of blind text by the name of Lorem Ipsum decided to leave for the far World of Grammar.&rdquo;</p>
						</blockquote>
						<div class="author d-flex mt-4">
							<div class="image mr-3 align-self-center">
								<img src="images/person_2.jpg" alt="">
							</div>
							<div class="name align-self-center">Louise Kelly <span class="position">Illustrator Designer</span></div>
						</div>
					</div>
				</div>
				<div class="col-lg align-self-sm-end ftco-animate">
					<div class="testimony">
						<blockquote>
							<p>&ldquo;Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line of blind text by the name. &rdquo;</p>
						</blockquote>
						<div class="author d-flex mt-4">
							<div class="image mr-3 align-self-center">
								<img src="images/person_3.jpg" alt="">
							</div>
							<div class="name align-self-center">Louise Kelly <span class="position">Illustrator Designer</span></div>
						</div>
					</div>
				</div>
				<div class="col-lg align-self-sm-end">
					<div class="testimony overlay">
						<blockquote>
							<p>&ldquo;Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however.&rdquo;</p>
						</blockquote>
						<div class="author d-flex mt-4">
							<div class="image mr-3 align-self-center">
								<img src="images/person_2.jpg" alt="">
							</div>
							<div class="name align-self-center">Louise Kelly <span class="position">Illustrator Designer</span></div>
						</div>
					</div>
				</div>
				<div class="col-lg align-self-sm-end ftco-animate">
					<div class="testimony">
						<blockquote>
							<p>&ldquo;Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line of blind text by the name. &rdquo;</p>
						</blockquote>
						<div class="author d-flex mt-4">
							<div class="image mr-3 align-self-center">
								<img src="images/person_3.jpg" alt="">
							</div>
							<div class="name align-self-center">Louise Kelly <span class="position">Illustrator Designer</span></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


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
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
	<script src="js/google-map.js"></script>
	<script src="js/main.js"></script>

</body>

</html>