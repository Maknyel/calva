<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%; /* thermal printer width */
            padding: 5px;
        }
        .header, .footer {
            text-align: center;
        }
        .logo {
            width: 60%;
            margin: 0 auto;
        }
        .ticket-info, .customer-info {
            margin-top: 5px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table td {
            padding: 2px 0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total {
            font-weight: bold;
        }
        hr {
            border: dashed 1px #000;
            margin: 5px 0;
        }
        .qr-code {
            width: 100px;
            margin: 5px auto;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Logo -->
    <div class="header">
        <img src="{{ asset('assets/images/calva.jpg') }}" class="logo" alt="Logo">
    </div>

    <!-- Ticket info -->
    <div class="ticket-info">
        <p>Ticket #{{ $order->first()->inventory_group_id }}</p>
        <p>{{ \Carbon\Carbon::now()->format('m/d/Y, h:i A') }}</p>
        <p>Calva Pharma</p>
    </div>

    <!-- <hr> -->

    <!-- Customer info -->
    <!-- <div class="customer-info">
        <p>{{ $customer_name ?? 'Customer' }}</p>
        <p>{{ $customer_address ?? '' }}</p>
    </div> -->

    <hr>

    <!-- Items table -->
    <table>
        @foreach($order as $item)
        <tr>
            <td>{{ $item->quantity_sold }} x {{ $item->name }}</td>
            <td class="text-right">₱ {{ number_format($item->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-size: 10px;">₱ {{ number_format($item->sale_price_cost, 2) }} / Unit</td>
        </tr>
        @endforeach
    </table>

    <hr>

    <!-- Grand total -->
    <table>
        <tr>
            <td class="total">Total</td>
            <td class="text-right total">₱ {{ number_format($inv_group->grand_total_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Cashless</td>
            <td class="text-right">₱ {{ number_format($inv_group->grand_total_amount, 2) }}</td>
        </tr>
    </table>

    <hr>

    <!-- QR code -->
    <!-- <div class="qr-code text-center">
        <img src="{{ public_path('images/qr.png') }}" alt="QR Code" width="100">
        <p style="font-size: 9px;">Need an invoice? https://calva-pharma/receipt/{{ $order->first()->inventory_group_id }}</p>
    </div> -->

    <hr>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your order!</p>
        <p>"THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAX."</p>
        <!-- <p>Calva Pharma | Tax ID: 409-604-053-00000 | bcdotg@gmail.com</p> -->
    </div>
</div>
</body>
</html>
