<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$cashierName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown'; // Get the cashier's name from the session
$currentDate = date('Y-m-d'); // Get the current date

include 'db.php';

// Fetch food items from the database
$sql = "SELECT * FROM foods";
$result = $conn->query($sql);

$foods = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $foods[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Food Items</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
</head>

<body>
    <div class="page_all">
        <div class="page_cards">
            <div class="cards_container">
                <?php foreach ($foods as $food) : ?>
                    <div class="card">
                        <img src="<?php echo $food['image']; ?>" alt="<?php echo $food['name']; ?>">
                        <div class="card_info">
                            <h2><?php echo $food['name']; ?></h2>
                            <p>Price: <?php echo $food['price']; ?></p>
                            <button class="add-to-order" data-id="<?php echo $food['id']; ?>" data-name="<?php echo $food['name']; ?>" data-price="<?php echo $food['price']; ?>">+</button>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="order-section">
                <h2>Order Details</h2>
                <p>Cashier: <?php echo $cashierName; ?></p> <!-- Display cashier's name -->
                <p>Date: <?php echo $currentDate; ?></p> <!-- Display current date -->
                <table id="order-table">
                    <thead>
                        <tr>
                            <th>Food Name</th>
                            <th>Price</th>
                            <th>Action</th> <!-- Added new column for the remove button -->
                        </tr>
                    </thead>
                    <tbody id="order-items">
                        <!-- Dynamically populated with JavaScript -->
                    </tbody>
                </table>
                <div class="payment-section">
                    <div class="forms">
                        <label for="payment-method">Select Payment Method:</label>
                        <select id="payment-method">
                            <option value="cash">Cash</option>
                            <option value="momo">Mobile Money</option>
                        </select>
                    </div>
                </div>
                <div class="forms">
                    <p class="subtotal">Subtotal: $<span id="subtotal">0.00</span></p>
                </div>
                <div class="forms">
                    <label for="discount">Discount:</label>
                    <input type="number" id="discount" value="0">
                </div>
                <div class="forms">
                    <p class="total">Total: $<span id="total">0.00</span></p>
                </div>
                <div class="forms">
                    <button id="checkout">Checkout</button>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addToOrderButtons = document.querySelectorAll('.add-to-order');
            const removeToOrderButtons = document.querySelectorAll('.remove-to-order'); // Select all remove buttons
            const orderItemsContainer = document.getElementById('order-items');
            const paymentMethodSelect = document.getElementById('payment-method');
            const subtotalElement = document.getElementById('subtotal');
            const totalElement = document.getElementById('total');
            let subtotal = 0;

            addToOrderButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const foodId = this.dataset.id;
                    const foodName = this.dataset.name;
                    const foodPrice = parseFloat(this.dataset.price);

                    // Add the selected food item to the order table
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>${foodName}</td>
                        <td>$${foodPrice.toFixed(2)}</td>
                        <td><button class="remove-item">Remove</button></td> <!-- Add remove button -->
                    `;
                    orderItemsContainer.appendChild(newRow);

                    // Update subtotal
                    subtotal += foodPrice;
                    subtotalElement.textContent = subtotal.toFixed(2);
                    // Update total (for now, it's the same as subtotal)
                    totalElement.textContent = subtotal.toFixed(2);
                });
            });

            // Listen for remove button clicks
            orderItemsContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-item')) {
                    const row = event.target.closest('tr');
                    const priceToRemove = parseFloat(row.children[1].textContent.replace('$', ''));

                    // Remove the row from the table
                    row.remove();

                    // Update subtotal and total
                    subtotal -= priceToRemove;
                    subtotalElement.textContent = subtotal.toFixed(2);
                    totalElement.textContent = subtotal.toFixed(2);
                }
            });
            // Listen for changes in the discount input field
            document.getElementById('discount').addEventListener('input', function() {
                const discount = parseFloat(this.value);
                const discountedTotal = subtotal - discount;
                totalElement.textContent = discountedTotal.toFixed(2);
            });

            // Listen for changes in the payment method
            paymentMethodSelect.addEventListener('change', function() {
                // You can add logic here to update the total based on the selected payment method
                // For now, let's keep it simple and assume the total remains the same
                totalElement.textContent = subtotal.toFixed(2);
            });

            // Checkout button event listener
            document.getElementById('checkout').addEventListener('click', function() {
                // Here, you can implement the checkout logic, e.g., send the order details to a server for processing
                // For this example, let's just display an alert
                const selectedPaymentMethod = paymentMethodSelect.value;
                const total = parseFloat(totalElement.textContent);
                alert(`Checkout completed! Payment method: ${selectedPaymentMethod}, Total amount: $${total.toFixed(2)}`);
            });
        });
    </script>
</body>

</html>