<?php

// Mailpit Setup
ini_set("SMTP", "localhost");
ini_set("smtp_port", "1025");

// 🔹 Get All Tasks
function getAllTasks() {
    $file = __DIR__ . '/tasks.txt';
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }
    $tasks = json_decode(file_get_contents($file), true);
    return is_array($tasks) ? $tasks : [];
}

// 🔹 Add Task
function addTask($task_name) {
    $file = __DIR__ . '/tasks.txt';
    $tasks = getAllTasks();

    foreach ($tasks as $task) {
        if (strtolower($task['name']) === strtolower($task_name)) {
            return false; // duplicate
        }
    }

    $new_task = [
        "id" => uniqid(),
        "name" => $task_name,
        "completed" => false
    ];

    $tasks[] = $new_task;
    file_put_contents($file, json_encode($tasks, JSON_PRETTY_PRINT));
    return true;
}

// 🔹 Mark Task Completed
function markTaskAsCompleted($task_id, $is_completed) {
    $tasks = getAllTasks();
    foreach ($tasks as &$task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = $is_completed;
            break;
        }
    }
    file_put_contents(__DIR__ . '/tasks.txt', json_encode($tasks, JSON_PRETTY_PRINT));
}

// 🔹 Delete Task
function deleteTask($task_id) {
    $tasks = getAllTasks();
    $tasks = array_filter($tasks, fn($task) => $task['id'] !== $task_id);
    file_put_contents(__DIR__ . '/tasks.txt', json_encode(array_values($tasks), JSON_PRETTY_PRINT));
}

// 🔹 Generate Code
function generateVerificationCode() {
    return rand(100000, 999999);
}

// 🔹 Subscribe Email
function subscribeEmail($email) {
    echo "<p>📩 subscribeEmail() called with: $email</p>"; // ✅ DEBUG LOG

    $code = generateVerificationCode();
    $timestamp = time();

    $pendingFile = __DIR__ . '/pending_subscriptions.txt';
    $pending = [];

    if (file_exists($pendingFile)) {
        $pending = json_decode(file_get_contents($pendingFile), true);
    }

    $pending[$email] = [
        'code' => $code,
        'timestamp' => $timestamp
    ];

    file_put_contents($pendingFile, json_encode($pending, JSON_PRETTY_PRINT));

    $verification_link = "http://localhost/task-scheduler-Priya/src/verify.php?email=" . urlencode($email) . "&code=" . $code;

    $subject = "Verify subscription to Task Planner";
    $body = "
        <p>Click the link below to verify your subscription to Task Planner:</p>
        <p><a id=\"verification-link\" href=\"$verification_link\">Verify Subscription</a></p>
    ";

    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-type: text/html\r\n";

    mail($email, $subject, $body, $headers);
}

// 🔹 Verify Subscription
function verifySubscription($email, $code) {
    $pendingList = json_decode(file_get_contents('pending_subscriptions.txt'), true) ?? [];

    if (!isset($pendingList[$email])) {
        echo "Verification failed. Code is incorrect or expired.";
        return;
    }

    if ($pendingList[$email]['code'] != $code) {
        echo "Verification failed. Code is incorrect or expired.";
        return;
    }

    // ✅ Read subscribers safely
    $subscribers = [];
    if (file_exists('subscribers.txt')) {
        $content = file_get_contents('subscribers.txt');
        if ($content !== false && !empty(trim($content))) {
            $subscribers = json_decode($content, true);
        }
    }

    // Make sure $subscribers is always an array
    if (!is_array($subscribers)) {
        $subscribers = [];
    }

    if (!in_array($email, $subscribers)) {
        $subscribers[] = $email;
        file_put_contents('subscribers.txt', json_encode($subscribers, JSON_PRETTY_PRINT));
    }

    unset($pendingList[$email]);
    file_put_contents('pending_subscriptions.txt', json_encode($pendingList, JSON_PRETTY_PRINT));

    echo "✅ Email added to subscribers list.<br>";
    echo "✅ Email removed from pending list.<br>";
    echo "✅ Subscription verified successfully!";
}


// 🔹 Unsubscribe Email
function unsubscribeEmail($email) {
    $subFile = __DIR__ . '/subscribers.txt';

    if (!file_exists($subFile)) return false;

    $subs = json_decode(file_get_contents($subFile), true);
    if (!is_array($subs)) $subs = [];

    $index = array_search($email, $subs);
    if ($index !== false) {
        unset($subs[$index]);
        $subs = array_values($subs); // re-index
        file_put_contents($subFile, json_encode($subs, JSON_PRETTY_PRINT));
        return true;
    }

    return false;
}

// 🔹 Send Reminders to All Subscribers
function sendTaskReminders() {
    $subscribers_file = __DIR__ . '/subscribers.txt';
    $tasks_file = __DIR__ . '/tasks.txt';

    if (!file_exists($subscribers_file) || !file_exists($tasks_file)) return;

    $subscribers = json_decode(file_get_contents($subscribers_file), true);
    $tasks = json_decode(file_get_contents($tasks_file), true);

    $pending_tasks = array_filter($tasks, fn($task) => !$task['completed']);

    foreach ($subscribers as $email) {
        sendTaskEmail($email, $pending_tasks);
    }
}

// 🔹 Send Email with Task List
function sendTaskEmail($email, $pending_tasks) {
    $subject = "Task Planner - Pending Tasks Reminder";
    $unsubscribe_link = "http://localhost/task-scheduler-Priya/src/unsubscribe.php?email=" . urlencode($email);

    $body = "<h2>Pending Tasks Reminder</h2><p>Here are the current pending tasks:</p><ul>";
    foreach ($pending_tasks as $task) {
        $body .= "<li>" . htmlspecialchars($task['name']) . "</li>";
    }
    $body .= "</ul>";
    $body .= "<p><a id='unsubscribe-link' href='$unsubscribe_link'>Unsubscribe from notifications</a></p>";

    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-type: text/html\r\n";

    mail($email, $subject, $body, $headers);
}
