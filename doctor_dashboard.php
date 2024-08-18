<?php
session_start();
include 'db.php';

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'doctor') {
    header("Location: login.php");
    exit();
}

// Fetch the list of patients and their appointments
$stmt = $pdo->prepare("SELECT a.id as appointment_id, u.first_name, u.last_name, u.id as patient_id, a.appointment_date 
                       FROM appointments a
                       JOIN users u ON a.patient_id = u.id
                       WHERE a.doctor_id = ? AND a.status = 'Pending'");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['review'])) {
    $review = $_POST['review']; // The review content
    $username = $_SESSION['username']; // Assuming you store the username in the session

    // Validate and sanitize input
    $review = htmlspecialchars($review, ENT_QUOTES, 'UTF-8');

    // Insert the review into the database
    $stmt = $pdo->prepare("INSERT INTO reviews (username, review, created_at) VALUES (?, ?, NOW())");
    if ($stmt->execute([$username, $review])) {
        $message = 'Your review is submitted successfully!';
    } else {
        $message = 'Error submitting review.';
    }
}

$message = isset($message) ? htmlspecialchars($message) : 'Welcome to the Doctor Dashboard!';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <style>
        /* Your existing styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        .welcome {
            text-align: center;
            font-size: 20px;
        }
        .appointment-list {
            margin: 20px auto;
            max-width: 800px;
            border-collapse: collapse;
            width: 100%;
        }
        .appointment-list th, .appointment-list td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .appointment-list th {
            background-color: #f2f2f2;
        }
        .action-buttons button {
            padding: 5px 10px;
            margin-right: 5px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .action-buttons button:hover {
            background-color: #45a049;
        }
        .logout-button {
            display: block;
            width: 100%;
            max-width: 200px;
            margin: 20px auto;
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        .logout-button:hover {
            background-color: #0056b3;
        }
        .review-form {
            margin: 20px auto;
            max-width: 800px;
        }
        .review-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .review-form input[type="submit"] {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .review-form input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Doctor Dashboard</h2>
    <p class="welcome"> <?php echo $message; ?></p>
    <p>Welcome, Doctor! Here you can manage your appointments, view patient details, and generate reports.</p>

    <!-- List of appointments -->
    <h3>Upcoming Appointments</h3>
    <?php if (count($appointments) > 0): ?>
        <table class="appointment-list">
            <tr>
                <th>Patient Name</th>
                <th>Appointment Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                    <td class="action-buttons">
                        <!-- View patient history -->
                        <a href="view_patient_history.php?patient_id=<?php echo $appointment['patient_id']; ?>"><button>View History</button></a>
                        <!-- Input patient data -->
                        <a href="input_patient_data.php?appointment_id=<?php echo $appointment['appointment_id']; ?>"><button>Input Data</button></a>
                        <!-- Generate report -->
                        <a href="generate_report.php?patient_id=<?php echo $appointment['patient_id']; ?>"><button>Generate Report</button></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No upcoming appointments.</p>
    <?php endif; ?>

    <!-- Review Submission Form -->
    <h3>Submit a Review</h3>
    <form method="POST" action="" class="review-form">
        <label for="review">Review:</label>
        <textarea name="review" id="review" rows="5" required></textarea>
        <br>
        <input type="submit" value="Submit Review">
    </form>

    <!-- Logout button -->
    <a href="logout.php" class="logout-button">Logout</a>
</body>
</html>
