<?php
include('db.php');


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare the SQL query to delete the menu item
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE menu_items_id = ?");
    $stmt->bind_param('i', $id);

    // Execute the query and check for success
    if ($stmt->execute()) {
        echo "Item deleted successfully!";
        header("Location: vendorDashboard.html"); 
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
?>
