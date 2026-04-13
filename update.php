<?php include 'db.php'; 

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE menu_items_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex h-screen bg-gray-100">
    <div class="max-w-lg mx-auto bg-white p-8 rounded shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Edit Menu Item</h2>
        <form method="POST">
            <input type="text" name="name" value="<?= $item['name'] ?>" class="w-full p-2 mb-4 border rounded" required>
            <textarea name="description" class="w-full p-2 mb-4 border rounded" required><?= $item['description'] ?></textarea>
            <input type="number" name="price" value="<?= $item['price'] ?>" step="0.01" class="w-full p-2 mb-4 border rounded" required>
            <input type="text" name="category" value="<?= $item['category'] ?>" class="w-full p-2 mb-4 border rounded" required>
            
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded">Update Item</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category = $_POST['category'];
            

            $stmt = $conn->prepare("UPDATE menu_items SET name=?, description=?, price=?, category=?  WHERE menu_items_id=?");
            $stmt->bind_param("ssdsi", $name, $description, $price, $category, $id);
            $stmt->execute();
            echo "<p class='mt-4 text-green-600'>Menu item updated successfully!</p>";
        }
        ?>
    </div>
</body>
</html>
