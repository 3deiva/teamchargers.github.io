<?php
include "session.php";

// If form is submitted, handle update BEFORE HTML output
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO(
            "pgsql:host=dpg-d4mmlbq4d50c73eq0gs0-a.oregon-postgres.render.com;port=5432;dbname=ev_8jta",
            "ev_8jta_user",
            "JMxhcUXabU16VLJjiyew6oxGgxJm4Boq"
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $id = $_POST["id"];
        $charger_type = $_POST["charger_type"];
        $queue = $_POST["queue"];

        // Update queue
        $query3 = "UPDATE machines SET queue = ? WHERE id = ? AND charger_type = ?";
        $stmt3 = $pdo->prepare($query3);
        $stmt3->execute([$queue, $id, $charger_type]);

        header("Location: operator.php"); // Immediate redirect
        exit();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// ---------- FETCH ALL DATA (ONLY FOR GET REQUEST) ---------- //

try {
    $pdo = new PDO(
        "pgsql:host=dpg-d4mmlbq4d50c73eq0gs0-a.oregon-postgres.render.com;port=5432;dbname=ev_8jta",
        "ev_8jta_user",
        "JMxhcUXabU16VLJjiyew6oxGgxJm4Boq"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $contact = $_SESSION['contact'];
    
    // operator
    $stmt = $pdo->prepare("SELECT * FROM operator WHERE contact = ?");
    $stmt->execute([$contact]);
    $operator = $stmt->fetch(PDO::FETCH_ASSOC);

    // station
    $stmt1 = $pdo->prepare("SELECT * FROM station WHERE operator_id = ?");
    $stmt1->execute([$operator['id']]);
    $station = $stmt1->fetch(PDO::FETCH_ASSOC);

    // machines
    $stmt2 = $pdo->prepare("SELECT * FROM machines WHERE station_id = ?");
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
    <h2 style="text-align:center;">Station : <?php echo $station['station_name']; ?></h2>

    <table class="station-info">
        <thead>
        <tr>
            <th>Charging Type</th>
            <th>Total Machines</th>
            <th>Current Users</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($machines as $machine) : ?>
            <tr>
                <td><?= $machine['charger_type']; ?></td>
                <td><?= $machine['total_number_available']; ?></td>

                <td>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $machine['id']; ?>">
                        <input type="hidden" name="charger_type" value="<?= $machine['charger_type']; ?>">
                        <input type="number" name="queue" value="<?= $machine['queue']; ?>">
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
