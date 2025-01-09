<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - TheFurnHub</title>
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
            text-align: left;
        }
        .content p {
            font-size: 16px;
            line-height: 1.5;
        }
        .order-summary {
            margin-top: 20px;
            border-top: 1px solid #eaeaea;
            padding-top: 20px;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .order-table th, .order-table td {
            border: 1px solid #eaeaea;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }
        .order-table th {
            background-color: #f9f9f9;
        }
        .order-table img {
            max-width: 50px;
            border-radius: 5px;
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
            <p>Dear {{ $name }},</p>
            <p>Thank you for placing an order with TheFurnHub. We have received your order and are currently working on it. Below are the details of your purchase:</p>

            <div class="order-summary">
                <p><strong>Order ID:</strong> {{ $order_id }}</p>
                <p><strong>Order Date:</strong> {{ $order_date }}</p>

                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Image</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order_items as $item)
                        <tr>
                            <td>{{ $item->product->name_ar }}</td>
                            <td><img src="{{ env('API_URL') . '/api/images' . $item->productVariationImages->first()->image_path }}" alt="{{ $item->product->name_ar }}"></td>
                            <td>{{ $item->pivot->quantity }}</td>
                            <td>{{ $item->price->formatted() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <p><strong>Total Price:</strong> {{ $total_price->formatted() }}</p>
            </div>

            <p>We appreciate your trust in TheFurnHub and will notify you once your order has been shipped. If you have any questions or need further assistance, feel free to contact us.</p>
        </div>
        <div class="footer">
            <p>If you have any questions, feel free to <a href="mailto:support@thefurnhub.com">contact our support team</a>.</p>
            <p>&copy; {{ date('Y') }} TheFurnHub. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
