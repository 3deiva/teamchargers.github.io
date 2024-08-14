<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV COMPANION - Register</title>
    <style>
        /* Custom background image and general styles */
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
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .fade-in {
            animation: fadeIn 2s ease-in-out;
        }

        .slide-up {
            animation: slideUp 0.5s ease-in-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* TailwindCSS based utility classes */
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        button {
            width: 100%;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        p {
            margin-top: 20px;
            text-align: center;
        }

        a {
            color: #0056b3;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 500px) {
            .container {
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="container registration-container">
        <h2 class="text-2xl font-bold text-green-600 text-center mb-6 fade-in">Create an Account</h2>
        
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
                echo '<p class="text-red-600 text-center mb-4 slide-up">Password and confirm password do not match.</p>';
            } else {
                try {
                    // Perform registration logic here (e.g., insert data into a PostgreSQL database)
                    // Make sure to hash the password before storing it

                    // Database insert query goes here using PDO
                    $host = "localhost";
                    $db = "ev";
                    $user = "postgres";
                    $db_password = '5112';

                    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $db_password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Insert user data into the 'users' table
                    $sql = "INSERT INTO reg (user_name, password, email, mobile_no, usertype) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$username, $password, $email, $phoneNumber, $userType]);

                    echo '<p class="text-green-600 text-center mb-4 slide-up">Registration successful. Redirecting...</p>';
                    header("Location: login.php");
                } catch (PDOException $e) {
                    // Check if the exception message contains the trigger error
                    if (strpos($e->getMessage(), 'Email address must be unique across users, eprofile, and jprofile tables.') !== false) {
                        echo '<p class="text-red-600 text-center mb-4 slide-up">Email address must be unique across users, eprofile, and jprofile tables.</p>';
                    } else if (strpos($e->getMessage(), 'duplicate key value violates unique constraint "users_email_key"') !== false) {
                        echo '<p class="text-red-600 text-center mb-4 slide-up">Error: Email address is already in use. Please choose a different email.</p>';
                    } else {
                        // If it's another exception, display a generic error
                        echo '<p class="text-red-600 text-center mb-4 slide-up">Error: ' . $e->getMessage() . '</p>';
                    }
                }

                // Close the connection
                $pdo = null;
            }
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-4">
            <div class="slide-up">
                <label for="username" class="block text-blue-600">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="slide-up">
                <label for="password" class="block text-blue-600">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="slide-up">
                <label for="confirmPassword" class="block text-blue-600">Confirm Password:</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required>
            </div>

            <div class="slide-up">
                <label for="email" class="block text-blue-600">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="slide-up">
                <label for="phoneNumber" class="block text-blue-600">Phone Number:</label>
                <input type="text" id="phoneNumber" name="phoneNumber" required>
            </div>

            <div class="slide-up">
                <label for="userType" class="block text-blue-600">Select User Type:</label>
                <select id="userType" name="userType" required>
                    <option value="user">User</option>
                    <option value="operator">Operator</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-green-500 text-white p-2 rounded hover:bg-green-600 transition duration-300 slide-up">Register</button>
        </form>
        <p class="mt-4 text-center text-blue-600 slide-up">Already have an account? <a href="login.php" class="underline hover:text-blue-700">Login here</a></p>
    </div>

    <script>
        // Add a class to trigger the animation when the form is shown
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.registration-container').classList.add('show');
        });
    </script>
</body>
</html>
