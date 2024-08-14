<?php
include "session.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV App Home</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            position: sticky;
            top: 0;
            width: 100%;
            background-color: #34d399; /* Tailwind green-400 */
            color: white;
            padding: 1rem;
            text-align: center;
            z-index: 1000;
        }

        header h1 {
            margin: 0;
            font-size: 2rem;
        }

        main {
            flex: 1;
            padding: 2rem;
            text-align: center;
            color: #333;
            background: white;
            margin: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        .hero h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #059669; /* Tailwind green-700 */
        }

        .hero p {
            font-size: 1.2rem;
            color: #4b5563; /* Tailwind gray-600 */
        }

        .features h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #059669; /* Tailwind green-700 */
        }

        .features ul {
            list-style: none;
            padding: 0;
        }

        .features li {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #4b5563; /* Tailwind gray-600 */
            opacity: 0;
            animation: fadeIn 1s ease-in-out forwards;
        }

        .emergency-text {
            color: #ef4444; /* Tailwind red-500 */
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .button {
            background-color: #ef4444; /* Tailwind red-500 */
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 1rem;
        }

        .button:hover {
            background-color: #dc2626; /* Tailwind red-600 */
        }

        footer {
            background-color: #34d399; /* Tailwind green-400 */
            color: white;
            text-align: center;
            padding: 1rem;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        input {
            display: none;
        }
    </style>
</head>
<body>
<header class="bg-green-600 text-white py-4">
    <div class="text-center">
        <h1 class="text-2xl font-bold">EV Companion</h1>
    </div>
    <nav class="mt-4">
        <ul class="flex justify-center">
            <li class="mx-4"><a href="#" class="text-blue-400">Home</a></li>
            <li class="mx-4"><a href="myprofile.php" class="hover:text-yellow-400">My Profile</a></li>
            <li class="mx-4"><a href="map.php" class="hover:text-yellow-400">Charging Stations</a></li>
            <li class="mx-4"><a href="about.html" class="hover:text-yellow-400">About Us</a></li>
            <li class="mx-4"><a href="session_unset.php" class="hover:text-yellow-400">Logout</a></li>
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
            <li>24/7 support available</li>
            <li class="emergency-text">EMERGENCY!!! We got you covered</li>
            <li class="emergency-text">If you want immediate services</li>
            <button class="button" name="button" id="button" onclick="emergency()">Press Me</button>
            <li class="emergency-text">We will reach out to you in a flash</li>
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
    <p>&copy; 2024 EV Companion. All rights reserved.</p>
</footer>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
<script>
    var chennaiAreas = [
        { name: 'Adyar', coordinates: [13.0064, 80.2572] },
        { name: 'T.Nagar', coordinates: [13.0329, 80.2344] },
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
                alert("Your request is submitted. " + nearestArea.name + " operator will reach out to you soon");
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
$port = "5432";
$dbname = "ev";
$user = "postgres";
$password = '5112';

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
        $sql = "INSERT INTO request (user_name,mobile_no,ulat, ulng, station) VALUES (:uname,:mob,:lat, :lng, :area)";
        $stmt = $pdo->prepare($sql);

        // Binding parameters and executing the statement
        $stmt->bindParam(':uname', $_SESSION['username']);
        $stmt->bindParam(':mob',$_SESSION['contact']);
        $stmt->bindParam(':lat', $lat);
        $stmt->bindParam(':lng', $lng);
        $stmt->bindParam(':area', $area);
        $stmt->execute();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
</body>
</html>
