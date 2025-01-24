<?php
session_start();
require_once('db_connectie.php');
require_once('../applicatie/functions/security.php');
error_reporting(0); 
ini_set('display_errors', 0); 
startSecureSession();
checkSessionTimeout();
checkIfPersonnel();
checkUserLoggedIn();
$conn = maakVerbinding();
$personnel_username = $_SESSION['username'];

function executeQuery($conn, $query, $params) {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    return $stmt;
}

function updateOrderStatus($conn, $order_id, $status, $personnel_username) {
    $updateQuery = "UPDATE Pizza_Order SET status = :status WHERE order_id = :order_id AND personnel_username = :personnel_username";
    executeQuery($conn, $updateQuery, [
        'status' => $status,
        'order_id' => $order_id,
        'personnel_username' => $personnel_username
    ]);
}

function fetchOrders($conn, $personnel_username) {
    $query = "SELECT po.order_id, po.client_name, po.datetime, po.status, po.address,
              STRING_AGG(CONCAT(pop.quantity, 'x ', pop.product_name), ', ') as order_items
              FROM Pizza_Order po
              LEFT JOIN Pizza_Order_Product pop ON po.order_id = pop.order_id
              WHERE po.personnel_username = :personnel_username
              GROUP BY po.order_id, po.client_name, po.datetime, po.status, po.address
              ORDER BY po.datetime DESC";
    
    $stmt = executeQuery($conn, $query, ['personnel_username' => $personnel_username]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
        updateOrderStatus($conn, $_POST['order_id'], $_POST['status'], $personnel_username);
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    $orders = fetchOrders($conn, $personnel_username);

} catch (PDOException $e) {
    header('Location: ../error.php'); 
    exit();
}
?>