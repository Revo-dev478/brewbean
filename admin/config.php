<?php
// Load environment variables (path relative to parent directory)
require_once __DIR__ . '/../env_loader.php';

$host = env('DB_HOST', 'localhost');
$user = env('DB_USERNAME', 'root');
$pass = env('DB_PASSWORD', '');
$db   = env('DB_DATABASE', 'db_brewbeans');

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
