<?php
include "session.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV App Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-image: url('https://img.freepik.com/premium-photo/electric-car-charging-station-green-background-power-supply-electric-car-charging-3d-render_576440-696.jpg');
            background-size: cover;
            background-position: center;
        }

        header {
            position: sticky;
            top: 0;
            width: 100%;
            background-color: rgba(51, 51, 51, 0.8);
            color: skyblue;
            padding: 1rem;
            text-align: center;
        }

        header h1 {
            margin: 0;
        }

        #page {
            color: rgb(17, 124, 53);
            text-decoration: none;
            padding: 1rem 2rem;
            display: block;
            background-color: #a59393;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        nav {
            background-color: #444;
            padding: 0.5rem;
            text-align: center;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        nav ul li {
            margin-right: 1rem;
        }

        nav ul li a {
            color: greenyellow;
            text-decoration: none;
            padding: 1rem 2rem;
            display: block;
            background-color: #666;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        nav ul li a:hover {
            background-color: #888;
        }

        main {
            padding: 2rem;
        }

        .hero {
            padding: 2rem;
            text-align: center;
            animation: slide-up 1s ease-in-out;
        }

        .features {
            margin-top: 2rem;
            text-align: center;
        }

        .features li {
            opacity: 0;
            animation: fade-in 1s ease-in-out forwards;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: rgba(51, 51, 51, 0.8);
            color: #fff;
            text-align: center;
            padding: 1rem;
        }

        @keyframes slide-up {
            0% {
                transform: translateY(100px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fade-in {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        .emergency-text {
            color: red;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .button {
            background-color: red;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: darkred;
        }
        input{
            display: none;
        }
    </style>
</head>
<body>
<header>
    <h1>EV companion</h1>
    <nav>
        <ul>
            <li id="page">Home</li>
            <li><a href="myprofile.php">Myprofile</a></li>
            <li><a href="map.php">Charging Stations</a></li>
            <li><a href="about.html">About Us</a></li>
            <li id="logout"><a href="session_unset.php"> Logout </a></li>
        </ul>
    </nav>
</header>
<main>
    <section class="hero">
        <h2>Welcome to EV App</h2>
        <p>Explore the future of transportation with our support.</p>
    </section>
    <section class="features">
        <h2>Our Features</h2>
        <ul>
            <li>Convenient charging stations</li>
            <li>Eco-friendly transportation options</li>
            <li>24/7 support available</li><br>
            <li class="emergency-text">EMERGENCY!!! We have got you covered </li><br>
            <li class="emergency-text"> If you want immediate services </li>
            <button class="button" name="button" id="button" onclick="emergency()">Press Me</button><br><br>
            <li class="emergency-text">We will reach out to you in a flash </li>
        </ul>
        <form id="emergencyForm" method="post">
            <input type="hidden" name="action" value="emergency">
            <input type="hidden" id="lat" name="lat"> 
            <input type="hidden" id="lng" name="lng"> 
            <input type="hidden" id="area" name="area"> 
        </form>
    </section>
</main>
<footer>
    <p>&copy; 2024 EV companion. All rights reserved.</p>
</footer>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
<script>
    var chennaiAreas = [
        { name: 'Adyar', coordinates: [13.0064, 80.2572] },
        { name: 'T.Nagar', coordinates: [13.0329, 
            80.2344] },
        { name: 'Mylapore', coordinates: [13.0339, 80.2707] },
        { name: 'Anna Nagar', coordinates: [13.0878, 80.2088] },
        { name: 'Guindy', coordinates: [13.0067, 80.2209] }
    ];

    function emergency() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var userLat = position.coords.latitude;
                var userLng = position.coords.longitude;
                var nearestArea = findNearestArea([userLat, userLng]);
                document.getElementById('lat').value = userLat;
                document.getElementById('lng').value = userLng;
                document.getElementById('area').value = nearestArea.name;
                alert("Your request is submitted. "+nearestArea.name+" operator will reach out to you soon");
                document.getElementById('emergencyForm').submit();
            });
        } else {
            alert('Geolocation is not supported by your browser');
        }
    }

    function findNearestArea(userCoordinates) {
        var nearestArea;
        var minDistance = Number.MAX_VALUE;
        chennaiAreas.forEach(area => {
            var areaCoordinates = area.coordinates;
            var distance = calculateDistance(userCoordinates[0], userCoordinates[1], areaCoordinates[0], areaCoordinates[1]);
            if (distance < minDistance) {
                minDistance = distance;
                nearestArea = area;
            }
        });
        return nearestArea;
    }

    function calculateDistance(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius of the earth in km
        var dLat = deg2rad(lat2 - lat1);
        var dLon = deg2rad(lon2 - lon1);
        var a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c; // Distance in km
        return d;
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }
</script>
<?php
// Database connection parameters
$host = "localhost";
$port = "5434";
$dbname = "ev";
$user = "postgres";
$password = '1234';

// Establishing the database connection using PDO
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    // Set PDO to throw exceptions on errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: Could not connect. " . $e->getMessage());
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'emergency') {
    // Retrieving data from the POST request
    $lat = $_POST["lat"];
    $lng = $_POST["lng"];
    $area = $_POST["area"];

    try {
        // Prepared statement to insert data into the table
        $sql = "INSERT INTO request (ulat, ulng, station) VALUES (:lat, :lng, :area)";
        $stmt = $pdo->prepare($sql);

        // Binding parameters and executing the statement
        $stmt->bindParam(':lat', $lat);
        $stmt->bindParam(':lng', $lng);
        $stmt->bindParam(':area', $area);
        $stmt->execute();

        echo "Data inserted successfully.";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
</body>
</html>