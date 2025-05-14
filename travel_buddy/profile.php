<?php
// Database connection and session start
session_start();
include 'config.php';

$conn = new mysqli('localhost', 'root', '', 'travel_buddy_finder');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: travel_buddy/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details (now including gender)
$user_query = "SELECT username, email, profile_picture, dob, phone, gender FROM users WHERE user_id = '$user_id'";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();

$username = $user['username'] ?? 'Unknown User';
$email = $user['email'] ?? 'Not available';
$profile_picture = $user['profile_picture'] ?? 'default-profile.jpg';
$dob = $user['dob'] ?? 'N/A';
$phone = $user['phone'] ?? 'N/A';
$gender = $user['gender'] ?? '';

// Count total trips
$trip_count = 0;
$count_query = $conn->query("SELECT COUNT(*) as total FROM trips WHERE user_id = '$user_id'");
if ($count_query && $row = $count_query->fetch_assoc()) {
    $trip_count = $row['total'];
}

// Update profile functionality (now including gender)
if (isset($_POST['update_profile'])) {
    $new_email = $_POST['email'];
    $new_dob = $_POST['dob'];
    $new_phone = $_POST['phone'];
    $new_gender = $_POST['gender'];

    $stmt = $conn->prepare("UPDATE users SET email = ?, dob = ?, phone = ?, gender = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $new_email, $new_dob, $new_phone, $new_gender, $user_id);
    $stmt->execute();
    $email = $new_email;
    $dob = $new_dob;
    $phone = $new_phone;
    $gender = $new_gender;

    // Profile picture update
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture_path = 'uploads/' . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture_path);

        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
        $stmt->bind_param("si", $profile_picture_path, $user_id);
        $stmt->execute();
        $profile_picture = $profile_picture_path;
    }
}

