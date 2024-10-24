<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $list_id = $_POST['list_id'];
    $notes = $_POST['notes'];

    $query = "UPDATE tasks SET notes = ? WHERE id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->execute([$notes, $task_id]);

    header("Location: view_list.php?list_id=" . $list_id);
    exit();
}
?>
