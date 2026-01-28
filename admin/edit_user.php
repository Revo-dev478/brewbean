<?php
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: data_user.php");
    exit();
}

$id = $_GET['id'];

// Mengambil data user berdasarkan ID
$query = false;
if ($koneksi) {
    $query = mysqli_query($koneksi, "SELECT * FROM tabel_user WHERE id_user='$id'");
}

$user = false;
if ($query) {
    $user = mysqli_fetch_assoc($query);
}

if (!$user) {
    echo "User tidak ditemukan!";
    exit();
}

if (isset($_POST['submit'])) {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $password = $_POST['password'];

    // Update query
    if (!empty($password)) {
        // Jika password diisi, update password juga
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE tabel_user SET username='$username', email='$email', phone='$phone', password='$hashedPassword' WHERE id_user='$id'";
    } else {
        // Jika password kosong, jangan update password
        $updateQuery = "UPDATE tabel_user SET username='$username', email='$email', phone='$phone' WHERE id_user='$id'";
    }

    if ($koneksi && mysqli_query($koneksi, $updateQuery)) {
        echo "<script>alert('Data user berhasil diupdate!'); window.location='data_user.php';</script>";
    } else {
        echo "Error: " . ($koneksi ? mysqli_error($koneksi) : "Database disconnected");
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin BrewBeans</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>Edit User</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Password (Isi jika ingin mengganti)</label>
                        <input type="password" class="form-control" name="password" placeholder="Biarkan kosong jika tidak ingin mengganti password">
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary">Update</button>
                    <a href="data_user.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>