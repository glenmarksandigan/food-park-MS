<?php
include('db.php');
session_start();

// Ensure the vendor is logged in
$vendor_id = $_SESSION['vendor_id'] ?? null;
if (!$vendor_id) {
    echo "You must be logged in as a vendor to add a stall.";
    exit;
}

$upload_dir = 'uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $stall_number = $_POST['stall_number'];
    $location_description = $_POST['location_description'];
    $name = $_POST['name'];
    $owner = $_POST['owner'];
    $description = $_POST['description'];
    $price_range = $_POST['price_range'];
    $contact = $_POST['contact'];
    $hours = $_POST['hours'];
    $availability = $_POST['availability'];
    $special_offers = $_POST['special_offers'];
    $accessibility_info = $_POST['accessibility_info'];

    // Handle image upload
    if ($_FILES['image']['name']) {
        $image = $_FILES['image'];
        $target_file = $upload_dir . basename($image["name"]);
        if (!move_uploaded_file($image["tmp_name"], $target_file)) {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    } else {
        $target_file = NULL;
    }

    // Insert the stall into the database with vendor_id
    $sql = "INSERT INTO stall (vendor_id, stall_number, location_description, name, owner, description, price_range, contact, hours, image, availability, special_offers, accessibility_info) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters (note that vendor_id is included)
        $stmt->bind_param("issssssssssss", $vendor_id, $stall_number, $location_description, $name, $owner, $description, $price_range, $contact, $hours, $target_file, $availability, $special_offers, $accessibility_info);
        if ($stmt->execute()) {
            echo "Stall added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing the SQL query.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add New Stall</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    .form-container {
      width: 60%;
      margin: 50px auto;
      background-color: white;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      position: relative;
    }
    .close-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background-color: #e3342f;
      color: white;
      border: none;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      text-align: center;
      line-height: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      transition: background 0.3s;
    }
    .close-btn:hover {
      background-color: #cc1f1a;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    input, textarea, select, button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    button {
      background-color: #4CAF50;
      color: white;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background-color: #45a049;
    }
    input[type="file"] {
      padding: 5px;
    }
    label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }
    @media (max-width: 768px) {
      .form-container {
        width: 90%;
      }
    }
  </style>
</head>
<body>
  <div class="form-container">
    <button class="close-btn" onclick="window.history.back();">&times;</button>

    <h2>Add New Stall</h2>
    <form method="POST" enctype="multipart/form-data">
      <label for="stall_number">Stall Number</label>
      <input type="text" id="stall_number" name="stall_number" placeholder="Stall Number" required>

      <label for="location_description">Location</label>
      <input type="text" id="location_description" name="location_description" placeholder="Location" required>

      <label for="name">Stall Name</label>
      <input type="text" id="name" name="name" placeholder="Stall Name" required>

      <label for="owner">Owner/Operator</label>
      <input type="text" id="owner" name="owner" placeholder="Owner/Operator" required>

      <label for="description">Products/Services</label>
      <textarea id="description" name="description" placeholder="Products/Services" required></textarea>

      <label for="price_range">Price Range</label>
      <input type="text" id="price_range" name="price_range" placeholder="Price Range">

      <label for="contact">Contact Info</label>
      <input type="text" id="contact" name="contact" placeholder="Contact Info">

      <label for="hours">Operating Hours</label>
      <input type="text" id="hours" name="hours" placeholder="Operating Hours">

      <label for="availability">Availability</label>
      <select id="availability" name="availability">
        <option value="Open">Open</option>
        <option value="Closed">Closed</option>
      </select>

      <label for="special_offers">Special Offers</label>
      <textarea id="special_offers" name="special_offers" placeholder="Special Offers"></textarea>

      <label for="accessibility_info">Accessibility Info</label>
      <textarea id="accessibility_info" name="accessibility_info" placeholder="Accessibility Info"></textarea>

      <label for="image">Upload Image</label>
      <input type="file" id="image" name="image">

      <button type="submit">Add Stall</button>
    </form>
  </div>
</body>
</html>
