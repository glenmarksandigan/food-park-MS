<?php
// Start session
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'fp_db');
if ($conn->connect_error) {
    die('Database Connection Failed: ' . htmlspecialchars($conn->connect_error));
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phonenumber = trim($_POST['phonenumber'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Initialize error array
    $errors = [];

    // Basic validation
    if (empty($firstname)) $errors[] = "Firstname is required.";
    if (empty($lastname)) $errors[] = "Lastname is required.";
    if (empty($address)) $errors[] = "Address is required.";
    if (empty($phonenumber)) $errors[] = "Phone number is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Phone number validation
    if (!preg_match('/^0[0-9]{10}$/', $phonenumber)) {
        $errors[] = "Invalid phone number format. It should be 11 digits and start with 0.";
    }

    // Display errors
    if (!empty($errors)) {
        echo "<h3>Errors:</h3>";
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    if ($stmt === false) {
        die('Prepare statement failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Email already exists. Please use a different email.";
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();

    
    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, address, phonenumber, email, password) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ssssss", $firstname, $lastname, $address, $phonenumber, $email, $password);

    if ($stmt->execute()) {
        header("Location: login.html");
        exit();
    } else {
        echo "<p>Error: " . htmlspecialchars($stmt->error) . "</p>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
