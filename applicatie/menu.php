<?php
session_start(); 
require_once('../applicatie/db_connectie.php');
require_once('../applicatie/functions/security.php');

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

$userData = getUserData($conn, $_SESSION['username']);
if ($userData) {
    $fullName = $userData['full_name'];
    $userAddress = $userData['address'] ?? '';
} else {
    $_SESSION['order_error'] = "User data not found. Please check your session.";
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pizza Menu</title>
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

        .menu-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .menu-table th, .menu-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .menu-table th {
            background-color: #4CAF50;
            color: white;
        }

        .menu-table tr:hover {
            background-color: #f9f9f9;
        }

        .quantity-selector input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .address-form {
            margin: 20px 0;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .address-form textarea {
            width: 100%;
            max-width: 500px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .order-button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 20px 0;
            font-size: 16px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .order-button:hover {
            background-color: #45a049;
        }

        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .back-button {
            background-color: #333;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 20px 0;
            font-size: 16px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .logout-button {
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px; 
            cursor: pointer;
        }

        .login-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</head>
<body>

<form method="POST" action="logout.php" style="display: flex; justify-content: flex-end; margin: 20px 0;">
    <button type="submit" class="logout-button">Logout</button>
</form>
    <h1>Our Menu</h1>
    <form method="POST" action="">
        <?php
        $current_type = '';
        
        echo "<table class='menu-table'>";
        echo "<thead><tr>
                <th>Product</th>
                <th>Price</th>
                <th>Ingredients</th>
                <th>Quantity</th>
              </tr></thead><tbody>";
        
        while ($row = $currentMenuItems->fetch(PDO::FETCH_ASSOC)) {
            if ($current_type !== $row['type_id']) {
                $current_type = $row['type_id'];
                echo "<tr><td colspan='4'><h2>" . htmlspecialchars($current_type) . "</h2></td></tr>";
            }
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($row['product_name']); ?></strong></td>
                <td>€<?php echo number_format($row['price'], 2); ?></td>
                <td><em><?php echo !empty($row['ingredients']) ? htmlspecialchars($row['ingredients']) : 'No ingredients listed'; ?></em></td>
                <td>
                    <div class="quantity-selector">
                        <input type="number" name="quantities[<?php echo htmlspecialchars($row['product_name']); ?>]" 
                               value="0" min="0" max="10">
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody></table>

    
        <div class="address-form">
            <h3>Delivery Address</h3>
            <div class="form-group">
                <textarea name="address" placeholder="Please enter your full address" required><?php echo htmlspecialchars($userAddress); ?></textarea>
            </div>
        </div>
        
        <button type="submit" class="order-button">Place Order</button>
    </form>

    <h2>Your Current Orders</h2>
    <div class="orders-table">
            <table class="menu-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Delivery Address</th>
                        <th>Personnel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $currentOrdersStmt = getCurrentOrders($conn, $_SESSION['username']);
                    
                    while ($order = $currentOrdersStmt->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <tr>
                            <td><strong>#<?php echo htmlspecialchars($order['order_id']); ?></strong></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></td>
                            <td><?php echo htmlspecialchars($order['items']); ?></td>
                            <td>€<?php echo number_format($order['total'], 2); ?></td>
                            <td><?php echo getStatusText($order['status']); ?></td>
                            <td><?php echo htmlspecialchars($order['delivery_address']); ?></td>
                            <td><?php echo htmlspecialchars($order['personnel_username']); ?></td>
                        </tr>
                        <?php
                    }
                    if ($currentOrdersStmt->rowCount() === 0) {
                        echo "<tr><td colspan='6' style='text-align: center;'>No current orders</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
    </div>

    <button onclick="window.location.href='privacyverklaring.php'" class="back-button">Privacy Verklaring</button>
</body>
</html>

