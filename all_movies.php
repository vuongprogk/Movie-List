<?php 
include 'includes/header.php';

// Fetch all movies
$allMoviesQuery = "SELECT * FROM movies";
$allMoviesResult = $conn->query($allMoviesQuery);
?>


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
<?php include 'includes/footer.php'; ?>