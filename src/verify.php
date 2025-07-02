<?php
require_once 'functions.php';

if (isset($_GET['email']) && isset($_GET['code'])) {
    $email = urldecode($_GET['email']);
    $code = $_GET['code'];

    verifySubscription($email, $code);
    exit(); // 🔥 IMPORTANT: Stop script after success
} else {
    echo "❌ Invalid verification link.";
}
?>
