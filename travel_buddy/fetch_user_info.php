<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT username, email, profile_picture, join_date, bio FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Count total trips
$stmt2 = $conn->prepare("SELECT COUNT(*) as total FROM trips WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$res2 = $stmt2->get_result();
$user['total_trips'] = $res2->fetch_assoc()['total'];

header('Content-Type: application/json');
echo json_encode($user);