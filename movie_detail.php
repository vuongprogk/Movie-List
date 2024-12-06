<?php
include 'includes/header.php';
// Check if movie ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: all_movies.php');
    exit();
}

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
?>

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
<?php include 'includes/footer.php'; ?>