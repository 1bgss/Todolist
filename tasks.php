<?php
session_start();
include 'db.php';

$list_id = $_GET['list_id'];
$search_query = sanitize_input($_GET['search'] ?? '');
$filter_status = $_GET['filter_status'] ?? 'all';

$sql = "SELECT * FROM tasks WHERE list_id = ? AND task_description LIKE ?";
$params = [$list_id, '%' . $search_query . '%'];

if ($filter_status === 'completed') {
    $sql .= " AND is_completed = 1";
} elseif ($filter_status === 'incomplete') {
    $sql .= " AND is_completed = 0";
}

$stmt = $koneksi->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $koneksi->prepare("SELECT * FROM tasks WHERE user_id = ? AND is_completed = 0 ORDER BY due_date ASC");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
<body>
<div class="container mt-5">
    <h1 class="text-center">Tasks</h1>

    <!-- Search sama Filter Form -->
    <form action="tasks.php" method="GET" class="form-inline mb-3">
        <input type="hidden" name="list_id" value="<?= htmlspecialchars($list_id) ?>">
        <div class="form-group mx-sm-3 mb-2">
            <input type="text" class="form-control" name="search" placeholder="Search tasks..." value="<?= htmlspecialchars($search_query) ?>">
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <select name="filter_status" class="form-control">
                <option value="all" <?= $filter_status === 'all' ? 'selected' : '' ?>>All Tasks</option>
                <option value="completed" <?= $filter_status === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="incomplete" <?= $filter_status === 'incomplete' ? 'selected' : '' ?>>Incomplete</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Search</button>
    </form>

    <ul class="list-group">
        <?php if (count($tasks) > 0): ?>
            <?php foreach ($tasks as $task): ?>
                <li class="list-group-item">
                    <h5><?= htmlspecialchars($task['task_description']) ?></h5>
                    <p><?= htmlspecialchars($task['description']) ?></p>
                    <p><strong>Due Date:</strong> <?= $task['due_date']; ?> 
                    <?php if (strtotime($task['due_date']) < strtotime('+2 days')): ?>
                        <span class="badge badge-danger">Upcoming!</span>
                    <?php endif; ?>
                    </p>
                    <p><strong>Status:</strong> <?= $task['is_completed'] ? 'Completed' : 'Incomplete' ?></p>
                    <a href="edit_task.php?task_id=<?= $task['id'] ?>" class="btn btn-info">Edit</a>
                    <a href="delete_task.php?task_id=<?= $task['id'] ?>" class="btn btn-danger">Delete</a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="list-group-item">No tasks found.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>
