
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

    // Retrieve operator data based on contact
    $contact = $_SESSION['contact'];
    $query = "SELECT * FROM operator WHERE contact = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$contact]);
    $operator = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch stations for the operator
    $query1 = "SELECT * FROM station WHERE operator_id = ?";
    $stmt1 = $pdo->prepare($query1);
    $stmt1->execute([$operator['id']]);
    $station = $stmt1->fetch(PDO::FETCH_ASSOC);

    // Fetch machines data for the station
    $query2 = "SELECT * FROM machines WHERE station_id = ?";
    $stmt2 = $pdo->prepare($query2);
    $stmt2->execute([$station['station_id']]);
    $machines = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV Station Dashboard</title>
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
            <li class="mx-4"><a href="operator.php" class="text-blue-300">Dashboard</a></li>
            <li class="mx-4"><a href="req.php" class="hover:text-yellow-400">Requests</a></li>
            <li class="mx-4"><a href="feed.php" class="hover:text-yellow-400">Feedback</a></li>
            <li class="mx-4"><a href="op-prof.php" class="hover:text-yellow-400">Profile</a></li>
            <li class="mx-4"><a href="session_unset.php" class="hover:text-yellow-400">Logout</a></li>
        </ul>
    </nav>
</header>


<main class="main-content">
    <h2 style="text-align : center;">Station : <?php echo $station['station_name']; ?></h2>
    <table class="station-info">
        <thead>
        <tr>
            <th>Charging Type</th>
            <th>Total Machines</th>
            <th>Current users</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($machines as $machine) : ?>
            <tr>
                <td><?php echo $machine['charger_type']; ?></td>
                <td><?php echo $machine['total_number_available']; ?></td>
                <td>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="id" value="<?php echo $machine['id']; ?>">
                        <input type="hidden" name="charger_type" value="<?php echo $machine['charger_type']; ?>">
                        <input type="number" name="queue" value="<?php echo $machine['queue']; ?>">
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $host = "localhost";
        $port = "5432";
        $dbname = "ev";
        $user = "postgres";
        $password = "5112";

        $pdo = new PDO(
    "pgsql:host=dpg-d4mmlbq4d50c73eq0gs0-a.oregon-postgres.render.com;port=5432;dbname=ev_8jta",
    "ev_8jta_user",
    "JMxhcUXabU16VLJjiyew6oxGgxJm4Boq"
);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $id = $_POST["id"];
        $charger_type = $_POST["charger_type"];
        $queue = $_POST["queue"];

        // Update the machines table
        $query3 = "UPDATE machines SET queue = ? WHERE id = ? AND charger_type = ?";
        $stmt3 = $pdo->prepare($query3);
        $stmt3->execute([$queue, $id, $charger_type]);

        // Close connection
        $pdo = null;

        // Redirect after 2 seconds
        header("Refresh: 2; URL=operator.php");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

