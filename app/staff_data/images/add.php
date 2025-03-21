<?php
$pageTitle = "Add Images To The System";
include_once dirname(__DIR__) . "/../includes/header.php";

authProtect("../../auth/signin");

use App\controllers\StaffDataController;

$staffDataController = new StaffDataController;

if (get('id') == null) {
    $webController->redirect("../index");
}

if (get('page') == null) {
    $_GET['page'] = 1;
}

$id = get('id');

$recordId = $staffDataController->validateHelper->filter(get('id'));
$deptRecord = $staffDataController->staffDataModel->getRecordsBy("user_id", get('id'));

if ($deptRecord == false) {
    session_redirect("index", ["_staffData__error" => "Record Access Error: Error couldn't retrieve requested record from database."]);
}

$IMAGE_LIMIT = $staffDataController->imageHelper::IMAGE_UPLOAD_LIMIT;

if ($staffDataController->getStaffDataImagesRecordCount($recordId) > $IMAGE_LIMIT) {
    session_redirect("index?id=$id", ["_attendantsRecord__info" => "Image Upload Limit: You can't upload any more images since the image upload limit is <b>$IMAGE_LIMIT</b>"]);
}

$imagePath = "/icuc_ars/images/staff_images/";

$parsedAllowedImgFormats = implode(', ', array_map(function ($item) {
    return ".$item";
}, $staffDataController->imageHelper->getValidExtensions()));

$imageCount = count($staffDataController->staffDataImagesModel->getRecordsBy("data_id", $deptRecord["id"], true));

?>


<div id="layout-wrapper">
    <?php
    include dirname(dirname(__DIR__)) . "/includes/layouts/topbar.php";
    include dirname(dirname(__DIR__)) . "/includes/layouts/sidebar.php";
    ?>
</div>

