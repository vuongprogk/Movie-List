<?php
include 'auth_middleware.php';
include 'db_connection.php';

$isLoggedIn = isset($_SESSION['user_id']);
function isAdmin($conn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
checkAuthentication();
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
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center text-white">
            <div class="space-x-4">
                <a href="popular_movies.php" class="hover:text-gray-300">Popular Movies</a>
                <a href="all_movies.php" class="hover:text-gray-300">All Movies</a>
                <a href="about.php" class="hover:text-gray-300">About Me</a>
                <?php if (isAdmin($conn)): ?>
                    <a href="admin.php" class="hover:text-gray-300">Admin</a>
                <?php endif; ?>
            </div>
            <div class="space-x-4">
                <?php if (!$isLoggedIn): ?>
                    <a href="login.php" class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-600">Sign In</a>
                    <a href="sign_up.php" class="bg-green-500 px-4 py-2 rounded hover:bg-green-600">Sign Up</a>
                <?php else: ?>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                    <a href="logout.php" class="bg-red-500 px-4 py-2 rounded hover:bg-red-600">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white shadow-lg rounded-lg p-8 max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">About the Movie Enthusiast</h1>
            
            <div class="flex flex-col items-center mb-8">
                <img src="your_photo.jpg" 
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
                    <li>Favorite Director: Christopher Nolan</li>
                    <li>Most Watched Movie: Inception</li>
                </ul>
                
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Contact Information</h2>
                <p class="text-gray-600">Email: movie.enthusiast@example.com</p>
                <p class="text-gray-600">GitHub: github.com/movie-lover</p>
            </div>
        </div>
    </div>
</body>
</html>