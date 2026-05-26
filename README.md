# Travel Buddy 🌍✈️

## Overview

Travel Buddy is a web-based travel companion platform that helps users connect with fellow travelers, create trips, search for travel buddies, exchange messages, and share reviews. The application provides a simple and user-friendly interface for managing travel plans and interacting with other users.

This project is developed using PHP, HTML, CSS, JavaScript, and MySQL.

Repository: [Travel Buddy GitHub Repository](https://github.com/Ramesh-Mathad/travel_buddy/tree/main/travel_buddy?utm_source=chatgpt.com)

---

## Features 🚀

* User Registration and Login System
* Admin Login and Dashboard
* Create and Manage Trips
* Search Trips and Travel Buddies
* Send and Receive Message Requests
* Chat and Messaging System
* User Profile Management
* Upload Profile Images
* Submit and View Reviews
* Travel Suggestions
* Responsive Frontend Design
* MySQL Database Integration

---

## Tech Stack 🛠️

### Frontend

* HTML5
* CSS3
* JavaScript

### Backend

* PHP

### Database

* MySQL

### Tools

* VS Code Workspace
* XAMPP / WAMP Server

---

## Project Structure 📂

```bash
travel_buddy/
│
├── database/
├── uploads/
├── index.html
├── index.php
├── login.html
├── login.php
├── register.html
├── register.php
├── profile.php
├── edit_profile.php
├── create_trip.php
├── add_trip.php
├── search_trips.php
├── search_results.php
├── buddies.php
├── send_message.php
├── view_messages.php
├── reviews.html
├── submit_review.php
├── admin_login.php
├── admin_dshboard.php
├── config.php
├── db_connection.php
├── styles.css
└── uploads/
```

---

## Modules 📌

### 1. Authentication Module

Handles:

* User Registration
* User Login
* Session Management
* Logout Functionality

Files:

* `register.php`
* `login.php`
* `logout.php`

---

### 2. User Profile Module

Allows users to:

* View Profile
* Edit Personal Information
* Upload Profile Picture

Files:

* `profile.php`
* `edit_profile.php`
* `upload_profile.php`

---

### 3. Trip Management Module

Users can:

* Create Trips
* Add Trip Details
* Search Available Trips
* Find Travel Buddies

Files:

* `create_trip.php`
* `add_trip.php`
* `search_trips.php`
* `find_users_on_trip.php`

---

### 4. Messaging Module

Features:

* Send Message Requests
* Accept/Reject Requests
* View Messages
* Real-time User Communication

Files:

* `send_message.php`
* `send_message_request.php`
* `fetch_message_requests.php`
* `view_messages.php`

---

### 5. Review System

Users can:

* Submit Reviews
* View Other User Reviews

Files:

* `submit_review.php`
* `fetch_reviews.php`

---

### 6. Admin Module

Admin functionalities include:

* Secure Admin Login
* Dashboard Access
* User and Trip Monitoring

Files:

* `admin_login.php`
* `admin_dshboard.php`

---

## Installation Guide ⚙️

### Prerequisites

Make sure you have the following installed:

* XAMPP / WAMP
* PHP 7+
* MySQL
* Web Browser

---

### Step 1: Clone the Repository

```bash
git clone https://github.com/Ramesh-Mathad/travel_buddy.git
```

---

### Step 2: Move Project to Server Directory

For XAMPP:

```bash
C:/xampp/htdocs/
```

---

### Step 3: Start Apache and MySQL

Open XAMPP Control Panel and start:

* Apache
* MySQL

---

### Step 4: Create Database

1. Open phpMyAdmin
2. Create a new database:

```sql
travel_buddy
```

3. Import the SQL file from the `database/` folder.

---

### Step 5: Configure Database Connection

Update database credentials inside:

```php
config.php
```

or

```php
db_connection.php
```

Example:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "travel_buddy";
```

---

### Step 6: Run the Project

Open your browser and visit:

```bash
http://localhost/travel_buddy/
```

---

## Screenshots 📸

You can add screenshots using the available images in the project:

* `travelbuddy_homepage.JPG`
* `travelbuddy-login.jpg`
* `travelbuddy_register.jpg`
* `travelbuddy image.jpg`
* `frontpage.jpg`

Example:

```md
![Homepage](travelbuddy_homepage.JPG)
```

---

## Future Enhancements 🔮

* Google Maps Integration
* AI-based Trip Suggestions
* Real-time Chat System
* Email Notifications
* Mobile Responsive Improvements
* Travel Expense Sharing
* OTP Authentication
* Social Media Login

---

## Learning Outcomes 📚

Through this project, developers can learn:

* Full Stack Web Development
* PHP and MySQL Integration
* Authentication Systems
* CRUD Operations
* Session Handling
* File Upload Management
* Database Design

---

## Contributing 🤝

Contributions are welcome.

Steps to contribute:

1. Fork the repository
2. Create a new branch
3. Commit your changes
4. Push to your branch
5. Open a Pull Request

---

## License 📄

This project is developed for educational and learning purposes.

---

## Author 👨‍💻

Developed by Ramesh Mathad.

GitHub: [Ramesh-Mathad GitHub Profile](https://github.com/Ramesh-Mathad?utm_source=chatgpt.com)

---

## Repository Information 🔗

* Repository Name: `travel_buddy`
* Language Used: PHP, HTML, CSS, JavaScript
* Database: MySQL
* Type: Web Application
* Category: Travel and Social Networking Platform

---

## File Highlights 📁

| File Name            | Description             |
| -------------------- | ----------------------- |
| `index.php`          | Main homepage           |
| `login.php`          | User authentication     |
| `register.php`       | User registration       |
| `profile.php`        | User profile management |
| `create_trip.php`    | Trip creation page      |
| `search_trips.php`   | Search available trips  |
| `send_message.php`   | Messaging functionality |
| `submit_review.php`  | Review submission       |
| `admin_dshboard.php` | Admin dashboard         |
| `config.php`         | Database configuration  |

---

## Conclusion ✅

Travel Buddy is a complete travel networking platform that enables users to connect with fellow travelers, organize trips, and interact through reviews and messaging. The project demonstrates practical implementation of full-stack web development concepts using PHP and MySQL.

---

## GitHub Repository

[Travel Buddy Repository](https://github.com/Ramesh-Mathad/travel_buddy/tree/main/travel_buddy?utm_source=chatgpt.com)

Source files and repository structure referenced from the GitHub repository. ([github.com](https://github.com/Ramesh-Mathad/travel_buddy/tree/main/travel_buddy))
