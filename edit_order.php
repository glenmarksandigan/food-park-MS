<?php
session_start();

if (!isset($_SESSION['email']) || !isset($_GET['order_id'])) {
    echo "Unauthorized access.";
    exit();
}

include('db.php');

$order_id = $_GET['order_id'];
$user_email = $_SESSION['email'];

// Verify the user owns the order
$user = $conn->query("SELECT user_id FROM users WHERE email = '$user_email'")->fetch_assoc();
$user_id = $user['user_id'];

// Fetch the order details with associated menu item, including price from menu_items
$order_query = "SELECT o.*, oi.menu_items_id, oi.quantity, mi.name, mi.price 
                FROM orders o 
                JOIN order_items oi ON o.order_id = oi.order_id 
                JOIN menu_items mi ON oi.menu_items_id = mi.menu_items_id
                WHERE o.order_id = '$order_id' AND o.user_id = '$user_id'";

$order_result = $conn->query($order_query);
$order = $order_result->fetch_assoc();

if (!$order || $order['status'] != 'Pending') {
    echo "This order cannot be edited.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Order</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 font-sans">

<div class="container w-full max-w-screen-xl bg-white shadow-lg rounded-lg p-6">
    <h1 class="text-3xl font-semibold text-center text-green-600 mb-6">Edit Order</h1>

    <form method="POST" action="update_order.php">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <input type="hidden" name="menu_item_id" value="<?php echo $order['menu_items_id']; ?>">

        <div class="mb-4">
            <label for="item_name" class="block text-sm font-medium text-green-600">Menu Item</label>
            <p class="mt-2 text-lg font-medium text-gray-700"><?php echo htmlspecialchars($order['name']); ?> - ₱ <?php echo number_format($order['price'], 2); ?></p>
        </div>

        <div class="mb-4">
            <label for="quantity" class="block text-sm font-medium text-green-600">Quantity</label>
            <input type="number" id="quantity" name="quantity" value="<?php echo $order['quantity']; ?>" class="mt-2 block w-full px-4 py-2 border border-gray-300 rounded-md" min="1" required>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md">Update Quantity</button>
        </div>
    </form>
</div>

</body>
</html>

<?php $conn->close(); ?>
