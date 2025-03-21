<?php
include dirname(__DIR__) . "/starter_tools/tools.php";
?>

<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-sidebar="dark" data-sidebar-size="lg" data-preloader="disable" data-theme="default" data-topbar="light" data-bs-theme="light">
<style>
    .app-menu.active {
        margin-left: -100% !important; 
    }

    .app-menu.active + .main-content { 
        transition: .3 ease; 
        margin-left: 0 !important;
    }

    .app-menu.active + .page-topbar { 
        transition: .3 ease; 
        margin-left: 0 !important;
        width: 100% !important; 
    }
</style>
<head>
    <meta charset="utf-8">
    <title>ICUC ARM System | <?= $pageTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="ICUC Attendance Registry Management And Monitoring System" name="description">
    <meta content="" name="author">
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= $appDirPath ?>assets/images/logo.png">
    <script src="<?= $appDirPath ?>assets/js/layout.js"></script>
    <!-- Layout config Js -->
    <script src="<?= $appDirPath ?>assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="<?= $appDirPath ?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Icons Css -->
    <link href="<?= $appDirPath ?>assets/css/icons.min.css" rel="stylesheet" type="text/css">
    <link href="<?= $appDirPath ?>assets/css/multiselect.css" rel="stylesheet" type="text/css">
    <!-- App Css-->
    <link href="<?= $appDirPath ?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <!-- custom Css-->
    <link href="<?= $appDirPath ?>assets/libs/fontawesome/css/all.css" rel="stylesheet" type="text/css">
    <link href="<?= $appDirPath ?>assets/css/app.min.css" rel="stylesheet" type="text/css">
    <link href="<?= $appDirPath ?>assets/css/custom.css" rel="stylesheet" type="text/css">
    <!-- Datatables -->
    <link rel="stylesheet" href="<?= $appDirPath ?>assets/css/datatables.css">
    <link rel="stylesheet" href="<?= $appDirPath ?>assets/libs/dropzone/dropzone.css" type="text/css">
    <link rel="stylesheet" href="<?= $appDirPath ?>assets/libs/choices/choices.min.css" type="text/css">
    <script src="<?= $appDirPath ?>assets/libs/datatables/jQuery-3.7.0/jquery-3.7.0.js"></script>
    <script src="<?= $appDirPath ?>assets/libs/choices/choices.min.js"></script>
    <script src="<?= $appDirPath ?>assets/libs/datatables/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $appDirPath ?>assets/libs/datatables/DataTables-1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="<?= $appDirPath ?>assets/libs/datatables/DataTables-1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.7.3/axios.min.js"></script>
    <script src="<?= $appDirPath ?>assets/js/popper.min.js"></script>
    <link rel="stylesheet" href="<?= $appDirPath ?>assets/libs/toastify/toastify.css">
    <script>
        window.addEventListener("load", () => {
            const loader = document.querySelector(".loader");

            loader.classList.add("loader--hidden");

            // loader.addEventListener("transitionend", () => {
            //     document.body.hasChildNodes(loader) 
            //     document.body.removeChild(loader);
            // });
        });
    </script>
    <div class="loader">
        <div class="loader-container d-flex align-items-center justify-content-center flex-column gap-3" style="height: 300px;">
            <div class="spinner-border text-success" style="width: 100px; height: 100px;"></div>
            <div class="text-dark mt-1" style="font-weight: bold;">Loading, Please Wait ...</div>
        </div>
    </div>