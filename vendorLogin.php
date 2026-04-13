<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM vendor WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        session_start();
        $vendor = $result->fetch_assoc();
        $_SESSION['vendor_id'] = $vendor['vendor_id'];
        $_SESSION['vendor_name'] = $vendor['name'];
        header("Location: vendorDashboard.php");
        exit;
    } else {
        echo "<script>alert('Invalid email or password.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: vendorLogin.html");
    exit;
}
