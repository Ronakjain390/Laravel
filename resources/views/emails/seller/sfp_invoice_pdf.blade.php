<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Email Template</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
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
        .content {
            padding: 20px;
        }
        .button-container {
            text-align: center;
            margin: 20px 0;
        }
        .button {
            background-color: #476a7b;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
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
            <h1 style="font-size: large" >TheParchi</h1>
        </div>
        <div class="content">
            <p>Hello, {{ ucfirst($userName)  }}</p>
            <p>You have received a new SFP Request from {{ $invoice->sellerUser->name }}, Invoice Number: {{ strtoupper($invoice->invoice_series) }}-{{ $invoice->series_num }}</p>
            <p>Please login to your <a href="https://theparchi.com/">theparchi.com</a> account to take further action.</p>
            <div class="button-container">
                <a href="{{ Storage::disk('s3')->temporaryUrl($invoice->pdf_url, now()->addMinutes(5)) }}" class="button" style="color: #ffffff">View Invoice</a>
            </div>
            <p>Thank you for using TheParchi.</p>
            <p>If you have any questions, feel free to message us at contact@theparchi.com.</p>
        </div>
        <div class="footer">
            Copyright © 2024 | TheParchi
        </div>
    </div>
</body>
</html>
