<?php  ?>
<main class="flex-1 p-8">
    <h1 class="text-3xl font-bold mb-2">Product Management</h1>
    <p class="text-gray-600 mb-6">Manage your products here.</p>

    <!-- Add Product Button -->
    <div class="text-center mb-6">
        <a href="add_product.php" class="bg-blue-600 text-white px-6 py-2 rounded">Add New Product</a>
    </div>

    <!-- Fetching Products from Database -->
    <?php
    include('db.php'); // Include your database connection

    // Fetch products from the database
    $sql = "SELECT * FROM menu_items"; 
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table class='w-full mt-4 table-auto'>";
        echo "<thead><tr><th class='p-2 border'>Name</th><th class='p-2 border'>Description</th><th class='p-2 border'>Price</th><th class='p-2 border'>Category</th><th class='p-2 border'>Actions</th></tr></thead><tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td class='p-2 border'>" . $row['name'] . "</td>";
            echo "<td class='p-2 border'>" . $row['description'] . "</td>";
            echo "<td class='p-2 border'>" . $row['price'] . "</td>";
            echo "<td class='p-2 border'>" . $row['category'] . "</td>";
            echo "<td class='p-2 border'>
                    <a href='edit.php?id=" . $row['menu_items_id'] . "' class='bg-yellow-500 text-white px-3 py-1 rounded'>Edit</a>
                    <a href='delete.php?id=" . $row['menu_items_id'] . "' class='bg-red-600 text-white px-3 py-1 rounded ml-2'>Delete</a>
                  </td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        // If no products are found, show a message saying so
        echo "<div class='text-center p-4'>No products found.</div>";
    }
    ?>
</main>
