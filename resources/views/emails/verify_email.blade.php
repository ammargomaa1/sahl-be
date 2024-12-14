<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - TheFurnHub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eaeaea;
        }
        .header h1 {
            color: rgba(146, 89, 70, 0.85);
        }
        .content {
            padding: 20px 0;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            line-height: 1.5;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            font-size: 16px;
            color: #ffffff;
            background-color: rgba(146, 89, 70, 0.85);
            text-decoration: none!important;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TheFurnHub</h1>
        </div>
        <div class="content">
            <p>Hello, {{$name}}</p>
            <p>Thank you for registering with TheFurnHub! To complete the sign-up process, please verify your email address by clicking the button below:</p>
            <a href="{{ $url }}" class="button" style="text-decoration: none; color: #ffffff;">Verify Email Address</a>
            <p>If you did not create an account, no further action is required.</p>
        </div>
        <div class="footer">
            <p>If you have any questions, feel free to <a href="mailto:support@thefurnhub.com">contact our support team</a>.</p>
            <p>&copy; {{ date('Y') }} TheFurnHub. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
