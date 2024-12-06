<?php
include 'includes/header.php';

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
$usersResult = $conn->query($usersQuery);
?>

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
<? include 'includes/footer.php'; ?>
