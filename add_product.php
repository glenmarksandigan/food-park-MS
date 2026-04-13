<?php
session_start();
include('db.php');

// Ensure the user is logged in as a vendor
if (!isset($_SESSION['vendor_id'])) {
    echo "You must be logged in as a vendor to add a product.";
    exit;
}

// Fetch the vendor's stall ID from the database (assuming each vendor has a single stall)
$vendor_id = $_SESSION['vendor_id'];
$stall = $conn->query("SELECT * FROM stall WHERE vendor_id = '$vendor_id' LIMIT 1")->fetch_assoc();

if (!$stall) {
    die("Stall not found for this vendor.");
}

$stall_id = $stall['stall_id']; // Automatically get the stall ID for the vendor

// Handle the product addition
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $size = $_POST['size']; // Sizes for the product
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);

    // Move the uploaded image to the target directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        // Insert product into the database
        $sql = "INSERT INTO menu_items (name, description, price, category, size, image, vendor_id, stall_id)
                VALUES ('$name', '$description', '$price', '$category', '$size', '$image', '$vendor_id', '$stall_id')";

        if ($conn->query($sql) === TRUE) {
            echo "New product added successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Product - Stall ID: <?php echo htmlspecialchars($stall['name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Header Section -->
    <header class="bg-green-700 text-white py-4 px-6 flex justify-between items-center">
        <h1 class="text-xl font-bold">Hello, Vendor!</h1>
        <nav class="space-x-4">
            <a href="dashboard.php" class="hover:underline">Dashboard</a>
            <a href="logout.php" class="hover:underline">Logout</a>
        </nav>
    </header>

    <!-- Main Content Section -->
    <main class="max-w-4xl mx-auto py-8 px-4">
        <h2 class="text-2xl font-semibold text-green-700">Add New Product to <?php echo htmlspecialchars($stall['name']); ?></h2>
        <p class="text-gray-600 mb-6">Please provide product details below.</p>

        <!-- Product Form -->
        <form action="add_product.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <label for="name" class="block text-gray-700">Product Name</label>
            <input type="text" name="name" class="w-full p-2 border border-gray-300 rounded-lg" required>

            <label for="description" class="block text-gray-700">Description</label>
            <textarea name="description" class="w-full p-2 border border-gray-300 rounded-lg" required></textarea>

            <label for="price" class="block text-gray-700">Price</label>
            <input type="text" name="price" class="w-full p-2 border border-gray-300 rounded-lg" required>

            <label for="category" class="block text-gray-700">Category</label>
            <input type="text" name="category" class="w-full p-2 border border-gray-300 rounded-lg" required>

            <label for="size" class="block text-gray-700">Size</label>
            <input type="text" name="size" class="w-full p-2 border border-gray-300 rounded-lg" required>

            <label for="image" class="block text-gray-700">Product Image</label>
            <input type="file" name="image" class="w-full p-2 border border-gray-300 rounded-lg" required>

            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Add Product</button>
        </form>
    </main>
</body>
</html>

<?php $conn->close(); ?>
