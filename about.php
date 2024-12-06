<?php
include 'auth_middleware.php';
include 'db_connection.php';

$isLoggedIn = isset($_SESSION['user_id']);
function isAdmin($conn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = :user_id AND role = 'admin'");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}
function getUsername($conn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['username'] : 'User';
}

checkAuthentication();
$username = getUsername($conn);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Me</title>
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

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-lg rounded-lg p-8 max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">About the Movie Enthusiast</h1>
            
            <div class="flex flex-col items-center mb-8">
                <img src="59494651.jpg" 
                     alt="Your Photo" 
                     class="w-48 h-48 rounded-full object-cover border-4 border-gray-300">
            </div>
            
            <div class="text-center">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Personal Information</h2>
                <p class="text-gray-600 mb-6">
                    Hello! I'm a passionate movie lover who enjoys exploring various genres and discovering hidden cinematic gems.
                </p>
                
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Movie Preferences</h2>
                <ul class="list-disc list-inside text-gray-600 mb-6">
                    <li>Favorite Genre: Science Fiction</li>
                    <li>Favorite Director: Tom Hollan</li>
                    <li>Most Watched Movie: Avengers</li>
                </ul>
                
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Contact Information</h2>
                <p class="text-gray-600">Email: dh52112120@student.stu.edu.vn</p>
                <p class="text-gray-600">GitHub: https://github.com/vuongprogk/Movie-List</p>
            </div>
        </div>
    </div>
</body>
</html>