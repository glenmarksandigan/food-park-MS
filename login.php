<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'fp_db');
if ($conn->connect_error) {
    die('Database Connection Failed: ' . htmlspecialchars($conn->connect_error));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo "Email and password are required.";
        $conn->close();
        exit();
    }


    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($storedPassword);
        $stmt->fetch();

        
        if ($password === $storedPassword) {
            session_regenerate_id();
            $_SESSION['email'] = $email;
            header("Location: customerDashboard.php"); 
            exit();
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "Email not found!";
    }

    $stmt->close();
    $conn->close();
}
?>
