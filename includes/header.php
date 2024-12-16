<?php
include 'config.php';
include 'functions.php';
$isLoggedIn = isset($_SESSION['user_id']);
checkAuthentication();
$username = getUsername($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tran Duc Vuong - DH52112120 </title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
     <nav class="bg-gray-800 p-4 text-white">
        <div class="container mx-auto flex flex-wrap justify-between items-center">
            <div class="flex items-center">
                <button id="nav-toggle" class="lg:hidden block text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
                <a href="popular_movies.php" class="hover:text-gray-300 ml-4 lg:ml-0">Popular Movies</a>
            </div>
            <div id="nav-content" class="w-full lg:flex lg:items-center lg:w-auto hidden lg:block">
                <div class="lg:flex-grow">
                    <a href="all_movies.php" class="block mt-4 lg:inline-block lg:mt-0 hover:text-gray-300 mr-4">All Movies</a>
                    <a href="about.php" class="block mt-4 lg:inline-block lg:mt-0 hover:text-gray-300 mr-4">About Me</a>
                    <?php if (isAdmin($conn)): ?>
                        <a href="admin.php" class="block mt-4 lg:inline-block lg:mt-0 hover:text-gray-300 mr-4">Manage Movies</a>
                        <a href="admin-user.php" class="block mt-4 lg:inline-block lg:mt-0 hover:text-gray-300 mr-4">Manage Users</a>
                <?php endif; ?>
                </div>
                    <div>
                    <span class="block mt-4 lg:inline-block lg:mt-0 mr-4">Welcome, <?php echo htmlspecialchars($username); ?></span>
                    <a href="logout.php" class="block mt-4 lg:inline-block lg:mt-0 bg-red-500 px-3 py-1 rounded hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>
<script>
        document.getElementById('nav-toggle').onclick = function() {
            var navContent = document.getElementById('nav-content');
            if (navContent.classList.contains('hidden')) {
                navContent.classList.remove('hidden');
            } else {
                navContent.classList.add('hidden');
            }
        };
    </script>