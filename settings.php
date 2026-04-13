<?php  ?>
<main class="flex-1 p-8">
    <h1 class="text-3xl font-bold mb-2">Settings</h1>
    <p class="text-gray-600 mb-6">Update your account settings here.</p>

    <?php
    include('db.php');

    
    session_start();
    $vendor_id = $_SESSION['vendor_id'];

    // Update form handling
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE vendor SET name=?, email=?, password=? WHERE vendor_id=?");
        $stmt->bind_param('sssi', $name, $email, $password, $vendor_id);
        $stmt->execute();
        $stmt->close();

        echo "<p class='text-green-600'>Settings updated successfully.</p>";
    }

    // Fetch current vendor info
    $stmt = $conn->prepare("SELECT * FROM vendor WHERE vendor_id = ?");
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vendor = $result->fetch_assoc();
    $stmt->close();
    ?>

    <form method="POST" class="bg-white p-6 rounded-lg shadow-md w-full max-w-lg mt-6">
        <label class="block mb-2">Name</label>
        <input type="text" name="name" value="<?= $vendor['name'] ?>" class="w-full p-2 mb-4 border rounded" required>

        <label class="block mb-2">Email</label>
        <input type="email" name="email" value="<?= $vendor['email'] ?>" class="w-full p-2 mb-4 border rounded" required>

        <label class="block mb-2">New Password</label>
        <input type="password" name="password" placeholder="Leave blank to keep current" class="w-full p-2 mb-4 border rounded">

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
    </form>
</main>
