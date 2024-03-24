<?php
include "session.php";


// Include database connection details
$host = "localhost";
$port = "5434";
$dbname = "ev";
$user = "postgres";
$password = '1234';

try {
    // Create PDO connection
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve user's contact number from session
    $contact = $_SESSION['contact'];

    // Query to fetch operator's station ID based on contact
    $sql_operator = "SELECT id FROM operator WHERE contact = :contact";
    $stmt_operator = $pdo->prepare($sql_operator);
    $stmt_operator->bindParam(':contact', $contact, PDO::PARAM_STR);
    $stmt_operator->execute();
    $row_operator = $stmt_operator->fetch(PDO::FETCH_ASSOC);

    if (!$row_operator) {
        echo "Station not found for the logged-in user.";
        exit;
    }

    $station_id = $row_operator['id'];

    // Query to fetch feedbacks for the station
    $sql_feedback = "SELECT * FROM feedback WHERE station_id = :station_id";
    $stmt_feedback = $pdo->prepare($sql_feedback);
    $stmt_feedback->bindParam(':station_id', $station_id, PDO::PARAM_INT);
    $stmt_feedback->execute();
    $feedbacks = $stmt_feedback->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV Station Feedback</title>
    <link rel="stylesheet" href="operator.css">
</head>
<body>
    <header class="header">
        <nav class="nav-bar">
            <ul>
                <li><a href="operator.php">Dashboard</a></li>
                <li>Customer Feedback</li>
                <li><a href="op-prof.php">My Profile</a></li>
                <li id="logout"><a href="session_unset.php"> Logout </a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <table class="station-info">
            <thead>
                <tr>
                    <th>Stars</th>
                    <th>Feedbacks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback) : ?>
                    <tr>
                        <td><?php echo $feedback['rating']; ?></td>
                        <td><?php echo $feedback['feedback']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
