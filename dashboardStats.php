<?php
include('db.php');

// Query for Total Products
$totalProductsResult = $conn->query("SELECT COUNT(*) AS total FROM menu_items");
$totalProducts = $totalProductsResult->fetch_assoc()['total'];

// Query for Total Orders
$totalOrdersResult = $conn->query("SELECT COUNT(*) AS total FROM `orders`");
$totalOrders = $totalOrdersResult->fetch_assoc()['total'];

// Query for Total Revenue
$totalRevenueResult = $conn->query("SELECT SUM(mi.price * oi.quantity) AS total
                                    FROM `order_items` oi
                                    JOIN `menu_items` mi ON oi.menu_items_id = mi.menu_items_id
                                    JOIN `orders` o ON oi.order_id = o.order_id
                                    WHERE o.status = 'completed'");
$totalRevenue = $totalRevenueResult->fetch_assoc()['total'];

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vendor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex h-screen bg-gray-100">
    <aside class="w-64 bg-gray-800 text-white flex flex-col">
        
    </aside>

    <main class="flex-1 p-8">
        <h1 class="text-3xl font-bold mb-6">Welcome to Your Dashboard</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <!-- Total Products -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-semibold">Total Products</h2>
                <p class="text-2xl font-bold text-blue-600"><?php echo $totalProducts; ?></p>
            </div>

            <!-- Total Orders -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-semibold">Total Orders</h2>
                <p class="text-2xl font-bold text-blue-600"><?php echo $totalOrders; ?></p>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h2 class="text-xl font-semibold">Total Revenue</h2>
                <p class="text-2xl font-bold text-blue-600"><?php echo number_format($totalRevenue, 2); ?></p>
            </div>
        </div>
    </main>
</body>
</html>
