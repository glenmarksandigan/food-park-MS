<?php
session_start();
include 'db.php';


if (!isset($_SESSION['email'])) {
    echo "User is not logged in.";
    exit();
}

// Assuming email is retrieved from session
$email = $_SESSION['email']; // Make sure this is set correctly

// Fetch user details from the database
$user_result = $conn->query("SELECT * FROM users WHERE email = '$email'");
$user = $user_result->fetch_assoc();

// Check if user data is retrieved
if (!$user) {
    echo "User not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated profile details
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $address = $_POST['address'];

    // Check if the email is already in use by another user
    $email_check_query = $conn->query("SELECT * FROM users WHERE email = '$email' AND email != '$email'");
    if ($email_check_query->num_rows > 0) {
        // If email is already taken by another user
        echo "The email '$email' is already in use. Please choose a different one.";
    } else {
        // Update the user profile in the database
        $update_query = "UPDATE users SET firstname = ?, lastname = ?, email = ?, phonenumber = ?, address = ? WHERE email = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssss", $firstname, $lastname, $email, $phonenumber, $address, $email);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Profile updated successfully!";
        } else {
            echo "No changes made or error occurred.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 flex items-center justify-center min-h-screen">
    <div class="p-4 w-full max-w-3xl bg-white rounded shadow-lg">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Update Profile</h2>

        <form method="POST">
            <div class="space-y-4">
                <div>
                    <label for="firstname" class="block text-gray-700">First Name</label>
                    <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" class="w-full p-2 border rounded mt-1" required>
                </div>

                <div>
                    <label for="lastname" class="block text-gray-700">Last Name</label>
                    <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" class="w-full p-2 border rounded mt-1" required>
                </div>

                <div>
                    <label for="email" class="block text-gray-700">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full p-2 border rounded mt-1" required>
                </div>

                <div>
                    <label for="phonenumber" class="block text-gray-700">Phone Number</label>
                    <input type="text" id="phonenumber" name="phonenumber" value="<?php echo htmlspecialchars($user['phonenumber']); ?>" class="w-full p-2 border rounded mt-1" required>
                </div>

                <div>
                    <label for="address" class="block text-gray-700">Address</label>
                    <textarea id="address" name="address" class="w-full p-2 border rounded mt-1" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Profile</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
