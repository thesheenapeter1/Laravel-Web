<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 50px; }
        .header h1 { color: #b8860b; margin: 0; text-transform: uppercase; letter-spacing: 2px; }
        .info { margin-bottom: 30px; }
        .info table { width: 100%; }
        .info td { vertical-align: top; }
        .items { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items th { background: #f8f8f8; border-bottom: 2px solid #eee; padding: 10px; text-align: left; }
        .items td { border-bottom: 1px solid #eee; padding: 10px; }
        .totals { text-align: right; }
        .totals p { margin: 5px 0; font-size: 14px; }
        .totals .grand-total { font-size: 18px; font-weight: bold; color: #b8860b; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Aura by Kiyara</h1>
        <p>Premium Perfume Collection</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td>
                    <strong>Billed To:</strong><br>
                    {{ $order->customer_name }}<br>
                    {{ $order->customer_address }}<br>
                    {{ $order->customer_phone }}
                </td>
                <td style="text-align: right;">
                    <strong>Invoice Details:</strong><br>
                    Invoice #: {{ $order->id }}<br>
                    Date: {{ $order->ordered_date->format('d M Y') }}<br>
                    Status: {{ ucfirst($order->status) }}
                </td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>LKR {{ number_format($item->price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>LKR {{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p>Subtotal: LKR {{ number_format($order->total_amount - $order->delivery_fee, 2) }}</p>
        <p>Delivery Fee: LKR {{ number_format($order->delivery_fee, 2) }}</p>
        <div class="grand-total">Total: LKR {{ number_format($order->total_amount, 2) }}</div>
    </div>

    <div style="margin-top: 50px; text-align: center; color: #999; font-size: 12px;">
        <p>Thank you for shopping with Aura by Kiyara!</p>
        <p>Visit us again at aura-by-kiyara.com</p>
    </div>
</body>
</html>
