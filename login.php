<?php
session_start();

// Jika sudah login, redirect ke home.php
if (!empty($_SESSION['id_user'])) {
  header("Location: utama.php");
  exit();
}

require_once 'config.php';

$error = '';

// Ambil redirect dari query (jika ada), default ke home.php
// Ambil redirect dari query (jika ada), default ke utama.php
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'utama.php';
// Sanitasi sederhana: jika mengandung 'http' atau '//' anggap tidak aman
if (stripos($redirect, 'http') === 0 || strpos($redirect, '//') !== false) {
  $redirect = 'utama.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = isset($_POST['email']) ? trim($_POST['email']) : '';
  $pass = isset($_POST['password']) ? $_POST['password'] : '';

  if (empty($email) || empty($pass)) {
    $error = "Email dan password wajib diisi.";
  } else {
    // Gunakan mysqli prepared statement
    $stmt = $koneksi->prepare("SELECT id_user, username, email, password FROM tabel_user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();
      if (password_verify($pass, $user['password'])) {
        // Simpan session dengan kolom yang benar
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username'];

        // gunakan redirect yang sudah disanitasi
        header("Location: " . $redirect);
        exit();
      } else {
        $error = "Email atau password salah.";
      }
    } else {
      $error = "Email atau password salah.";
    }
    $stmt->close();
  }
}

// Siapkan action form agar mempertahankan redirect jika ada
$formAction = 'login.php' . (isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : '');
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BrewBeans Login</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      margin: 0;
      padding: 0;
      background: #f8f5f1;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: #fff;
      width: 400px;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    h1 {
      color: #3e2723;
      font-size: 26px;
      margin-bottom: 10px;
    }

    p {
      color: #6d4c41;
      font-size: 14px;
      margin-bottom: 25px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 14px;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #3e2723;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      margin-top: 10px;
      transition: background 0.3s;
    }

    button:hover {
      background: #5d4037;
    }

    .divider {
      margin: 20px 0;
      font-size: 14px;
      color: #8d6e63;
    }

    .social-login {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .social-btn {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      border: 1px solid #ccc;
      border-radius: 10px;
      padding: 10px;
      cursor: pointer;
      background: #fff;
      font-weight: 500;
      transition: all 0.2s;
    }

    .social-btn:hover {
      background: #f1f1f1;
    }

    .bottom-text {
      margin-top: 20px;
      font-size: 14px;
      color: #6d4c41;
    }

    .bottom-text a {
      color: #3e2723;
      font-weight: bold;
      text-decoration: none;
    }

    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>BREWBEANS</h1>
    <p>COFFEE ROASTERY SHOP</p>

    <?php if (!empty($error)): ?>
      <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">LOGIN</button>
    </form>

    <div class="divider">Or</div>

    <div class="social-login">
      <div class="social-btn" onclick="alert('Login dengan Google sedang dalam pengembangan ☕')">
        <img src="img/google.svg" width="20" />
        Sign up with Google
      </div>
      <div class="social-btn" onclick="alert('Login dengan Facebook sedang dalam pengembangan ☕')">
        <img src="img/facebook.svg" width="20" />
        Sign up with Facebook
      </div>
      <div class="social-btn" onclick="alert('Login dengan Apple sedang dalam pengembangan ☕')">
        <img src="img/apple.svg" width="20" />
        Sign up with Apple
      </div>
    </div>

    <div class="bottom-text" style="margin-top: 15px;">
      <a href="forgot-password.php" style="font-size: 13px; color: #8d6e63;">Forgot Password?</a>
    </div>

    <div class="bottom-text">
      Don’t have an account yet? <a href="sign-up.php">Create an Account</a>
    </div>
  </div>
</body>

</html>