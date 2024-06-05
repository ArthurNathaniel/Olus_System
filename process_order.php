<?php
include 'db.php';

// Retrieve data sent via POST request
$data = json_decode(file_get_contents("php://input"), true);

// Extract order data
$cashierName = $data['cashierName'];
$orderDate = $data['orderDate'];
$selectedPaymentMethod = $data['selectedPaymentMethod'];
$total = $data['total'];
$orderItems = $data['orderItems'];

foreach ($orderItems as $item) {
    $orderId = $item['orderId'];
    $foodName = $item['foodName'];
    $price = $item['price'];
    $quantity = $item['quantity'];

    // Insert order into database
    $sql = "INSERT INTO orders (order_id, food_name, price, quantity, date, cashier_name, payment_method, total)
            VALUES ('$orderId', '$foodName', '$price', '$quantity', '$orderDate', '$cashierName', '$selectedPaymentMethod', '$total')";
    $conn->query($sql);
}

$conn->close();

echo "Order processed successfully!";
?>
