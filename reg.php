<?php

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username       = $_POST["username"];
    $password       = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $email          = $_POST["email"];
    $phoneNumber    = $_POST["phoneNumber"];
    $userType       = $_POST["userType"];

    if ($password !== $confirmPassword) {
        $error_message = "Password and confirm password do not match.";
    } else {
        try {
            // Connect to Render PostgreSQL
            $pdo = new PDO(
                "pgsql:host=dpg-d4mmlbq4d50c73eq0gs0-a.oregon-postgres.render.com;port=5432;dbname=ev_8jta",
                "ev_8jta_user",
                "JMxhcUXabU16VLJjiyew6oxGgxJm4Boq"
            );

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // INSERT query
            $sql = "INSERT INTO reg (user_name, password, email, mobile_no, usertype)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $username,
                $password,      // (optional) change to password_hash($password, PASSWORD_DEFAULT)
                $email,
                $phoneNumber,
                $userType
            ]);

            // Redirect BEFORE output
            header("Location: login.php");
            exit();

        } catch (PDOException $e) {

            if (strpos($e->getMessage(), "duplicate key") !== false) {
                $error_message = "Email already exists. Try another.";
            } else {
                $error_message = "Database Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV COMPANION - Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('img/log1.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #007bff;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            max-width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .slide-up { animation: slideUp 0.5s ease-in-out; }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        input, select {
            width: 100%; padding: 10px; margin-bottom: 20px;
            border: 1px solid #ccc; border-radius: 5px;
            transition: 0.3s;
        }

        input:focus, select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.5);
        }

        button {
            width: 100%; padding: 10px;
            background: #007bff; color: white;
            border: none; border-radius: 5px;
        }

        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container registration-container">
        <h2 class="text-2xl font-bold text-green-600 text-center mb-6 fade-in">
            Create an Account
        </h2>

        <!-- Show errors safely -->
        <?php if (!empty($error_message)): ?>
            <p class="text-red-600 text-center mb-4 slide-up">
                <?= $error_message ?>
            </p>
        <?php endif; ?>

        <form action="" method="post" class="space-y-4">

            <div class="slide-up">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="slide-up">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="slide-up">
                <label for="confirmPassword">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
            </div>

            <div class="slide-up">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="slide-up">
                <label for="phoneNumber">Phone Number:</label>
                <input type="text" id="phoneNumber" name="phoneNumber" required>
            </div>

            <div class="slide-up">
                <label for="userType">Select User Type:</label>
                <select id="userType" name="userType" required>
                    <option value="user">User</option>
                    <option value="operator">Operator</option>
                </select>
            </div>

            <button type="submit" class="slide-up">
                Register
            </button>
        </form>

        <p class="mt-4 text-center slide-up">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>
    </div>
</body>
</html>
