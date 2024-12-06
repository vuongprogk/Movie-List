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

// Function to handle file upload
function uploadPoster($file) {
    // Define upload directory
    $upload_dir = 'uploads/posters/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $unique_filename = uniqid('poster_', true) . '.' . $file_extension;
    $upload_path = $upload_dir . $unique_filename;

    // Allowed file types
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // Validate file
    if (!in_array($file_extension, $allowed_types)) {
        throw new Exception("Invalid file type. Allowed types: " . implode(', ', $allowed_types));
    }

    // Max file size (5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception("File is too large. Maximum size is 5MB.");
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $upload_path;
    } else {
        throw new Exception("Failed to upload file.");
    }
}

// Check authentication and admin status
checkAuthentication();
if (!isAdmin($conn)) {
    header("Location: popular_movies.php");
    exit();
}
function getUsername($conn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['username'] : 'User';
}

// Get movie details for editing
$movie_id = $_GET['id'] ?? null;
$movie = null;
$error_message = '';
$success_message = '';
$username = getUsername($conn);

if ($movie_id) {
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = :movie_id");
    $stmt->bindParam(':movie_id', $movie_id);
    $stmt->execute();
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle movie update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_movie'])) {
    try {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $genre = $_POST['genre'];
        $release_year = $_POST['release_year'];
        $rating = $_POST['rating'];
        $popularity = $_POST['popularity'];
        $trailer_url = $_POST['trailer_url'];

        // Handle poster upload
        $poster_url = $_POST['poster_url']; // Keep existing URL if no new file uploaded
        if (!empty($_FILES['poster']['name'])) {
            $poster_url = uploadPoster($_FILES['poster']);
        }

        $stmt = $conn->prepare("UPDATE movies SET title=:title, description=:description, genre=:genre, release_year=:release_year, rating=:rating, popularity=:popularity, trailer_url=:trailer_url, poster_url=:poster_url WHERE id=:movie_id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':release_year', $release_year);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':popularity', $popularity);
        $stmt->bindParam(':trailer_url', $trailer_url);
        $stmt->bindParam(':poster_url', $poster_url);
        $stmt->bindParam(':movie_id', $movie_id);
        $stmt->execute();

        $success_message = "Movie updated successfully!";
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie</title>
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
            <h1 class="text-2xl font-bold mb-6">Edit Movie</h1>
            <a href="admin.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Admin Panel
            </a>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="title" placeholder="Movie Title" 
                    value="<?php echo htmlspecialchars($movie['title'] ?? ''); ?>" required 
                    class="border p-2 rounded w-full">
                <input type="text" name="genre" placeholder="Genre" 
                    value="<?php echo htmlspecialchars($movie['genre'] ?? ''); ?>" required 
                    class="border p-2 rounded w-full">
                <input type="number" name="release_year" placeholder="Release Year" 
                    value="<?php echo htmlspecialchars($movie['release_year'] ?? ''); ?>" required 
                    class="border p-2 rounded w-full">
                <input type="number" step="0.1" name="rating" placeholder="Rating" 
                    value="<?php echo htmlspecialchars($movie['rating'] ?? ''); ?>" required 
                    class="border p-2 rounded w-full">
                <input type="number" name="popularity" placeholder="Popularity" 
                    value="<?php echo htmlspecialchars($movie['popularity'] ?? ''); ?>" required 
                    class="border p-2 rounded w-full">
                <input type="text" name="trailer_url" placeholder="Trailer URL" 
                    value="<?php echo htmlspecialchars($movie['trailer_url'] ?? ''); ?>" 
                    class="border p-2 rounded w-full">
                <textarea name="description" placeholder="Movie Description" 
                    class="border p-2 rounded col-span-1 sm:col-span-2" rows="3"><?php echo htmlspecialchars($movie['description'] ?? ''); ?></textarea>
                
                <!-- Existing Poster URL Input -->
                <input type="text" name="poster_url" placeholder="Poster URL" 
                    value="<?php echo htmlspecialchars($movie['poster_url'] ?? ''); ?>" 
                    class="border p-2 rounded col-span-1 sm:col-span-2">
                
                <!-- New Poster File Upload -->
                <div class="col-span-1 sm:col-span-2">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="poster">
                        Upload New Poster Image (Optional)
                    </label>
                    <input type="file" name="poster" accept="image/*" 
                        class="border p-2 rounded w-full file:mr-4 file:rounded file:border-0 file:bg-gray-200 file:px-4 file:py-2">
                </div>

                <button type="submit" name="update_movie" 
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 col-span-1 sm:col-span-2">
                    Update Movie
                </button>
            </form>
        </div>
    </div>
</body>
</html>