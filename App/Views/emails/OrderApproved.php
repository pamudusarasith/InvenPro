<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order Approved - InvenPro</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }

        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }

        .content {
            padding: 20px 0;
        }

        .order-details {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #4CAF50;
        }

        .order-details p {
            margin: 5px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table th {
            background-color: #f0f0f0;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #ddd;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            font-size: 0.8em;
            color: #777;
            border-top: 1px solid #eee;
        }

        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }

        .important {
            color: #e53935;
            font-weight: bold;
        }

        .total-row {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="<?php echo htmlspecialchars($logo_url ?? 'https://example.com/images/logo.png'); ?>" alt="InvenPro Logo" class="logo">
            <h1>Purchase Order Approved</h1>
        </div>
        <div class="content">
            <p>Dear <?php echo htmlspecialchars($supplier_name ?? 'Valued Supplier'); ?>,</p>

            <p>We are pleased to inform you that purchase order <strong><?php echo htmlspecialchars($order_reference ?? ''); ?></strong> has been approved and is now ready for processing.</p>

            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Reference:</strong> <?php echo htmlspecialchars($order_reference ?? ''); ?></p>
                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order_date ?? ''); ?></p>
                <p><strong>Expected Delivery Date:</strong> <?php echo htmlspecialchars($expected_date ?? 'As soon as possible'); ?></p>
                <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($shipping_address ?? ''); ?></p>
                <p><strong>Contact Person:</strong> <?php echo htmlspecialchars($contact_person ?? ''); ?></p>
                <p><strong>Contact Email:</strong> <?php echo htmlspecialchars($contact_email ?? ''); ?></p>
                <p><strong>Contact Phone:</strong> <?php echo htmlspecialchars($contact_phone ?? ''); ?></p>
            </div>

            <h3>Order Items</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($order_items) && is_array($order_items)): ?>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($item['unit_price'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($item['total_price'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Order items not available</td>
                        </tr>
                    <?php endif; ?>
                    <tr class="total-row">
                        <td colspan="4" style="text-align: right;"><strong>Total Amount:</strong></td>
                        <td><?php echo htmlspecialchars($order_total ?? '0.00'); ?></td>
                    </tr>
                </tbody>
            </table>

            <?php if (isset($special_instructions) && !empty($special_instructions)): ?>
                <div class="order-details" style="border-left-color: #FFA500;">
                    <h3>Special Instructions</h3>
                    <p><?php echo nl2br(htmlspecialchars($special_instructions)); ?></p>
                </div>
            <?php endif; ?>

            <p>Please process this order according to our agreed terms. If you have any questions or concerns regarding this order, please contact us immediately.</p>

            <?php if (isset($portal_url) && !empty($portal_url)): ?>
                <div style="text-align: center;">
                    <a href="<?php echo htmlspecialchars($portal_url); ?>" class="button">View Order Details in Supplier Portal</a>
                </div>
            <?php endif; ?>

            <p>Thank you for your continued partnership.</p>

            <p>Best regards,<br>
            <?php echo htmlspecialchars($company_name ?? 'InvenPro Team'); ?><br>
            <?php echo htmlspecialchars($company_address ?? ''); ?></p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please direct any questions to your account manager.</p>
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($company_name ?? 'InvenPro'); ?>. All rights reserved.</p>
        </div>
    </div>
</body>

</html>