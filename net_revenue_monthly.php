<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Default to the current year
$selectedYear = date('Y');

// If form is submitted, update the selected year
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedYear = $_POST['year'];
}

// Initialize arrays to store data
$months = [
    'January', 'February', 'March', 'April', 'May', 'June', 
    'July', 'August', 'September', 'October', 'November', 'December'
];
$revenueData = array_fill(0, 12, 0);
$expenseData = array_fill(0, 12, 0);
$netRevenueData = array_fill(0, 12, 0);

// Fetch total revenue and expenses for each month of the selected year
for ($month = 1; $month <= 12; $month++) {
    $startDate = "$selectedYear-$month-01";
    $endDate = date("Y-m-t", strtotime($startDate)); // Get last day of the month

    // Fetch total revenue for the month
    $sqlRevenue = "SELECT SUM(total) as total_revenue FROM orders WHERE date BETWEEN '$startDate' AND '$endDate'";
    $resultRevenue = $conn->query($sqlRevenue);
    $totalRevenue = ($resultRevenue->num_rows > 0) ? $resultRevenue->fetch_assoc()['total_revenue'] : 0;
    $revenueData[$month - 1] = $totalRevenue;

    // Fetch total expenses for the month
    $sqlExpenses = "SELECT SUM(amount) as total_expenses FROM expenses WHERE date BETWEEN '$startDate' AND '$endDate'";
    $resultExpenses = $conn->query($sqlExpenses);
    $totalExpenses = ($resultExpenses->num_rows > 0) ? $resultExpenses->fetch_assoc()['total_expenses'] : 0;
    $expenseData[$month - 1] = $totalExpenses;

    // Calculate net revenue for the month
    $netRevenueData[$month - 1] = $totalRevenue - $totalExpenses;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Net Revenue by Month</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
    <link rel="stylesheet" href="./css/expenses.css">
</head>
<body>
    <div class="all">
        <h2>Net Revenue by Month</h2>
        <form method="POST" action="net_revenue_monthly.php">
            <div class="forms">
                <label for="year">Year</label>
                <select id="year" name="year" required>
                    <?php
                    for ($year = 2024; $year <= 2094; $year++) {
                        $selected = ($year == $selectedYear) ? "selected" : "";
                        echo "<option value='$year' $selected>$year</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="forms">
                <button type="submit">Query</button>
            </div>
        </form>
        <h3>Year: <?php echo $selectedYear; ?></h3>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Revenue</th>
                    <th>Total Expenses</th>
                    <th>Net Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($months as $index => $month) : ?>
                    <tr>
                        <td><?php echo $month; ?></td>
                        <td>$<?php echo number_format($revenueData[$index], 2); ?></td>
                        <td>$<?php echo number_format($expenseData[$index], 2); ?></td>
                        <td>$<?php echo number_format($netRevenueData[$index], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
