<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .footer {
            font-size: 12px;
            color: #777777;
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #dddddd;
        }
        .user-details {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #dddddd;
            border-radius: 4px;
        }
        .user-details p {
            margin: 0;
            padding: 5px 0;
        }
        .user-details .label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $subject }}</h1>
        </div>
        <div class="content">
            <div class="user-details">
                <p><span class="label">Name:</span> {{ $userName }}</p>
                <p><span class="label">Email:</span> {{ $userEmail }}</p>
                <p><span class="label">Phone:</span> {{ $userPhone }}</p>
            </div>
            <p>{{ $messages }}</p>
        </div>
        <div class="footer">
            <p>Thank you for your attention.</p>
            <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
