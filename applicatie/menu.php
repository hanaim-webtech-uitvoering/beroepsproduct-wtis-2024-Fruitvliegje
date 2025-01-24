<?php include('../applicatie/controllers/menuController.php') ?>

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

<form method="POST" action="../controllers/logout.php" style="display: flex; justify-content: flex-end; margin: 20px 0;">
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

