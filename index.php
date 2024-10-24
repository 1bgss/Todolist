<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List - Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Section Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">To-Do List</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Sign Up</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Section Hero -->
    <div class="hero-section">
        <div class="hero-text">
            <h1>To-Do List</h1>
            <p>Stay organized dan Jadikan Hidupmu Senantiasa Efisien!</p>
            <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
        </div>
    </div>

    <!-- Section Footer -->
    <footer class="footer text-center">
        <p>&copy; <?php echo date("Y"); ?> To-Do List App. All rights reserved.</p>
    </footer>

    <script src="assets/jquery.min.js"></script>
    <script src="assets/bootstrap.bundle.min.js"></script>
</body>
</html>
