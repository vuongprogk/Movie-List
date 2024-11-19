<?php
include 'auth_middleware.php';


// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: login.php');
    exit();
}

// If logged in, redirect to popular movies
header('Location: popular_movies.php');
exit();
?>
