<?php
include 'auth_middleware.php';
include 'db_connection.php';
function isAdmin($conn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = :id AND role = 'admin'");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
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
$isLoggedIn = isset($_SESSION['user_id']);
// Fetch all movies
$allMoviesQuery = "SELECT * FROM movies";
$username = getUsername($conn);
$allMoviesResult = $conn->query($allMoviesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Movies</title>
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

    <?php if ($isLoggedIn): ?>
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">All Movies in Database</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php while($movie = $allMoviesResult->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden transform transition duration-300 hover:scale-105">
                        <img src="<?php echo $movie['poster_url']; ?>" 
                             alt="<?php echo $movie['title']; ?>" 
                             class="w-full h-96 object-cover">
                        <div class="p-4">
                            <h3 class="text-xl font-semibold mb-2"><?php echo $movie['title']; ?></h3>
                            <p class="text-gray-600">Genre: <?php echo $movie['genre']; ?></p>
                            <p class="text-gray-600">Release Year: <?php echo $movie['release_year']; ?></p>
                            <p class="text-gray-600">Rating: <?php echo $movie['rating']; ?></p>
                            <a href="movie_detail.php?id=<?php echo $movie['id']; ?>" 
                               class="mt-4 block bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 text-center">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="container mx-auto px-4 py-8 text-center">
            <h2 class="text-2xl font-bold mb-4">Please Sign In to View Movies</h2>
            <p class="mb-6">Authentication is required to access movie content.</p>
            <div class="space-x-4">
                <a href="login.php" class="bg-blue-500 px-6 py-3 rounded text-white hover:bg-blue-600">Sign In</a>
                <a href="sign_up.php" class="bg-green-500 px-6 py-3 rounded text-white hover:bg-green-600">Sign Up</a>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>