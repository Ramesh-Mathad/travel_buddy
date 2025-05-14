<?php
session_start();
include 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

function showMessage($message, $success = true) {
    $quote = '"Travel leaves you speechless, then turns you into a storyteller."';
    $quote = "thank you for your review!";
    $color = $success ? "#28a745" : "#dc3545";
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Submission</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(120deg, #e0eafc, #cfdef3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
        }
        .msg-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 6px 32px rgba(0,0,0,0.12);
            padding: 40px 32px 32px 32px;
            max-width: 420px;
            width: 100%;
            text-align: center;
        }
        .result-msg {
            color: $color;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 18px;
        }
        .quote {
            font-style: italic;
            color: #555;
            margin-top: 18px;
            font-size: 1.1rem;
        }
        .btn-home {
            margin-top: 24px;
            background: #007bff;
            color: #fff;
            padding: 10px 28px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
            display: inline-block;
        }
        .btn-home:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="msg-container">
        <div class="result-msg">$message</div>
        <div class="quote">$quote</div>
        <a href="reviews.html" class="btn-home">Back to Reviews</a>
    </div>
</body>
</html>
HTML;
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $reviewed_user_id = $_POST['reviewed_user_id'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, reviewed_user_id, rating, comments) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        showMessage("Prepare failed: " . $conn->error, false);
    }
    $stmt->bind_param("iiis", $user_id, $reviewed_user_id, $rating, $comments);

    if ($stmt->execute()) {
        showMessage("Review submitted successfully!");
    } else {
        showMessage("Error: " . $stmt->error, false);
    }
    $stmt->close();
} else {
    showMessage("Invalid request or not logged in.", false);
}
?>