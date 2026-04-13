<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.html");
    exit();
}

include('db.php');

$user_email = $_SESSION['email'];
$user = $conn->query("SELECT user_id, firstname, lastname FROM users WHERE email = '$user_email'")->fetch_assoc();
$user_id = $user['user_id'];
$firstname = $user['firstname'];

$stall_id = intval($_GET['id']);
$stall = $conn->query("SELECT * FROM stall WHERE stall_id = '$stall_id'")->fetch_assoc();
if (!$stall) die("Stall not found.");

$vendor_id = $stall['vendor_id'];

$menu_items = $conn->query("SELECT * FROM menu_items WHERE stall_id = '$stall_id'");

$reviews = $conn->query("SELECT r.*, u.firstname FROM review r JOIN users u ON r.user_id = u.user_id WHERE r.vendor_id='$vendor_id' ORDER BY r.time_stamp DESC");

$_SESSION['review_token'] = bin2hex(random_bytes(16));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($stall['name']); ?> | Stall Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 flex items-center justify-center min-h-screen relative">

    <main class="w-full max-w-3xl bg-white p-6 shadow-lg rounded-lg">
        <!-- Stall Details Section -->
        <div class="text-center mb-8">
            <img src="uploads/<?php echo htmlspecialchars($stall['image'] ?: 'default.png'); ?>" class="mx-auto w-32 h-32 rounded-full object-cover mb-4">
            <h2 class="text-2xl font-semibold text-green-700"><?php echo htmlspecialchars($stall['name']); ?></h2>
            <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($stall['description']); ?></p>
        </div>

        <!-- Search Bar -->
        <div class="mb-8">
            <input type="text" id="searchInput" placeholder="Search menu items..." class="w-full border rounded px-4 py-2">
        </div>
        <a href="javascript:history.back()" class="absolute top-4 right-4 text-3xl text-gray-600 hover:text-gray-900 z-10">&times;</a>

        <!-- Menu Items Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-green-800">Popular Menu</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="menuList">
                <?php if ($menu_items->num_rows > 0): ?>
                    <?php while($item = $menu_items->fetch_assoc()): ?>
                        <div class="menu-item-card bg-white p-4 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                            <img src="uploads/<?php echo htmlspecialchars($item['image'] ?: 'default.png'); ?>"class="w-28 h-28 rounded-full object-cover mx-auto mb-3">
                            <div class="font-semibold"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="text-gray-500"><?php echo htmlspecialchars($item['description']); ?></div>
                            <div class="text-gray-700"><?php echo "₱" . htmlspecialchars($item['price']); ?></div>
                            <div class="text-gray-500"><?php echo htmlspecialchars($item['category']); ?></div>
                            <div class="mt-3 flex justify-between items-center">
                                <!-- Add to Cart Button inside menu item loop -->
<button class="add-to-cart bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700" 
    data-id="<?= $item['menu_items_id'] ?>"
    data-action="addToCart.php">Add to Cart</button>

                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-500">No menu items found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-green-800">Reviews</h3>
            <?php if ($reviews->num_rows > 0): ?>
                <?php while($r = $reviews->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded-lg shadow mb-4">
                        <div class="text-yellow-500 font-bold"><?php echo str_repeat('★', $r['rating']); ?></div>
                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($r['firstname']) . " on " . date('M d, Y', strtotime($r['time_stamp'])); ?></div>
                        <div class="text-gray-700 mt-2"><?php echo htmlspecialchars($r['comment']); ?></div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-500">No reviews yet.</p>
            <?php endif; ?>
        </div>

        <!-- Leave a Review Section -->
        <div>
            <h3 class="text-lg font-semibold text-green-800">Leave a Review</h3>
            <form method="POST" class="mt-4 space-y-4">
                <input type="hidden" name="token" value="<?php echo $_SESSION['review_token']; ?>">
                <div>
                    <label for="rating" class="block text-sm font-medium">Rating (1–5)</label>
                    <input type="number" name="rating" id="rating" min="1" max="5" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label for="comment" class="block text-sm font-medium">Comment</label>
                    <textarea name="comment" id="comment" rows="4" required class="w-full border rounded px-3 py-2"></textarea>
                </div>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">Submit</button>
            </form>
        </div>
    </main>

    <script>
        const searchInput = document.getElementById('searchInput');
        const menuItems = document.querySelectorAll('.menu-item-card');

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase();
            menuItems.forEach(item => {
                const name = item.querySelector('.font-semibold').innerText.toLowerCase();
                const description = item.querySelector('.text-gray-500').innerText.toLowerCase();
                const match = name.includes(query) || description.includes(query);
                item.style.display = match ? '' : 'none';
            });
        });

        document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', () => {
        const menuItemId = btn.dataset.id;
        const actionFile = btn.dataset.action;

        // Send request to the add_to_cart.php script
        fetch(actionFile, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `menu_items_id=${menuItemId}`
        })
        .then(response => response.text())
        .then(responseText => {
            alert(responseText);  // Show success or failure message from add_to_cart.php
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Something went wrong!');
        });
    });
});
fetch('addToCart.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `menu_items_id=${itemId}&quantity=${quantity}`
})
.then(response => response.text())
.then(data => {
    alert(data); // Show the response
    document.getElementById('cartModal').classList.add('hidden');
});


    </script>

</body>
</html>

<?php $conn->close(); ?>
