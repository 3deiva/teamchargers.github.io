<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV COMPANION</title>
    <link rel="stylesheet" type="text/css" href="a.css">
</head>
<body>
    <div class="registration-container">
        <h2>Create an Account</h2>
        
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $confirmPassword = $_POST["confirmPassword"];
            $email = $_POST["email"];
            $phoneNumber = $_POST["phoneNumber"]; 
            $userType = $_POST["userType"];

            // Validate input (you may want to add more validation)
            if ($password !== $confirmPassword) {
                echo '<p style="color: red;">Password and confirm password do not match.</p>';
            } else {
                try {
                    // Perform registration logic here (e.g., insert data into a PostgreSQL database)
                    // Make sure to hash the password before storing it

                    // Database insert query goes here using PDO
                    $host = "localhost";
                    $db = "ev";
                    $user = "postgres";
                    $password = '1234' ;

                    $pdo = new PDO("pgsql:host=$host;port='5434';dbname=$db", $user, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Insert user data into the 'users' table
                    $sql = "INSERT INTO reg (user_name, password, email, mobile_no, usertype) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$username, $password, $email, $phoneNumber, $userType]);

                    echo '<p style="color: green;">Registration successful.</p>';
                    header("Location: login.php");
                } catch (PDOException $e) {
                    // Check if the exception message contains the trigger error
                    if (strpos($e->getMessage(), 'Email address must be unique across users, eprofile, and jprofile tables.') !== false) {
                        echo '<p style="color: red;">Email address must be unique across users, eprofile, and jprofile tables.</p>';
                    } else if (strpos($e->getMessage(), 'duplicate key value violates unique constraint "users_email_key"') !== false) {
                        echo '<p style="color: red;">Error: Email address is already in use. Please choose a different email.</p>';
                    } else {
                        // If it's another exception, display a generic error
                        echo '<p style="color: red;">Error: ' . $e->getMessage() . '</p>';
                    }
                }

                // Close the connection
                $pdo = null;
            }
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="phoneNumber">Phone Number:</label>
            <input type="text" id="phoneNumber" name="phoneNumber" required>

            <label for="userType">Select User Type:</label>
            <select id="userType" name="userType" required>
                <option value="user">user</option>
                <option value="operator">operator</option>
            </select>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

    <script>
        // Add a class to trigger the animation when the form is shown
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.registration-container').classList.add('show');
        });
    </script>
</body>
</html>
