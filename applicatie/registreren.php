<?php
session_start();
require_once('../applicatie/db_connectie.php');
$conn = maakVerbinding();
$error = '';

function isUsernameTaken($conn, $username) {
    // Check if username already exists
    $checkQuery = "SELECT username FROM [User] WHERE username = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->execute([$username]);
    return $stmt->fetch() !== false; // Returns true if username exists, false otherwise
}

function isValidCredentialsLength($username, $password) {
    return strlen($username) >= 3 && strlen($password) >= 8;
}

function registerUser($conn, $username, $password, $firstName, $lastName, $address) {
    if (!isValidCredentialsLength($username, $password)) {
        return "Username must be at least 3 characters and password at least 8 characters.";
    }

    if (isUsernameTaken($conn, $username)) {
        return "Registration failed! Please try again."; 
    } else {
        // Insert new user
        $insertQuery = "INSERT INTO [User] (username, password, first_name, last_name, address, role) 
                        VALUES (?, ?, ?, ?, ?, 'Client')";
        $stmt = $conn->prepare($insertQuery);
        if ($stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $firstName, $lastName, $address])) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } else {
            return "Registration failed! Please try again.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $address = trim($_POST['address']);
    $error = registerUser($conn, $username, $password, $firstName, $lastName, $address);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Register</h2>
    
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div> 
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" required>
        </div>

        <div class="form-group">
            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" required>
        </div>

        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address">
        </div>

        <button type="submit" class="submit-btn">Register</button>
    </form>
    
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>