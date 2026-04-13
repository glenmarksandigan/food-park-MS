<?php
$conn = new mysqli("localhost", "root", "", "fp_db");

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM stall WHERE stall_id = $id");
$stall = $result->fetch_assoc();

$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stall_number = $_POST['stall_number'];
    $location = $_POST['location_description'];
    $name = $_POST['name'];
    $owner = $_POST['owner'];
    $description = $_POST['description'];
    $price_range = $_POST['price_range'];
    $contact = $_POST['contact'];
    $hours = $_POST['hours'];
    $availability = $_POST['availability'];
    $special_offers = $_POST['special_offers'];
    $accessibility_info = $_POST['accessibility_info'];

    $image = $stall['image'];
    if ($_FILES['image']['name']) {
        $upload_dir = "uploads/";
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        $image = $upload_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    $stmt = $conn->prepare("UPDATE stall SET stall_number=?, location_description=?, name=?, owner=?, description=?, price_range=?, contact=?, hours=?, image=?, availability=?, special_offers=?, accessibility_info=? WHERE stall_id=?");
    $stmt->bind_param("ssssssssssssi", $stall_number, $location, $name, $owner, $description, $price_range, $contact, $hours, $image, $availability, $special_offers, $accessibility_info, $id);
    
    if ($stmt->execute()) {
        $success = true;
        $result = $conn->query("SELECT * FROM stall WHERE stall_id = $id");
        $stall = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Stall</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            position: relative;
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 14px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        button[type="submit"] {
            background: #007BFF;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            border-radius: 6px;
            transition: background 0.2s ease;
        }
        button[type="submit"]:hover {
            background: #0056b3;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            font-weight: bold;
            color: #fff;
            background-color: #e3342f;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            line-height: 32px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .close-btn:hover {
            background-color: #cc1f1a;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        img {
            display: block;
            max-width: 100px;
            margin: 8px 0 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .success-msg {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 12px;
            margin-bottom: 16px;
            border-radius: 6px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="form-container">
    <button class="close-btn" onclick="window.history.back();">&times;</button>
    <h2>Edit Stall</h2>

    <?php if ($success): ?>
        <div class="success-msg">Stall updated successfully!</div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="stall_number">Stall Number</label>
        <input type="text" id="stall_number" name="stall_number" value="<?= $stall['stall_number'] ?>" required>

        <label for="location_description">Location Description</label>
        <input type="text" id="location_description" name="location_description" value="<?= $stall['location_description'] ?>" required>

        <label for="name">Stall Name</label>
        <input type="text" id="name" name="name" value="<?= $stall['name'] ?>" required>

        <label for="owner">Owner Name</label>
        <input type="text" id="owner" name="owner" value="<?= $stall['owner'] ?>" required>

        <label for="description">Stall Description</label>
        <textarea id="description" name="description" required><?= $stall['description'] ?></textarea>

        <label for="price_range">Price Range</label>
        <input type="text" id="price_range" name="price_range" value="<?= $stall['price_range'] ?>">

        <label for="contact">Contact Info</label>
        <input type="text" id="contact" name="contact" value="<?= $stall['contact'] ?>">

        <label for="hours">Operating Hours</label>
        <input type="text" id="hours" name="hours" value="<?= $stall['hours'] ?>">

        <label for="image">Current Image</label>
        <img src="<?= $stall['image'] ?>" alt="Stall Image">
        <input type="file" id="image" name="image">

        <label for="availability">Availability</label>
        <select id="availability" name="availability">
            <option value="Open" <?= $stall['availability'] === 'Open' ? 'selected' : '' ?>>Open</option>
            <option value="Closed" <?= $stall['availability'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
        </select>

        <label for="special_offers">Special Offers</label>
        <textarea id="special_offers" name="special_offers"><?= $stall['special_offers'] ?></textarea>

        <label for="accessibility_info">Accessibility Info</label>
        <textarea id="accessibility_info" name="accessibility_info"><?= $stall['accessibility_info'] ?></textarea>

        <button type="submit">Update Stall</button>
    </form>
</div>
</body>
</html>
