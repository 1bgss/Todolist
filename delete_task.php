<?php
session_start();
include 'db.php'; // Database connection

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = $_GET['task_id']; // Get the task ID from the URL

// Verify that the task belongs to the logged-in user before deletion
$query = "SELECT * FROM tasks WHERE id = ? AND user_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->execute([$task_id, $user_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    // If the task doesn't exist or doesn't belong to the user, redirect back to the list page
    header('Location: dashboard.php');
    exit();
}

// Proceed to delete the task
$deleteQuery = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
$deleteStmt = $koneksi->prepare($deleteQuery);
$deleteStmt->execute([$task_id, $user_id]);

// Redirect back to the task list page after deletion
header('Location: view_list.php?list_id=' . $task['list_id']);
exit();
?>
