<?php
session_start();
include 'db.php';

$login_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $birthday = $_POST['birthday'];
    $role = strtolower(trim($_POST['role'])); // Normalize role

    // Check for username and birthday
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND birthday = ?");
    $stmt->execute([$username, $birthday]);
    $user = $stmt->fetch();

    if ($user) {
        if (strtolower($user['role']) === $role) { // Ensure case-insensitive comparison
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username']; // Store username in session

            // Redirect based on role
            if ($role === 'admin') {
                header("Location: admin_dashboard.php");
                exit();
            } elseif ($role === 'doctor') {
                $username = urlencode($username); // Encode username for URL
                header("Location: doctor_dashboard.php?message=Hello%20Doctor%20$username!");
                exit();
            } elseif ($role === 'patient') {
                header("Location: patient_dashboard.php");
                exit();
            }
        } else {
            // Role mismatch
            $login_message = "Login failed: Incorrect role selected.";
        }
    } else {
        // No user found or incorrect birthday
        $login_message = "Login failed: Incorrect username or birthday.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"],
        input[type="date"],
        select,
        button {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        p {
            text-align: center;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="date" name="birthday" placeholder="Birthday" required>
            <select name="role" required>
                <option value="admin">Admin</option>
                <option value="doctor">Doctor</option>
                <option value="patient">Patient</option>
            </select>
            <button type="submit">Login</button>
        </form>
        <?php
        // Display the login message
        if (!empty($login_message)) {
            echo "<p>$login_message</p>";
        }
        ?>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>