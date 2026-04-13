<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['email'])) {
    echo "Please log in to place an order.";
    exit();
}

// Include DB connection
include('db.php');

$user_email = $_SESSION['email'];

// Fetch user_id from the session
$user = $conn->query("SELECT user_id FROM users WHERE email = '$user_email'")->fetch_assoc();
$user_id = $user['user_id'];

// Handle single item order placement
if (isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    // Fetch the item details from the cart
    $cart_item = $conn->query("SELECT c.*, m.name, m.price, m.quantity FROM cart c JOIN menu_items m ON c.menu_items_id = m.menu_items_id WHERE c.cart_id = '$cart_id' AND c.user_id = '$user_id'")->fetch_assoc();

    if ($cart_item) {
        // Create an order in the orders table
        $order_time = date("Y-m-d H:i:s");
        $status = "pending";

        $order_query = "INSERT INTO orders (user_id, time_stamp, status) VALUES ('$user_id', '$order_time', '$status')";
        if ($conn->query($order_query)) {
            $order_id = $conn->insert_id;

            // Insert the cart item as an order item
            $order_item_query = "INSERT INTO order_items (order_id, menu_items_id, quantity, price) VALUES ('$order_id', '{$cart_item['menu_items_id']}', '{$cart_item['quantity']}', '{$cart_item['price']}')";
            if ($conn->query($order_item_query)) {
                // Remove the item from the cart after placing the order
                $delete_cart_query = "DELETE FROM cart WHERE cart_id = '$cart_id' AND user_id = '$user_id'";
                $conn->query($delete_cart_query);

                echo "Order placed successfully!";
                // Optionally, redirect to a success page or back to the cart
                header("Location: cart.php");
                exit();
            } else {
                echo "Error placing order: " . $conn->error;
            }
        } else {
            echo "Error creating order: " . $conn->error;
        }
    } else {
        echo "Cart item not found!";
    }
}

$conn->close();
?>
