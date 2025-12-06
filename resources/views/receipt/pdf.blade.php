<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Receipt #{{ $order->first()->inventory_group_id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .container {
            width: 100%;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            width: 60%;
            margin: 0 auto 8px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }

        .company-address {
            font-size: 10px;
            margin: 3px 0;
            line-height: 1.3;
        }

        .ticket-info {
            text-align: center;
            margin: 10px 0;
            font-size: 11px;
        }

        .ticket-number {
            font-size: 13px;
            font-weight: bold;
            margin: 3px 0;
        }

        .customer-info {
            margin: 10px 0;
            padding: 8px 5px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }

        .customer-info p {
            margin: 3px 0;
            font-size: 11px;
        }

        .customer-label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
            vertical-align: top;
        }

        .customer-label-data {
            display: inline-block;
            width: 80px;
            vertical-align: top;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .items-table td {
            padding: 4px 2px;
            vertical-align: top;
        }

        .items-table thead td:first-child {
            width: 60%;
        }

        .items-table thead td:last-child {
            width: 40%;
        }

        .item-row {
            border-bottom: 1px solid #eee;
        }

        .item-name {
            font-weight: 500;
        }

        .item-details {
            font-size: 10px;
            color: #555;
            padding-left: 15px !important;
        }

        .summary-table {
            width: 100%;
            margin: 10px 0;
        }

        .summary-table td {
            padding: 3px 2px;
        }

        .summary-table td:first-child {
            width: 60%;
        }

        .summary-table td:last-child {
            width: 40%;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #000;
            padding-top: 5px !important;
        }

        .payment-row {
            font-size: 11px;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        hr.solid {
            border-top: 1px solid #000;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
        }

        .footer p {
            margin: 5px 0;
            font-size: 10px;
        }

        .thank-you {
            font-size: 12px;
            font-weight: bold;
        }

        .tax-notice {
            font-size: 9px;
            font-style: italic;
        }

        .qr-code {
            margin: 10px auto;
            text-align: center;
        }

        .qr-code img {
            width: 100px;
            height: 100px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            @if($company_logo)
            <img src="{{ public_path($company_logo) }}" class="logo" alt="Logo">
            @endif
            <div class="company-name">{{ $company_name }}</div>
            <div class="company-address">
                {{ $company_address }}<br>
                @if($company_phone)Tel: {{ $company_phone }}@endif @if($company_email)| Email: {{ $company_email }}@endif<br>
                @if($company_tax_id)Tax ID: {{ $company_tax_id }}@endif
            </div>
        </div>

        <hr class="solid">

        <!-- Receipt Info -->
        <div class="ticket-info">
            <p class="ticket-number">Receipt #{{ $order->first()->inventory_group_id }}</p>
            <p>Date: {{ \Carbon\Carbon::now()->format('F d, Y') }}</p>
            <p>Time: {{ \Carbon\Carbon::now()->format('h:i A') }}</p>
        </div>

        <hr>

        <!-- Customer Information -->
        <div class="customer-info">
            <p><span class="customer-label">Customer:</span>
                <span class="customer-label-data"> {{ $customer_name ?? 'Walk-in Customer' }}</span>
            </p>
            @if(isset($customer_address) && $customer_address)
            <p><span class="customer-label">Address:</span>
                <span class="customer-label-data"> {{ $customer_address }}</span>
            </p>
            @endif
            @if(isset($customer_phone) && $customer_phone)
            <p><span class="customer-label">Phone:</span>
                <span class="customer-label-data"> {{ $customer_phone }}</span>
            </p>
            @endif
        </div>

        <hr>

        <!-- Items Purchased -->
        <table class="items-table">
            <thead>
                <tr>
                    <td style="font-weight: bold; padding-bottom: 5px;">Item</td>
                    <td style="font-weight: bold; text-align: right; padding-bottom: 5px;">Amount</td>
                </tr>
            </thead>
            <tbody>
                @foreach($order as $item)
                <tr class="item-row">
                    <td class="item-name">{{ $item->quantity_sold }} x {{ $item->name }}</td>
                    <td class="text-right">₱ {{ number_format($item->total, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="item-details">
                        @ ₱ {{ number_format($item->sale_price_cost, 2) }} per unit
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr class="solid">

        <!-- Summary Section -->
        <table class="summary-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">₱ {{ number_format($inv_group->grand_total_amount, 2) }}</td>
            </tr>
            @if(isset($discount) && $discount > 0)
            <tr class="payment-row">
                <td>Discount:</td>
                <td class="text-right">- ₱ {{ number_format($discount, 2) }}</td>
            </tr>
            @endif
            @if(isset($tax) && $tax > 0)
            <tr class="payment-row">
                <td>Tax:</td>
                <td class="text-right">₱ {{ number_format($tax, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>TOTAL:</td>
                <td class="text-right">₱ {{ number_format($inv_group->grand_total_amount, 2) }}</td>
            </tr>
            <tr class="payment-row">
                <td>Payment Method:</td>
                <td class="text-right">{{ $payment_method ?? 'Cashless' }}</td>
            </tr>
            @if(isset($amount_paid) && $amount_paid > 0)
            <tr class="payment-row">
                <td>Amount Paid:</td>
                <td class="text-right">₱ {{ number_format($amount_paid, 2) }}</td>
            </tr>
            <tr class="payment-row">
                <td>Change:</td>
                <td class="text-right">₱ {{ number_format($amount_paid - $inv_group->grand_total_amount, 2) }}</td>
            </tr>
            @endif
        </table>

        <hr>

        <!-- QR Code Section (Optional) -->
        @if(isset($show_qr) && $show_qr)
        <div class="qr-code">
            <img src="{{ public_path('images/qr.png') }}" alt="QR Code">
            <p style="font-size: 9px;">Scan for digital receipt<br>
                https://calva-pharma.com/receipt/{{ $order->first()->inventory_group_id }}</p>
        </div>
        <hr>
        @endif

        <!-- Footer Section -->
        <div class="footer">
            <p class="thank-you">Thank you for your purchase!</p>
            <p>We appreciate your business</p>
            <p class="tax-notice">"THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAX."</p>
            @if(isset($footer_message) && $footer_message)
            <p style="margin-top: 8px;">{{ $footer_message }}</p>
            @endif
        </div>
    </div>
</body>

</html>