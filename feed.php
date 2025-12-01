<?php
include "session.php";

$host = "localhost";
$port = "5432";
$dbname = "ev";
$user = "postgres";
$password = "5112";

try {
    // Create PDO connection
   $pdo = new PDO(
    "pgsql:host=dpg-d4mmlbq4d50c73eq0gs0-a.oregon-postgres.render.com;port=5432;dbname=ev_8jta",
    "ev_8jta_user",
    "JMxhcUXabU16VLJjiyew6oxGgxJm4Boq"
);

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

    $query1 = "SELECT station_name FROM station WHERE station_id = :station_id";
    $stmt1 = $pdo->prepare($query1);
    $stmt1->bindParam(':station_id', $station_id);
    $stmt1->execute();
    $station = $stmt1->fetch(PDO::FETCH_ASSOC);
    

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
            <li class="mx-4"><a href="feed.php" class="text-blue-300">Feedback</a></li>
            <li class="mx-4"><a href="op-prof.php" class="hover:text-yellow-400">Profile</a></li>
            <li class="mx-4"><a href="session_unset.php" class="hover:text-yellow-400">Logout</a></li>
        </ul>
    </nav>
</header>
    <main class="main-content">
    <h2 style="text-align : center;">Station : <?php echo $station["station_name"]; ?></h2>
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

