<?php

use App\enums\SourceLocationPaths;

# To set the project path name e.g. app folder
$appDirPath = strlen(SourceLocationPaths::BASE_PROJECT_DIR_NAME->value) ? "/" . SourceLocationPaths::BASE_PROJECT_DIR_NAME->value . "/app/" : "";

?>


<script src="<?= $appDirPath ?>assets/js/multiselect.js"></script>
<script src="<?= $appDirPath ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $appDirPath ?>assets/libs/simplebar/simplebar.min.js"></script>
<script src="<?= $appDirPath ?>assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- list js-->
<script src="<?= $appDirPath ?>assets/libs/list.js/list.min.js"></script>
<script src="<?= $appDirPath ?>assets/libs/list.pagination.js/list.pagination.min.js"></script>

<!-- App js -->
<script src="<?= $appDirPath ?>assets/js/app.js"></script>
<script src="<?= $appDirPath ?>assets/libs/sweetalert2/sweetalert2.min.js"></script>
<script src="<?= $appDirPath ?>assets/js/pages/sweetalerts.init.js"></script>

<!-- <script src="../plugins/datatables/dataTables.bootstrap4.js"></script> -->
<!-- <script src="../plugins/datatables/dataTables.responsive.min.js"></script> -->
<script src="<?= $appDirPath ?>assets/libs/datatables/pdfmake-0.2.7/pdfmake.min.js"></script>
<script src="<?= $appDirPath ?>assets/libs/toastify/toastify.js"></script>
<script src="<?= $appDirPath ?>assets/js/pages/"></script>
<script src="<?= $appDirPath ?>assets/libs/toastify/custom.js"></script>
<?php $webController->handleAllAlertSessions(); ?>
</body>

</html>