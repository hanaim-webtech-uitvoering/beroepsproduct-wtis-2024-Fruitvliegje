<?php
session_start();
require_once('../applicatie/db_connectie.php');
require_once('../applicatie/functions/security.php');
error_reporting(0); 
ini_set('display_errors', 0); 
$error = '';



function authenticateUser($username, $password, $conn) {
    $query = "SELECT * FROM [User] WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$username]);
    
    if ($row = $stmt->fetch()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            return true;
        }
    }
    return false;
}

function authenticateUserWithoutHash($username, $conn) {
    $query = "SELECT * FROM [User] WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$username]);
    
    if ($row = $stmt->fetch()) {
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        return true;
    }
    return false;
}

function redirectUser() {
    $redirectPage = ($_SESSION['role'] === 'Personnel') ? "dashboard.php" : "menu.php";
    header("Location: $redirectPage");
    exit();
}

try {
    $conn = maakverbinding(); 

    checkSessionTimeout();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        startSecureSession();

        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if (authenticateUserWithoutHash($username, $conn)) {
            redirectUser();
        } else {
            $error = "Invalid username or password!";
        }
    }
} catch (Exception $e) {
    header("Location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
        .success {
            color: green;
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
    <h2>Login</h2>
    
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

        <button type="submit" class="submit-btn">Login</button>
    </form>
    
    <p>Don't have an account? <a href="registreren.php">Register here</a></p>
</body>
</html>