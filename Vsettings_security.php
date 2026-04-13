<?php
include('db.php');
session_start();

$message = "";
$messageType = ""; // Variable to store message type (success or error)

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vendorId = $_SESSION['vendor_id']; 
    $currentPassword = $_POST['current-password'];
    $newPassword = $_POST['new-password'];
    $confirmPassword = $_POST['confirm-password'];

    if ($newPassword !== $confirmPassword) {
        $message = "New passwords do not match.";
        $messageType = "error"; // Error message type
    } else {
        $stmt = $conn->prepare("SELECT password FROM vendor WHERE vendor_id = ?");
        $stmt->bind_param("i", $vendorId); // Changed from $userId to $vendorId
        $stmt->execute();
        $stmt->bind_result($storedPassword);
        $stmt->fetch();
        $stmt->close();

        if ($currentPassword !== $storedPassword) { // Plain text password check
            $message = "Current password is incorrect.";
            $messageType = "error"; // Error message type
        } else {
            $update = $conn->prepare("UPDATE vendor SET password = ? WHERE vendor_id = ?");
            $update->bind_param("si", $newPassword, $vendorId); // Store new plain text password
            if ($update->execute()) {
                $message = "Password updated successfully.";
                $messageType = "success"; // Success message type
            } else {
                $message = "Error updating password.";
                $messageType = "error"; // Error message type
            }
            $update->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Security Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #E1E9FF;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h2, h3 {
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 4px;
            background-color: #3b82f6;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #2563eb;
        }
        .message {
            font-size: 0.9em;
            display: <?php echo $message ? 'block' : 'none'; ?>;
            margin-top: 10px;
            text-align: center;
        }
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Security Settings</h2>
        <form method="POST">
            <label for="current-password">Current Password</label>
            <input type="password" id="current-password" name="current-password" required>

            <label for="new-password">New Password</label>
            <input type="password" id="new-password" name="new-password" required>

            <label for="confirm-password">Confirm New Password</label>
            <input type="password" id="confirm-password" name="confirm-password" required>

            <button type="submit">Update Password</button>
        </form>

        <!-- Display message based on type (error or success) -->
        <div class="message <?php echo ($messageType === 'error') ? 'error-message' : 'success-message'; ?>">
            <?php echo $message; ?>
        </div>

        <h3>Two-Factor Authentication</h3>
        <p>Enable 2FA for added security and protection.</p>
        <button type="button">Enable 2FA</button>

        <h3>Recent Login Activity</h3>
        <p>View the history of recent logins and devices connected to your account.</p>
        <button type="button">View Activity</button>
    </div>
</body>
</html>
