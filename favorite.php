<?php
session_start();
include 'db.php';

$user_id = 1; // Replace with session user_id

$query = "SELECT s.stall_id, s.name, s.description
          FROM favorites f
          JOIN stall s ON f.stall_id = s.stall_id
          WHERE f.user_id = $user_id";

$result = mysqli_query($conn, $query);
?>

<div class="p-4">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Favorite Stalls</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white rounded shadow p-4">
                    <h3 class="text-lg font-bold text-gray-800"><?php echo $row['name']; ?></h3>
                    <p class="text-gray-600 mt-2"><?php echo $row['description']; ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="bg-white p-6 rounded shadow text-center text-gray-600">
            You haven't favorited any stalls yet.
        </div>
    <?php endif; ?>
</div>
