<?php
session_start();
require_once('db_connectie.php');
error_reporting(0); 
ini_set('display_errors', 0); 
$conn = maakVerbinding();
$error = '';

function isUsernameTaken($conn, $username) {
    $checkQuery = "SELECT username FROM [User] WHERE username = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->execute([$username]);
    return $stmt->fetch() !== false; 
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
        $insertQuery = "INSERT INTO [User] (username, password, first_name, last_name, address, role) 
                        VALUES (?, ?, ?, ?, ?, 'Client')";
        $stmt = $conn->prepare($insertQuery);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if ($stmt->execute([$username, $hashedPassword, $firstName, $lastName, $address])) {
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
