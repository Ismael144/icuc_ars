<?php
$pageTitle = "Page Not Found";
include_once dirname(__DIR__)."/includes/header.php";

?>
<section class="auth-page-wrapper position-relative d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class=" mb-0">
                    <div class="row g-0 align-items-center">
                        <!--end col-->
                        <div class="col-xxl-6 mx-auto">
                            <div class="mb-0 border-0 shadow-none mb-0">
                                <div class="card-body p-sm-5 m-lg-4">
                                    <div class="error-img text-center px-5">
                                        <img src="<?= $appDirPath ?>assets/images/svg/404.svg" class="img-fluid" alt="" width="400px">
                                    </div>
                                    <div class="mt-2 text-center pt-3">
                                        <div class="position-relative">
                                            <h4 class="fs-2xl error-subtitle text-uppercase mb-0">OOps, page not found</h4>
                                            <p class="fs-base text-muted mt-3">It will be as simple as Occidental in fact,
                                                it will Occidental to an English person</p>
                                            <div class="mt-4">
                                                <a href="<?= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "/icuc_crm/app/dashboard" ?>" class="btn btn-primary"><i class="fas fa-arrow-left me-2"></i>Go back</a>
                                            </div>
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

<?php include_once dirname(__DIR__)."/includes/footer.php"; ?>
