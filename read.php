<?php
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM menu_items WHERE menu_items_id = $id";
    $result = $conn->query($sql);
    $item = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("UPDATE menu_items SET name=?, description=?, price=?, category=? WHERE menu_items_id=?");
    $stmt->bind_param('ssdsi', $name, $description, $price, $category, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: vendorDashboard.php'); 
}

?>

<form method="POST">
    <input type="text" name="name" value="<?php echo $item['name']; ?>" required />
    <input type="text" name="description" value="<?php echo $item['description']; ?>" required />
    <input type="number" name="price" value="<?php echo $item['price']; ?>" required />
    <input type="text" name="category" value="<?php echo $item['category']; ?>" required />
    <button type="submit">Update Item</button>
</form>
