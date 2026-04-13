<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $phone    = $_POST['contact_info'];
    $address  = $_POST['address'];
    $password = $_POST['password']; // plain password

    // Check if email already exists
    $check = $conn->prepare("SELECT * FROM vendor WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists.'); window.history.back();</script>";
        exit;
    }

    // Insert into vendors table
    $stmt = $conn->prepare("INSERT INTO vendor (name, email, contact_info, address, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $address, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Vendor registered successfully!'); window.location.href = 'vendorLogin.html';</script>";
    } else {
        echo "<script>alert('Registration failed.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: vendor_signup.html");
    exit;
}
