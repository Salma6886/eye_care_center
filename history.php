<?php
include 'db.php';
session_start();

if ($_SESSION['role'] != 'Doctor') {
    die("Access denied");
}

$patient_id = $_GET['patient_id'];
$stmt = $pdo->prepare("SELECT * FROM patient_history WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$history = $stmt->fetchAll();

foreach ($history as $entry) {
    echo "<p>Date: {$entry['visit_date']}, Medication: {$entry['medication']}, Lab Tests: {$entry['lab_tests']}</p>";
}

echo "<a href='generate_report.php?patient_id={$patient_id}'>Generate Report</a>";
?>
