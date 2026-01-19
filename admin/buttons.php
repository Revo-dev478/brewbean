<?php
// Proteksi session (opsional)
// session_start();
// if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- meta, title, css -->
</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

    
        <?php include('buttons.php'); ?>  
        

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- buttons -->
                <?php include('buttons.php'); ?>  <!-- ✅ Ini file terpisah -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Konten buttons di sini -->
                </div>

            </div>

            <!-- Footer -->
            <?php include('buttons.php'); ?>

        </div>
    </div>

    <!-- Logout Modal -->
    <?php include('logout_modal.php'); ?>

    <!-- JS scripts -->
</body>
</html>