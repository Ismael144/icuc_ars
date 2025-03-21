<?php

// Function to map days of the month to holidays
function mapHolidays($month, $year) {
    // Array to store holidays
    $holidays = array();
    
    // Add your holiday rules here
    // Example: New Year's Day (January 1)
    if ($month == 1 && $year >= 2022) {
        $holidays[] = "New Year's Day";
    }

    // Example: Valentine's Day (February 14)
    if ($month == 2 && $year >= 2022) {
        $holidays[] = "Valentine's Day";
    }

    // Example: Independence Day (July 4)
    if ($month == 7 && $year >= 2022) {
        $holidays[] = "Independence Day";
    }

    // Add more holiday rules as needed

    return $holidays;
}

// Function to check if a given date is a holiday
function isHoliday($day, $month, $year) {
    // Get the array of holidays for the given month and year
    $holidays = mapHolidays($month, $year);

    // Check if the given day matches any holiday
    $holidayDate = date_create("$year-$month-$day");
    $holidayName = date_format($holidayDate, "F j");
    if (in_array($holidayName, $holidays)) {
        return true;
    }

    return false;
}

// Function to calculate attendance percentage considering excuses and holidays
function calculateAttendancePercentage($excuses, $holidays, $totalDays) {
    $attendedDays = 0;
    // Count only the days that are not excused and not holidays
    for ($day = 1; $day <= $totalDays; $day++) {
        if (!isset($excuses[$day]) && !isHoliday($day, $excuses['month'], $excuses['year'])) {
            $attendedDays++;
        }
    }
    return ($attendedDays / $totalDays) * 100;
}

// Get selected month, day, and year from the form
$selectedMonth = $_POST['month'];
$selectedDay = $_POST['day'];
$selectedYear = $_POST['year'];

$totalDays = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear); // Total days in the month

// Simulated attendance data
// You should replace this with your actual attendance data retrieval logic
// For the demonstration purpose, let's assume some attendance data here
$excuses = array(
    'month' => $selectedMonth,
    'year' => $selectedYear,
    1 => true,  // Attendee had an excuse on the 1st day
    14 => true, // Attendee had an excuse on the 14th day
    25 => false // Attendee did not have an excuse on the 25th day
);

$holidays = mapHolidays($selectedMonth, $selectedYear);

$attendancePercentage = calculateAttendancePercentage($excuses, $holidays, $totalDays);

// Output the results
echo "<h2>Attendance and Holidays for $selectedMonth/$selectedDay/$selectedYear</h2>";
echo "Attendance percentage: ". round($attendancePercentage, 1) . "% <br>";
echo "Excused days: " . implode(", ", array_keys(array_filter($excuses, function($key) { return is_int($key); }))) . "<br>";
echo "Holidays: " . implode(", ", $holidays);
echo "Absenteeism";
// Sample data (replace with your actual data)
$totalDays = 30; // Total days in the month
$attendanceDays = 25; // Number of days attended
$excusedDays = [1, 14]; // Days with excuses
$holidays = ['New Year\'s Day']; // List of holidays

// Calculate attendance percentage
$attendancePercentage = ($attendanceDays / $totalDays) * 100;

// Calculate excuses percentage
$excusesCount = count($excusedDays);
$excusesPercentage = ($excusesCount / $totalDays) * 100;

// Calculate holidays percentage
$holidaysCount = count($holidays);
$holidaysPercentage = ($holidaysCount / $totalDays) * 100;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance and Holidays</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-heatmap"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>
    <h2>Attendance and Holidays</h2>
    <canvas id="donutChart" width="300" height="300"></canvas>

    <script>
        // Get data from PHP
        const attendancePercentage = <?php echo json_encode($attendancePercentage); ?>;
        const excusesPercentage = <?php echo json_encode($excusesPercentage); ?>;
        const holidaysPercentage = <?php echo json_encode($holidaysPercentage); ?>;

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
                    `rgba(0, 0, 255, ${blueOpacity(attendancePercentage)})`,
                    `rgba(0, 0, 255, ${blueOpacity(excusesPercentage)})`,
                    `rgba(0, 0, 255, ${blueOpacity(holidaysPercentage)})`
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

<h2>Attendance Percentage for the Year (Excluding Holidays and Excuses)</h2>
    <canvas id="attendanceChart" width="800" height="400"></canvas>

    <script>
        // Sample data for attendance percentages for each month (replace with actual data)
        const monthlyAttendanceData = [
            { month: 'January', attendancePercentage: 90 },
            { month: 'February', attendancePercentage: 85 },
            { month: 'March', attendancePercentage: 47 },
            { month: 'April', attendancePercentage: 36 },
            { month: 'May', attendancePercentage: 95 },
            { month: 'June', attendancePercentage: 78 },
            { month: 'July', attendancePercentage: 80 },
            { month: 'August', attendancePercentage: 40 },
            { month: 'September', attendancePercentage: 50 },
            { month: 'October', attendancePercentage: 95 },
            { month: 'November', attendancePercentage: 60 },
            { month: 'December', attendancePercentage: 70 },
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
                    backgroundColor: 'rgba(54, 162, 235, 0.6)', // Blue color
                    borderColor: 'rgba(54, 162, 235, 1)',
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
    <h2>Attendance Trends</h2>
    <canvas id="attendanceChart1" width="800" height="400"></canvas>

    <script>
        // Sample attendance data (replace with your actual data)
        const attendanceData = {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [{
                label: 'Attendance Percentage',
                data: [80, 85, 90, 87, 92, 88, 85, 89, 91, 86, 90, 88], // Sample attendance percentages for each month
                borderColor: 'blue',
                fill: false
            }]
        };

        // Configuration options for the line chart
        const options = {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Attendance Percentage'
                    },
                    min: 0,
                    max: 100
                }
            }
        };

        // Get the canvas element
        const ctx3 = document.getElementById('attendanceChart1').getContext('2d');

        // Check if there's an existing chart instance
        if (window.attendanceChartInstance !== undefined) {
            // Destroy the existing chart
            window.attendanceChartInstance.destroy();
        }

        // Create the line chart
        window.attendanceChartInstance = new Chart(ctx3, {
            type: 'line',
            data: attendanceData,
            options: options
        });
    </script>
    <h1>Total Excuses Made</h1>
    <p>
        Put there total excuses made here, and also put filters here
        and also displaying the number of excuses made
    </p>
    <h1>Holiday Status</h1>
    <p>Display their holiday status (The holidays they attended, and the ones they did not)</p>
    <h1>User Attendance Comparison.</h1>
    <p>
        Put the user attendance analysis comparison here. 
    </p>
    <h1>Best on leaderboard</h1>
    <p>
        And then display the user who attended the most 
    </p>

</body>
</html>