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

// Check if latitude and longitude parameters are set in the URL
if(isset($_GET['lat']) && isset($_GET['lng'])) {
    // Retrieve latitude and longitude from URL parameters
    $lat = $_GET['lat'];
    $lng = $_GET['lng'];

    try {
        // Prepare SQL statement to fetch data from the station table based on latitude and longitude
        $sql = "select * from station
        inner join machines 
        using(station_id) WHERE latitude = :lat AND longitude = :lng";
        $stmt = $pdo->prepare($sql);
        // Bind parameters
        $stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
        $stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
        // Execute the query
        $stmt->execute();
        // Fetch data as associative array
        $stations = $stmt->fetchAll(PDO::FETCH_ASSOC);



        // Check if data is found
        if(count($stations) > 0) {
            // Display the station data
            foreach ($stations as $station) {
                
            }
        } else {
            echo "No station found for the provided coordinates.";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    echo "Latitude and longitude parameters are missing in the URL.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Station Information</title>
    <link rel="stylesheet" href="home.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .btn-container {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
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
        .btn:hover {
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
    <h2>Station Information</h2>
    <?php
    $stat = $stmt->fetch(PDO::FETCH_ASSOC); 
    if($station) {
        // Display the station data
        echo "<h3>Station Name: {$station['station_name']}</h3>";
        echo "<p>Station ID: {$station['station_id']}</p>";
        echo "Latitude : "."$lat"."<br>" ;
        echo "Longitude : "."$lng"."<br>" ; 
    } else {
        echo "No station found for the provided coordinates.";
    }
    ?>
    <table>
        <tr>
            <th>Charge type</th>
            <th>Port</th>
            <th>Queue</th>
        </tr>
        <?php foreach ($stations as $station): ?>
            <tr>
                <td><?php echo $station['charger_type']; ?></td>
                <td><?php echo $station['total_number_available']; ?></td>
                <td><?php echo $station['queue']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="btn-container">
        <button class="btn" onclick="window.location.href='rating.php?station_id=<?php echo $station['station_id']; ?>'">Give feedback for this station</button>
    </div>
</body>
</html>
