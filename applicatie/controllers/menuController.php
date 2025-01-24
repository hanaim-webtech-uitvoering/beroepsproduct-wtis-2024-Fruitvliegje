<?php
session_start(); 
require_once('db_connectie.php');
require_once('../applicatie/functions/security.php');
error_reporting(0); 
ini_set('display_errors', 0); 
startSecureSession();
checkSessionTimeout();
checkUserLoggedIn();
$conn = maakVerbinding();


function getStatusText($status) {
    switch ($status) {
        case 1: return 'Pending';
        case 2: return 'In Progress';
        case 3: return 'Delivered';
        default: return 'Unknown';
    }
}

function getUserData($conn, $username) {
    $userQuery = "SELECT first_name, last_name, address FROM [User] WHERE username = ?";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->execute([$username]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userData) {
        $userData['full_name'] = $userData['first_name'] . ' ' . $userData['last_name'];
    }
    
    return $userData;
}

function getRandomPersonnelUsername($conn) {
    $personnelQuery = "SELECT TOP 1 username FROM [User] WHERE role = 'Personnel' ORDER BY NEWID()";
    $personnelStmt = $conn->prepare($personnelQuery);
    $personnelStmt->execute();
    return $personnelStmt->fetchColumn();
}

function createOrder($conn, $username, $fullName, $personnelUsername, $userAddress) {
    $orderQuery = "INSERT INTO Pizza_Order (client_username, client_name, personnel_username, datetime, status, address) 
                 VALUES (?, ?, ?, GETDATE(), 1, ?)";
    $orderStmt = $conn->prepare($orderQuery);
    $orderStmt->execute([$username, $fullName, $personnelUsername, $userAddress]);
    return $conn->lastInsertId();
}

function placeOrder($conn, $username, $userAddress) {
    $userData = getUserData($conn, $username);
    $fullName = $userData['full_name'];

    $personnelUsername = getRandomPersonnelUsername($conn);
    
    return createOrder($conn, $username, $fullName, $personnelUsername, $userAddress);
}

function insertOrderItems($conn, $orderId, $quantities) {
    $itemsQuery = "INSERT INTO Pizza_Order_Product (order_id, product_name, quantity) VALUES (?, ?, ?)";
    $itemsStmt = $conn->prepare($itemsQuery);
    
    foreach ($quantities as $productName => $quantity) {
        if ($quantity > 0) {
            $itemsStmt->execute([$orderId, $productName, $quantity]);
        }
    }
}

function getMenuItems($conn) {
    $query = "
        SELECT 
            p.name AS product_name,
            p.price,
            p.type_id,
            STRING_AGG(i.name, ', ') AS ingredients
        FROM 
            Product p
            LEFT JOIN Product_Ingredient pi ON p.name = pi.product_name
            LEFT JOIN Ingredient i ON pi.ingredient_name = i.name
        GROUP BY 
            p.name, p.price, p.type_id
        ORDER BY 
            p.type_id, p.name
    ";
    return $conn->query($query);
}

function getCurrentOrders($conn, $username) {
    $currentOrdersQuery = "
        SELECT po.order_id, po.datetime as order_date, po.status, po.address as delivery_address,
               STRING_AGG(CONCAT(pop.quantity, 'x ', pop.product_name), ', ') as items,
               SUM(pop.quantity * p.price) as total,
               po.personnel_username
        FROM Pizza_Order po
        JOIN Pizza_Order_Product pop ON po.order_id = pop.order_id
        JOIN Product p ON pop.product_name = p.name
        WHERE po.client_username = ? AND po.status != 3
        GROUP BY po.order_id, po.datetime, po.status, po.address, po.personnel_username
        ORDER BY po.datetime DESC
    ";
    $currentOrdersStmt = $conn->prepare($currentOrdersQuery);
    $currentOrdersStmt->execute([$username]);
    return $currentOrdersStmt;
}

function displaySessionMessage($sessionKey, $cssClass) {
    if (isset($_SESSION[$sessionKey])) {
        echo '<div class="' . htmlspecialchars($cssClass) . '">' . htmlspecialchars($_SESSION[$sessionKey]) . '</div>';
        unset($_SESSION[$sessionKey]);
    }
}

function isOrderEmpty($quantities) {
    foreach ($quantities as $quantity) {
        if ($quantity > 0) {
            return false; 
        }
    }
    return true; 
}

function initializeUserData($conn) {
    $userData = getUserData($conn, $_SESSION['username']);
    if ($userData) {
        return [
            'fullName' => $userData['full_name'],
            'userAddress' => $userData['address'] ?? ''
        ];
    } else {
        $_SESSION['order_error'] = "User data not found. Please check your session.";
        return null;
    }
}

$userData = initializeUserData($conn);
if ($userData) {
    $fullName = $userData['fullName'];
    $userAddress = $userData['userAddress'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAddress = $_POST['address'] ?? '';
    $quantities = $_POST['quantities'] ?? [];

    if (isOrderEmpty($quantities)) {
        $_SESSION['order_error'] = "You must select at least one item to place an order.";
    } else {
        $orderId = placeOrder($conn, $_SESSION['username'], $userAddress);
        insertOrderItems($conn, $orderId, $quantities);
        
        $_SESSION['order_message'] = "Order #" . $orderId . " placed successfully!";
        header("Location: menu.php");
        exit();
    }
}

displaySessionMessage('order_error', 'error-message');
displaySessionMessage('order_message', 'success-message');
$currentMenuItems = getMenuItems($conn);
?>