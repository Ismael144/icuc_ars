<?php
$pageTitle = "Maintenance Page";
include_once "includes/header.php";

?>

<body>

    <section class="auth-page-wrapper py-5 position-relative d-flex align-items-center justify-content-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="card mb-0">
                        <div class="row g-0 align-items-center">
                            <!--end col-->
                            <div class="col-xxl-6 mx-auto">
                                <div class="card mb-0 border-0 shadow-none mb-0">
                                    <div class="card-body p-sm-5 m-lg-4">
                                        <div class="text-center">
                                            <div class="mb-5">
                                                <h3>Site is Under Maintenance</h3>
                                                <p class="text-muted fs-md">Please check back in sometime</p>
                                            </div>
                                            <div class="row justify-content-center">
                                                <div class="col-xl-8 col-sm-5 col-8">
                                                    <img src="assets/images/auth/maintenance.png" alt="" class="img-fluid">
                                                </div>
                                            </div>
                                            <div class="mt-4 pt-3">
                                                <a href="dashboard" class="btn btn-primary"><i class="mdi mdi-home me-1"></i> Back to Home</a>
                                            </div>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                            </div>
                            <!--end col-->
                        </div>
                        <!--end row-->
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->
        </div>
        <!--end container-->
    </section>


    <?php include_once "includes/footer.php"; ?>