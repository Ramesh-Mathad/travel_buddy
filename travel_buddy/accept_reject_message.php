<?php
include 'config.php';
session_start();

if (isset($_POST['message_id'], $_POST['status'])) {
    $message_id = intval($_POST['message_id']);
    $status = $_POST['status'];
    $allowed = ['Accepted', 'Rejected'];
    if (!in_array($status, $allowed)) {
        echo json_encode(['success' => false]);
        exit;
    }
    $stmt = $conn->prepare("UPDATE message_requests SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $message_id);
    $stmt->execute();
    echo json_encode(['success' => $stmt->affected_rows > 0]);
} else {
    echo json_encode(['success' => false]);
}