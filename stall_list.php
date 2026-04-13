<?php
session_start();
include('db.php');

// Make sure the vendor is logged in and has a vendor_id in the session
if (!isset($_SESSION['vendor_id'])) {
    echo "You must be logged in as a vendor to view your stalls.";
    exit;
}

$vendor_id = $_SESSION['vendor_id']; // Get the vendor ID from the session

// Modify the SQL query to fetch only stalls that belong to the logged-in vendor
$stalls = $conn->query("SELECT * FROM stall WHERE vendor_id = $vendor_id");
?>

<div class="bg-white p-6 rounded-xl shadow-lg">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-3xl font-semibold text-gray-800">Your Stalls</h2>
    <a href="add_stall.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition duration-300">Add New Stall</a>
  </div>

  <?php if ($stalls->num_rows > 0): ?>
    <div class="overflow-x-auto rounded-lg border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100 text-sm font-semibold text-gray-600 uppercase">
          <tr>
            <th class="px-6 py-3 text-left">Stall Number</th>
            <th class="px-6 py-3 text-left">Name</th>
            <th class="px-6 py-3 text-left">Location</th>
            <th class="px-6 py-3 text-left">Availability</th>
            <th class="px-6 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
          <?php while ($stall = $stalls->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50 transition duration-300">
              <td class="px-6 py-4"><?= htmlspecialchars($stall['stall_number']) ?></td>
              <td class="px-6 py-4"><?= htmlspecialchars($stall['name']) ?></td>
              <td class="px-6 py-4"><?= htmlspecialchars($stall['location_description']) ?></td>
              <td class="px-6 py-4">
                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium 
                  <?= $stall['availability'] === 'Open' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                  <?= htmlspecialchars($stall['availability']) ?>
                </span>
              </td>
              <td class="px-6 py-4 text-center space-x-4">
                <a href="edit_stall.php?id=<?= $stall['stall_id'] ?>" class="text-blue-600 hover:text-blue-800 font-medium transition duration-200">Edit</a>
                <a href="delete_stall.php?id=<?= $stall['stall_id'] ?>" class="text-red-600 hover:text-red-800 font-medium transition duration-200">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <div class="text-center text-gray-500 py-12 text-lg">You have no stalls associated with your account.</div>
  <?php endif; ?>
</div>

<style>
  /* Custom hover effects for table rows */
  tr:hover {
    background-color: #f9fafb; /* Slight gray on hover */
  }

  /* Action buttons styling */
  a {
    transition: all 0.3s ease-in-out;
  }

  a:hover {
    text-decoration: underline;
    transform: scale(1.05);
  }

  /* Table styling */
  table {
    border-collapse: collapse;
    width: 100%;
  }

  th, td {
    padding: 12px;
    text-align: left;
  }

  th {
    background-color: #f3f4f6;
  }

  td {
    border-top: 1px solid #e5e7eb;
  }

  /* Responsive table scroll */
  .overflow-x-auto {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  /* Add shadow effect on hover */
  .table-hover:hover {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  /* Add transition to buttons */
  .bg-blue-600:hover {
    background-color: #3b82f6;
  }
</style>
