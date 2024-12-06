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

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    header("Location: admin-user.php");
    exit();
}

// Handle user role update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];
    $stmt = $conn->prepare("UPDATE users SET role = :new_role WHERE id = :user_id");
    $stmt->bindParam(':new_role', $new_role);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    header("Location: admin-user.php");
    exit();
}

// Existing movie-related code from previous implementation...
// (Keep the movie addition, deletion, and fetching code from the previous admin-user.php)

// Fetch all users for management
$usersQuery = "SELECT * FROM users ORDER BY id";
$username = getUsername($conn);
$usersResult = $conn->query($usersQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Movie and User Management</title>
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
            <h1 class="text-3xl font-bold">User Management</h1>
            <a href="add_user.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Add New User
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6 overflow-x-auto">
            <h2 class="text-2xl font-semibold mb-4">Existing Users</h2>
            <table class="w-full min-w-max">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">ID</th>
                        <th class="p-2">Username</th>
                        <th class="p-2">Email</th>
                        <th class="p-2">Role</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $usersResult->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr class="border-b">
                            <td class="p-2"><?php echo htmlspecialchars($user['id']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="p-2">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="role" class="border rounded p-1">
                                        <option value="user" <?php echo ($user['role'] == 'user' ? 'selected' : ''); ?>>User</option>
                                        <option value="admin" <?php echo ($user['role'] == 'admin' ? 'selected' : ''); ?>>Admin</option>
                                    </select>
                                    <button type="submit" name="update_user_role" 
                                        class="bg-blue-500 text-white px-2 py-1 rounded ml-2 text-sm">
                                        Update Role
                                    </button>
                                </form>
                            </td>
                            <td class="p-2">
                                <a href="?delete_user=<?php echo $user['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this user?');" 
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
