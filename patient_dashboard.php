<?php
session_start();
include 'db.php';

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'patient') {
    header("Location: login.php");
    exit();
}

$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Welcome to the Patient Dashboard!';

// Fetch Eye Specialists from the database
$stmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE role = 'doctor'");
$stmt->execute();
$specialists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle appointment request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['doctor_id'], $_POST['appointment_date'])) {
    $patient_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];

    $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, status) VALUES (?, ?, ?, 'Pending')");
    if ($stmt->execute([$patient_id, $doctor_id, $appointment_date])) {
        $message = "Appointment request sent successfully!";
        header("Location: patient_dashboard.php?message=" . urlencode($message));
        exit();
    } else {
        $message = "Failed to send appointment request.";
    }
}

// Fetch completed appointments for review
$stmt = $pdo->prepare("SELECT a.id as appointment_id, a.appointment_date 
                       FROM appointments a
                       WHERE a.patient_id = ? AND a.status = 'Completed'");
$stmt->execute([$_SESSION['user_id']]);
$completed_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['review']) && isset($_POST['appointment_id'])) {
    $review = $_POST['review'];
    $appointment_id = $_POST['appointment_id'];
    $username = $_SESSION['username']; // Assuming you store the username in the session

    // Validate and sanitize input
    $review = htmlspecialchars($review, ENT_QUOTES, 'UTF-8');

    // Insert the review into the database
    $stmt = $pdo->prepare("INSERT INTO reviews (username, review, created_at) VALUES (?, ?, NOW())");
    if ($stmt->execute([$username, $review])) {
        $message = 'Review submitted successfully!';
        header("Location: patient_dashboard.php?message=" . urlencode($message));
        exit();
    } else {
        $message = 'Error submitting review.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            text-align: center;
            font-size: 18px;
        }
        form {
            max-width: 600px;
            margin: 20px auto;
        }
        select, input[type="date"], textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
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
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Patient Dashboard</h2>
        <p><?php echo $message; ?></p>
        <p>Welcome, Patient! Here you can view your health records, appointments, and more.</p>
        
        <h3>Request an Appointment</h3>
        <form method="POST" action="">
            <select name="doctor_id" required>
                <option value="">Select Eye Specialist</option>
                <?php foreach ($specialists as $specialist): ?>
                    <option value="<?php echo $specialist['id']; ?>">
                        Dr. <?php echo $specialist['first_name'] . ' ' . $specialist['last_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="appointment_date" required>
            <button type="submit">Request Appointment</button>
        </form>
        
        <h3>Submit a Review</h3>
        <?php if (count($completed_appointments) > 0): ?>
            <?php foreach ($completed_appointments as $appointment): ?>
                <form method="POST" action="" class="review-form">
                    <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment['appointment_id']); ?>">
                    <label for="review">Review for appointment on <?php echo htmlspecialchars($appointment['appointment_date']); ?>:</label>
                    <textarea name="review" id="review" rows="5" required></textarea>
                    <br>
                    <button type="submit">Submit Review</button>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No completed appointments available for review.</p>
        <?php endif; ?>

        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
