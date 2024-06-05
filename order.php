<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$cashierName = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown'; // Get the cashier's name from the session

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
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Food</title>
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
    </script>

</head>

<body onload="greetUser()">
    <?php include 'sidebar.php'; ?>
    <div class="page_all">
        <div class="welcome_base">
            <div class="greetings">
                <h1 id="greeting"> <?php echo $cashierName; ?></h1>
                <!-- <p>Welcome to Olu's Kitchen, </p> -->
            </div>
            <div class="profile"></div>
        </div>
        <div class="page_cards">
            <!-- <div class="cards_container">
                <?php foreach ($foods as $food) : ?>
                    <div class="card">
                        <img src="<?php echo $food['image']; ?>" alt="<?php echo $food['name']; ?>">
                        <div class="card_info">
                            <h2><?php echo $food['name']; ?></h2>
                            <button class="add-to-order" data-id="<?php echo $food['id']; ?>" data-name="<?php echo $food['name']; ?>" data-price="<?php echo $food['price']; ?>">
                                <i class="fa-solid fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div> -->
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <?php foreach ($foods as $food) : ?>
                        <div class="swiper-slide">
                            <div class="card">
                                <img src="<?php echo $food['image']; ?>" alt="<?php echo $food['name']; ?>">
                                <div class="card_info">
                                    <h2><?php echo $food['name']; ?></h2>
                                    <button class="add-to-order" data-id="<?php echo $food['id']; ?>" data-name="<?php echo $food['name']; ?>" data-price="<?php echo $food['price']; ?>">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="order-section ">
                <div class="forms">
                    <h2>Order Details</h2>
                </div>
                <div class="forms">
                    <p>Cashier: <br>
                    <div class="circle"><?php echo $cashierName; ?></div>
                    </p>
                </div>
                <div class="forms">
                    <label for="order-date">Date:</label>
                    <input type="date" id="order-date" required> <!-- Input for cashier to enter date -->
                </div>
                <table id="order-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Food Name</th>
                            <th>Enter Price</th> <!-- New column for entering price -->
                            <th>Quantity</th> <!-- New column for entering quantity -->
                            <th>Action</th> <!-- Added new column for the remove button -->
                        </tr>
                    </thead>
                    <tbody id="order-items">
                        <!-- Dynamically populated with JavaScript -->
                    </tbody>
                </table>
<br> <br>
                <div class="forms">
                    <label for="payment-method">Select Payment Method:</label>
                    <select id="payment-method" required>
                        <option value="" selected hidden>Select the payment method</option>
                        <option value="cash">Cash</option>
                        <option value="momo">Mobile Money</option>
                    </select>
                </div>

                <div class="forms">
                    <p class="subtotal">Subtotal: GH₵<span id="subtotal">0.00</span></p>
                </div>
                <div class="forms">
                    <p class="total">Total: GH₵<span id="total">0.00</span></p>
                </div>
                <div class="forms">
                    <button id="checkout">Checkout</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const swiper = new Swiper('.swiper-container', {
                loop: true,
                spaceBetween: 30,
                centeredSlides: true,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                breakpoints: {
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 30
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 40
                    }
                }
            });
        });
    </script>

    <script src="./js/order.js"></script> <!-- External JS file -->
</body>

</html>