<?php
session_start();
include 'db.php';

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'doctor') {
    header("Location: login.php");
    exit();
}

$appointment_id = $_GET['appointment_id'] ?? null;

if (!$appointment_id) {
    echo "Invalid appointment ID.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Medication data
    if (isset($_POST['medication_name'])) {
        $medication_name = htmlspecialchars($_POST['medication_name'], ENT_QUOTES, 'UTF-8');
        $dosage = htmlspecialchars($_POST['dosage'], ENT_QUOTES, 'UTF-8');
        $frequency = htmlspecialchars($_POST['frequency'], ENT_QUOTES, 'UTF-8');
        $start_date = htmlspecialchars($_POST['start_date'], ENT_QUOTES, 'UTF-8');
        $end_date = htmlspecialchars($_POST['end_date'], ENT_QUOTES, 'UTF-8');
        $notes = htmlspecialchars($_POST['medication_notes'], ENT_QUOTES, 'UTF-8');

        $stmt = $pdo->prepare("INSERT INTO medications (appointment_id, medication_name, dosage, frequency, start_date, end_date, notes) 
                                VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE 
                                medication_name = VALUES(medication_name), dosage = VALUES(dosage), frequency = VALUES(frequency), 
                                start_date = VALUES(start_date), end_date = VALUES(end_date), notes = VALUES(notes)");
        if ($stmt->execute([$appointment_id, $medication_name, $dosage, $frequency, $start_date, $end_date, $notes])) {
            $message = 'Medication data has been updated successfully!';
        } else {
            $message = 'Error updating medication data.';
        }
    }

    // Lab tests data
    if (isset($_POST['test_name'])) {
        $test_name = htmlspecialchars($_POST['test_name'], ENT_QUOTES, 'UTF-8');
        $test_date = htmlspecialchars($_POST['test_date'], ENT_QUOTES, 'UTF-8');
        $result = htmlspecialchars($_POST['result'], ENT_QUOTES, 'UTF-8');
        $test_notes = htmlspecialchars($_POST['test_notes'], ENT_QUOTES, 'UTF-8');

        $stmt = $pdo->prepare("INSERT INTO lab_tests (appointment_id, test_name, test_date, result, notes) 
                                VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE 
                                test_name = VALUES(test_name), test_date = VALUES(test_date), result = VALUES(result), notes = VALUES(notes)");
        if ($stmt->execute([$appointment_id, $test_name, $test_date, $result, $test_notes])) {
            $message = 'Lab tests data has been updated successfully!';
        } else {
            $message = 'Error updating lab tests data.';
        }
    }
}

// Fetch existing medication and lab tests data
$medication_stmt = $pdo->prepare("SELECT * FROM medications WHERE appointment_id = ?");
$medication_stmt->execute([$appointment_id]);
$medications = $medication_stmt->fetchAll(PDO::FETCH_ASSOC);

$lab_tests_stmt = $pdo->prepare("SELECT * FROM lab_tests WHERE appointment_id = ?");
$lab_tests_stmt->execute([$appointment_id]);
$lab_tests = $lab_tests_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Input Patient Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .input-form {
            margin: 20px auto;
            max-width: 800px;
        }
        .input-form input, .input-form textarea, .input-form select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .input-form input[type="submit"] {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .input-form input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Input Patient Data</h2>
    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <!-- Medication Form -->
    <h3>Medication</h3>
    <form method="POST" action="" class="input-form">
        <label for="medication_name">Medication Name:</label>
        <input type="text" name="medication_name" id="medication_name" required>
        
        <label for="dosage">Dosage:</label>
        <input type="text" name="dosage" id="dosage">
        
        <label for="frequency">Frequency:</label>
        <input type="text" name="frequency" id="frequency">
        
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date">
        
        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date">
        
        <label for="medication_notes">Notes:</label>
        <textarea name="medication_notes" id="medication_notes" rows="5"></textarea>
        
        <input type="submit" value="Submit Medication">
    </form>

    <!-- Lab Tests Form -->
    <h3>Lab Tests</h3>
    <form method="POST" action="" class="input-form">
        <label for="test_name">Test Name:</label>
        <input type="text" name="test_name" id="test_name" required>
        
        <label for="test_date">Test Date:</label>
        <input type="date" name="test_date" id="test_date">
        
        <label for="result">Result:</label>
        <textarea name="result" id="result" rows="5"></textarea>
        
        <label for="test_notes">Notes:</label>
        <textarea name="test_notes" id="test_notes" rows="5"></textarea>
        
        <input type="submit" value="Submit Lab Test">
    </form>

    <a href="doctor_dashboard.php">Back to Dashboard</a>
</body>
</html>