<div class="main-content">

    <script src="/icuc_ars/app/assets/js/api/FaceDetectionAPIClient.js"></script>
    <script src="/icuc_ars/app/assets/js/webcam.min.js"></script>

    <script>
        const faceDetectionAPIClient = new FaceDetectionAPIClient(
            "http://localhost/icuc_ars/api/facedetector/detect",
            "<?= $id ?>"
        );
    </script>

    <div class="page-content mx-5">
        <a href="<?= $appDirPath ?>staff_data/images/index?id=<?= $id ?>" class="d-flex align-items-center gap-2"><i
                class="ph ph-arrow-left"></i><span>Go back</span></a>
        <div class="col-xl-12 d-flex align-items-center justify-content-center">
            <div class="modal fade" id="cameraCapture" tabindex="-1" role="dialog" aria-labelledby="img_capture"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header d-flex align-items-center justify-content-between">
                            <h5 class="modal-title" id="img_capture">
                                Capture Images
                            </h5>
                            <button type="button" onclick="faceDetectionAPIClient.closeWebcam()"
                                class="btn btn-sm btn-danger" data-bs-dismiss="modal" id="closeWebcamBtn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body mx-0 px-0">
                            <div class="container-fluid m-0 p-0 position-relative" style="margin: 0 40px;">
                                <div style="background: #eee; margin: 15px; border-radius: 5px;">
                                    <div id="webcam-capture">
                                        Loading, please wait ...
                                    </div>
                                </div>
                                <button onclick="faceDetectionAPIClient.captureImage()"
                                    class="btn btn-sm btn-success d-flex align-items-center gap-2"
                                    style="position: absolute; top: 10px; right: 50px;">
                                    <span><i class="fas fa-camera"></i></span>
                                    <span>
                                        Capture
                                    </span>
                                </button>
                            </div>

                            <style>
                                #captured-images {
                                    padding: 10px;
                                    height: 100px;
                                    background: #eee;
                                }

                                #captured-images>img {
                                    width: 80px;
                                    border-radius: 10px;
                                    height: 80px;
                                }
                            </style>
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="mx-3 my-2">Captured Images</h6>
                                <div class="control-btns mx-3 my-2">
                                    <button onclick="faceDetectionAPIClient.sendWebCamImagesToBackend()"
                                        class="btn btn-sm btn-primary">
                                        <span><i class="fas fa-server"></i></span>
                                        <span>
                                            Upload
                                        </span>
                                    </button>
                                    <button onclick="faceDetectionAPIClient.resetWebcam()"
                                        class="btn btn-sm btn-danger">
                                        <span>
                                            <i class="fas fa-loop"></i>
                                        </span>
                                        <span>
                                            Reset
                                        </span>
                                    </button>
                                    </button>
                                </div>
                            </div>
                            <div id="captured-images"
                                class="mx-3 d-flex align-items-center justify-content-center gap-2"
                                style="border-radius: 5px;"></div>

                        </div>
                    </div>
                </div>
            </div>

            <script>
                var cameraCapture = document.getElementById('cameraCapture');

                cameraCapture.addEventListener('show.bs.modal', function (event) {
                    // Button that triggered the modal
                    let button = event.relatedTarget;
                    // Extract info from data-bs-* attributes
                    let recipient = button.getAttribute('data-bs-whatever');
                    // Use above variables to manipulate the DOM
                });
            </script>

            <form id="fileUploadForm" class="w-100" method="POST" enctype="multipart/form-data">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="record-info d-flex flex-column my-3">
                        <h3 class="my-1"><?= $deptRecord['first_name'] . ' ' . $deptRecord['last_name'] ?></h3>
                        <b class="font-bold">Has <?= $imageCount ?> Images</b>
                    </div>
                    <div class="refresh-button">
                        <a href="" class="btn btn-sm btn-dark">Refresh</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Drop Your Images Here</h4>
                        <div class="right-section d-flex align-items-center gap-2">
                            <div class="button camera-button btn btn-sm btn-light d-flex align-items-center gap-2" title="Take camera shot"
                                data-bs-toggle="modal" data-bs-target="#cameraCapture"
                                onclick="faceDetectionAPIClient.configure()">
                                <i class="bi bi-camera" style="font-size: 16px;"></i>
                                <span>
                                    Take Images
                                </span>
                            </div>
                            
                            <button class="btn btn-sm btn-success d-flex align-items-center gap-2">
                                <span>
                                <i class="bi bi-arrow-up" style="font-size: 15px;"></i>
            </span>
                                <span>
                                    Upload
            </span>
                            </button>
                            <!-- <input type="submit" value="Upload" class="btn btn-sm btn-success" style="font-size: 13px;"> -->
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <p class="text-muted small">Drag and drop the images to be uploaded here, and click the upload
                            button to upload them, <span class="text-info"> limit to numbers of images to upload is
                                <?= $IMAGE_LIMIT ?> </span>.
                            <br>
                            <span class="text-info">Note: If a face is not detected or more than two faces are detected
                                in the image, it will be automatically discarded to avoid confusion.</span>
                        </p>

                        <div class="dropzone dz-clickable position-relative">
                            <input type="file" name="images[]"
                                style="position: absolute; top: 0; bottom: 0; right: 0; left: 0; cursor: pointer !important; opacity: 0;"
                                accept="<?= $parsedAllowedImgFormats ?>" onchange="carryOutOperations()" id="fileInput"
                                multiple>
                            <div class="dz-message needsclick">
                                <div class="mb-3">
                                    <i class="display-1 text-muted bx bx-cloud-upload"></i>
                                </div>
                                <h4>Drop images here or click to upload.</h4>
                            </div>
                        </div>

                        <div class="results mt-2" id="upload-results"></div>
                    </div>
                    <!-- end card body -->
                </div>
            </form>
        </div>
        <ul id="fileList" class="list-unstyled mb-0" id="imageContainer"></ul>
    </div>
</div>
<script>
    faceDetectionAPIClient.sendImagesToBackend(document.getElementById("fileUploadForm"));
</script>
<script src="/icuc_ars/app/assets/js/imageUploader.js"></script>

<?php include_once dirname(__DIR__) . "/../includes/footer.php"; ?>