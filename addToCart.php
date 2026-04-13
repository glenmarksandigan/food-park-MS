<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['email'])) {
    echo "Please log in to add items to the cart.";
    exit();
}

// Include DB connection
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_items_id'])) {
    $item_id = intval($_POST['menu_items_id']);
    $user_email = $_SESSION['email'];

    // Fetch user_id from the session
    $user = $conn->query("SELECT user_id FROM users WHERE email = '$user_email'")->fetch_assoc();
    $user_id = $user['user_id'];

    // Check if the item exists in the menu
    $item = $conn->query("SELECT * FROM menu_items WHERE menu_items_id = '$item_id'")->fetch_assoc();
    
    if ($item) {
        // Check if the item is already in the user's cart
        $existingCart = $conn->query("SELECT * FROM cart WHERE user_id = '$user_id' AND menu_items_id = '$item_id'")->fetch_assoc();

        if ($existingCart) {
            // If the item is already in the cart, update the quantity
            $newQuantity = $existingCart['quantity'] + 1;
            $conn->query("UPDATE cart SET quantity = '$newQuantity' WHERE user_id = '$user_id' AND menu_items_id = '$item_id'");
        } else {
            // Otherwise, insert the item into the cart
            $conn->query("INSERT INTO cart (user_id, menu_items_id, quantity) VALUES ('$user_id', '$item_id', 1)");
        }
        
        echo "Item added to cart.";
    } else {
        echo "Item not found.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
