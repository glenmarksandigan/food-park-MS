<?php
session_start();

if (!isset($_SESSION['email'])) {
    echo "Please log in to view your cart.";
    exit();
}

include('db.php');

$user_email = $_SESSION['email'];
$user = $conn->query("SELECT user_id FROM users WHERE email = '$user_email'")->fetch_assoc();
$user_id = $user['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $cart_id => $quantity) {
            $quantity = (int)$quantity;
            
            if ($quantity > 0) {
                $update_query = "UPDATE cart SET quantity = '$quantity' WHERE cart_id = '$cart_id' AND user_id = '$user_id'";
                $conn->query($update_query);
              
            }
        }
        echo '<script type="text/javascript">
            alert("Update successful");
            window.history.back();
        </script>';
        exit();
    }
}

if (isset($_POST['place_single_order'])) {
    $cart_id = $_POST['place_single_order'];
    $cart_item = $conn->query("SELECT * FROM cart WHERE cart_id = '$cart_id' AND user_id = '$user_id'")->fetch_assoc();

    if ($cart_item) {
        $menu_items_id = $cart_item['menu_items_id'];
        $quantity = $cart_item['quantity'];
        $menu_item = $conn->query("SELECT vendor_id FROM menu_items WHERE menu_items_id = '$menu_items_id'")->fetch_assoc();
        $vendor_id = $menu_item['vendor_id'];

        $insert_order_query = "INSERT INTO `orders` (user_id, vendor_id, time_stamp, status) VALUES ('$user_id', '$vendor_id', NOW(), 'Pending')";
        if ($conn->query($insert_order_query)) {
            $order_id = $conn->insert_id;
            $insert_order_items_query = "INSERT INTO order_items (order_id, menu_items_id, quantity) VALUES ('$order_id', '$menu_items_id', '$quantity')";
            if ($conn->query($insert_order_items_query)) {
                $conn->query("DELETE FROM cart WHERE cart_id = '$cart_id' AND user_id = '$user_id'");
                echo '<script type="text/javascript">
                alert("Update successful");
                window.history.back();
            </script>';
            exit();
            } else {
                echo "Error inserting order item: " . $conn->error;
            }
        } else {
            echo "Error placing the order: " . $conn->error;
        }
       
    }
}

if (isset($_POST['place_all_orders'])) {
    $cart_items = $conn->query("SELECT * FROM cart WHERE user_id = '$user_id'");
    if ($cart_items->num_rows > 0) {
        while ($item = $cart_items->fetch_assoc()) {
            $menu_items_id = $item['menu_items_id'];
            $quantity = $item['quantity'];
            $menu_item = $conn->query("SELECT vendor_id FROM menu_items WHERE menu_items_id = '$menu_items_id'")->fetch_assoc();
            $vendor_id = $menu_item['vendor_id'];

            $conn->query("INSERT INTO `orders` (user_id, vendor_id, time_stamp, status) VALUES ('$user_id', '$vendor_id', NOW(), 'Pending')");
            $order_id = $conn->insert_id;
            $conn->query("INSERT INTO order_items (order_id, menu_items_id, quantity) VALUES ('$order_id', '$menu_items_id', '$quantity')");
        }
        $conn->query("DELETE FROM cart WHERE user_id = '$user_id'");
        echo '<script type="text/javascript">
            alert("Update successful");
            window.history.back();
        </script>';
        exit();
    }
}

$cart_items = $conn->query("SELECT c.*, m.name, m.price, m.image FROM cart c JOIN menu_items m ON c.menu_items_id = m.menu_items_id WHERE c.user_id = '$user_id'");
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 font-sans">

<div class="container w-full max-w-screen-xl bg-white shadow-lg rounded-lg p-6">
    <h1 class="text-3xl font-semibold text-center text-green-600 mb-6">Your Cart</h1>

    <form method="POST" action="cart.php">
        <?php if ($cart_items->num_rows > 0): ?>
            <?php while ($item = $cart_items->fetch_assoc()): ?>
                <?php
                    $item_total = $item['price'] * $item['quantity'];
                    $total_price += $item_total;
                    $image_path = $item['image'] ? "uploads/" . htmlspecialchars($item['image']) : 'uploads/default.png';
                ?>
                <div class="flex items-center justify-between border-b py-4 gap-6 mb-4">
                    <div class="flex flex-col items-center w-1/5">
                        <p class="text-sm font-medium text-green-600">Photo</p>
                        <img src="<?php echo $image_path; ?>" class="w-24 h-24 rounded-full object-cover mb-3">
                    </div>

                    <div class="flex flex-col items-start flex-1">
                        <p class="text-sm font-medium text-green-600">Name</p>
                        <p class="text-lg font-medium text-gray-800"><?php echo htmlspecialchars($item['name']); ?></p>
                    </div>

                    <div class="flex flex-col items-center w-1/5">
                        <p class="text-sm font-medium text-green-600">Quantity</p>
                        <input type="number" name="quantity[<?php echo $item['cart_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="w-20 p-1 border rounded-md text-center text-sm">
                    </div>

                    <div class="flex flex-col items-center w-1/5">
                        <p class="text-sm font-medium text-green-600">Price</p>
                        <p class="text-lg font-semibold text-gray-700">₱ <?php echo number_format($item['price'], 2); ?></p>
                    </div>

                    <div class="flex flex-col items-center w-1/5">
                        <p class="text-sm font-medium text-green-600">Total</p>
                        <p class="text-lg font-semibold text-gray-700">₱ <?php echo number_format($item_total, 2); ?></p>
                        <button name="place_single_order" value="<?php echo $item['cart_id']; ?>" class="mt-2 text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Place Order</button>
                    </div>
                </div>
            <?php endwhile; ?>

            <div class="flex justify-between items-center mt-6 border-t pt-4">
                <div class="text-xl font-bold text-green-700">
                    Total: ₱ <?php echo number_format($total_price, 2); ?>
                </div>
                <div class="flex gap-3">
                    <button type="submit" name="update_cart" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-md text-sm">Update Cart</button>
                    <button type="submit" name="place_all_orders" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md text-sm">Place All Orders</button>
                </div>
            </div>

        <?php else: ?>
            <p class="text-center text-gray-500 text-sm">Your cart is empty.</p>
        <?php endif; ?>
    </form>
</div>

</body>
</html>

<?php $conn->close(); ?>
