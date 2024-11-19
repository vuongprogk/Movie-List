<?php
session_start();

function checkAuthentication() {
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page if not authenticated
        header("Location: login.php");
        exit();
    }
}

function getCurrentUser($conn) {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
