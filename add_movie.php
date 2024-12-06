<?php
include 'auth_middleware.php';
include 'db_connection.php';

// Check if user is admin
function isAdmin($conn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = :user_id AND role = 'admin'");
    $stmt->bindParam(':user_id', $user_id);
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

// Check authentication and admin status
checkAuthentication();
if (!isAdmin($conn)) {
    header("Location: popular_movies.php");
    exit();
}
$username = getUsername($conn);

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Movie</title>
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
</body>
</html>
