<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$task_id = $_GET['task_id'];

$stmt = $koneksi->prepare("SELECT is_completed FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

$new_status = $task['is_completed'] ? 0 : 1;

$stmt = $koneksi->prepare("UPDATE tasks SET is_completed = ? WHERE id = ?");
$stmt->execute([$new_status, $task_id]);

header("Location: tasks.php?list_id=" . $_GET['list_id']);
exit();
?>
