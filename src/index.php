<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Task
    if (isset($_POST['task-name'])) {
        addTask(trim($_POST['task-name']));
    }

    // Mark Complete / Incomplete
    if (isset($_POST['task-id']) && isset($_POST['is_completed'])) {
        markTaskAsCompleted($_POST['task-id'], true);
    } elseif (isset($_POST['task-id'])) {
        markTaskAsCompleted($_POST['task-id'], false);
    }

    // Delete Task
    if (isset($_POST['delete-task-id'])) {
        deleteTask($_POST['delete-task-id']);
    }

    // Email Subscription
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        subscribeEmail($email);
    }

    // Prevent form resubmission
    header("Location: index.php");
    exit();
}

$tasks = getAllTasks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Scheduler</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f7f9fc;
      padding: 40px;
      color: #333;
    }

    h1 {
      text-align: center;
      color: #444;
    }

    .container {
      max-width: 700px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    form {
      margin-bottom: 30px;
    }

    input[type="text"], input[type="email"] {
      padding: 10px;
      width: 70%;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-right: 10px;
      font-size: 16px;
    }

    button {
      padding: 10px 16px;
      background: #1e88e5;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }

    button:hover {
      background: #1565c0;
    }

    .task-list {
      list-style: none;
      padding-left: 0;
    }

    .task-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 15px;
      margin-bottom: 8px;
      border: 1px solid #ddd;
      border-radius: 6px;
      background: #fdfdfd;
    }

    .task-item.completed {
      background: #e0f7fa;
      text-decoration: line-through;
      color: #888;
    }

    .delete-task {
      background: #e53935;
      color: #fff;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
    }

    .delete-task:hover {
      background: #c62828;
    }

    .checkbox {
      margin-right: 15px;
    }
  </style>
</head>
<body>

  <div class="container">
    <h1>🗓️ Task Scheduler</h1>

    <!-- Add Task -->
    <form method="POST" action="index.php">
      <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
      <button type="submit" id="add-task">Add Task</button>
    </form>

    <!-- Task List -->
    <ul class="task-list">
      <?php foreach ($tasks as $task): ?>
        <li class="task-item <?= $task['completed'] ? 'completed' : '' ?>">
          <form method="POST" action="index.php">
            <input type="hidden" name="task-id" value="<?= $task['id'] ?>">
            <input type="checkbox" class="task-status" name="is_completed" value="1" <?= $task['completed'] ? 'checked' : '' ?> onchange="this.form.submit()">
          </form>
          <span><?= htmlspecialchars($task['name']) ?></span>
          <form method="POST" action="index.php" style="margin-left:auto;">
            <input type="hidden" name="delete-task-id" value="<?= $task['id'] ?>">
            <button type="submit" class="delete-task">Delete</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>

    <hr>

    <!-- Email Subscription -->
    <h2>📩 Subscribe to Task Reminders</h2>
    <form method="POST" action="index.php">
      <input type="email" name="email" required>
      <button id="submit-email">Submit</button>

    </form>
	<p style="color:gray; font-size:12px;">Submitted by Priya</p>


  </div>

</body>
</html>


<!-- Final version by Priya -->
