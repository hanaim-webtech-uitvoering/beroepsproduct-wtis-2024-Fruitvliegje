<?php include('../applicatie/controllers/orderOverviewController.php')?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?= htmlspecialchars($order['order_id']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .order-details {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
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

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="back-button">Back to Dashboard</a>

    <div class="order-details">
        <h1>Order Details #<?= htmlspecialchars($order['order_id']) ?></h1>
        <p><strong>Client Name:</strong> <?= htmlspecialchars($order['client_name']) ?></p>
        <p><strong>Delivery Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
        <p><strong>Order Date:</strong> <?= htmlspecialchars(date('Y-m-d H:i', strtotime($order['datetime']))) ?></p>
        <p><strong>Status:</strong> 
            <form method="POST" onclick="event.stopPropagation();" style="display: inline;">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                <select name="status" onchange="this.form.submit()">
                    <option value="1" <?= $order['status'] == 1 ? 'selected' : '' ?>>Not started</option>
                    <option value="2" <?= $order['status'] == 2 ? 'selected' : '' ?>>In Progress</option>
                    <option value="3" <?= $order['status'] == 3 ? 'selected' : '' ?>>Delivered</option>
                </select>
            </form>
        </p>
    </div>

    <h2>Order Items</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price per item</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orderItems)): ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No items found for this order.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orderItems as $orderItem): ?>
                    <?php 
                        $productName = htmlspecialchars($orderItem['product_name']);
                        $quantity = htmlspecialchars($orderItem['quantity']);
                        $price = number_format($orderItem['price'], 2);
                        $subtotal = number_format($orderItem['subtotal'], 2);
                    ?>
                    <tr>
                        <td><?= $productName ?></td>
                        <td><?= $quantity ?></td>
                        <td>€<?= $price ?></td>
                        <td>€<?= $subtotal ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr>
                <td colspan="3" style="text-align: right;"><strong>Total Amount:</strong></td>
                <?php 
                    $totalAmount = number_format($order['total_amount'], 2);
                ?>
                <td><strong>€<?= $totalAmount ?></strong></td>
            </tr>
        </tbody>
    </table>

   
</body>
</html>