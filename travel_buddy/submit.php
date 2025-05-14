<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $destination = $_POST['destination'];
    $travel_date = $_POST['travel_date'];
    $bio = $_POST['bio'];

    $stmt = $conn->prepare("INSERT INTO buddies (name, email, destination, travel_date, bio) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $destination, $travel_date, $bio);

    if ($stmt->execute()) {
        echo "Travel buddy registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
