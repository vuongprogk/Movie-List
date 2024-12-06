<?php
session_start();


function getCurrentUser($conn) {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(":id", $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit();
}
function checkAuthentication() {
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page if not authenticated
        header("Location: login.php");
        exit();
    }
}
function isAdmin($conn) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = :id AND role = 'admin'");
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
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
?>