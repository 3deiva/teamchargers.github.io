<?php

// Include session.php for session management
include "session.php";

// PostgreSQL connection parameters
$host = "localhost";
$port = "5432";
$dbname = "ev";
$user = "postgres";
$password = "5112";

try {
    // Establish a connection to the PostgreSQL database
   $pdo = new PDO(
    "pgsql:host=dpg-d4mmlbq4d50c73eq0gs0-a.oregon-postgres.render.com;port=5432;dbname=ev_8jta",
    "ev_8jta_user",
    "JMxhcUXabU16VLJjiyew6oxGgxJm4Boq"
);


    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle form submission to save profile data
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize and validate form data
        $name = ($_POST["name"]);
        $phoneNumber = ($_POST["phoneNumber"]);
        $carModel = ($_POST["carModel"]);
        $carNumber = ($_POST["carNumber"]);
        $chargerType = ($_POST["chargerType"]);
        $address = ($_POST["address"]);

        // Prepare SQL statement to insert data into the PostgreSQL table
        $sql = "INSERT INTO profile (name, phone_number, car_model, car_number, charger_type, address) 
                VALUES (:name, :phoneNumber, :carModel, :carNumber, :chargerType, :address)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phoneNumber', $phoneNumber);
        $stmt->bindParam(':carModel', $carModel);
        $stmt->bindParam(':carNumber', $carNumber);
        $stmt->bindParam(':chargerType', $chargerType);
        $stmt->bindParam(':address', $address);

        // Execute the statement
        $stmt->execute();

        // Redirect after successful save
        header("Location: myprofile.php");
        exit();
    }

    // Fetch the user profile data for display
    $username = $_SESSION["username"];
    $sql = "SELECT * FROM profile WHERE name = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Fetch the user profile data
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<script>alert('Database Error: " . $e->getMessage() . "')</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My EV Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 4%;
            max-width: 600px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        h1 {
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff ;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body class="bg-gray-100">
<header class="bg-green-600 text-white py-4">
    <div class="text-center">
        <h1 class="text-2xl font-bold">EV Companion</h1>
    </div>
    <nav class="mt-4">
        <ul class="flex justify-center">
            <li class="mx-4"><a href="hu.php" class="hover:text-yellow-400">Home</a></li>
            <li class="mx-4"><a href="#" class="text-blue-400">My Profile</a></li>
            <li class="mx-4"><a href="map.php" class="hover:text-yellow-400">Charging Stations</a></li>
            <li class="mx-4"><a href="about.html" class="hover:text-yellow-400">About Us</a></li>
            <li class="mx-4"><a href="session_unset.php" class="hover:text-yellow-400">Logout</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <h1 class="text-2xl font-bold mb-4">My EV Profile</h1>
    <form method="post" class="space-y-4">
        <input type="text" name="name" id="name" placeholder="Name" value="<?php echo isset($profile['name']) ? htmlspecialchars($profile['name']) : ''; ?>" class="block w-full px-4 py-3 border border-gray-300 rounded-md placeholder-gray-500 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
        <input type="tel" name="phoneNumber" id="phoneNumber" placeholder="Phone Number" value="<?php echo isset($profile['phone_number']) ? htmlspecialchars($profile['phone_number']) : ''; ?>" class="block w-full px-4 py-3 border border-gray-300 rounded-md placeholder-gray-500 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
        <input type="text" name="carModel" id="carModel" placeholder="Car Model" value="<?php echo isset($profile['car_model']) ? htmlspecialchars($profile['car_model']) : ''; ?>" class="block w-full px-4 py-3 border border-gray-300 rounded-md placeholder-gray-500 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
        <input type="text" name="carNumber" id="carNumber" placeholder="Car Number" value="<?php echo isset($profile['car_number']) ? htmlspecialchars($profile['car_number']) : ''; ?>" class="block w-full px-4 py-3 border border-gray-300 rounded-md placeholder-gray-500 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
        <input type="text" name="chargerType" id="chargerType" placeholder="Charger Type" value="<?php echo isset($profile['charger_type']) ? htmlspecialchars($profile['charger_type']) : ''; ?>" class="block w-full px-4 py-3 border border-gray-300 rounded-md placeholder-gray-500 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
        <input type="text" name="address" id="address" placeholder="Address (Coordinates)" value="<?php echo isset($profile['address']) ? htmlspecialchars($profile['address']) : ''; ?>" class="block w-full px-4 py-3 border border-gray-300 rounded-md placeholder-gray-500 focus:outline-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400">
        <button type="submit" class="w-full py-3 bg-green-500 text-white rounded-md hover:bg-blue-600 transition duration-300">Save Profile</button>
    </form>
</div>

</body>
</html>

