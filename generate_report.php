<?php
session_start();
include 'db.php';

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) != 'doctor') {
    header("Location: login.php");
    exit();
}

// Fetch patient details for the report
$patient_id = $_GET['patient_id'] ?? null;

if (!$patient_id) {
    echo "Invalid patient ID.";
    exit();
}

// Fetch patient data
$stmt = $pdo->prepare("SELECT * FROM patient_data WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$patient_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate report (example: displaying data, could also be a PDF generation)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Report</title>
    <style>
        /* Your existing styles */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Generate Report</h2>
    <?php if (count($patient_data) > 0): ?>
        <table class="report-table">
            <tr>
                <th>Data</th>
            </tr>
            <?php foreach ($patient_data as $data): ?>
                <tr>
                    <td><?php echo htmlspecialchars($data['data']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No data available for this patient.</p>
    <?php endif; ?>
    <a href="doctor_dashboard.php">Back to Dashboard</a>
</body>
</html>
