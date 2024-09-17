<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Demande Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #444;
        }
        p {
            line-height: 1.5;
            margin: 10px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>New Demande Submitted</h1>
    <p>A new demande has been created with the following details:</p>
    <ul>
        <li>Total Amount: {{ $demande->total_amount}} FCFA</li>
        <li>Description: {{ $demande->description ?? 'N/A' }}</li>
        <li>Client ID: {{ $demande->client_id }}</li>
    </ul>
    <p>If you have any questions about this demande, please contact our support team.</p>
</div>
</body>
</html>
