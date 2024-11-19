<?php
include 'auth_middleware.php';
include 'db_connection.php';

// Check if user is admin
function isAdmin($conn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Check authentication and admin status
checkAuthentication();
if (!isAdmin($conn)) {
    header("Location: popular_movies.php");
    exit();
}

// Handle movie deletion
if (isset($_GET['delete'])) {
    $movie_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $movie_id);
    $stmt->execute();
}

// Fetch all movies for management
$moviesQuery = "SELECT * FROM movies ORDER BY popularity DESC";
$moviesResult = $conn->query($moviesQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Movie Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-gray-800 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <a href="popular_movies.php" class="hover:text-gray-300 mr-4">Popular Movies</a>
                <a href="all_movies.php" class="hover:text-gray-300 mr-4">All Movies</a>
                <a href="about.php" class="hover:text-gray-300 mr-4">About Me</a>
                <a href="admin.php" class="hover:text-gray-300 mr-4">Manage Movies</a>
                <a href="admin-user.php" class="hover:text-gray-300 mr-4">Manage Users</a>
                
            </div>
            <div>
                <span class="mr-4">Welcome, Admin</span>
                <a href="logout.php" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Movie Management</h1>
            <a href="add_movie.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Add New Movie
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Existing Movies</h2>
            <table class="w-full">
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
                    <?php while($movie = $moviesResult->fetch_assoc()): ?>
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
</body>
</html>