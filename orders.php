<?php
include('db.php');
session_start();


$vendor_id = $_SESSION['vendor_id'];

// Query to fetch the orders for the logged-in vendor
$query = "
    SELECT 
        o.order_id,
        o.time_stamp,
        o.status,
        CONCAT(u.firstname, ' ', u.lastname) AS customer_name,
        mi.name AS item_name,
        mi.price,
        oi.quantity,
        (mi.price * oi.quantity) AS total_price
    FROM `orders` o
    JOIN users u ON o.user_id = u.user_id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN menu_items mi ON oi.menu_items_id = mi.menu_items_id
    WHERE o.vendor_id = ?
    ORDER BY o.time_stamp DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $vendor_id); // Bind the vendor_id
$stmt->execute();
$result = $stmt->get_result();

$orders = [];

// Loop through the results and organize them
while ($row = $result->fetch_assoc()) {
    $orders[$row['order_id']]['time_stamp'] = $row['time_stamp'];
    $orders[$row['order_id']]['status'] = $row['status'];
    $orders[$row['order_id']]['customer'] = $row['customer_name'];
    $orders[$row['order_id']]['items'][] = [
        'name' => $row['item_name'],
        'price' => $row['price'],
        'quantity' => $row['quantity'],
        'total' => $row['total_price']
    ];
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Vendor Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-3xl font-bold mb-6">Orders</h1>

        <?php if (empty($orders)): ?>
            <p class="text-gray-600">No orders found.</p>
        <?php else: ?>
            <?php foreach ($orders as $order_id => $order): ?>
                <div class="border p-4 rounded mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-semibold">Order #<?= $order_id ?></h2>
                            <p class="text-gray-500">Customer: <?= $order['customer'] ?></p>
                            <p class="text-gray-500">Date: <?= $order['time_stamp'] ?></p>
                        </div>
                        <div>
                            <span class="px-3 py-1 rounded text-white <?= $order['status'] === 'completed' ? 'bg-green-600' : 'bg-yellow-500' ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                    </div>
                    <table class="w-full mt-4 border">
                        <thead>
                            <tr>
                                <th class="p-2 border">Item</th>
                                <th class="p-2 border">Price</th>
                                <th class="p-2 border">Quantity</th>
                                <th class="p-2 border">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $subtotal = 0;
                            foreach ($order['items'] as $item):
                                $subtotal += $item['total'];
                            ?>
                                <tr>
                                    <td class="p-2 border"><?= $item['name'] ?></td>
                                    <td class="p-2 border">₱<?= number_format($item['price'], 2) ?></td>
                                    <td class="p-2 border"><?= $item['quantity'] ?></td>
                                    <td class="p-2 border">₱<?= number_format($item['total'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="p-2 text-right font-bold border">Subtotal</td>
                                <td class="p-2 border font-bold">₱<?= number_format($subtotal, 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
