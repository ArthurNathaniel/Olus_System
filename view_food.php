<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to unauthorized page or login page
    header("Location: unauthorized.php");
    exit();
}
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // SQL query to delete the food item
    $sql = "DELETE FROM foods WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $success = "Food item deleted successfully!";
    } else {
        $error = "Error deleting food item: " . $conn->error;
    }

    $stmt->close();
}

// Query to fetch all food items from the database
$sql = "SELECT id, name, image FROM foods";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Food Items</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/food.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px dashed black;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        /* th {
            background-color: #f2f2f2;
        } */
        img {
            width: 100px;
            height: auto;
        }
        form button{
            width: 100%;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
    <div class="all">
        <div class="page_login">
            <div class="forms">
                <h2>Food Items</h2>
                <p>List of all food items</p>
            </div>
            <?php if (!empty($error)): ?>
                <div class="forms error">
                    <p><?php echo $error; ?></p>
                    <span class="close-error"><i class="fa-solid fa-xmark"></i></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="forms success">
                    <p><?php echo $success; ?></p>
                    <span class="close-success"><i class="fa-solid fa-xmark"></i></span>
                </div>
            <?php endif; ?>
            <div class="forms">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["name"] . "</td>";
                            echo "<td><img src='" . $row["image"] . "' alt='" . $row["name"] . "'></td>";
                            echo "<td>";
                            echo "<form method='POST' action='' onsubmit='return confirmDeletion();'>";
                            echo "<input type='hidden' name='delete_id' value='" . $row["id"] . "'>";
                            echo "<button type='submit'>Delete</button>";
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No food items found</td></tr>";
                    }
                    $conn->close();
                    ?>
                </table>
            </div>
        </div>
    </div>
    <script src="./js/swiper.js"></script>
    <script>
        // Close error message
        document.querySelectorAll('.close-error').forEach(el => {
            el.addEventListener('click', function() {
                const errorDiv = this.parentElement;
                errorDiv.style.display = 'none';
            });
        });

        // Close success message
        document.querySelectorAll('.close-success').forEach(el => {
            el.addEventListener('click', function() {
                const successDiv = this.parentElement;
                successDiv.style.display = 'none';
            });
        });

        // Confirm deletion
        function confirmDeletion() {
            return confirm('Are you sure you want to delete this item?');
        }
    </script>
</body>
</html>
