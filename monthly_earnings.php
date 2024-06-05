<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Initialize total earnings variable
$totalEarnings = 0;

// Set the default selected year to the current year
$selectedYear = date('Y');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the selected month and year from the form
    $selectedDate = $_POST['selected_date'];
    list($selectedYear, $selectedMonth) = explode('-', $selectedDate);

    // Prepare the start and end dates for the selected month and year
    $startDate = date('Y-m-d', strtotime("first day of $selectedYear-$selectedMonth"));
    $endDate = date('Y-m-d', strtotime("last day of $selectedYear-$selectedMonth"));

    // Prepare SQL query to fetch earnings for each food item in the selected month and year
    $sql = "SELECT food_name, SUM(price * quantity) AS total_earnings 
            FROM orders 
            WHERE DATE(date) BETWEEN '$startDate' AND '$endDate'
            GROUP BY food_name";
    $result = $conn->query($sql);

    $earnings = [];
    if ($result->num_rows > 0) {
        // Fetch the results into an associative array
        while ($row = $result->fetch_assoc()) {
            $earnings[] = $row;
            $totalEarnings += $row['total_earnings']; // Increment total earnings
        }
    } else {
        // If no results found, display a message
        $errorMessage = "No earnings found for $selectedMonth $selectedYear.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Earnings</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="history_all">
        <h1>Monthly Earnings</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="forms">
                <label for="selected_date">Select Month and Year:</label>
                <input type="month" id="selected_date" name="selected_date" value="<?php echo date('Y-m'); ?>" required>
            </div>
            <button type="submit">Submit</button>
        </form>

        <?php if (isset($errorMessage)) : ?>
            <p><?php echo $errorMessage; ?></p>
        <?php elseif (isset($earnings)) : ?>
            <h2>Month: <?php echo date('F', mktime(0, 0, 0, $selectedMonth, 1)) . ' ' . $selectedYear; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>Food Name</th>
                        <th>Total Earnings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($earnings as $earning) : ?>
                        <tr>
                            <td><?php echo $earning['food_name']; ?></td>
                            <td>$<?php echo number_format($earning['total_earnings'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>Total Earnings</strong></td>
                        <td><strong>$<?php echo number_format($totalEarnings, 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
