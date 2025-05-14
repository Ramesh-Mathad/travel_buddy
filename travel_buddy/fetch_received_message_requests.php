<?php
session_start();
include 'config.php';
$user_id = $_SESSION['user_id'];

$sql = "SELECT mr.id, u.username AS sender, mr.message_content AS message, mr.status
        FROM message_requests mr
        JOIN users u ON mr.sender_id = u.user_id
        WHERE mr.receiver_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
header('Content-Type: application/json');
echo json_encode($messages);