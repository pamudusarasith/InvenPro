<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your New InvenPro Account</title>
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

        .content {
            padding: 20px 0;
        }

        .credentials {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #4a90e2;
        }

        .credentials p {
            margin: 5px 0;
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
            background-color: #4a90e2;
            color: black;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to InvenPro</h1>
        </div>
        <div class="content">
            <p>Hello,</p>

            <p>Your account for the InvenPro System has been created successfully.
                <?php if (isset($role) && !empty($role)): ?>
                    As a <?php echo htmlspecialchars($role); ?>, you now have access to our inventory management system.
                <?php else: ?>
                    You now have access to our inventory management system.
                <?php endif; ?>
            </p>

            <div class="credentials">
                <h3>Your Login Credentials</h3>
                <p><strong>Username/Email:</strong> <?php echo htmlspecialchars($email ?? ''); ?></p>
                <p><strong>Temporary Password:</strong> <?php echo htmlspecialchars($password ?? ''); ?></p>
            </div>

            <p><span class="important">Important:</span> For security reasons, you will be required to change this password when you first log in.</p>

            <p>To access the system, please click the button below:</p>

            <div style="text-align: center;">
                <a href="<?php echo htmlspecialchars($login_url ?? ''); ?>" class="button">Login to InvenPro</a>
            </div>

            <p>If you have any questions or need assistance, please contact your system administrator.</p>

            <p>Thank you,<br>
                The InvenPro Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; <?php echo date('Y'); ?> InvenPro. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
