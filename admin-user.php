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

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: admin-user.php");
    exit();
}

// Handle user role update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);
    $stmt->execute();
    header("Location: admin-user.php");
    exit();
}

// Existing movie-related code from previous implementation...
// (Keep the movie addition, deletion, and fetching code from the previous admin-user.php)

// Fetch all users for management
$usersQuery = "SELECT * FROM users ORDER BY id";
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
            <h1 class="text-3xl font-bold">User Management</h1>
            <a href="add_user.php" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Add New User
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Existing Users</h2>
            <table class="w-full">
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
                    <?php while($user = $usersResult->fetch_assoc()): ?>
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
