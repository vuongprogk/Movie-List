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
?>
