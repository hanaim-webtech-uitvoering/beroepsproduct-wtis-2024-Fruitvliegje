<?php
require_once('db_connectie.php');
require_once('../applicatie/functions/security.php');
session_start();
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

function redirectUser() {
    $redirectPage = ($_SESSION['role'] === 'Personnel') ? "dashboard.php" : "menu.php";
    header("Location: $redirectPage");
    exit();
}

try {
    $conn = maakverbinding(); 


    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if (authenticateUser($username, $password, $conn)) {
            redirectUser();
        } else {
            $error = "Invalid username or password!";
        }
    }
} catch (Exception $e) {
    header('Location: ../error.php'); 
    exit();
}
?>