<?php
$pageTitle = "User Analysis";
include dirname(__DIR__) . "/includes/header.php" ?>

<?php

// header('Location: ../maintenance');

use App\controllers\AttendanceDataAnalysisController;

// Checking whether the department or user selected exists 

// if (is_null(get('dept'))) session_redirect("index", ["_attendance_analysis__error" => "Record Access Error: Sorry, We can't find the requested record from database!"]);

// $userDeptController->userDeptModel->getRecordsBy('id', get('dept')); 

alert(_attendance_analysis__info: "Attendance Analysis: There is not enough data to run an analysis on it...");

$attendanceDataAnalysisController = new AttendanceDataAnalysisController(departmentId: 7, staffMemberId: get('user'));

$viewContent = false;

?>

<?php
include dirname(__DIR__) . "/includes/layouts/topbar.php";
include dirname(__DIR__) . "/includes/layouts/sidebar.php";
?>

<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-chart-heatmap"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.8.0/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> -->

<div class="main-content">
    <div class="page-content">
        <div class="d-flex align-items-center gap-2 my-2">
            <span>
                <i class="fas fa-arrow-left"></i>
            </span>
            <span>
                <a href="index">Go back</a>
            </span>
        </div>
            <div class="intro-banner alert alert-info w-100 d-flex align-items-center justify-content-center flex-column bg-light mb-4" style="height: 250px; border-radius: 10px;">
            <img src="../assets/images/logo.png" alt="" width="100px" height="100px">
            <h4 class="my-2">Attendance Analysis Notice</h4>
            <p>There is not enough data to run an analysis on it.</p>
        </div>
    </div>
    <?php if ($viewContent) : ?>
        <div class="page-content">
            <h4 class="my-3 text-capitalize">Analysis On <?= $attendanceDataAnalysisController->getDepartmentInfo()["name"] ?> Department</h4>
            <form action="" method="get">
                <input type="date" name="date_filter" id="">
                <input type="submit" value="Filter">
            </form>
            <div class="row">
                <div class="col-3">
                    <div class="card">
                        <div class="card-body">
                            <b class="card-title">
                                <small>
                                    Total Working Hours
                                </small>
                            </b>
                            <h3 class="mt-1"><?= $attendanceDataAnalysisController->getTotalAttendanceTimeForGivenDept()['hours']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body">
                            <b class="card-title">
                                <small>
                                    Total Working Days
                                </small>
                            </b>
                            <h3 class="mt-1"><?= $attendanceDataAnalysisController->getTotalAttendanceTimeForGivenDept()['days']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body">
                            <b class="card-title">
                                <small>
                                    Total Working Months
                                </small>
                            </b>
                            <h3 class="mt-1"><?= $attendanceDataAnalysisController->getTotalAttendanceTimeForGivenDept()['months']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <div class="card-body">
                            <b class="card-title">
                                <small>
                                    Total Excuses Made
                                </small>
                            </b>
                            <h3 class="mt-1"><?= count($attendanceDataAnalysisController->getAttendanceExcusesInformationForGivenDept()); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="card">
                        <div class="card-body">
                            <b class="card-title mb-2">
                                <small>Staff Member Excuses Rate</small>
                            </b>
                            <div>
                                <small>There are <?= count($attendanceDataAnalysisController->getAttendanceExcusesInformationForGivenDept()); ?> Excuses made by staff members in this department</small>
                            </div>
                            <div class="my-2">
                                <a href="">View All <i class="fas fa-arrow-right"></i></a>
                            </div>

                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <b class="card-title mb-2">
                                <small>Users In Department</small>
                            </b>
                            <div>
                                <small>There are <?= count($attendanceDataAnalysisController->getStaffMembersDataInGivenDept()) ?> Users in this department</small>
                            </div>
                            <div class="users_in_dept">
                                <div class="search-bar">
                                    <input type="search" name="" class="outline: 2px solid green;" id="" style="margin: 10px 0; padding: 6px 10px; border-radius: 15px; width: 100%; border: 0; background: #eee;" placeholder="Search for users...">
                                </div>
                                <style>
                                    .users-container::-webkit-scrollbar {
                                        background: #eee;
                                        width: 4px;
                                        border-radius: 5px;
                                    }

                                    .users-container::-webkit-scrollbar-track {
                                        background: white;
                                    }

                                    .users-container::-webkit-scrollbar-thumb {
                                        width: 5px;
                                        border-radius: 5px;
                                        background: #eee;
                                    }
                                </style>
                                <div class="users-container" style="max-height: 150px; overflow-y: auto;">
                                    <style>
                                        .single-user {
                                            transition: all 0.2s ease-in-out;
                                            padding: 10px;
                                            border-radius: 5px;
                                            margin-right: 4px;
                                        }

                                        .single-user:hover {
                                            background: #eee;
                                        }
                                    </style>
                                    <?php foreach ($attendanceDataAnalysisController->getStaffMembersDataInGivenDept() as $staffMemberUser) :  ?>
                                        <a href="../users/single?id=<?= $staffMemberUser['user_id'] ?>" class="text-dark single-user-link">
                                            <div class="single-user d-flex align-items-center gap-2 my-2">
                                                <div class="img">
                                                    <img src="<?= userImage($staffMemberUser['user_info']['avatar']) ?>" width="40px" height="40px" style="border-radius: 50%;" alt="">
                                                </div>
                                                <div class="user-details">
                                                    <b>
                                                        <?= $staffMemberUser['user_info']["username"] ?>
                                                    </b>
                                                    <div class="email">
                                                        <?= $staffMemberUser['user_info']["email"] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <b class="card-title mb-3">
                                <small>Attendance Rate (%)</small>
                            </b>
                            <canvas id="donutChart" width="300" height="300"></canvas>

                            <script>
                                // Get data from PHP
                                const attendancePercentage = 70;
                                const excusesPercentage = 21;
                                const holidaysPercentage = 9;

                                // Calculate opacity for blue color based on percentage
                                const blueOpacity = (percentage) => {
                                    return (percentage / 100) * 0.8 + 0.2; // Opacity ranges from 0.2 to 1.0
                                };

                                // Create a data object for Chart.js
                                const data = {
                                    labels: ['Attendance', 'Excuses', 'Holidays'],
                                    datasets: [{
                                        data: [attendancePercentage, excusesPercentage, holidaysPercentage],
                                        backgroundColor: [
                                            `rgba(41, 183, 104, ${blueOpacity(attendancePercentage)})`,
                                            `rgba(41, 183, 104, ${blueOpacity(excusesPercentage)})`,
                                            `rgba(41, 183, 104, ${blueOpacity(holidaysPercentage)})`
                                        ]
                                    }]
                                };

                                // Get the canvas element
                                const canvas = document.getElementById('donutChart');
                                const ctx = canvas.getContext('2d');

                                // Create a new Chart instance
                                new Chart(ctx, {
                                    type: 'doughnut',
                                    data: data,
                                    options: {
                                        responsive: false,
                                        legend: {
                                            display: false
                                        }
                                    }
                                });

                                // Draw percentage labels
                                const centerX = canvas.width / 2;
                                const centerY = canvas.height / 2;
                                const radius = Math.min(centerX, centerY) * 0.7; // 70% of the smaller dimension

                                ctx.font = '16px Arial';
                                ctx.fillStyle = '#333';
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';

                                // Draw attendance percentage label
                                ctx.fillText(attendancePercentage.toFixed(2) + '%', centerX, centerY - radius / 2);

                                // Draw excuses percentage label
                                ctx.fillText(excusesPercentage.toFixed(2) + '%', centerX - radius * Math.cos(Math.PI / 3), centerY + radius / 2);

                                // Draw holidays percentage label
                                ctx.fillText(holidaysPercentage.toFixed(2) + '%', centerX + radius * Math.cos(Math.PI / 3), centerY + radius / 2);
                            </script>

                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5>Graph showing the attendance performance rate each month</h5>
                            <div id="attendance-rate-graph">
                                <canvas id="attendanceChart"></canvas>

                                <script>
                                    // Sample data for attendance percentages for each month (replace with actual data)
                                    const monthlyAttendanceData = [{
                                            month: 'January',
                                            attendancePercentage: 90
                                        },
                                        {
                                            month: 'February',
                                            attendancePercentage: 85
                                        },
                                        {
                                            month: 'March',
                                            attendancePercentage: 47
                                        },
                                        {
                                            month: 'April',
                                            attendancePercentage: 36
                                        },
                                        {
                                            month: 'May',
                                            attendancePercentage: 95
                                        },
                                        {
                                            month: 'June',
                                            attendancePercentage: 78
                                        },
                                        {
                                            month: 'July',
                                            attendancePercentage: 80
                                        },
                                        {
                                            month: 'August',
                                            attendancePercentage: 40
                                        },
                                        {
                                            month: 'September',
                                            attendancePercentage: 50
                                        },
                                        {
                                            month: 'October',
                                            attendancePercentage: 95
                                        },
                                        {
                                            month: 'November',
                                            attendancePercentage: 60
                                        },
                                        {
                                            month: 'December',
                                            attendancePercentage: 70
                                        },
                                        // Add data for remaining months...
                                    ];

                                    // Get canvas element
                                    const ctx2 = document.getElementById('attendanceChart').getContext('2d');

                                    // Extract month names and attendance percentages for chart labels and data
                                    const months = monthlyAttendanceData.map(data => data.month);
                                    const attendancePercentages = monthlyAttendanceData.map(data => data.attendancePercentage);

                                    // Create column chart
                                    new Chart(ctx2, {
                                        type: 'bar',
                                        data: {
                                            labels: months,
                                            datasets: [{
                                                label: 'Attendance Percentage',
                                                data: attendancePercentages,
                                                backgroundColor: '#2dcb73', // Blue color
                                                borderColor: '#eee',
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            scales: {
                                                y: {
                                                    beginAtZero: true,
                                                    title: {
                                                        display: true,
                                                        text: 'Attendance Percentage (%)'
                                                    }
                                                },
                                                x: {
                                                    title: {
                                                        display: true,
                                                        text: 'Month'
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <canvas id="myChart111"></canvas>

                                <script>
                                    const ctx5 = document.getElementById('myChart111').getContext('2d');
                                    document.getElementById('myChart').getContext('2d');
                                    const myChart = new Chart(ctx5, {
                                        type: 'bar', // Keep type as 'bar'
                                        // ... rest of your chart configuration
                                        options: {
                                            indexAxis: 'y' // Set indexAxis to 'y' for horizontal bars
                                        },
                                        data: {
                                            labels: ['A', 'B', 'C'],
                                            datasets: [{
                                                label: 'My Dataset',
                                                data: [10, 20, 30],
                                                backgroundColor: ['#2ecc71', '#3498db', '#9b59b6'],
                                                borderColor: ['#2ecc71', '#3498db', '#9b59b6'],
                                                borderWidth: 1
                                            }]
                                        },
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include dirname(__DIR__) . "/includes/footer.php" ?>