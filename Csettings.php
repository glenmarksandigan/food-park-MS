<?php
session_start();
include 'db.php';

// Assuming user_id is retrieved from session
$user_id = 1; // Replace with actual session user_id
?>

<div class="p-4">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Account Settings</h2>

    <div class="space-y-4">
        <div>
            <a href="profile_update.php" class="text-blue-600 hover:underline">Update Profile</a>
        </div>

        <div>
            <a href="change_password.php" class="text-blue-600 hover:underline">Change Password</a>
        </div>

        <div>
            <a href="notifications_preferences.php" class="text-blue-600 hover:underline">Manage Notifications</a>
        </div>

        <div class="mt-4">
            <a href="delete_account.php" class="text-red-600 hover:underline">Delete Account</a>
        </div>
    </div>
</div>
