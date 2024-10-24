<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $photo_name = $_FILES['profile_photo']['name'];
        $photo_tmp = $_FILES['profile_photo']['tmp_name'];
        $photo_ext = pathinfo($photo_name, PATHINFO_EXTENSION);
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($photo_ext, $allowed_exts)) {
            $new_photo_name = uniqid() . '.' . $photo_ext;
            move_uploaded_file($photo_tmp, 'uploads/' . $new_photo_name);
            $stmt = $koneksi->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
            $stmt->execute([$new_photo_name, $user_id]);
        }
    }

    $stmt = $koneksi->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $user_id]);

    header('Location: profile.php');
    exit();
}

$stmt = $koneksi->prepare("SELECT name, email, profile_photo FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Edit Your Profile</h1>
    <form action="edit_profile.php" method="POST" enctype="multipart/form-data" class="form-group">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="form-group">
            <label for="profile_photo">Profile Photo</label>
            <?php if (!empty($user['profile_photo'])): ?>
                <img src="uploads/<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Photo" style="width: 100px; height: 100px; border-radius: 50%;">
            <?php endif; ?>
            <input type="file" class="form-control" id="profile_photo" name="profile_photo">
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
</body>
</html>
