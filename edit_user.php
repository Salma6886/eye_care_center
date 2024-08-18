<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $city = $_POST['city'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];

    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, city = ?, gender = ?, age = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $city, $gender, $age, $id]);

    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        <input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" required>
        <input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" required>
        <input type="text" name="city" value="<?php echo $user['city']; ?>" required>
        <select name="gender" required>
            <option value="male" <?php if ($user['gender'] == 'male') echo 'selected'; ?>>Male</option>
            <option value="female" <?php if ($user['gender'] == 'female') echo 'selected'; ?>>Female</option>
            <option value="other" <?php if ($user['gender'] == 'other') echo 'selected'; ?>>Other</option>
        </select>
        <input type="number" name="age" value="<?php echo $user['age']; ?>" required>
        <button type="submit">Update</button>
    </form>
</body>
</html>
