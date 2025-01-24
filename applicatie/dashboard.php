<?php
session_start();
require_once('../applicatie/db_connectie.php');
require_once('../applicatie/functions/security.php');
startSecureSession();
checkSessionTimeout();
checkIfPersonnel();
checkUserLoggedIn();
$personnel_username = $_SESSION['username'];


function updateOrderStatus($conn, $order_id, $status, $personnel_username) {
    $updateQuery = "UPDATE Pizza_Order SET status = :status WHERE order_id = :order_id AND personnel_username = :personnel_username";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->execute([
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
    
    $stmt = $conn->prepare($query);
    $stmt->execute(['personnel_username' => $personnel_username]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

try {
    $conn = maakVerbinding();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
        updateOrderStatus($conn, $_POST['order_id'], $_POST['status'], $personnel_username);
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    $orders = fetchOrders($conn, $personnel_username);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnel Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        h1, h2 {
            color: #333;
            text-align: center;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #4CAF50;
            color: white;
        }

        .table tr:hover {
            background-color: #f9f9f9;
        }

        .back-button {
            background-color: #333;
            color: white;
            border: none;
            text-decoration: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }


        .logout-button {
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Assigned Orders</h1>

        <form method="POST" action="logout.php" style="text-align: right">
            <button type="submit" class="logout-button">Logout</button>
        </form>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Client Name</th>
                    <th>Order Items</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Delivery Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr onclick="window.location='bestellingOverzicht.php?order_id=<?= htmlspecialchars($order['order_id']) ?>'" style="cursor: pointer;">
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= htmlspecialchars($order['client_name']) ?></td>
                        <td><?= htmlspecialchars($order['order_items']) ?></td>
                        <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($order['datetime']))) ?></td>
                        <td>
                            <form method="POST" style="margin: 0;" onclick="event.stopPropagation();">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="1" <?= $order['status'] == 1 ? 'selected' : '' ?>>Not started</option>
                                    <option value="2" <?= $order['status'] == 2 ? 'selected' : '' ?>>In Progress</option>
                                    <option value="3" <?= $order['status'] == 3 ? 'selected' : '' ?>>Delivered</option>
                                </select>
                            </form>
                        </td>
                        <td><?= htmlspecialchars($order['address']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
