<?php
session_start();
include 'db.php';

$search_query = sanitize_input($_GET['search_query'] ?? '');

if (empty($search_query)) {
    echo "Please enter a search query.";
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM tasks INNER JOIN todo_lists ON tasks.list_id = todo_lists.id WHERE tasks.task_description LIKE ? AND todo_lists.user_id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->execute(['%' . $search_query . '%', $user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Search Results for "<?= htmlspecialchars($search_query) ?>"</h1>
    <ul class="list-group">
        <?php if (count($tasks) > 0): ?>
            <?php foreach ($tasks as $task): ?>
                <li class="list-group-item">
                    <h5><?= htmlspecialchars($task['task_description']) ?></h5>
                    <p>In List: <?= htmlspecialchars($task['title']) ?></p>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="list-group-item">No tasks found.</li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>
