<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$list_id = $_GET['list_id'];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_description = htmlspecialchars($_POST['task_description']);
    $due_date = $_POST['due_date']; 
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;

    $stmt = $koneksi->prepare("INSERT INTO tasks (list_id, user_id, task_description, due_date, is_completed) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$list_id, $user_id, $task_description, $due_date, $is_completed]);

    header("Location: view_list.php?list_id=" . $list_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">To-Do List</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center">Create New Task</h1>

        <form action="create_task.php?list_id=<?= $list_id ?>" method="POST" class="form-group">
            <div class="form-group">
                <label for="task_description">Task Description</label>
                <input type="text" class="form-control" id="task_description" name="task_description" required>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" required>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="is_completed" name="is_completed">
                <label class="form-check-label" for="is_completed">Mark as completed</label>
            </div>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>
    </div>

    <script src="assets/bootstrap.bundle.min.js"></script>
</body>
</html>