// Handle adding a trip
if (isset($_POST['add_trip'])) {
    $destination = $_POST['destination'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("INSERT INTO trips (user_id, destination, start_date, end_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $destination, $start_date, $end_date);
    $stmt->execute();
}

// Handle editing a trip
if (isset($_POST['edit_trip'])) {
    $trip_id = $_POST['trip_id'];
    $destination = $_POST['destination'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("UPDATE trips SET destination = ?, start_date = ?, end_date = ? WHERE trip_id = ?");
    $stmt->bind_param("sssi", $destination, $start_date, $end_date, $trip_id);
    $stmt->execute();
}

// Handle deleting a trip
if (isset($_POST['delete_trip'])) {
    $trip_id = $_POST['trip_id'];
    $stmt = $conn->prepare("DELETE FROM trips WHERE trip_id = ?");
    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
}

// Handle deleting a sent message
if (isset($_POST['delete_sent_message']) && isset($_POST['sent_message_id'])) {
    $sent_msg_id = intval($_POST['sent_message_id']);
    $stmt = $conn->prepare("DELETE FROM message_requests WHERE id = ? AND sender_id = ?");
    $stmt->bind_param("ii", $sent_msg_id, $user_id);
    $stmt->execute();
}

// Fetch trips
$trips_result = $conn->query("SELECT * FROM trips WHERE user_id = '$user_id'");

// Fetch all users for message sending
$users_result = $conn->query("SELECT * FROM users");
$users = $users_result->fetch_all(MYSQLI_ASSOC);

// Accept/Reject message logic
if (isset($_POST['accept_message']) && isset($_POST['message_id'])) {
    $msg_id = intval($_POST['message_id']);
    $stmt = $conn->prepare("UPDATE message_requests SET status = 'Accepted' WHERE id = ?");
    $stmt->bind_param("i", $msg_id);
    $stmt->execute();
}
if (isset($_POST['reject_message']) && isset($_POST['message_id'])) {
    $msg_id = intval($_POST['message_id']);
    $stmt = $conn->prepare("UPDATE message_requests SET status = 'Rejected' WHERE id = ?");
    $stmt->bind_param("i", $msg_id);
    $stmt->execute();
}

// Fetching message requests with sender's username and profile picture
$query = "
    SELECT mr.*, u.username AS sender_username, u.profile_picture AS sender_profile_picture 
    FROM message_requests mr
    JOIN users u ON u.user_id = mr.sender_id
    WHERE mr.receiver_id = ? 
    ORDER BY mr.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$message_requests = $result->fetch_all(MYSQLI_ASSOC);

// Query to fetch sent messages along with recipient's username, profile picture, and trip information
$query = "
    SELECT mr.*, u.username, u.profile_picture, t.destination 
    FROM message_requests mr
    JOIN users u ON mr.receiver_id = u.user_id
    LEFT JOIN trips t ON u.user_id = t.user_id
    WHERE mr.sender_id = ?
";
$message_box = '';
if (isset($_POST['send_message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    // Prevent user from sending message to himself
    if ($user_id == $receiver_id) {
        $message_box = '<div class="alert alert-danger">You cannot send a message to yourself.</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO message_requests (sender_id, receiver_id, message_content, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("iis", $user_id, $receiver_id, $message);
        $stmt->execute();
        $message_box = '<div class="alert alert-success">Message sent successfully!</div>';
    }
}

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sent_messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$conn->close();

// Trip suggestion logic
$months = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];
$month_places = [
    1 => ['Goa', 'Jaipur', 'Kerala'],
    2 => ['Rajasthan', 'Andaman', 'Coorg'],
    3 => ['Ooty', 'Varanasi', 'Rishikesh'],
    4 => ['Darjeeling', 'Shillong', 'Kodaikanal'],
    5 => ['Manali', 'Ladakh', 'Shimla'],
    6 => ['Munnar', 'Tawang', 'Spiti Valley'],
    7 => ['Lonavala', 'Mahabaleshwar', 'Coorg'],
    8 => ['Wayanad', 'Valley of Flowers', 'Panchgani'],
    9 => ['Ziro', 'Udaipur', 'Amritsar'],
    10 => ['Hampi', 'Jodhpur', 'Kolkata'],
    11 => ['Goa', 'Jaisalmer', 'Pondicherry'],
    12 => ['Rann of Kutch', 'Udaipur', 'Goa'],
];
$selected_month = isset($_GET['suggest_month']) ? intval($_GET['suggest_month']) : 0;
$show_places = $selected_month && isset($month_places[$selected_month]) ? $month_places[$selected_month] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(120deg, #1e3c72, #2a5298 100%);
            margin: 0;
            color: #333;
        }
        /* Header/Navbar */
        .main-header {
            background: #232946;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .main-header .nav {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            height: 60px;
        }
        .main-header .brand {
            margin-right: auto;
            color: #fff;
            font-weight: bold;
            font-size: 24px;
            letter-spacing: 1px;
            text-decoration: none;
        }
        .main-header .nav-link {
            color: #fff;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: 5px;
            margin-left: 10px;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
        }
        .main-header .nav-link:hover, .main-header .nav-link.active {
            background: #007bff;
            color: #fff;
        }
        .container {
            max-width: 1200px;
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            margin: 40px auto 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        /* User Info Card */
        .user-info-card {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 24px 28px;
            margin-bottom: 32px;
            gap: 28px;
        }
        .user-info-card .profile-pic {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.08);
        }
        .user-info-card .user-details h2 {
            margin: 0 0 8px 0;
            color: #007bff;
            font-size: 2rem;
        }
        .user-info-card .user-details p {
            margin: 4px 0;
            color: #444;
            font-size: 1rem;
        }
        .profile-form, .trip-form, .trip-list, .message-form, .message-requests, .trip-suggestion-box {
            background: #f0f4f8;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }
        .trip-suggestion-box {
            border-left: 6px solid #007bff;
            margin-bottom: 32px;
        }
        .trip-suggestion-box h3 {
            color: #007bff;
            margin-top: 0;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 0.5px;
        }
        .trip-suggestion-box ul {
            margin: 10px 0 0 18px;
        }
        .trip-suggestion-box .month-place-link {
            color: #007bff;
            font-weight: 600;
            text-decoration: underline;
        }
        .trip-suggestion-box .gemini-link {
            color: #28a745;
            font-weight: 600;
            text-decoration: underline;
        }
        .success-message {
            background-color: #28a745;
            color: #fff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert {
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-weight: 500;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #b7e0c3;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .profile-form input[type="email"],
        .profile-form input[type="file"],
        .trip-form input[type="text"],
        .trip-form input[type="date"],
        .trip-item input[type="text"],
        .trip-item input[type="date"],
        .message-form select,
        .message-form textarea {
            width: 100%;
            padding: 10px 12px;
            margin: 10px 0 18px 0;
            border: 1.5px solid #ced4da;
            border-radius: 6px;
            font-size: 16px;
            background: #f8f9fa;
            transition: border-color 0.2s;
        }
        .profile-form select[name="gender"] {
            margin-bottom: 10px;
            padding: 10px 12px;
            width: 100%;
            border-radius: 6px;
            border: 1.5px solid #ced4da;
            font-size: 16px;
            background: #f8f9fa;
            transition: border-color 0.2s;
        }
        .profile-form input[type="file"] {
            padding: 6px 0;
        }
        .profile-form input[type="email"]:focus,
        .trip-form input[type="text"]:focus,
        .trip-form input[type="date"]:focus,
        .trip-item input[type="text"]:focus,
        .trip-item input[type="date"]:focus,
        .profile-form select[name="gender"]:focus,
        .message-form select:focus,
        .message-form textarea:focus {
            border-color: #007bff;
            outline: none;
            background: #fff;
        }
        button, .message-form button {
            background: linear-gradient(45deg, #3498db, #2ecc71);
            color: #fff;
            padding: 10px 24px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            border: none;
            border-radius: 25px;
            margin-top: 8px;
        }
        button:hover, .message-form button:hover {
            background: linear-gradient(45deg, #007bff, #28a745);
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 2px 8px rgba(0,123,255,0.08);
        }
        button[name="delete_sent_message"] {
            background: linear-gradient(45deg, #dc3545, #a71d2a);
        }
        button[name="delete_sent_message"]:hover {
            background: linear-gradient(45deg, #a71d2a, #dc3545);
        }
        h3 {
            font-size: 1.5rem;
            color: #232946;
            margin-bottom: 15px;
            font-weight: 600;
            padding-bottom: 5px;
        }
        .trip-item, .message-item {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
            color: #333;
            font-size: 1rem;
            transition: box-shadow 0.2s, background 0.2s;
        }
        .trip-item:hover, .message-item:hover {
            background: #e9f2ff;
            box-shadow: 0 6px 16px rgba(0,123,255,0.07);
        }
        .trip-item form,
        .trip-item form input,
        .trip-item form button {
            display: inline-block;
            margin-right: 8px;
        }
        .icon {
            cursor: pointer;
            font-size: 24px;
            margin: 10px;
        }
        input, textarea {
            margin-bottom: 10px;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }
        textarea {
            resize: vertical;
        }
        /* Profile image in message requests */
        .message-item img, .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #007bff;
            object-fit: cover;
            margin-right: 10px;
            vertical-align: middle;
        }
        @media (max-width: 900px) {
            .container {
                max-width: 98vw;
                padding: 10px;
            }
            .main-header .nav {
                flex-direction: column;
                height: auto;
                padding: 10px 0;
            }
            .main-header .brand {
                margin-bottom: 8px;
            }
            .user-info-card {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px 10px;
                gap: 12px;
            }
            .user-info-card .profile-pic {
                width: 70px;
                height: 70px;
            }
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <header class="main-header">
        <nav class="nav">
            <a href="index.html" class="brand">Travel Buddy</a>
            <a href="index.html" class="nav-link">Home</a>
            <a href="reviews.html" class="nav-link">Reviews</a>
        </nav>
    </header>
    <div class="container">
        <!-- User Info Card -->
        <div class="user-info-card">
            <img class="profile-pic" id="profile-pic" src="<?= htmlspecialchars($profile_picture) ?>" alt="Profile Picture">
            <div class="user-details">
                <h2 id="profile-username"><?= htmlspecialchars($username) ?></h2>
                <p id="profile-email"><?= htmlspecialchars($email) ?></p>
                <p id="profile-dob"><strong>Date of Birth:</strong> <span><?= htmlspecialchars($dob) ?></span></p>
                <p id="profile-phone"><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
                <p id="profile-gender"><strong>Gender:</strong> <?= htmlspecialchars($gender) ?></p>
                <p id="profile-total-trips"><strong>Total Trips:</strong> <span><?= $trip_count ?></span></p>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="profile-form">
            <h3>Update Profile</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                <input type="date" name="dob" value="<?= htmlspecialchars($dob) ?>" required>
                <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>" placeholder="Phone Number" required>
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?= $gender == 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $gender == 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $gender == 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
                <input type="file" name="profile_picture" accept="image/*">
                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>

        <!-- Trip Suggestion Box -->
        <div class="trip-suggestion-box">
            <h3>Trip Suggestions</h3>
            <form method="GET" style="margin-bottom:12px;">
                <label for="suggest_month"><strong>Select Month:</strong></label>
                <select name="suggest_month" id="suggest_month" style="margin-left:10px;padding:6px 10px;border-radius:5px;">
                    <option value="">-- Select Month --</option>
                    <?php foreach ($months as $num => $name): ?>
                        <option value="<?= $num ?>"<?= $selected_month == $num ? ' selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" style="margin-left:10px;">Show Suggestions</button>
            </form>
            <?php if ($selected_month && $show_places): ?>
                <div style="margin-bottom:10px;">
                    <strong>Best Places to Visit in <?= $months[$selected_month] ?>:</strong>
                    <ul style="margin:8px 0 0 18px;">
                        <?php foreach ($show_places as $place): ?>
                            <li>
                                <a href="https://www.google.com/search?q=best+places+to+visit+in+<?= urlencode($place) ?>" target="_blank" class="month-place-link">
                                    <?= htmlspecialchars($place) ?>
                                </a>
                                <a href="https://gemini.google.com/app?query=best+places+to+visit+in+<?= urlencode($place) ?>" target="_blank" class="gemini-link" style="margin-left:10px;">
                                    (Gemini Tips)
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div style="margin-top:10px;">
                    <strong>Tips for Planning Your Trip:</strong>
                    <ul style="margin:8px 0 0 18px;">
                        <li>Check the weather and pack accordingly.</li>
                        <li>Book your accommodation in advance for popular destinations.</li>
                        <li>Look for local festivals or events during your travel dates.</li>
                        <li>Read reviews and travel blogs for hidden gems.</li>
                        <li>Keep digital and paper copies of important documents.</li>
                    </ul>
                </div>
            <?php elseif ($selected_month): ?>
                <div style="margin-top:10px;color:#dc3545;">No suggestions found for this month.</div>
            <?php endif; ?>
        </div>

        <!-- Add Trip Form -->
        <div class="trip-form">
            <h3>Add Trip</h3>
            <form method="POST">
                <input type="text" name="destination" placeholder="Destination" required>
                <input type="date" name="start_date" required>
                <input type="date" name="end_date" required>
                <button type="submit" name="add_trip">Add Trip</button>
            </form>
        </div>

        <!-- Trips -->
        <div class="trip-list">
            <h3>Your Trips</h3>
            <?php while ($trip = $trips_result->fetch_assoc()): ?>
                <div class="trip-item">
                    <strong><?= htmlspecialchars($trip['destination']) ?></strong>
                    <p>Start Date: <?= htmlspecialchars($trip['start_date']) ?></p>
                    <p>End Date: <?= htmlspecialchars($trip['end_date']) ?></p>
                    <a href="trip_suggestions.php?destination=<?= urlencode($trip['destination']) ?>"
                       style="display:inline-block;margin-bottom:8px;padding:7px 16px;background:#007bff;color:#fff;border-radius:5px;text-decoration:none;font-weight:500;">
                        Trip Suggestion
                    </a>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="trip_id" value="<?= $trip['trip_id'] ?>">
                        <input type="text" name="destination" value="<?= htmlspecialchars($trip['destination']) ?>" required>
                        <input type="date" name="start_date" value="<?= htmlspecialchars($trip['start_date']) ?>" required>
                        <input type="date" name="end_date" value="<?= htmlspecialchars($trip['end_date']) ?>" required>
                        <button type="submit" name="edit_trip">Edit Trip</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="trip_id" value="<?= $trip['trip_id'] ?>">
                        <button type="submit" name="delete_trip">Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Send Message Form -->
        <div class="message-form">
            <h3>Send Message</h3>
            <?= $message_box ?>
            <form method="POST">
                <label for="recipient" style="font-weight:600; color:#007bff; margin-bottom:6px; display:block;">Recipient:</label>
                <select name="receiver_id" id="recipient" required
    style="margin-bottom: 14px; padding: 12px; width: 100%; border-radius: 6px; border: 1.5px solid #007bff; font-size: 1.08rem; background: #f7faff; color: #232946; box-shadow: 0 2px 8px rgba(0,123,255,0.06); transition: border 0.2s;">
                    <option value="">Select a user</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= htmlspecialchars($user['user_id']) ?>">
                            <?= htmlspecialchars($user['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <textarea name="message" rows="4" placeholder="Type your message..." required></textarea>
                <button type="submit" name="send_message">Send Message</button>
            </form>
        </div>

        <!-- Sent Messages -->
        <div class="message-requests">
            <h3>Your Sent Messages</h3>
            <?php foreach ($sent_messages as $msg): ?>
                <div class="message-item">
                    <strong>To: </strong> <?= htmlspecialchars($msg['username']) ?>
                    <img src="<?= htmlspecialchars($msg['profile_picture']) ?: 'default-profile.jpg' ?>" alt="Receiver Profile" class="profile-img">
                    <p><strong>Message:</strong> <?= htmlspecialchars($msg['message_content']) ?></p>
                    <p><strong>Trip: </strong> <?= htmlspecialchars($msg['destination'] ?? 'No Trip') ?></p>
                    <p>Status: <?= htmlspecialchars($msg['status']) ?></p>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this sent message?');">
                        <input type="hidden" name="sent_message_id" value="<?= $msg['id'] ?>">
                        <button type="submit" name="delete_sent_message">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Received Messages -->
        <div class="message-requests">
            <h3>Your Received Messages</h3>
            <?php foreach ($message_requests as $request): ?>
                <?php if ($request['status'] === 'Rejected') continue; // Hide rejected messages ?>
                <div class="message-item">
                    <strong>From: </strong> <?= htmlspecialchars($request['sender_username']) ?>
                    <img src="<?= htmlspecialchars($request['sender_profile_picture']) ?: 'default-profile.jpg' ?>" alt="Sender Profile" class="profile-img">
                    <p><?= htmlspecialchars($request['message_content']) ?></p>
                    <?php if ($request['status'] === 'Pending'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="message_id" value="<?= $request['id'] ?>">
                            <button type="submit" name="accept_message">Accept</button>
                            <button type="submit" name="reject_message">Reject</button>
                        </form>
                    <?php elseif ($request['status'] === 'Accepted'): ?>
                        <div style="margin-top:10px;padding:10px;background:#e9f7ef;border-radius:8px;">
                            <strong>Sender Info:</strong><br>
                            Username: <?= htmlspecialchars($request['sender_username']) ?><br>
                            <?php
                            // Fetch sender details
                            $sender_id = $request['sender_id'];
                            $conn2 = new mysqli('localhost', 'root', '', 'travel_buddy_finder');
                            $sender_info = $conn2->query("SELECT email, phone, gender FROM users WHERE user_id = '$sender_id'")->fetch_assoc();
                            $conn2->close();
                            ?>
                            Email: <?= htmlspecialchars($sender_info['email'] ?? 'N/A') ?><br>
                            Phone: <?= htmlspecialchars($sender_info['phone'] ?? 'N/A') ?><br>
                            Gender: <?= htmlspecialchars($sender_info['gender'] ?? 'N/A') ?>
                        </div>
                        <p style="color:green;font-weight:600;">Status: Accepted</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Logout -->
        <button onclick="window.location.href='logout.php'">Logout</button>
    </div>
</body>
</html>