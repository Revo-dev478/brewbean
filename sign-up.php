<?php
// sign-up.php

session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check DB Connection first
  if (!$koneksi) {
    $error = "Terjadi gangguan koneksi database. Silakan coba lagi nanti.";
  } else {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validasi dasar
    if (empty($email) || empty($username) || empty($phone) || empty($password)) {
      $error = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = "Format email tidak valid.";
    } elseif (strlen($password) < 6) {
      $error = "Password minimal 6 karakter.";
    } else {
      // Cek apakah email atau username sudah ada (menggunakan mysqli)
      $stmt = $koneksi->prepare("SELECT id_user FROM tabel_user WHERE email = ? OR username = ?");
      if (!$stmt) {
        $error = "Error prepare: " . $koneksi->error;
      } else {
        $stmt->bind_param("ss", $email, $username);
        if ($stmt->execute()) {
          $result = $stmt->get_result();
        } else {
          $result = false; // Handle execution failure
        }

        if ($result && $result->num_rows > 0) {
          $error = "Email atau username sudah digunakan.";
        } else {
          // Hash password
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

          // Simpan ke database (menggunakan mysqli)
          // Sesuaikan nama kolom dengan tabel tabel_user yang ada
          $stmt = $koneksi->prepare("INSERT INTO tabel_user (email, username, phone, password) VALUES (?, ?, ?, ?)");
          if (!$stmt) {
            $error = "Error prepare: " . $koneksi->error;
          } else {
            $stmt->bind_param("ssss", $email, $username, $phone, $hashedPassword);

            if ($stmt->execute()) {
              $success = "Akun berhasil dibuat! Selamat datang, " . htmlspecialchars($username) . " ☕";
              // Redirect ke login setelah 2 detik (opsional)
              header("Refresh: 2; url=login.php");
            } else {
              $error = "Terjadi kesalahan saat menyimpan data: " . $stmt->error;
            }
            $stmt->close();
          }
        }
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BrewBeans Sign Up</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:400,700" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Great+Vibes" rel="stylesheet">

  <!-- Main CSS -->
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
    .auth-page {
      min-height: 100vh;
      background: linear-gradient(135deg, #1a0f0a 0%, #3e2723 50%, #5d4037 100%);
      position: relative;
      overflow-x: hidden;
    }

    .auth-page::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: url('images/bg_2.jpg');
      background-size: cover;
      background-position: center;
      opacity: 0.15;
      z-index: 0;
    }

    .auth-content {
      position: relative;
      z-index: 1;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 120px 20px 60px 20px;
    }

    .auth-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      width: 100%;
      max-width: 480px;
      padding: 50px 40px;
      border-radius: 24px;
      box-shadow:
        0 25px 50px -12px rgba(0, 0, 0, 0.4),
        0 0 0 1px rgba(255, 255, 255, 0.1);
      text-align: center;
      animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .auth-logo {
      margin-bottom: 10px;
    }

    .auth-logo h1 {
      font-family: 'Josefin Sans', sans-serif;
      color: #3e2723;
      font-size: 32px;
      font-weight: 700;
      margin: 0;
      letter-spacing: 3px;
    }

    .auth-logo .coffee-icon {
      font-size: 48px;
      color: #8d6e63;
      margin-bottom: 15px;
      display: block;
    }

    .auth-subtitle {
      color: #8d6e63;
      font-size: 13px;
      letter-spacing: 4px;
      text-transform: uppercase;
      margin-bottom: 35px;
      font-weight: 500;
    }

    .form-row {
      display: flex;
      gap: 15px;
    }

    .form-row .form-group {
      flex: 1;
    }

    .form-group {
      margin-bottom: 18px;
      text-align: left;
    }

    .form-group label {
      display: block;
      color: #5d4037;
      font-size: 13px;
      font-weight: 500;
      margin-bottom: 8px;
    }

    .auth-input {
      width: 100%;
      padding: 14px 18px;
      border: 2px solid #e8e0db;
      border-radius: 12px;
      font-size: 14px;
      font-family: 'Poppins', sans-serif;
      transition: all 0.3s ease;
      background: #faf8f6;
    }

    .auth-input:focus {
      outline: none;
      border-color: #8d6e63;
      background: #fff;
      box-shadow: 0 0 0 4px rgba(141, 110, 99, 0.1);
    }

    .auth-input::placeholder {
      color: #b0a099;
    }

    .auth-btn {
      width: 100%;
      padding: 16px;
      background: linear-gradient(135deg, #3e2723 0%, #5d4037 100%);
      color: white;
      font-size: 15px;
      font-weight: 600;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      margin-top: 10px;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 2px;
      font-family: 'Poppins', sans-serif;
    }

    .auth-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(62, 39, 35, 0.3);
      background: linear-gradient(135deg, #5d4037 0%, #6d4c41 100%);
    }

    .auth-btn:active {
      transform: translateY(0);
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 28px 0;
      color: #a1887f;
      font-size: 13px;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: linear-gradient(90deg, transparent, #d7ccc8, transparent);
    }

    .divider span {
      padding: 0 15px;
      font-weight: 500;
    }

    .social-login {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .social-btn {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
      border: 2px solid #e8e0db;
      border-radius: 12px;
      padding: 14px 20px;
      cursor: pointer;
      background: #fff;
      font-weight: 500;
      font-size: 14px;
      color: #5d4037;
      transition: all 0.3s ease;
      font-family: 'Poppins', sans-serif;
    }

    .social-btn:hover {
      background: #faf8f6;
      border-color: #8d6e63;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .social-btn img {
      width: 22px;
      height: 22px;
    }

    .auth-links {
      margin-top: 28px;
    }

    .signup-text {
      font-size: 14px;
      color: #6d4c41;
    }

    .signup-text a {
      color: #3e2723;
      font-weight: 600;
      text-decoration: none;
      position: relative;
      transition: color 0.3s;
    }

    .signup-text a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: #8d6e63;
      transition: width 0.3s;
    }

    .signup-text a:hover::after {
      width: 100%;
    }

    .error-message {
      background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
      color: #c62828;
      padding: 14px 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      font-size: 14px;
      border-left: 4px solid #c62828;
      text-align: left;
      animation: shake 0.5s ease-in-out;
    }

    .success-message {
      background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
      color: #2e7d32;
      padding: 14px 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      font-size: 14px;
      border-left: 4px solid #2e7d32;
      text-align: left;
      animation: slideDown 0.5s ease-out;
    }

    @keyframes shake {

      0%,
      100% {
        transform: translateX(0);
      }

      25% {
        transform: translateX(-5px);
      }

      75% {
        transform: translateX(5px);
      }
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Navbar styling for auth pages */
    .ftco-navbar-light.scrolled {
      background: #000 !important;
    }

    /* Password requirements hint */
    .password-hint {
      font-size: 11px;
      color: #a1887f;
      margin-top: 5px;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .password-hint::before {
      content: 'ⓘ';
      font-size: 12px;
    }

    /* Responsive */
    @media (max-width: 576px) {
      .auth-card {
        padding: 35px 22px;
        margin: 20px;
      }

      .auth-logo h1 {
        font-size: 26px;
      }

      .form-row {
        flex-direction: column;
        gap: 0;
      }
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <?php include 'partials/navbar.php'; ?>

  <div class="auth-page">
    <div class="auth-content">
      <div class="auth-card">
        <div class="auth-logo">
          <span class="coffee-icon">☕</span>
          <h1>BREWBEANS</h1>
        </div>
        <p class="auth-subtitle">Join Our Coffee Family</p>

        <?php if ($error): ?>
          <div class="error-message">
            <strong>Oops!</strong> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>

        <?php if ($success): ?>
          <div class="success-message">
            <strong>Welcome!</strong> <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="">
          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="auth-input" placeholder="Enter your email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" id="username" name="username" class="auth-input" placeholder="Choose a username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required />
            </div>
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone" class="auth-input" placeholder="Your phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required />
            </div>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="auth-input" placeholder="Create a password" required />
            <div class="password-hint">Minimum 6 characters</div>
          </div>

          <button type="submit" class="auth-btn">Create Account</button>
        </form>

        <div class="divider"><span>Or sign up with</span></div>

        <div class="social-login">
          <div class="social-btn" onclick="socialSignup('Google')">
            <img src="img/google.svg" alt="Google" />
            Continue with Google
          </div>
          <div class="social-btn" onclick="socialSignup('Facebook')">
            <img src="img/facebook.svg" alt="Facebook" />
            Continue with Facebook
          </div>
          <div class="social-btn" onclick="socialSignup('Apple')">
            <img src="img/apple.svg" alt="Apple" />
            Continue with Apple
          </div>
        </div>

        <div class="auth-links">
          <p class="signup-text">
            Already have an account? <a href="login.php">Login here</a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
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
    function socialSignup(platform) {
      alert(`Pendaftaran dengan ${platform} sedang dikembangkan ☕`);
    }
  </script>
</body>

</html>