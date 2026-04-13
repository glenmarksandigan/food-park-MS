<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Food Park Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        .sidebar {
            position: fixed; 
            top: 0;
            left: 0;
            bottom: 0;
            width: 16rem; 
            background-color: #2d3748; 
            color: white;
            padding-top: 2rem;
            z-index: 100;
        }

        .main-content {
            margin-left: 16rem; 
            padding: 2rem;
            overflow-y: auto;
            height: 100vh;
            width: 100%; /* ✅ Fix: ensures full width */
        }

        #content-area {
            min-height: 50vh;
            overflow-y: auto;
        }

        nav ul li {
            margin-bottom: 1rem;
        }

        nav ul li a {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            text-decoration: none;
            color: white;
        }

        nav ul li a:hover {
            background-color: #4a5568;
            border-radius: 0.375rem;
        }
    </style>
</head>
<body class="flex bg-gray-100">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="p-6 text-xl font-bold flex items-center justify-between">
            <span>Food Park</span>
        </div>
        <nav class="flex-1 px-4 mt-4">
            <ul class="space-y-2">
                <li><a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page="home"><i class="fas fa-home mr-3"></i> Home</a></li>
                <li><a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page="order"><i class="fas fa-shopping-cart mr-3"></i> Orders</a></li>
                <li><a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page="cart"><i class="fas fa-cart-plus mr-3"></i> Cart</a></li>
                <li><a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page="status"><i class="fas fa-sync-alt mr-3"></i> Status</a></li>
                <li><a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page="favorite"><i class="fas fa-heart mr-3"></i> Favorite</a></li>
                <li><a href="#" class="flex items-center p-2 hover:bg-gray-700 rounded" data-page="Csettings"><i class="fas fa-cogs mr-3"></i> Settings</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <div id="content-area">
            <h2 class="text-3xl text-gray-800">Welcome to the Dashboard</h2>
        </div>
    </div>

    <script>
        document.querySelectorAll('nav ul li a').forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                const page = this.getAttribute('data-page');
                loadPage(page);
            });
        });

        function loadPage(page) {
            const contentArea = document.querySelector('#content-area');

            fetch(page + '.php')
                .then(response => response.text())
                .then(data => {
                    contentArea.innerHTML = data;
                    initializeSearch();
                })
                .catch(error => {
                    contentArea.innerHTML = '<p>Error loading content.</p>';
                    console.error(error);
                });
        }

        function initializeSearch() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
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
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadPage('home');
        });
    </script>
</body>
</html>
