<?php
include "session.php";
$host = "localhost";
$port = "5434";
$dbname = "ev";
$user = "postgres";
$password = '1234';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Check if station_id parameter is set in the URL
if(isset($_GET['station_id'])) {
    // Retrieve station ID from URL parameter
    $station_id = $_GET['station_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate and sanitize input
        $rating = $_POST['rating'] ?? '';
        $feedback = $_POST['feedback'] ?? '';
        
        // Prepare SQL statement to insert feedback into the feedback table
        $sql = "INSERT INTO feedback (station_id, rating, feedback) VALUES (:station_id, :rating, :feedback)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':station_id', $station_id, PDO::PARAM_INT);
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':feedback', $feedback, PDO::PARAM_STR);

        // Execute the query
        if ($stmt->execute()) {
            echo "<script>alert('Feedback submitted successfully.');</script>";
            echo "<script>setTimeout(function(){ window.location.href='map.php'; }, 1000);</script>";
        } else {
            echo "Error occurred while submitting feedback.";
        }
    }
} else {
    echo "Station ID parameter is missing in the URL.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <link rel="stylesheet" href="home.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        form {
            width: 50%;
            margin: auto;
            margin-top: 50px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<header>
        <h1>EV companion</h1>
    <nav>
        <ul>
            <li><a href="hu.php">Home</a></li>
            <li><a href="myprofile.php">Myprofile</a></li>
            <li><a href="map.php">Charging Stations</a></li>
            <li><a href="about.html">About Us</a></li>
            <li id="logout"><a href="session_unset.php"> Logout </a></li>
        </ul>
    </nav>
    </header>
    <h2>Feedback Form</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?station_id=" . $station_id); ?>">
        <label for="rating">Rating (1-5):</label>
        <input type="number" id="rating" name="rating" min="1" max="5" required>
        <label for="feedback">Feedback:</label>
        <textarea id="feedback" name="feedback" rows="4" required></textarea>
        <input type="submit" value="Submit Feedback">
    </form>
</body>
</html>
