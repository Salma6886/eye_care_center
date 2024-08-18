<?php
session_start();
include 'db.php';

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'doctor') {
    header("Location: login.php");
    exit();
}

// Fetch patient history
$patient_id = $_GET['patient_id'] ?? null;

if (!$patient_id) {
    echo "Invalid patient ID.";
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM patient_history WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient History</title>
    <style>
        /* Your existing styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
        }
        .history-table th, .history-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .history-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Patient History</h2>
    <?php if (count($history) > 0): ?>
        <table class="history-table">
            <tr>
                <th>Date</th>
                <th>Description</th>
            </tr>
            <?php foreach ($history as $entry): ?>
                <tr>
                    <td><?php echo htmlspecialchars($entry['date']); ?></td>
                    <td><?php echo htmlspecialchars($entry['description']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No history found for this patient.</p>
    <?php endif; ?>
    <a href="doctor_dashboard.php">Back to Dashboard</a>
</body>
</html>
