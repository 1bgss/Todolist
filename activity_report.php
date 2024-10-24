<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$total_tasks_query = "SELECT COUNT(*) as total_tasks FROM tasks WHERE user_id = ?";
$total_tasks_stmt = $koneksi->prepare($total_tasks_query);
$total_tasks_stmt->execute([$user_id]);
$total_tasks = $total_tasks_stmt->fetch(PDO::FETCH_ASSOC)['total_tasks'];

$completed_tasks_query = "SELECT COUNT(*) as completed_tasks FROM tasks WHERE user_id = ? AND is_completed = 1";
$completed_tasks_stmt = $koneksi->prepare($completed_tasks_query);
$completed_tasks_stmt->execute([$user_id]);
$completed_tasks = $completed_tasks_stmt->fetch(PDO::FETCH_ASSOC)['completed_tasks'];

$incomplete_tasks_query = "SELECT COUNT(*) as incomplete_tasks FROM tasks WHERE user_id = ? AND is_completed = 0";
$incomplete_tasks_stmt = $koneksi->prepare($incomplete_tasks_query);
$incomplete_tasks_stmt->execute([$user_id]);
$incomplete_tasks = $incomplete_tasks_stmt->fetch(PDO::FETCH_ASSOC)['incomplete_tasks'];

$completion_percentage = $total_tasks > 0 ? ($completed_tasks / $total_tasks) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Report</title>
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
        <h1 class="text-center">Task Activity Report</h1>

        <!-- Statistik Tugas -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Overall Task Statistics</h5>
                <p><strong>Total Tasks:</strong> <?= $total_tasks; ?></p>
                <p><strong>Completed Tasks:</strong> <?= $completed_tasks; ?></p>
                <p><strong>Incomplete Tasks:</strong> <?= $incomplete_tasks; ?></p>

                <div class="progress mt-3">
                    <div class="progress-bar" role="progressbar" style="width: <?= $completion_percentage; ?>%;" aria-valuenow="<?= $completion_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                        <?= round($completion_percentage); ?>% Completed
                    </div>
                </div>
            </div>
        </div>
    </div>
    <canvas id="taskChart" width="400" height="400"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('taskChart').getContext('2d');
    var taskChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Completed', 'Incomplete'],
            datasets: [{
                label: 'Task Completion',
                data: [<?= $completed_tasks; ?>, <?= $incomplete_tasks; ?>],
                backgroundColor: ['#28a745', '#ffc107'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
</script>

    <script src="assets/bootstrap.bundle.min.js"></script>
</body>
</html>
