<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

$user_email = $_SESSION['email'];
include('db.php');

$sql = "SELECT firstname, lastname FROM users WHERE email = '$user_email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $firstname = $user['firstname'];
    $lastname = $user['lastname'];
} else {
    echo "User not found!";
    exit();
}

$sql_stalls = "SELECT * FROM stall";
$result_stalls = $conn->query($sql_stalls);
$stalls = [];

if ($result_stalls->num_rows > 0) {
    while($stall = $result_stalls->fetch_assoc()) {
        $stalls[] = $stall; // Store stalls in an array for easier access
    }
} else {
    $stalls = []; // No stalls found
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $image = $_FILES['image'];
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $target_file = $upload_dir . basename($image["name"]);
    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        echo "The file " . basename($image["name"]) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Home</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .stall img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 15px;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

    .vendor-stalls {
      display: flex;
      flex-wrap: wrap;
      gap: 24px;
      justify-content: flex-start;
      width: 100%;
      max-width: 100%;
      box-sizing: border-box;
    }

    .stall {
      background-color: white;
      border-radius: 1rem;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      padding: 1rem;
      text-align: center;
      width: 240px;
      transition: transform 0.2s;
    }

    .stall:hover {
      transform: scale(1.03);
    }

    .stall h3 {
      font-size: 1.125rem;
      font-weight: 600;
      color: #2f855a;
      margin: 0.5rem 0;
    }

    .stall p {
      font-size: 0.95rem;
      color: #4a5568;
      margin-bottom: 0.5rem;
    }

    .stall a {
      display: inline-block;
      margin-top: 0.5rem;
      padding: 0.4rem 1rem;
      background-color: #48bb78;
      color: white;
      border-radius: 9999px;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .rating {
      color: #ecc94b;
      margin-top: 0.3rem;
    }

    .review {
      font-size: 0.85rem;
      color: #718096;
      margin-top: 0.25rem;
    }
  </style>
</head>
<body class="bg-green-100 min-h-screen font-sans">
  <!-- Navbar -->
  <div class="bg-green-700 text-white py-4 px-8 flex justify-between items-center">
    <div class="text-xl font-bold">Hello!</div>
    <nav class="space-x-6">
      <a href="home.php" class="hover:underline">Home</a>
      <a href="about.php" class="hover:underline">About Us</a>
      <a href="contact.php" class="hover:underline">Contact Us</a>
    </nav>
  </div>

  <!-- Content -->
  <div class="p-6 max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold text-center mb-6">Welcome, <?php echo htmlspecialchars($firstname . ' ' . $lastname); ?>!</h1>

    <div class="w-full max-w-4xl mx-auto mb-4 px-4">
      <input
        type="text"
        id="searchInput"
        placeholder="Search stalls..."
        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
      />
    </div>

    <!-- Featured -->
    <div class="mb-6">
      <h2 class="text-2xl text-orange-500 font-semibold mb-1">Featured Items</h2>
      <p class="text-gray-600">Explore the best food options!</p>
    </div>

    <!-- Stalls -->
    <h2 class="text-2xl text-green-700 font-semibold mb-4">Available Vendor Stalls</h2>

    <!-- Use Tailwind grid to display the stalls responsively -->
    <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4" id="stallList">
      <?php if (count($stalls) > 0): ?>
        <?php foreach($stalls as $stall): ?>
          <div class="stall stall-card" data-name="<?php echo htmlspecialchars($stall['name']); ?>" data-description="<?php echo htmlspecialchars($stall['description']); ?>">
              <?php
                  $imagePath = !empty($stall['image']) ? htmlspecialchars($stall['image']) : 'uploads/default.png';
              ?>
              <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($stall['name']); ?> Image">
              <h3><?php echo htmlspecialchars($stall['name']); ?></h3>
              <p><?php echo htmlspecialchars($stall['description']); ?></p>
              <a href="stall_details.php?id=<?php echo $stall['stall_id']; ?>">View Stall</a>
              <div class="rating"><span>★ ★ ★ ★ ☆</span></div>
              <div class="review"><p>Great stall with delicious food!</p></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center text-gray-600">No stalls available at the moment.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
  const searchInput = document.getElementById('searchInput');
  const stalls = document.querySelectorAll('#stallList .stall-card');

  searchInput.addEventListener('input', () => {
    const query = searchInput.value.toLowerCase();
    stalls.forEach(stall => {
      const name = stall.querySelector('h3').innerText.toLowerCase();
      const description = stall.querySelector('p').innerText.toLowerCase();
      const match = name.includes(query) || description.includes(query);
      stall.style.display = match ? '' : 'none';
    });
  });
</script>

</body>
</html>

<?php $conn->close(); ?>
