
<?php
include('db.php');
session_start();

$vendor_id = $_SESSION['vendor_id'] ?? null;

if (!$vendor_id) {
    echo json_encode(['status' => 'error', 'message' => 'Vendor not logged in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $contact_info = htmlspecialchars(trim($_POST['contact_info'] ?? ''));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE vendor SET name=?, email=?, contact_info=? WHERE vendor_id=?");
    $stmt->bind_param("sssi", $name, $email, $contact_info, $vendor_id);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Account info updated.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update.']);
    }
    $stmt->close();
}
?>
