<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Percentage</title>
    <style>
        canvas {
            display: block;
            margin: 0 auto;
            border: 1px solid #ccc;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <h2>Attendance and Holidays</h2>
    <!-- Your form for selecting month, day, and year -->
    <form action="calculate_attendance.php" method="post">
        <label for="month">Month:</label>
        <select name="month" id="month">
            <!-- Options for selecting month -->
            <?php
            for ($i = 1; $i <= 12; $i++) {
                $monthName = date("F", mktime(0, 0, 0, $i, 1));
                echo "<option value='$i'>$monthName</option>";
            }
            ?>
        </select>
        <label for="day">Day:</label>
        <select name="day" id="day">
            <!-- Options for selecting day -->
            <?php
            for ($i = 1; $i <= 31; $i++) {
                echo "<option value='$i'>$i</option>";
            }
            ?>
        </select>
        <label for="year">Year:</label>
        <select name="year" id="year">
            <!-- Options for selecting year -->
            <?php
            $currentYear = date("Y");
            for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                echo "<option value='$i'>$i</option>";
            }
            ?>
        </select>
        <input type="submit" value="Calculate">
    </form>

    <!-- Canvas for the circular graph -->
    <canvas id="attendanceChart" width="200" height="200"></canvas>

    <script>
        // Get attendance percentage from PHP
        const attendancePercentage = <?php echo isset($attendancePercentage) ? $attendancePercentage : '0'; ?>;

        // Get canvas element
        const canvas = document.getElementById('attendanceChart');
        const ctx = canvas.getContext('2d');

        // Set circle center and radius
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = canvas.width / 2;

        // Convert percentage to radians
        const startAngle = -Math.PI / 2;
        const endAngle = (attendancePercentage / 100) * (Math.PI * 2) + startAngle;

        // Draw attendance percentage
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.closePath();
        ctx.fillStyle = 'green';
        ctx.fill();

        // Draw the border of the circle
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, 0, Math.PI * 2);
        ctx.strokeStyle = '#000';
        ctx.lineWidth = 2;
        ctx.stroke();
    </script>
</body>
</html>
