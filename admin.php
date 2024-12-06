<?php
include "includes/header.php";


// Check authentication and admin status
if (!isAdmin($conn)) {
    header("Location: popular_movies.php");
    exit();
}

// Handle movie deletion
if (isset($_GET['delete'])) {
    $movie_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = :movie_id");
    $stmt->bindParam(':movie_id', $movie_id);
    $stmt->execute();
}

// Fetch all movies for management
$moviesQuery = "SELECT * FROM movies ORDER BY popularity DESC";
$moviesResult = $conn->query($moviesQuery);
?>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-wrap justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Movie Management</h1>
            <a href="add_movie.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Add New Movie
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6 overflow-x-auto">
            <h2 class="text-2xl font-semibold mb-4">Existing Movies</h2>
            <table class="w-full min-w-max">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">Title</th>
                        <th class="p-2">Genre</th>
                        <th class="p-2">Release Year</th>
                        <th class="p-2">Rating</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($movie = $moviesResult->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr class="border-b">
                            <td class="p-2"><?php echo htmlspecialchars($movie['title']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($movie['genre']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($movie['release_year']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($movie['rating']); ?></td>
                            <td class="p-2">
                                <a href="edit_movie.php?id=<?php echo $movie['id']; ?>" 
                                   class="bg-blue-500 text-white px-2 py-1 rounded mr-2">Edit</a>
                                <a href="?delete=<?php echo $movie['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this movie?');" 
                                   class="bg-red-500 text-white px-2 py-1 rounded">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php include "includes/footer.php"; ?>