<?php
include "session.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My EV Profile</title>
  <link rel="stylesheet" href="home.css">
  <style>

    .container {
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
    #name{
      display:none;
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
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #0056b3;
    }

    /* CSS animation effects */
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .fadeIn {
      animation: fadeIn 1s ease-out;
    }
  </style>
</head>
<body>
<header>
    <h1>EV companion</h1>
    <nav>
        <ul>
            <li><a href="hu.php">Home</a></li>
            <li id="page">Myprofile</li>
            <li><a href="map.php">Charging Stations</a></li>
            <li><a href="about.html">About Us</a></li>
            <li id="logout"><a href="session_unset.php"> Logout </a></li>
        </ul>
    </nav>
</header>
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // PostgreSQL connection parameters
    $host = "localhost";
    $port = "5434";
    $dbname = "ev";
    $user = "postgres";
    $password = "1234";

    try {
        // Establish a connection to the PostgreSQL database
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password");

        // Set PDO to throw exceptions on error
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
    } catch (PDOException $e) {
        echo "<script>alert('Database Error: " . $e->getMessage() . "')</script>";
    }
}
?>
<div class="container">
  <h1>My EV Profile</h1>
  <form method="post">
    <input type="text" name="name" id="name" placeholder="Name" value="<?php echo isset($_SESSION["username"]) ? htmlspecialchars($_SESSION["username"]) : ''; ?>">
    <input type="tel" name="phoneNumber" id="phoneNumber" placeholder="Phone Number" required>
    <input type="text" name="carModel" id="carModel" placeholder="Car Model" required>
    <input type="text" name="carNumber" id="carNumber" placeholder="Car Number" required>
    <input type="text" name="chargerType" id="chargerType" placeholder="Charger Type" required>
    <input type="text" name="address" id="address" placeholder="Address (Coordinates)" required>
    <button type="submit">Save Profile</button>
  </form>

</div>
</body>
</html>
