<?php
$conn = new mysqli("localhost", "root", "", "fp_db");

$id = $_GET['id'];

// Optional: delete image file too
$result = $conn->query("SELECT image FROM stall WHERE stall_id = $id");
if ($row = $result->fetch_assoc()) {
    if ($row['image'] && file_exists($row['image'])) {
        unlink($row['image']);
    }
}

$conn->query("DELETE FROM stall WHERE stall_id = $id");
header("Location: stall_list.php");
?>
