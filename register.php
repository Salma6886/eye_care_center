<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all required fields are set
    if (isset($_POST['email'], $_POST['username'], $_POST['first_name'], $_POST['last_name'], $_POST['birthday'], $_POST['city'], $_POST['gender'], $_POST['age'], $_POST['role'])) {
        
        // Sanitize and assign POST variables
        $email = $_POST['email'];
        $username = $_POST['username'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $birthday = $_POST['birthday'];
        $city = $_POST['city'];
        $gender = $_POST['gender'];
        $age = $_POST['age'];
        $role = strtolower(trim($_POST['role']));
        
        // Default values for certificate fields
        $certificate_number = null;
        $certificate = null;

        // Handle certificate upload for doctor and patient roles
        if ($role === 'doctor' || $role === 'patient') {
            if (isset($_POST['certificate_number'], $_FILES['certificate']) && $_FILES['certificate']['error'] == UPLOAD_ERR_OK) {
                $certificate_number = $_POST['certificate_number'];
                $certificate = $_FILES['certificate']['name'];
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($certificate);

                // Move uploaded file to the target directory
                if (!move_uploaded_file($_FILES['certificate']['tmp_name'], $target_file)) {
                    echo "There was an error uploading the certificate file.";
                    exit();
                }
            } else {
                echo "Please upload a valid certificate file.";
                exit();
            }
        }

        // Prepare and execute the SQL statement
        $stmt = $pdo->prepare("INSERT INTO users (email, username, first_name, last_name, birthday, city, gender, age, role, certificate, certificate_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        try {
            $stmt->execute([$email, $username, $first_name, $last_name, $birthday, $city, $gender, $age, $role, $certificate, $certificate_number]);
            echo "Registration successful! <a href='login.php'>Click here to login</a>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 14px 20px;
            text-align: center;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }
        p {
            text-align: center;
        }
    </style>
    <script>
        function toggleCertificateField() {
            var role = document.querySelector('select[name="role"]').value;
            var certificateField = document.getElementById('certificateField');
            
            if (role === 'doctor' || role === 'patient') {
                certificateField.style.display = 'block';
            } else {
                certificateField.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h1>EYE CARE CENTER</h1>
    <h2>Register</h2>
    <form method="POST" action="register.php" enctype="multipart/form-data">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="date" name="birthday" placeholder="Birthday" required>
        <input type="text" name="city" placeholder="City" required>
        <select name="gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
        <input type="number" name="age" placeholder="Age" required>
        <select name="role" required onchange="toggleCertificateField()">
            <option value="admin">Admin</option>
            <option value="doctor">Doctor</option>
            <option value="patient">Patient</option>
        </select>
        <div id="certificateField" style="display: none;">
            <input type="text" name="certificate_number" placeholder="Certificate Number">
            <input type="file" name="certificate">
        </div>
        <button type="submit">Register</button>
    </form>
    
    <!-- Link to login page -->
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
