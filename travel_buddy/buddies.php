<?php
include 'db.php';

$sql = "SELECT name, destination, travel_date, bio FROM buddies ORDER BY travel_date ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Available Travel Buddies:</h2>";
    while ($row = $result->fetch_assoc()) {
        echo "<div style='margin-bottom:20px;'>";
        echo "<strong>Name:</strong> " . htmlspecialchars($row['name']) . "<br>";
        echo "<strong>Destination:</strong> " . htmlspecialchars($row['destination']) . "<br>";
        echo "<strong>Date:</strong> " . htmlspecialchars($row['travel_date']) . "<br>";
        echo "<strong>About:</strong> " . htmlspecialchars($row['bio']) . "<br>";
        echo "</div>";
    }
} else {
    echo "No travel buddies found.";
}

$conn->close();
?>
