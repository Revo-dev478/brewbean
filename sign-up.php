<?php
// sign-up.php

session_start();
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BrewBeans Sign Up</title>
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
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
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
      margin: 8px 0;
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
    .error, .success {
      padding: 8px;
      margin-bottom: 10px;
      border-radius: 5px;
    }
    .error {
      background: #ffebee;
      color: #c62828;
    }
    .success {
      background: #e8f5e9;
      color: #2e7d32;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>BREWBEANS</h1>
    <p>COFFEE ROASTERY</p>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="email" name="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required />
      <input type="text" name="username" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required />
      <input type="tel" name="phone" placeholder="No Telepon" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">CREATE ACCOUNT</button>
    </form>

    <div class="divider">Or</div>

    <div class="social-login">
      <div class="social-btn" onclick="socialSignup('Google')">
        <img src="https://www.svgrepo.com/show/355037/google.svg" width="20" />
        Sign up with Google
      </div>
      <div class="social-btn" onclick="socialSignup('Facebook')">
        <img src="https://www.svgrepo.com/show/452196/facebook-1.svg" width="20" />
        Sign up with Facebook
      </div>
      <div class="social-btn" onclick="socialSignup('Apple')">
        <img src="https://www.svgrepo.com/show/349442/apple.svg" width="20" />
        Sign up with Apple
      </div>
    </div>

    <div class="bottom-text">
      Already have an account? <a href="login.php">Login Here</a>
    </div>
  </div>

  <script>
    function socialSignup(platform) {
      alert(`Pendaftaran dengan ${platform} sedang dikembangkan ☕`);
    }
  </script>
</body>
</html>