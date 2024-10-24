<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $koneksi->prepare("SELECT name, email, profile_photo FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Your Profile</h1>
    <div class="card">
    <div class="card-body">
        <img src="uploads/<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Photo" class="profile-photo">
        <h5 class="card-title"><?= htmlspecialchars($user['name']) ?></h5>
        <p class="card-text"><?= htmlspecialchars($user['email']) ?></p>
        <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
    </div>
</div>
</div>
</body>
</html>
