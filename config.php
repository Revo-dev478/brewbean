<?php
// Load environment variables
require_once __DIR__ . '/env_loader.php';

// Set session lifetime to 1 day (Only if session not started)
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 86400);
    session_set_cookie_params(86400);
    session_start();
}

$host = env('DB_HOST', 'localhost');
$username = env('DB_USERNAME', 'root');
$password = env('DB_PASSWORD', '');
$database = env('DB_DATABASE', 'db_brewbeans');

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8");
