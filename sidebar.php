<?php
// sidebar.php
?>

<style>
    .sidebar {
        width: 240px;
        height: 100vh;
        background: #1f1f1f;
        color: #fff;
        position: fixed;
        left: 0;
        top: 0;
        padding: 20px;
        font-family: Arial, sans-serif;
    }

    .sidebar h2 {
        margin-bottom: 20px;
        text-transform: uppercase;
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        letter-spacing: 1px;
    }

    .sidebar a {
        display: block;
        color: #e0e0e0;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 5px;
        text-decoration: none;
        transition: 0.3s;
        font-size: 15px;
    }

    .sidebar a:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .sidebar .active {
        background: #c49b63;
        color: #fff;
    }
</style>