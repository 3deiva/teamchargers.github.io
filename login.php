<!-- login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV-Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('img/ev5.jpg') no-repeat center center fixed; /* Add background image */
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #007bff; /* Set default text color to blue */
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.9); /* Set container color to green with transparency */
            max-width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); /* Box shadow for a slight visual lift */
        }

        .login-container h2 {
            color: #007bff; /* Change header text color to blue */
            text-align: center;
            margin-bottom: 20px;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container label {
            margin-bottom: 10px;
            color: #007bff; /* Change label text color to blue */
            display: block;
        }

        .login-container input,
        .login-container select {
            padding: 10px;
            margin-bottom: 20px;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
            padding: 10px 20px;
            background-color: #007bff; /* Change button background color to blue */
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #0056b3; /* Change button background color on hover */
        }

        .login-container p {
            margin-top: 20px;
            text-align: center;
        }

        .login-container a {
            color: #0056b3; /* Change link color to a darker shade of blue */
            text-decoration: none;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 500px) {
            .login-container {
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>EV COMPANION</h2>

        <?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_number = $_POST["phone_number"];
    $password = $_POST["password"];
    $userType = $_POST["userType"];

    $host = "localhost";
    $db = "ev";
    $user = "postgres";
    $db_password = '1234'; // Changed variable name to avoid conflict

    try {
        $pdo = new PDO("pgsql:host=$host;port=5434;dbname=$db", $user, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Retrieve user data from the 'users' table including user_id
        $sql = "SELECT userid, user_name, password, usertype FROM reg WHERE mobile_no = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$phone_number]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($password == $row['password'] && $userType == $row['usertype']) {
            // Login successful
            // Store user_id in the session
            $_SESSION['user_type'] = $row['usertype'];
            $_SESSION['username'] = $row['user_name'];
            $_SESSION['contact']=$phone_number;
            
            echo '<p style="color: #28a745;">Login successful. Redirecting...</p>'; /* Change success message color */
            if ($userType == 'user') {
                header("Refresh: 2; URL=hu.php");
            } else {
                header("Refresh: 2; URL=operator.php");
            }
            exit();
        } else {
            // Login failed
            echo '<p style="color: #dc3545;">Invalid phone number, password, or user type.</p>'; /* Change error message color */
        }
    } catch (PDOException $e) {
        echo '<p style="color: #dc3545;">Error: ' . $e->getMessage() . '</p>'; /* Change error message color */
    }

    $pdo = null;
}
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="userType">Select User Type:</label>
            <select id="userType" name="userType" required>
                <option value="user">User</option>
                <option value="operator">Operator</option>
            </select>

            <button type="submit">Login</button>
        </form>


        <p>Don't have an account? <a href="reg.php">Register here</a></p>
    </div>
</body>
</html>
