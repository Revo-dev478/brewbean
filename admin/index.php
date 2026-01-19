<?php
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <!-- CSS SB Admin -->
    <link rel="stylesheet" href="vendor/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="css/sb-admin-2.min.css">
</head>



<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        <!-- End Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- Topbar -->
                <?php include 'topbar.php'; ?>
                <!-- End Topbar -->

                <!-- MAIN PAGE -->
                <div class="container-fluid">

                    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

                    <div class="card shadow">
                        <div class="card-body">
                            Sistem Admin berjalan ✔️
                        </div>
                    </div>

                </div>
                <!-- END MAIN PAGE -->

            </div>

            <?php include 'footer.php'; ?>
        </div>

    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>
</html>
