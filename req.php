<?php
include "session.php";

$host = "localhost";
$port = "5432";
$dbname = "ev";
$user = "postgres";
$password = "5112";

try {
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

    $station_name = $station['station_name'];
    

    // Query to fetch feedbacks for the station
    $sql_req = "SELECT user_name,ulat,ulng,mobile_no FROM request WHERE station = :station_name";
    $stmt_req = $pdo->prepare($sql_req);
    $stmt_req->bindParam(':station_name', $station_name, PDO::PARAM_INT);
    $stmt_req->execute();
    $requests = $stmt_req->fetchAll(PDO::FETCH_ASSOC);
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
            <li class="mx-4"><a href="req.php" class="text-blue-300">Requests</a></li>
            <li class="mx-4"><a href="feed.php" class="hover:text-yellow-400">Feedback</a></li>
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
                    <th>Customer</th>
                    <th>Location</th>
                    <th>Mobile Number</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request) : ?>
                    <tr>
                        <td><?php echo $request['user_name']; ?></td>
                        <td><?php echo $request['ulng'].",". $request['ulat']; ?></td>
                        <td><?php echo $request['mobile_no']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>

</html>
