<?php
include 'auth_middleware.php';
include 'db_connection.php';

// Check if movie ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: all_movies.php');
    exit();
}
function isAdmin($conn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = :id AND role = 'admin'");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}
checkAuthentication();
$movieId = intval($_GET['id']);

// Fetch specific movie details
$movieQuery = "SELECT * FROM movies WHERE id = :id";
$stmt = $conn->prepare($movieQuery);
$stmt->bindParam(':id', $movieId, PDO::PARAM_INT);
$stmt->execute();
$movieResult = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if movie exists
if (!$movieResult) {
    header('Location: all_movies.php');
    exit();
}
function getUsername($conn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['username'] : 'User';
}

$movie = $movieResult;
$username = getUsername($conn);
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - Movie Details</title>
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
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Movie Poster -->
            <div class="w-full md:w-1/3">
                <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" 
                     alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                     class="w-full rounded-lg shadow-lg">
            </div>
            
            <!-- Movie Details -->
            <div class="w-full md:w-2/3">
                <h1 class="text-4xl font-bold mb-4 text-gray-800"><?php echo htmlspecialchars($movie['title']); ?></h1>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 text-gray-700">
                    <p><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
                    <p><strong>Release Year:</strong> <?php echo htmlspecialchars($movie['release_year']); ?></p>
                    <p><strong>Rating:</strong> <?php echo htmlspecialchars($movie['rating']); ?></p>
                    <p><strong>Popularity:</strong> <?php echo htmlspecialchars($movie['popularity']); ?></p>
                </div>

                <!-- Movie Description -->
                <div class="mt-4">
                    <h2 class="text-2xl font-bold mb-2 text-gray-800">Description</h2>
                    <p class="text-gray-700 leading-relaxed">
                        <?php echo htmlspecialchars($movie['description'] ?? 'No description available.'); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Movie Trailer -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4 text-gray-800">Movie Trailer</h2>
            <div class="aspect-w-16 aspect-h-9">
                <iframe 
                    width="100%" 
                    height="500" 
                    src="<?php echo str_replace('watch?v=', 'embed/', htmlspecialchars($movie['trailer_url'])); ?>" 
                    title="<?php echo htmlspecialchars($movie['title']); ?> Trailer"
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</body>
</html>