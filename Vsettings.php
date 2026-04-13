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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'account') {
        $name = htmlspecialchars(trim($_POST['name']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $contact_info = htmlspecialchars(trim($_POST['contact_info'] ?? ''));

        // Debugging the input values
        var_dump($name, $email, $contact_info);
        exit();

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
    <title>Vendor Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 relative">

    <main class="p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-900">Vendor Settings</h1>

        <div class="flex gap-6 mb-6">
            
            <a href="Vsettings_security.php" class="px-6 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition-all">Security</a>
            <a href="Vsettings_notifications.php" class="px-6 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition-all">Notifications</a>
            <a href="Vsettings_preferences.php" class="px-6 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition-all" onclick="toggleSection('preferences')">Preferences</a>
         
            <a href="Vaction.php" class="px-6 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition-all" onclick="toggleSection('actions')">Account Actions</a>
        </div>

        <div id="accountSection" class="section-content">
            <p class="text-gray-700 mb-4">You can manage your account details under "Account Actions".</p>
        </div>

        <div id="preferencesSection" class="section-content hidden">
            <h3 class="font-bold text-xl mb-2">Preferences</h3>
            <form method="POST">
                <div class="mb-4">
                    <label class="block font-medium text-gray-700">Theme</label>
                    <select name="theme" class="w-full p-3 border rounded-lg">
                        <option value="light">Light</option>
                        <option value="dark">Dark</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block font-medium text-gray-700">Language</label>
                    <select name="language" class="w-full p-3 border rounded-lg">
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                    </select>
                </div>

                <button type="submit" name="action" value="preferences" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all">Save Preferences</button>
            </form>
        </div>

        <div id="vendorSection" class="section-content hidden">
            <h3 class="font-bold text-xl mb-2">Vendor Options</h3>
            <p>Business hours, Status (open/closed), and food stall/menu links.</p>
        </div>

        <div id="actionsSection" class="section-content hidden">
            <h3 class="font-bold text-xl mb-2">Account Actions</h3>
            <p>Delete account (with confirmation) and logout button.</p>
            <form method="POST" action="delete_account.php" class="mt-6">
                <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-all">Delete Account</button>
            </form>
            <form method="POST" action="logout.php" class="mt-6">
                <button type="submit" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-all">Logout</button>
            </form>
        </div>
    </main>

    <script>
       function loadPage(page) {
    const mainContent = document.querySelector('main');
    if (page !== 'Vsettings') {
        lastPage = page;
    }

    fetch(page + '.php')
        .then(res => res.text())
        .then(data => {
            mainContent.innerHTML = data;
        })
        .catch(err => {
            mainContent.innerHTML = '<p>Error loading page.</p>';
            console.error(err);
        });
}

    </script>

</body>
</html>
