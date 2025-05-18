<?php
include 'config.php';

// Get search parameters from GET
$destination = trim($_GET['destination'] ?? '');
$start_date = trim($_GET['start_date'] ?? '');
$end_date = trim($_GET['end_date'] ?? '');
$interests = trim($_GET['interests'] ?? '');

// Build query
$sql = "SELECT users.username, trips.destination, trips.start_date, trips.end_date, trips.interests
        FROM trips
        JOIN users ON trips.user_id = users.user_id
        WHERE trips.destination LIKE ?";
$params = ["%$destination%"];
$types = "s";

if ($start_date) {
    $sql .= " AND trips.start_date >= ?";
    $params[] = $start_date;
    $types .= "s";
}
if ($end_date) {
    $sql .= " AND trips.end_date <= ?";
    $params[] = $end_date;
    $types .= "s";
}
// if ($interests) {
//     $sql .= " AND trips.interests LIKE ?";
//     $params[] = "%$interests%";
//     $types .= "s";
// }

$sql .= " ORDER BY trips.start_date ASC";

// Prepare and execute
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Build response
$trips = [];
while ($row = $result->fetch_assoc()) {
    $trips[] = [
        'username'    => $row['username'],
        'destination' => $row['destination'],
        'start_date'  => $row['start_date'],
        'end_date'    => $row['end_date'],
        'interests'   => $row['interests']
    ];
}

// Output JSON
header('Content-Type: application/json');
echo json_encode($trips);

$stmt->close();
$conn->close();