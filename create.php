<?php
include('db.php'); // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    // Prepare and execute the query to insert the new menu item
    $stmt = $conn->prepare("INSERT INTO menu_items (name, description, price, category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssis', $name, $description, $price, $category); // Bind parameters
    $stmt->execute(); // Execute the query
    $stmt->close(); // Close the statement

    // Redirect to the dashboard after adding the item
    header('Location: vendorDashboard.php'); 
    exit();
}
?>
