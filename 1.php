<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Fetch orders from the database, including the 'status' column
$sql = "SELECT * FROM orders ORDER BY id DESC"; // Assuming the ID column name is 'id'
$result = $conn->query($sql);

$groupedOrders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderID = $row['order_id'];
        if (!isset($groupedOrders[$orderID])) {
            $groupedOrders[$orderID] = [];
        }
        $groupedOrders[$orderID][] = $row;
    }
}

// Fetch cashier's name from session
$cashierName = $_SESSION['username'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
    <script>
        function greetUser() {
            var currentTime = new Date();
            var currentHour = currentTime.getHours();
            var greeting;

            if (currentHour < 12) {
                greeting = "Good morning";
            } else if (currentHour < 18) {
                greeting = "Good afternoon";
            } else {
                greeting = "Good evening";
            }

            var cashierName = "<?php echo $cashierName; ?>";
            document.getElementById("greeting").innerHTML = greeting + ", " + cashierName;
        }

        function markOrderReady(orderID) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_order_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Successfully updated, update the UI
                    document.getElementById("status-" + orderID).innerText = "Ready";
                }
            };
            xhr.send("order_id=" + orderID);
        }
    </script>
</head>

<body onload="greetUser()">
    <?php include 'sidebar.php'; ?>
    <div class="history_all">
        <div class="welcome_base">
            <div class="greetings">
                <h1 id="greeting"> <?php echo $cashierName; ?></h1>
                <p>Welcome to Olu's Kitchen, </p>
            </div>
            <div class="profile"></div>
        </div>
        <h1>Orders</h1>
        <table id="order-table"> <!-- Added ID to the table -->
            <thead>
                <tr>
                    <th>ID</th> <!-- Added ID column -->
                    <th>Order ID</th>
                    <th>Food Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Date, Payment Method</th> <!-- Combined as one -->
                    <th>Total</th>
                    <th>Status</th> <!-- New column for status -->
                    <th>Mark Ready</th> <!-- New column for marking order as ready -->
                </tr>
            </thead>
            <tbody id="order-body"> <!-- Added ID to the tbody -->
                <?php foreach ($groupedOrders as $orderID => $orders) : ?>
                    <?php $totalPrice = 0; ?>
                    <?php foreach ($orders as $index => $order) : ?>
                        <?php $totalPrice += $order['price'] * $order['quantity']; ?>
                    <?php endforeach; ?>
                    <?php $firstOrder = reset($orders); ?>
                    <tr>
                        <td><?php echo $firstOrder['id']; ?></td> <!-- Display the ID of the first order -->
                        <td rowspan="<?php echo count($orders); ?>"><?php echo $orderID; ?></td>
                        <td><?php echo isset($firstOrder['items']) ? $firstOrder['food_name'] . ', ' . $firstOrder['items'] : $firstOrder['food_name']; ?></td>
                        <td>$<?php echo number_format($firstOrder['price'], 2); ?></td>
                        <td><?php echo $firstOrder['quantity']; ?></td>
                        <td><?php echo $firstOrder['date'] . ', ' . $firstOrder['payment_method']; ?></td>
                        <td rowspan="<?php echo count($orders); ?>">$<?php echo number_format($totalPrice, 2); ?></td>
                        <td rowspan="<?php echo count($orders); ?>" id="status-<?php echo $orderID; ?>">
                            <?php echo isset($firstOrder['status']) && $firstOrder['status'] == 'ready' ? 'Ready' : 'Not Ready'; ?>
                        </td>
                        <td rowspan="<?php echo count($orders); ?>">
                            <button onclick="markOrderReady(<?php echo $orderID; ?>)">Mark as Ready</button>
                        </td>
                    </tr>
                    <?php for ($i = 1; $i < count($orders); $i++) : ?>
                        <?php $order = $orders[$i]; ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td> <!-- Display the ID for subsequent orders -->
                            <td><?php echo isset($order['items']) ? $order['food_name'] . ', ' . $order['items'] : $order['food_name']; ?></td>
                            <td>$<?php echo number_format($order['price'], 2); ?></td>
                            <td><?php echo $order['quantity']; ?></td>
                            <td><?php echo $order['date'] . ', ' . $order['payment_method']; ?></td>
                        </tr>
                    <?php endfor; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
