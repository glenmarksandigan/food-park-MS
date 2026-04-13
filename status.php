<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

include('db.php');

// Get the logged-in user's email
$user_email = $_SESSION['email'];

// Get the user_id associated with the email
$user_result = $conn->query("SELECT user_id FROM users WHERE email = '$user_email'");
$user = $user_result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

$user_id = $user['user_id'];

// Handle remove order request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_order_id'])) {
    $order_id = $_POST['remove_order_id'];

    // Update order status to 'Deleted' instead of actually removing the order
    $conn->query("UPDATE orders SET status = 'Deleted' WHERE order_id = '$order_id' AND status = 'Cancelled'");

    // Redirect after operation
    header("Location: order_status.php");
    exit();
}

// Fetch all orders for the logged-in user, excluding 'Deleted' orders
$order_query = $conn->query("SELECT order_id, time_stamp, status FROM orders WHERE user_id = '$user_id' AND status != 'Deleted' ORDER BY time_stamp DESC");

if ($order_query->num_rows == 0) {
    echo "There are currently no orders.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 flex items-center justify-center min-h-screen">
    <div class="p-4 w-full max-w-5xl">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Order Status</h2>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full table-auto text-left">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2">Menu Items</th>
                        <th class="px-4 py-2">Time</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $order_query->fetch_assoc()): ?>
                        <tr class="border-b">
                            <td class="px-4 py-2">
                                <?php
                                $order_id = $order['order_id'];
                                $menu_query = $conn->query("SELECT m.name FROM order_items oi 
                                                            JOIN menu_items m ON oi.menu_items_id = m.menu_items_id 
                                                            WHERE oi.order_id = '$order_id'");
                                if ($menu_query->num_rows > 0) {
                                    while ($menu_item = $menu_query->fetch_assoc()) {
                                        echo htmlspecialchars($menu_item['name']) . "<br>";
                                    }
                                } else {
                                    echo "No items found.";
                                }
                                ?>
                            </td>
                            <td class="px-4 py-2"><?php echo $order['time_stamp']; ?></td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-white 
                                    <?php
                                        echo match ($order['status']) {
                                            'Pending' => 'bg-yellow-500',
                                            'Preparing' => 'bg-blue-500',
                                            'Out for Delivery' => 'bg-purple-500',
                                            'Completed' => 'bg-green-600',
                                            'Cancelled' => 'bg-red-600',
                                            'Deleted' => 'bg-gray-600',  // For "Deleted" status
                                            default => 'bg-gray-500'
                                        };
                                    ?>">
                                    <?php echo $order['status']; ?>
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <?php if ($order['status'] === 'Cancelled'): ?>
                                    <form method="POST" onsubmit="return confirm('Remove this order from view?');">
                                        <input type="hidden" name="remove_order_id" value="<?php echo $order['order_id']; ?>">
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Remove</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
