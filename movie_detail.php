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

$movie = $movieResult;
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
    <nav class="bg-gray-800 p-4">
        <div class="container mx-auto flex justify-between items-center text-white">
            <div class="space-x-4">
                <a href="popular_movies.php" class="hover:text-gray-300">Popular Movies</a>
                <a href="all_movies.php" class="hover:text-gray-300">All Movies</a>
                <a href="about_me.php" class="hover:text-gray-300">About Me</a>
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
                
                <div class="grid grid-cols-2 gap-4 mb-6 text-gray-700">
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