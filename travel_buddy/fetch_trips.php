<?php
// fetch_trips.php

session_start(); // Add this to use session variables

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'travel_buddy_finder';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's id from session
$userId = $_SESSION['user_id'] ?? 0;
if (!$userId) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT * FROM trips WHERE user_id = ? AND start_date >= CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$trips = [];
while ($row = $result->fetch_assoc()) {
    $trips[] = $row;
}

header('Content-Type: application/json');
echo json_encode($trips);

$stmt->close();
$conn->close();
?>