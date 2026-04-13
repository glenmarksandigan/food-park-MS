<?php
session_start();

if (!isset($_SESSION['email'])) {
    echo "Please log in to view your orders.";
    exit();
}

include('db.php');

$user_email = $_SESSION['email'];
$user = $conn->query("SELECT user_id FROM users WHERE email = '$user_email'")->fetch_assoc();
$user_id = $user['user_id'];

// Fetch the user's orders excluding cancelled ones
$orders = $conn->query("SELECT o.*, oi.*, m.name, m.price, m.image FROM `orders` o
                        JOIN order_items oi ON o.order_id = oi.order_id
                        JOIN menu_items m ON oi.menu_items_id = m.menu_items_id
                        WHERE o.user_id = '$user_id' AND o.status != 'Cancelled' ORDER BY o.time_stamp DESC");
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 font-sans">

<!-- Green Horizontal Background Section -->
<div class="w-full bg-green-600 py-8">
    <div class="container w-full max-w-screen-xl bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-3xl font-semibold text-center text-black mb-6">Your Orders</h1>

        <?php if ($orders->num_rows > 0): ?>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <?php
                    $item_total = $order['price'] * $order['quantity'];
                    $total_price += $item_total;
                    $image_path = $order['image'] ? "uploads/" . htmlspecialchars($order['image']) : 'uploads/default.png';
                ?>
                <div class="flex items-center justify-between border-b py-4 gap-6 mb-4">
                    <div class="flex flex-col items-center w-1/5">
                        <p class="text-sm font-medium text-green-600">Photo</p>
                        <img src="<?php echo $image_path; ?>" class="w-24 h-24 rounded-full object-cover mb-3 shadow-md">
                    </div>

                    <div class="flex flex-col items-start flex-1">
                        <p class="text-sm font-medium text-green-600">Name</p>
                        <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($order['name']); ?></p>
                    </div>

                    <div class="flex flex-col items-center w-1/5">
                        <p class="text-sm font-medium text-green-600">Quantity</p>
                        <p class="text-lg font-semibold text-gray-700"><?php echo $order['quantity']; ?></p>
                    </div>

                    <div class="flex flex-col items-center w-1/5">
                        <p class="text-sm font-medium text-green-600">Price</p>
                        <p class="text-lg font-semibold text-gray-700">₱ <?php echo number_format($order['price'], 2); ?></p>
                    </div>

                    <div class="flex flex-col items-center w-1/5">
                        <p class="text-sm font-medium text-green-600">Total</p>
                        <p class="text-lg font-semibold text-gray-700">₱ <?php echo number_format($item_total, 2); ?></p>
                    </div>

                    <div class="flex flex-col items-center w-1/5">
                        <p class="text-sm font-medium text-green-600">Order Status</p>
                        <p class="text-lg font-semibold text-gray-700"><?php echo htmlspecialchars($order['status']); ?></p>
                    </div>

                    <div class="flex flex-col items-center w-1/5">
                        <p class="text-sm font-medium text-green-600">Order Date</p>
                        <p class="text-lg font-semibold text-gray-700"><?php echo date("F j, Y, g:i a", strtotime($order['time_stamp'])); ?></p>
                    </div>

                    <!-- Add Edit option -->
                    <div class="flex flex-col items-center w-[10%] mt-2">
                        <?php if ($order['status'] === 'Pending'): ?>
                            <a href="edit_order.php?order_id=<?php echo $order['order_id']; ?>" class="text-sm text-blue-500 font-bold hover:underline">Edit</a>
                        <?php endif; ?>
                    </div>

                    <!-- Add cancel option -->
                    <div class="flex flex-col items-center w-[10%] mt-2">
                        <?php if ($order['status'] === 'Pending'): ?>
                            <form method="POST" action="cancel_order.php" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                <button type="submit" class="text-sm text-red-500 font-bold hover:underline">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endwhile; ?>

            <div class="flex justify-between items-center mt-6 border-t pt-4">
                <div class="text-xl font-bold text-green-700">
                    Total: ₱ <?php echo number_format($total_price, 2); ?>
                </div>
            </div>

        <?php else: ?>
            <p class="text-center text-gray-500 text-sm">You don't have any orders yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
