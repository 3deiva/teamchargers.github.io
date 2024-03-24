<?php
include "session.php";

$host = "localhost";
$port = "5434";
$dbname = "ev";
$user = "postgres";
$password = '1234';

try {
    // Create PDO connection
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve user ID from the session
    $contact = $_SESSION['contact'];

    // Query to fetch operator data based on contact
    $query = "SELECT * FROM operator WHERE contact = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$contact]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Error: User not found.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="operator.css">
    <style>
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav-bar">
            <ul>
                <li><a href="operator.php">Dashboard</a></li>
                <li><a href="feed.php">Customer feedback</a></li>
                <li>My Profile</li>
                <li id="logout"><a href="session_unset.php"> Logout </a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h1>User Profile</h1>
        <h2>Welcome, <?php echo $user['name']; ?>!</h2>
        <p>Contact Number: <span id="contact"><?php echo $user['contact']; ?></span></p>
        <p>Age: <span id="age"><?php echo $user['age']; ?></span></p>
    </div>
</body>
</html>
