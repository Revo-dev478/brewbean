<?php
// topbar.php
?>

<style>
    .topbar {
        height: 60px;
        background: #fff;
        border-bottom: 1px solid #ddd;
        padding: 10px 25px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-left: 240px; /* sesuai lebar sidebar */
        font-family: Arial, sans-serif;
    }

    .topbar .user-box {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        color: #333;
        font-size: 14px;
    }

    .topbar .user-box img {
        border-radius: 50%;
        width: 35px;
        height: 35px;
    }
</style>

<div class="topbar">
    <div class="user-box">
        <img src="https://i.pravatar.cc/35" alt="user">
        Admin
    </div>
</div>
