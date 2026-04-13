<?php
include('db.php'); // Include your database connection

// Fetch the menu item details if an ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE menu_items_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();

    // If the item doesn't exist, redirect to the dashboard
    if (!$item) {
        header('Location: vendorDashboard.php');
        exit();
    }
}

// Update the menu item if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    // Update the item in the database
    $stmt = $conn->prepare("UPDATE menu_items SET name = ?, description = ?, price = ?, category = ? WHERE menu_items_id = ?");
    $stmt->bind_param('ssisi', $name, $description, $price, $category, $id);
    $stmt->execute();
    $stmt->close();

    // Redirect after update to vendor dashboard
    header('Location: vendorDashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Menu Item</title>
</head>
<body>
    <h2>Edit Menu Item</h2>

    <form method="POST">
        <input type="text" name="name" value="<?php echo $item['name']; ?>" placeholder="Name" required>
        <input type="text" name="description" value="<?php echo $item['description']; ?>" placeholder="Description" required>
        <input type="number" name="price" value="<?php echo $item['price']; ?>" placeholder="Price" required>
        <input type="text" name="category" value="<?php echo $item['category']; ?>" placeholder="Category" required>
        <button type="submit">Update Item</button>
    </form>
</body>
</html>
