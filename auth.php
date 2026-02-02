<?php
// auth.php - Helper untuk cek status login

function isLoggedIn()
{
    return isset($_SESSION['id_user']) && !empty($_SESSION['id_user']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireAdmin()
{
    // Pastikan session sudah start
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Cek apakah user logged in DAN memiliki flag is_admin
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        // Jika bukan admin, redirect ke login atau home
        header("Location: ../login.php");
        exit();
    }
}
