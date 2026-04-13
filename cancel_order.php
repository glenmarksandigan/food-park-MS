<?php
session_start();
include('db.php');

if (!isset($_SESSION['email']) || !isset($_POST['order_id'])) {
    echo "Unauthorized access.";
    exit;
}

$order_id = $_POST['order_id'];
$user_email = $_SESSION['email'];

// Verify the user owns the order
$user = $conn->query("SELECT user_id FROM users WHERE email = '$user_email'")->fetch_assoc();
$user_id = $user['user_id'];

$check = $conn->query("SELECT * FROM `orders` WHERE order_id = '$order_id' AND user_id = '$user_id' AND status = 'Pending'");

if ($check->num_rows === 0) {
    echo "You can't cancel this order.";
    exit;
}

// Cancel the order (update status)
$conn->query("UPDATE `orders` SET status = 'Cancelled' WHERE order_id = '$order_id'");

$conn->close();
echo '<script type="text/javascript">
alert("update successful");
window.history.back();
</script>'

?>
