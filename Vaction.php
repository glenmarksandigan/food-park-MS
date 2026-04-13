<?php
include('db.php');
session_start();

// Get the vendor_id from session (assuming it's stored there)
$vendor_id = $_SESSION['vendor_id'] ?? null;  // Get vendor ID from session

// Check if vendor is logged in
if (!$vendor_id) {
    echo "<p class='text-red-600 text-center mt-10 text-lg'>Vendor not logged in.</p>";
    exit;
}

// Fetch vendor info from database
$vendor = null;
if ($vendor_id) {
    $stmt = $conn->prepare("SELECT * FROM vendor WHERE vendor_id=?");
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vendor = $result->fetch_assoc();
    $stmt->close();
}

// Handle account info and password change
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'update_info') {
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

    if ($_POST['action'] === 'password') {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {
            $stmt = $conn->prepare("UPDATE vendor SET password=? WHERE vendor_id=?");
            $stmt->bind_param("si", $new_password, $vendor_id);  // Plain text password without hashing
            if ($stmt->execute()) {
                $message = "<p class='text-green-600 mb-4'>Password changed successfully.</p>";
            } else {
                $message = "<p class='text-red-600 mb-4'>Failed to change password.</p>";
            }
            $stmt->close();
        } else {
            $message = "<p class='text-red-600 mb-4'>Passwords do not match.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 relative">

<main class="p-8 max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-gray-900">Vendor Settings</h1>

    <div class="flex gap-6 mb-6">
        <a href="#" id="accountTab" class="px-6 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition-all" onclick="toggleSection('account')">Account Information</a>
        <a href="#" id="actionsTab" class="px-6 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition-all" onclick="toggleSection('actions')">Account Actions</a>
    </div>

    <!-- Account Information Section -->
    <div id="accountSection" class="section-content mb-6">
        <h3 class="font-bold text-xl mb-4">Account Information</h3>
        <button class="absolute top-0 right-0 p-2 text-xl text-red-600"  onclick="window.history.back();">&times;</button>

        <div class="mb-4">
            <p><strong>Name:</strong> <?= htmlspecialchars($vendor['name']); ?></p>
        </div>
        <div class="mb-4">
            <p><strong>Email:</strong> <?= htmlspecialchars($vendor['email']); ?></p>
        </div>
        <div class="mb-4">
            <p><strong>Contact Information:</strong> <?= htmlspecialchars($vendor['contact_info']); ?></p>
        </div>
        <div class="mb-4">
            <p><strong>Stall ID:</strong> <?= htmlspecialchars($vendor['stall_id']); ?></p>
        </div>
        <div class="mb-4">
            <p><strong>Status:</strong> <?= htmlspecialchars($vendor['status']); ?></p>
        </div>
        <div class="mb-4">
            <p><strong>Notifications Enabled:</strong> <?= $vendor['notifications_enabled'] ? 'Yes' : 'No'; ?></p>
        </div>
        <div class="mb-4">
            <p><strong>Theme:</strong> <?= htmlspecialchars($vendor['theme']); ?></p>
        </div>
        <div class="mb-4">
            <p><strong>Language:</strong> <?= htmlspecialchars($vendor['language']); ?></p>
        </div>
        <div class="mb-4">
            <p><strong>Business Hours:</strong> <?= htmlspecialchars($vendor['business_hours']); ?></p>
        </div>
    </div>

    <!-- Account Actions Section -->
    <div id="actionsSection" class="section-content hidden">
        <h3 class="font-bold text-xl mb-4">Account Actions</h3>
        <button class="absolute top-0 right-0 p-2 text-xl text-red-600"  onclick="window.history.back();">&times;</button>

        <!-- Update Account Info -->
        <form method="POST">
            <input type="hidden" name="action" value="update_info">
            <div class="mb-4">
                <label class="block font-medium text-gray-700">Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($vendor['name']); ?>" class="w-full p-3 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($vendor['email']); ?>" class="w-full p-3 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium text-gray-700">Contact Information</label>
                <input type="text" name="contact_info" value="<?= htmlspecialchars($vendor['contact_info']); ?>" class="w-full p-3 border rounded-lg" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all">Update Information</button>
        </form>

        <!-- Change Password -->
        <form method="POST" class="mt-6">
            <input type="hidden" name="action" value="password">
            <div class="mb-4">
                <label class="block font-medium text-gray-700">New Password</label>
                <input type="password" name="new_password" class="w-full p-3 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium text-gray-700">Confirm Password</label>
                <input type="password" name="confirm_password" class="w-full p-3 border rounded-lg" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all">Change Password</button>
        </form>

        <form action="vendorLogin.php" method="POST" class="mt-4">
            <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-all">Logout</button>
        </form>
    </div>

</main>

<script>
    function toggleSection(section) {
        const sections = ['account', 'actions'];
        sections.forEach(sec => {
            const el = document.getElementById(sec + 'Section');
            const tab = document.getElementById(sec + 'Tab');
            if (el && tab) {
                if (sec === section) {
                    el.classList.remove('hidden');
                    tab.classList.add('bg-blue-300');  // Active tab color
                } else {
                    el.classList.add('hidden');
                    tab.classList.remove('bg-blue-300'); // Inactive tab color
                }
            }
        });
    }

    function closeSection(section) {
        const el = document.getElementById(section + 'Section');
        if (el) {
            el.classList.add('hidden');
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        toggleSection('account');  // Default section to show
    });
</script>

</body>
</html>
