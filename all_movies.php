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
checkAuthentication();
$isLoggedIn = isset($_SESSION['user_id']);
// Fetch all movies
$allMoviesQuery = "SELECT * FROM movies";
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

   
    <?php if ($isLoggedIn): ?>
           <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">All Movies in Database</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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