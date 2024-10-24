<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$list_id = $_GET['list_id'];

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM todo_lists WHERE id = ? AND user_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->execute([$list_id, $user_id]);
$list = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$list) {
    header('Location: dashboard.php');
    exit();
}

$query = "DELETE FROM todo_lists WHERE id = ? AND user_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->execute([$list_id, $user_id]);

$query = "DELETE FROM tasks WHERE list_id = ? AND user_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->execute([$list_id, $user_id]);

header('Location: dashboard.php');
exit();
?>
