<?php
include 'includes/header.php';
checkAuthentication();
if (!isAdmin($conn)) {
    header("Location: popular_movies.php");
    exit();
}

// Handle movie addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_movie'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $genre = $_POST['genre'];
    $release_year = $_POST['release_year'];
    $rating = $_POST['rating'];
    $popularity = $_POST['popularity'];
    $trailer_url = $_POST['trailer_url'];
    $poster_url = $_POST['poster_url'];

    $stmt = $conn->prepare("INSERT INTO movies (title, description, genre, release_year, rating, popularity, trailer_url, poster_url) VALUES (:title, :description, :genre, :release_year, :rating, :popularity, :trailer_url, :poster_url)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':release_year', $release_year);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':popularity', $popularity);
    $stmt->bindParam(':trailer_url', $trailer_url);
    $stmt->bindParam(':poster_url', $poster_url);
    
    if ($stmt->execute()) {
        $success_message = "Movie added successfully!";
    } else {
        $error_message = "Error adding movie: " . $conn->error;
    }
}
?>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-wrap justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Add New Movie</h1>
            <a href="admin.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Admin Panel
            </a>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form method="POST" class="grid grid-cols-2 gap-4">
                <div class="col-span-2 grid grid-cols-2 gap-4">
                    <input type="text" name="title" placeholder="Movie Title" required 
                        class="border p-2 rounded">
                    <input type="text" name="genre" placeholder="Genre" required 
                        class="border p-2 rounded">
                    <input type="number" name="release_year" placeholder="Release Year" required 
                        class="border p-2 rounded">
                    <input type="number" step="0.1" name="rating" placeholder="Rating" required 
                        class="border p-2 rounded">
                    <input type="number" name="popularity" placeholder="Popularity" required 
                        class="border p-2 rounded">
                    <input type="text" name="trailer_url" placeholder="Trailer URL" 
                        class="border p-2 rounded">
                </div>
                <textarea name="description" placeholder="Movie Description" 
                    class="border p-2 rounded col-span-2" rows="3"></textarea>
                <input type="text" name="poster_url" placeholder="Poster URL" 
                    class="border p-2 rounded col-span-2">
                <div class="col-span-2 flex justify-end space-x-4">
                    <a href="admin.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Cancel
                    </a>
                    <button type="submit" name="add_movie" 
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Add Movie
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>
