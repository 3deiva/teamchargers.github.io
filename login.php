<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EV-Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
            color: green;
        }

        .login-container {
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
    </style>
</head>
<body>
    <div class="login-container p-6 bg-white bg-opacity-90 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-bold text-green-600 text-center mb-6 fade-in">EV COMPANION</h2>

        <?php
        session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $phone_number = $_POST["phone_number"];
            $password = $_POST["password"];
            $userType = $_POST["userType"];

            $host = "localhost";
            $db = "ev";
            $port='5432';
            $user = "postgres";
            $db_password = '5112';

            try {
                $pdo = new PDO(
    "pgsql:host=dpg-d4mmlbq4d50c73eq0gs0-a.oregon-postgres.render.com;port=5432;dbname=ev_8jta",
    "ev_8jta_user",
    "JMxhcUXabU16VLJjiyew6oxGgxJm4Boq"
);

                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $sql = "SELECT userid, user_name, password, usertype FROM reg WHERE mobile_no = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$phone_number]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row && $password == $row['password'] && $userType == $row['usertype']) {
                    $_SESSION['user_type'] = $row['usertype'];
                    $_SESSION['username'] = $row['user_name'];
                    $_SESSION['contact'] = $phone_number;

                    echo '<p class="text-green-600 text-center mb-4 slide-up">Login successful. Redirecting...</p>';
                    if ($userType == 'user') {
                        header("Refresh: 2; URL=hu.php");
                    } else {
                        header("Refresh: 2; URL=operator.php");
                    }
                    exit();
                } else {
                    echo '<p class="text-red-600 text-center mb-4 slide-up">Invalid phone number, password, or user type.</p>';
                }
            } catch (PDOException $e) {
                echo '<p class="text-red-600 text-center mb-4 slide-up">Error: ' . $e->getMessage() . '</p>';
            }

            $pdo = null;
        }
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-4">
            <div class="slide-up">
                <label for="phone_number" class="block text-blue-600">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" required class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="slide-up">
                <label for="password" class="block text-blue-600">Password:</label>
                <input type="password" id="password" name="password" required class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="slide-up">
                <label for="userType" class="block text-blue-600">Select User Type:</label>
                <select id="userType" name="userType" required class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="user">User</option>
                    <option value="operator">Operator</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-green-500 text-white p-2 rounded hover:bg-blue-600 transition duration-300 slide-up">Login</button>
        </form>

        <p class="mt-4 text-center text-blue-600 slide-up">Don't have an account? <a href="reg.php" class="underline hover:text-blue-700">Register here</a></p>
    </div>
</body>
</html>

