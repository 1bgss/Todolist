<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$list_id = $_GET['list_id']; 
$user_id = $_SESSION['user_id'];

$filter_status = $_GET['filter_status'] ?? 'all';

$query = "SELECT * FROM tasks WHERE list_id = ? AND user_id = ?";
$params = [$list_id, $user_id];

if ($filter_status === 'completed') {
    $query .= " AND is_completed = 1"; 
} elseif ($filter_status === 'incomplete') {
    $query .= " AND is_completed = 0"; 
}

$stmt = $koneksi->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tasks</title>
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
        <h1 class="text-center">Tasks in This List</h1>

        <div class="mb-4">
            <a href="delete_list.php?list_id=<?= $list_id ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this list?')">Delete List</a>
            <a href="create_task.php?list_id=<?= $list_id ?>" class="btn btn-success">Add New Task</a>
        </div>

        <form action="view_list.php" method="GET" class="form-inline mb-3">
            <input type="hidden" name="list_id" value="<?= htmlspecialchars($list_id) ?>">
            <select name="filter_status" class="form-control mr-2">
                <option value="all" <?= $filter_status === 'all' ? 'selected' : '' ?>>All Tasks</option>
                <option value="completed" <?= $filter_status === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="incomplete" <?= $filter_status === 'incomplete' ? 'selected' : '' ?>>Incomplete</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <?php if (count($tasks) > 0): ?>
            <ul class="list-group">
                <?php foreach ($tasks as $task): ?>
                    <li class="list-group-item <?= $task['is_completed'] ? 'bg-success text-white' : 'bg-warning' ?>">
                        <h5><?= htmlspecialchars($task['task_description']) ?></h5>
                        <p><strong>Status:</strong> <?= $task['is_completed'] ? 'Completed' : 'Incomplete' ?></p>
                        <p><strong>Due Date:</strong> <?= htmlspecialchars($task['due_date']) ?></p>

                        <p><strong>Notes:</strong> <?= nl2br(htmlspecialchars($task['notes'])) ?></p>

                        <form action="edit_notes.php" method="POST" class="mt-2">
                            <div class="form-group">
                                <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($task['notes']) ?></textarea>
                            </div>
                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                            <input type="hidden" name="list_id" value="<?= $list_id ?>">
                            <button type="submit" class="btn btn-primary">Save Notes</button>
                        </form>

                        <div class="mt-2">
                            <a href="edit_task.php?task_id=<?= $task['id'] ?>&list_id=<?= $list_id ?>" class="btn btn-info">Edit</a>
                            <a href="delete_task.php?task_id=<?= $task['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No tasks found in this list. <a href="create_task.php?list_id=<?= $list_id ?>">Add a new task</a></p>
        <?php endif; ?>
    </div>

    <script src="assets/bootstrap.bundle.min.js"></script>
</body>
</html>
