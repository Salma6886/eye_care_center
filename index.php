<?php
session_start(); // Start the session to access session variables
include 'db.php';

// Fetch eye specialists data from the database
try {
    $stmt = $pdo->query("SELECT name, specialty, bio FROM eye_specialists");
    $specialists = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching data: " . $e->getMessage();
    exit();
}

// Fetch reviews from the database
try {
    $stmt = $pdo->query("SELECT username, review FROM reviews");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching reviews: " . $e->getMessage();
    exit();
}

// Get the username from the session
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EyeCare Center</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        header {
            text-align: center;
            padding: 20px 0;
            background-color: #007bff;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        nav {
            background-color: #333;
            overflow: hidden;
        }
        nav a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        nav a:hover {
            background-color: #ddd;
            color: black;
        }
        section {
            margin-bottom: 40px;
        }
        section h2 {
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #007bff;
        }
        .specialist, .review {
            padding: 20px;
            background-color: #f9f9f9;
            margin-bottom: 20px;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .specialist h3, .review h3 {
            margin: 0;
            font-size: 20px;
            color: #007bff;
        }
        .specialist p, .review p {
            margin: 5px 0;
            line-height: 1.6;
        }
        footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: white;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <header>
        EyeCare Center
    </header>

    <nav>
        <a href="#home">Home</a>
        <a href="#about">About Us</a>
        <a href="#services">Our Services</a>
        <a href="#tests">Tests</a>
        <a href="#contact">Contact Us</a>
        <a href="#reviews">Reviews</a>
        <a href="register.php">Register Here</a>

    </nav>

    <div class="container">
        <section id="home">
            <h2>Welcome to EyeCare Center</h2>
            <p>Hello, <?php echo $username; ?>! Welcome to our website.</p>
        </section>

        <section id="about">
            <h2>About Us</h2>
            <p>We are dedicated to providing the best eye care services to our community. Our team of experienced eye specialists is here to help with all your eye care needs.</p>
        </section>

        <section id="services">
            <h2>Our Services</h2>
            <p>We offer a wide range of services, including comprehensive eye exams, treatment for eye diseases, and specialized care for various eye conditions.</p>
        </section>

        <section id="tests">
            <h2>Tests</h2>
            <p>Our center is equipped with the latest technology to conduct various eye tests such as vision acuity tests, retinal exams, and more.</p>
        </section>

        <section id="contact">
            <h2>Contact Us</h2>
            <p>If you have any questions or need to schedule an appointment, please feel free to contact us at [Your Contact Information Here].</p>
        </section>

        <section id="specialists">
            <h2>Our Eye Specialists</h2>
            <?php if ($specialists): ?>
                <?php foreach ($specialists as $specialist): ?>
                    <div class="specialist">
                        <h3><?php echo htmlspecialchars($specialist['name']); ?></h3>
                        <p><strong>Specialty:</strong> <?php echo htmlspecialchars($specialist['specialty']); ?></p>
                        <p><?php echo htmlspecialchars($specialist['bio']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No eye specialists found.</p>
            <?php endif; ?>
        </section>

        <section id="reviews">
            <h2>Reviews</h2>
            <?php if ($reviews): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review">
                        <h3><?php echo htmlspecialchars($review['username']); ?></h3>
                        <p><?php echo htmlspecialchars($review['review']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reviews available.</p>
            <?php endif; ?>
        </section>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> EyeCare Center. All rights reserved.
    </footer>

</body>
</html>
