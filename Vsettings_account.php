<?php
include('db.php');
session_start();
$vendor_id = $_SESSION['vendor_id'] ?? null;

if (!$vendor_id) {
    echo "<p class='text-red-600 text-center mt-10 text-lg'>Vendor not logged in.</p>";
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'account') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $contact_info = htmlspecialchars(trim($_POST['contact_info'] ?? ''));

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare("UPDATE vendor SET name=?, email=?, contact_info=? WHERE vendor_id=?");
        $stmt->bind_param("sssi", $name, $email, $contact_info, $vendor_id);
        if ($stmt->execute()) {
            $message = "<p class='text-green-600 mb-4'>Account info updated successfully.</p>";
        } else {
            $message = "<p class='text-red-600 mb-4'>Failed to update account info.</p>";
        }
        $stmt->close();
    } else {
        $message = "<p class='text-red-600 mb-4'>Please enter a valid email address.</p>";
    }
}

$vendor = null;
if ($vendor_id) {
    $stmt = $conn->prepare("SELECT * FROM vendor WHERE vendor_id=?");
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vendor = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Settings - Account Information</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 relative">

    <main class="p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-900">Account Information</h1>

        <?= $message ?>

        <form method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow-md">
            <input type="hidden" name="action" value="account">
            <div>
                <label class="block font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($vendor['name'] ?? '') ?>" class="w-full p-3 border rounded-lg" required>
            </div>
            <div>
                <label class="block font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($vendor['email'] ?? '') ?>" class="w-full p-3 border rounded-lg" required>
            </div>
            <div>
                <label class="block font-medium text-gray-700">Phone Number (optional)</label>
                <input type="text" name="contact_info" value="<?= htmlspecialchars($vendor['contact_info'] ?? '') ?>" class="w-full p-3 border rounded-lg">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all">Update Info</button>
        </form>
    </main>

</body>
</html>
