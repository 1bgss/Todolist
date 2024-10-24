<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $koneksi->prepare("SELECT * FROM tasks WHERE user_id = ? AND is_completed = 0 ORDER BY FIELD(DAYNAME(due_date), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), due_date");
$stmt->execute([$user_id]);
$weekly_schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$schedule_by_day = [];
foreach ($days as $day) {
    $schedule_by_day[$day] = array_filter($weekly_schedule, function ($task) use ($day) {
        return date('l', strtotime($task['due_date'])) === $day;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Week Schedule</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="stylesweek.css"> 
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">To-Do List</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="week_schedule.php">Week Schedule</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center">Weekly Schedule</h1>
        <div class="week-schedule row">
            <?php foreach ($days as $day): ?>
                <div class="day-card col-lg-4 col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3><?= $day ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="tasks">
                                <?php if (!empty($schedule_by_day[$day])): ?>
                                    <?php foreach ($schedule_by_day[$day] as $task): ?>
                                        <div class="task-item" onclick="openTaskModal('<?= htmlspecialchars($task['task_description']) ?>', '<?= date('Y-m-d', strtotime($task['due_date'])) ?>', <?= $task['is_completed'] ?>)">
                                            <p><strong><?= htmlspecialchars($task['task_description']) ?></strong> <br>
                                            Due: <?= htmlspecialchars(date('Y-m-d', strtotime($task['due_date']))) ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No tasks for <?= $day ?>.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="taskModalLabel">Task Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="taskDescription"></p>
                    <p><strong>Due Date:</strong> <span id="taskDueDate"></span></p>
                    <p><strong>Status:</strong> <span id="taskStatus"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openTaskModal(description, dueDate, status) {
            document.getElementById('taskDescription').innerText = description;
            document.getElementById('taskDueDate').innerText = dueDate;
            document.getElementById('taskStatus').innerText = status ? 'Completed' : 'Incomplete';
            $('#taskModal').modal('show');
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
