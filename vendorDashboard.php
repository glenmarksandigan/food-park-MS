<?php
// vendor_dashboard.php

include('db.php'); // Include the database connection
?>
<style>
    
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vendor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-800 text-white flex flex-col position: fixed; ">
        <div class="p-6 text-xl font-bold flex items-center justify-between">
            <span>Food Park</span>
        </div>
        <nav class="flex-1 px-4 mt-4">
            <ul class="space-y-2">
                <li>
                    <a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page="dashboardStats">
                        <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page="products">
                        <i class="fas fa-store mr-3"></i> Products
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page="orders">
                        <i class="fas fa-shopping-cart mr-3"></i> Orders
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page="customer">
                        <i class="fas fa-users mr-3"></i> Customers
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page="stall_list">
                        <i class="fas fa-warehouse mr-3"></i> Stall Management
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page=" Vsettings">
                        <i class="fas fa-cog mr-3"></i> Settings
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
        <!-- Default content will be dynamically loaded here -->
    </main>

    <script>
        // Add an event listener to all sidebar links
        document.querySelectorAll('nav ul li a').forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault(); // Prevent the default link behavior

                const page = this.getAttribute('data-page'); // Get the page from data-page attribute
                loadPage(page); // Load the page content dynamically
            });
        });

        // Function to load content based on the selected page
        function loadPage(page) {
            const mainContent = document.querySelector('main'); // The main content area

            // Use AJAX to fetch the content for the selected page
            fetch(page + '.php')
                .then(response => response.text())
                .then(data => {
                    mainContent.innerHTML = data; // Replace the current main content with the new content
                })
                .catch(error => {
                    mainContent.innerHTML = '<p>Error loading page content.</p>';
                    console.error(error);
                });
        }

        // Load the default page (dashboard) when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadPage('dashboardStats'); // Default page is 'dashboard'
        });
    </script>
</body>
</html>
