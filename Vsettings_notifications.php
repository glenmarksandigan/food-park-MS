<?php
include('db.php');
session_start();

$vendor_id = $_SESSION['vendor_id'] ?? null;
$message = '';

// Ensure the vendor_id is set
if ($vendor_id) {
    // Fetch vendor data
    $stmt = $conn->prepare("SELECT * FROM vendor WHERE vendor_id=?");
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $vendor_result = $stmt->get_result();
    $vendor = $vendor_result->fetch_assoc();
    $stmt->close();

    // Get the number of pending orders
    $orders_stmt = $conn->prepare("SELECT COUNT(*) FROM `order` WHERE vendor_id = ? AND status = 'pending'");
    $orders_stmt->bind_param("i", $vendor_id);
    $orders_stmt->execute();
    $orders_stmt->bind_result($orders_count);
    $orders_stmt->fetch();
    $orders_stmt->close();

    // Get the number of pending bookings
    $bookings_stmt = $conn->prepare("SELECT COUNT(*) FROM booking WHERE booking_id = ? AND booking_status = 'pending'");
    $bookings_stmt->bind_param("i", $vendor_id);
    $bookings_stmt->execute();
    $bookings_stmt->bind_result($booking_count);
    $bookings_stmt->fetch();
    $bookings_stmt->close();

    // Get the number of pending rental requests
    $rentals_stmt = $conn->prepare("SELECT COUNT(*) FROM stall_rent WHERE vendor_id = ? AND payment_status = 'pending'");
    $rentals_stmt->bind_param("i", $vendor_id);
    $rentals_stmt->execute();
    $rentals_stmt->bind_result($rental_count);
    $rentals_stmt->fetch();
    $rentals_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #E1E9FF;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            position: relative;
        }
        h2, h3 {
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .info-box label {
            font-weight: bold;
        }
        .info-box span {
            font-size: 1.1em;
            color: #4CAF50;
        }
        
        }
        .exit-btn:hover {
            color: #000;
        }
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 4px;
            background-color: #3b82f6;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #2563eb;
        }
        .message {
            font-size: 0.9em;
            display: <?php echo $message ? 'block' : 'none'; ?>;
            margin-top: 10px;
            text-align: center;
        }
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Exit Button -->
      

        <h3 class="font-semibold text-3xl text-center text-blue-600 mb-6">Vendor Notifications</h3>

        <!-- Success or Error Message -->
        <div class="message <?php echo ($messageType === 'error') ? 'error-message' : 'success-message'; ?>">
            <?php echo $message; ?>
        </div>

        <!-- Notifications Information -->
        <div class="info-box">
            <label for="orders">Orders:</label>
            <span><?= isset($orders_count) ? $orders_count : 0 ?> pending orders</span>
        </div>

        <div class="info-box">
            <label for="bookings">Bookings:</label>
            <span><?= isset($booking_count) ? $booking_count : 0 ?> pending bookings</span>
        </div>

        <div class="info-box">
            <label for="rentals">Rentals:</label>
            <span><?= isset($rental_count) ? $rental_count : 0 ?> pending rental requests</span>
        </div>

        <button class="close-btn" onclick="window.history.back();">&times;</button>
            
        </div>
    </div>
</body>
</html>
