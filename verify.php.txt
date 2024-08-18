<?php
include 'db.php';
session_start();

if ($_SESSION['role'] != 'Patient') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];

    $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, status) 
                          VALUES (?, ?, ?, 'Pending')");
    $stmt->execute([$patient_id, $doctor_id, $appointment_date]);

    echo "Appointment requested successfully!";
}

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'Doctor'");
$doctors = $stmt->fetchAll();

echo "<form method='POST' action='request_appointment.php'>
        <select name='doctor_id'>";
foreach ($doctors as $doctor) {
    echo "<option value='{$doctor['id']}'>{$doctor['first_name']} {$doctor['last_name']}</option>";
}
echo "  </select>
        <input type='date' name='appointment_date' required>
        <button type='submit'>Request Appointment</button>
      </form>";
?>
