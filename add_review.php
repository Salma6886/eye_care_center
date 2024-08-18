<?php
session_start();
include('db_connection.php'); // Include your database connection file

// Check if the user is logged in and verified
if (!isset($_SESSION['user_id']) || !isset($_SESSION['verified']) || $_SESSION['verified'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve review details from the form
    $username = $_SESSION['username']; // Assuming you store the username in the session
    $review = $_POST['review']; // The review content

    // Validate and sanitize input
    $username = mysqli_real_escape_string($conn, $username);
    $review = mysqli_real_escape_string($conn, $review);

    // Insert the review into the database
    $query = "INSERT INTO reviews (username, review) VALUES ('$username', '$review')";
    if (mysqli_query($conn, $query)) {
        echo "Review submitted successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Review</title>
</head>
<body>
    <h1>Submit Review</h1>
    <form method="POST" action="">
        <label for="review">Review:</label>
        <textarea name="review" id="review" rows="5" required></textarea>
        <br>
        <input type="submit" value="Submit Review">
    </form>
</body>
</html>
