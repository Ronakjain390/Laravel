<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Update Notification</title>
    <style>
        /* Add any additional styles here */
        .email-container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            color: #ffffff;
            padding: 5px;
        }
        .header {
            background-color: #ebebeb;
            color: #7449f0;
            text-align: center;
            padding: 15px 11px 0;
            border-bottom: 1px solid black;
        }
        .header img {
            max-width: 35px;
            width: 100%;
            border: none;
            display: inline-block !important;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
            clear: both;
        }
        .footer {
            background-color: #333333;
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 12px;
        }
        @media screen and (max-width: 600px) {
            .content, .button-container, .footer {
                padding: 15px;
            }
            .button {
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <!-- Replace with your logo -->
            <img align="center" src="{{ asset('image/Vector.png') }}" alt="Logo" title="Logo">
            <h1 style="font-size: large">TheParchi</h1>
        </div>
        <div class="content">
            <p>Hello, {{ ucfirst($data['userName']) }}</p>
            <p>This is to inform you that there has been a stock {{ ucfirst($data['action']) }} in your inventory.</p>
            <p>Details:</p>
            <ul>
                <li>Action: {{ ucfirst($data['action']) }}</li>
                <li>Number of products {{ $data['action'] }}: {{ $data['count'] }}</li>
                @if($data['teamMemberName'])
                    <li>Team Member: {{ $data['teamMemberName'] }}</li>
                @endif
                <li>Timestamp: {{ $data['timestamp'] }}</li>
            </ul>
            <p>Please login to your <a href="https://theparchi.com/">theparchi.com</a> account to view the updated inventory.</p>
            <p>Thank you for using TheParchi.</p>
            <p>If you have any questions, feel free to message us at contact@theparchi.com.</p>
        </div>
        <div class="footer">
            Copyright © 2024 | TheParchi
        </div>
    </div>
</body>
</html>
