<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $koneksi->prepare("SELECT * FROM todo_lists WHERE user_id = ?");
$stmt->execute([$user_id]);
$todo_lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

$today = date('Y-m-d');
$stmt = $koneksi->prepare("SELECT * FROM tasks WHERE due_date = ? AND is_completed = 0 AND list_id IN (SELECT id FROM todo_lists WHERE user_id = ?)");
$stmt->execute([$today, $user_id]);
$tasks_due_today = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $koneksi->prepare("SELECT task_description, due_date FROM tasks WHERE list_id IN (SELECT id FROM todo_lists WHERE user_id = ?)");
$stmt->execute([$user_id]);
$tasks_with_dates = $stmt->fetchAll(PDO::FETCH_ASSOC);

$next_week = date('Y-m-d', strtotime('+7 days'));
$stmt = $koneksi->prepare("SELECT * FROM tasks WHERE due_date BETWEEN ? AND ? AND is_completed = 0 AND list_id IN (SELECT id FROM todo_lists WHERE user_id = ?)");
$stmt->execute([$today, $next_week, $user_id]);
$tasks_due_week = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            grid-gap: 5px;
            margin-top: 20px;
            text-align: center;
        }

        .day {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            cursor: pointer;
        }

        .day.task-day {
            background-color: #ffeb3b;
        }

        .day.today {
            background-color: #4caf50;
            color: white;
        }

        .calendar-controls {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .calendar-controls button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .calendar-controls button:hover {
            background-color: #0056b3;
        }
    </style>
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

        <form class="form-inline my-2 my-lg-0" action="search_tasks.php" method="GET">
            <input class="form-control mr-sm-2" type="search" name="search_query" placeholder="Search tasks" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>


<div class="container mt-5">
    <h1 class="text-center">My To-Do Lists</h1>
    <a href="activity_report.php" class="btn btn-info mb-3">View Task Statistics</a>
    <a href="create_list.php" class="btn btn-primary mb-3">Create New List</a>
    <ul class="list-group">
        <?php foreach ($todo_lists as $list): ?>
            <li class="list-group-item">
                <a href="view_list.php?list_id=<?= $list['id'] ?>"><?= htmlspecialchars($list['title']) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="container mt-5">
        <h2 class="text-center">Tasks Due Today</h2>
        <ul class="list-group">
            <?php if (count($tasks_due_today) > 0): ?>
                <?php foreach ($tasks_due_today as $task): ?>
                    <li class="list-group-item list-group-item-danger">
                        <?= htmlspecialchars($task['task_description']) ?> - Due today!
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item">No tasks due today.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<div class="container mt-5">
        <div class="calendar-header">
            <button id="prevMonthBtn">&lt; Prev Month</button>
            <h2 id="calendarMonthYear"></h2>
            <button id="nextMonthBtn">Next Month &gt;</button>
        </div>
        <div class="calendar" id="calendar"></div>
    </div>

<div class="modal fade" id="taskDetailModal" tabindex="-1" role="dialog" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskDetailModalLabel">Task Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="taskDetailsContent">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    const tasksWithDates = <?= json_encode($tasks_with_dates); ?>;
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();

    function generateCalendar(month, year) {
        const calendarEl = document.getElementById('calendar');
        const monthYearEl = document.getElementById('calendarMonthYear');
        calendarEl.innerHTML = '';
        
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        monthYearEl.textContent = `${monthNames[month]} ${year}`;

        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const firstDay = new Date(year, month, 1).getDay();

        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            emptyCell.classList.add('day');
            calendarEl.appendChild(emptyCell);
        }

        for (let i = 1; i <= daysInMonth; i++) {
            const dayEl = document.createElement('div');
            const dayDate = new Date(year, month, i).toISOString().split('T')[0];

            dayEl.classList.add('day');
            dayEl.textContent = i;

            const taskForDay = tasksWithDates.filter(task => task.due_date === dayDate);

            if (taskForDay.length > 0) {
                dayEl.classList.add('task-day');
                dayEl.addEventListener('click', () => showTaskDetails(taskForDay));
            }

            if (dayDate === new Date().toISOString().split('T')[0]) {
                dayEl.classList.add('today');
            }

            calendarEl.appendChild(dayEl);
        }
    }

    document.getElementById('prevMonthBtn').addEventListener('click', () => {
        if (currentMonth === 0) {
            currentMonth = 11;
            currentYear--;
        } else {
            currentMonth--;
        }
        generateCalendar(currentMonth, currentYear);
    });

    document.getElementById('nextMonthBtn').addEventListener('click', () => {
        if (currentMonth === 11) {
            currentMonth = 0;
            currentYear++;
        } else {
            currentMonth++;
        }
        generateCalendar(currentMonth, currentYear);
    });

    function showTaskDetails(tasks) {
        const taskDetailsContent = document.getElementById('taskDetailsContent');
        taskDetailsContent.innerHTML = ''; 

        tasks.forEach(task => {
            const taskDetail = document.createElement('p');
            taskDetail.textContent = `Task: ${task.task_description}, Due Date: ${task.due_date}`;
            taskDetailsContent.appendChild(taskDetail);
        });

        $('#taskDetailModal').modal('show');
    }

    generateCalendar(currentMonth, currentYear);
</script>

<script>
    $(document).ready(function () {
        $(".hamburger").click(function () {
            $(".navbar-nav").toggleClass("active");
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>

