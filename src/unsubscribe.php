<?php
require_once 'functions.php';

$email = $_GET['email'] ?? '';

if (!$email) {
    echo "<h2>No email provided.</h2>";
    exit;
}

if (unsubscribeEmail($email)) {
    echo "<h2>You have been unsubscribed from Task Planner reminders.</h2>";
} else {
    echo "<h2>Email not found in subscribers list.</h2>";
}
