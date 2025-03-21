<?php
$pageTitle = "Dashboard Page";
include_once "../includes/header.php";

authProtect("auth/signin");

use App\models\StaffData;
use App\controllers\{UserController, AttendanceDataController};
use App\models\Attendance;

$userController = new UserController;
$attendanceDataController = new AttendanceDataController;
?>
<!-- Begin page -->
<div id="layout-wrapper">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.8.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.0/build/global/luxon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.1.0/dist/chartjs-adapter-luxon.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }

        .online {
            background-color: #28a745;
        }

        .offline {
            background-color: #dc3545;
        }

        .search-bar {
            position: relative;
        }

        .search-bar input {
            padding-right: 30px;
        }

        .search-bar i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }
    </style>

    <!-- ========== App Menu ========== -->
    <?php include "../includes/layouts/sidebar.php" ?>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>
    <?php include "../includes/layouts/topbar.php" ?>
</div>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="container my-3">
                <h3 class="text-center mb-4">System Monitor</h3>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-server me-2"></i>System Status
                                </h5>
                                <p class="card-text">
                                    <span class="status-indicator online"></span>
                                    <strong>Online</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-memory me-2"></i>Memory Usage
                                </h5>
                                <p class="card-text">
                                    <strong>197.77 MB</strong>
                                    <small class="text-muted">(as of 2024-08-09 15:58:08)</small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-database me-2"></i>Data Usage
                                </h5>
                                <p class="card-text">
                                    <strong>120.45 MB</strong>
                                    <small class="text-muted">(as of 2024-08-09 15:58:08)</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Error Logs
                                    </h5>
                                    <div class="mb-3 search-bar">
                                        <input type="text" class="form-control" id="searchInput" placeholder="Search logs...">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Timestamp</th>
                                                    <th>Error Message</th>
                                                </tr>
                                            </thead>
                                            <tbody id="error-logs">
                                                <!-- Error logs will be populated here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
                <script>
                    // Function to populate error logs
                    function populateErrorLogs() {
                        const errorLogs = [
                            "2024-08-08 23:24:59,946 - logger - ERROR - Failed to download image from http://localhost/icuc_ars/images/staff_images/ICUC-65dac7ddc502a-2024_02_25.jpg.",
                            "2024-08-08 23:24:59,965 - logger - ERROR - Failed to download image from http://localhost/icuc_ars/images/staff_images/ICUC-65dac7f0ace4a-2024_02_25.jpg.",
                            "2024-08-08 23:24:59,994 - logger - ERROR - Failed to download image from http://localhost/icuc_ars/images/staff_images/ICUC-65db7c961fc7b-2024_02_25.jpg.",
                            "2024-08-08 23:25:00,004 - logger - ERROR - Failed to download image from http://localhost/icuc_ars/images/staff_images/ICUC-65dac7f11ba86-2024_02_25.jpg.",
                            "2024-08-08 23:25:00,028 - logger - ERROR - Failed to download image from http://localhost/icuc_ars/images/staff_images/ICUC-65d7a1b80f600-2024_02_22.jpg."
                        ];

                        const errorLogsTable = document.getElementById('error-logs');
                        errorLogs.forEach(log => {
                            const [timestamp, ...message] = log.split(' - ');
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${timestamp}</td>
                                <td>${message.join(' - ')}</td>
                            `;
                            errorLogsTable.appendChild(row);
                        });
                    }

                    // Function to filter logs based on search input
                    function filterLogs() {
                        const searchValue = document.getElementById('searchInput').value.toLowerCase();
                        const logRows = document.querySelectorAll('#error-logs tr');

                        logRows.forEach(row => {
                            const timestamp = row.cells[0].textContent.toLowerCase();
                            const message = row.cells[1].textContent.toLowerCase();

                            if (timestamp.includes(searchValue) || message.includes(searchValue)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    }

                    // Event listener for search input
                    document.getElementById('searchInput').addEventListener('input', filterLogs);

                    // Call the function to populate error logs
                    populateErrorLogs();
                </script>
            </div>
        </div>
    </div><!-- end row -->
</div><!-- end layout-wrapper -->

<?php include_once "../includes/footer.php"; ?>
