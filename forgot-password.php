<?php
session_start();
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        if ($koneksi) {
            // Check if email exists
            $stmt = $koneksi->prepare("SELECT id_user FROM tabel_user WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Simulation: In a real app, send email here.
                    $message = "A reset link has been sent to " . htmlspecialchars($email) . " (Simulation)";
                } else {
                    $error = "Email address not found.";
                }
                $stmt->close();
            } else {
                $error = "System error: Unable to prepare statement.";
            }
        } else {
            $error = "Database connection unavailable. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>BrewBeans - Forgot Password</title>
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
            font-size: 24px;
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

        .alert {
            padding: 10px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .back-link {
            margin-top: 20px;
            display: block;
            font-size: 14px;
            color: #6d4c41;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Forgot Password?</h1>
        <p>Enter your email to receive reset instructions.</p>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required />
            <button type="submit">Reset Password</button>
        </form>

        <a href="login.php" class="back-link">Back to Login</a>
    </div>
</body>

</html>