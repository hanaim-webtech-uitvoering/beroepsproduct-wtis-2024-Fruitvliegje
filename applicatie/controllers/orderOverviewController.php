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

if (!isset($_GET['order_id'])) {
    header('Location: dashboard.php');
    exit();
}


function updateOrderStatus($conn, $order_id, $status, $personnel_username) {
    $updateQuery = "UPDATE Pizza_Order SET status = :status WHERE order_id = :order_id AND personnel_username = :personnel_username";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->execute([
        'status' => $status,
        'order_id' => $order_id,
        'personnel_username' => $personnel_username
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $personnel_username = $_SESSION['username']; 

    updateOrderStatus($conn, $order_id, $status, $personnel_username);
    header("Location: orderOverview.php?order_id=" . $order_id);
    exit();
}


function fetchOrderData($conn, $orderId, $username) {
    $query = "SELECT po.order_id, po.client_name, po.datetime, po.status, po.address, po.personnel_username, 
              STRING_AGG(CONCAT(pop.quantity, 'x ', pop.product_name), ', ') as order_items,
              SUM(pop.quantity * p.price) as total_amount
              FROM Pizza_Order po
              LEFT JOIN Pizza_Order_Product pop ON po.order_id = pop.order_id
              LEFT JOIN Product p ON pop.product_name = p.name
              WHERE po.order_id = :order_id AND po.personnel_username = :personnel_username
              GROUP BY po.order_id, po.client_name, po.datetime, po.status, po.address, po.personnel_username";

    $stmt = $conn->prepare($query);
    $stmt->execute([
        'order_id' => $orderId,
        'personnel_username' => $username
    ]);
    
    $orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($orderDetails) {
        $itemsQuery = "SELECT pop.*, p.price, (p.price * pop.quantity) as subtotal
                       FROM Pizza_Order_Product pop
                       JOIN Product p ON pop.product_name = p.name
                       WHERE pop.order_id = :order_id";

        $itemsStmt = $conn->prepare($itemsQuery);
        $itemsStmt->execute(['order_id' => $orderId]);
        $orderDetails['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    return $orderDetails;
}

try {
    $conn = maakVerbinding();
    $order = fetchOrderData($conn, $_GET['order_id'], $_SESSION['username']);

    if (!$order) {
        header('Location: dashboard.php');
        exit();
    }
    $orderItems = $order['items']; 

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>