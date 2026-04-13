<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_POST['order_id']) || !isset($_POST['quantity'])) {
    echo "Unauthorized access.";
    exit();
}

include('db.php');

$order_id = $_POST['order_id'];
$new_quantity = $_POST['quantity'];
$menu_item_id = $_POST['menu_item_id'];
$user_email = $_SESSION['email'];

// Verify the user owns the order
$user = $conn->query("SELECT user_id FROM users WHERE email = '$user_email'")->fetch_assoc();
$user_id = $user['user_id'];

// Fetch the order details
$order_query = "SELECT * FROM orders WHERE order_id = '$order_id' AND user_id = '$user_id'";
$order_result = $conn->query($order_query);

if ($order_result->num_rows == 0) {
    echo "Order not found or you don't have permission to edit this order.";
    exit();
}

// Update the quantity in the order_items table
$update_query = "UPDATE order_items 
                 SET quantity = '$new_quantity' 
                 WHERE order_id = '$order_id' AND menu_items_id = '$menu_item_id'";

if ($conn->query($update_query) === TRUE) {
    echo '<script type="text/javascript">
    alert("update successful");
    window.history.back();
    </script>';
    exit();
} else {
    echo "Error updating order: " . $conn->error;
}

$conn->close();
?>
