<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - Order #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .invoice-title {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .invoice-details {
            margin-bottom: 25px;
        }
        .invoice-details p {
            margin: 3px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .table th, .table td {
            padding: 7px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .table th {
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            width: 50%;
            margin-left: auto;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="invoice-title">Invoice</h1>
            <p>Order #{{ $order->order_number }}</p>
            <p>Date: {{ $order->created_at->format('M d, Y') }}</p>
        </div>

        <div class="invoice-details">
            <div style="float: left; width: 50%;">
                <h3>Billed To:</h3>
                @if($order->billingAddress)
                    <p>{{ $order->billingAddress->name }}</p>
                    <p>{{ $order->billingAddress->street_address }}</p>
                    <p>{{ $order->billingAddress->city }}, {{ $order->billingAddress->state }} {{ $order->billingAddress->postal_code }}</p>
                    <p>{{ $order->billingAddress->country }}</p>
                    <p>Phone: {{ $order->billingAddress->phone }}</p>
                @endif
            </div>
            <div style="float: right; width: 50%;">
                <h3>Shipped To:</h3>
                @if($order->shippingAddress)
                    <p>{{ $order->shippingAddress->name }}</p>
                    <p>{{ $order->shippingAddress->street_address }}</p>
                    <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                    <p>{{ $order->shippingAddress->country }}</p>
                    <p>Phone: {{ $order->shippingAddress->phone }}</p>
                @endif
            </div>
            <div style="clear: both;"></div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->product ? $item->product->name : '[Deleted Product]' }}
                            @if($item->customization_details)
                                <br>
                                <small>
                                    @foreach($item->customization_details as $key => $value)
                                        {{ $key }}: {{ $value }}
                                        @if(!$loop->last), @endif
                                    @endforeach
                                </small>
                            @endif
                        </td>
                        <td>₹{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="table totals">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">₹{{ number_format($order->items->sum('subtotal'), 2) }}</td>
            </tr>
            <tr>
                <td>Shipping:</td>
                <td class="text-right">
                    {{ $order->shipping_cost > 0 ? '₹' . number_format($order->shipping_cost, 2) : 'Free' }}
                </td>
            </tr>
            <tr>
                <td>Tax (10%):</td>
                <td class="text-right">₹{{ number_format($order->items->sum('subtotal') * 0.1, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td class="text-right"><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>{{ config('app.name') }} - {{ config('app.url') }}</p>
        </div>
    </div>
</body>
</html> 