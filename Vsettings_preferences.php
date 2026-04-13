<?php
include('db.php');
session_start();

// Get vendor ID from session (ensure the vendor is logged in)
$vendor_id = $_SESSION['vendor_id'] ?? null;

if (!$vendor_id) {
    echo "<p class='text-red-600 text-center mt-10 text-lg'>Vendor not logged in.</p>";
    exit;
}

// Handle form submission for preferences
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_preferences') {
    $theme = $_POST['theme'] ?? 'light'; // Default to 'light' theme
    $language = $_POST['language'] ?? 'en'; // Default to 'en' language

    // Store preferences in session
    $_SESSION['theme'] = $theme;
    $_SESSION['language'] = $language;
}

// Set the current theme from session or default to light
$current_theme = $_SESSION['theme'] ?? 'light'; // Default to 'light'
$current_language = $_SESSION['language'] ?? 'en'; // Default to 'en'

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Settings - Preferences</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="<?= $current_theme === 'dark' ? 'dark' : '' ?> bg-gray-50 text-gray-900 relative">

    <main class="p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-900">Preferences</h1>

        <div id="preferencesSection">
        <button class="close-btn" onclick="window.history.back();">&times;</button>

            <form method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow-md">
                <input type="hidden" name="action" value="save_preferences">
                <div>
                    <label class="block font-medium text-gray-700">Theme</label>
                    <select name="theme" class="w-full p-3 border rounded-lg">
                        <option value="light" <?= $current_theme === 'light' ? 'selected' : '' ?>>Light</option>
                        <option value="dark" <?= $current_theme === 'dark' ? 'selected' : '' ?>>Dark</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-gray-700">Language</label>
                    <select name="language" class="w-full p-3 border rounded-lg">
                        <option value="en" <?= $current_language === 'en' ? 'selected' : '' ?>>English</option>
                        <option value="fr" <?= $current_language === 'fr' ? 'selected' : '' ?>>French</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all">Save Preferences</button>
            </form>
        </div>
    </main>

</body>
</html>
