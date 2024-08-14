<?php
include "session.php";

$host = "localhost";
$port = "5432";
$dbname = "ev";
$user = "postgres";
$password = '5112';

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
        #container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
 <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<header class="bg-green-600 text-white py-4">
    <div class="text-center">
        <h1 class="text-2xl font-bold">EV Companion</h1>
    </div>
    <nav class="mt-4">
        <ul class="flex justify-center">
            <li class="mx-4"><a href="operator.php" class="hover:text-yellow-400">Dashboard</a></li>
            <li class="mx-4"><a href="req.php" class="hover:text-yellow-400">Requests</a></li>
            <li class="mx-4"><a href="feed.php" class="hover:text-yellow-400">Feedback</a></li>
            <li class="mx-4"><a href="op-prof.php" class="text-blue-300">Profile</a></li>
            <li class="mx-4"><a href="session_unset.php" class="hover:text-yellow-400">Logout</a></li>
        </ul>
    </nav>
</header>
    <div id="container">
        <h2>Welcome, <?php echo $user['name']; ?>!</h2>
        <p>Contact Number: <span id="contact"><?php echo $user['contact']; ?></span></p>
        <p>Age: <span id="age"><?php echo $user['age']; ?></span></p>
    </div>
</body>
</html>
