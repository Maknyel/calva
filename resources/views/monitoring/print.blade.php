<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Report - {{ date('F d, Y', strtotime($dateFrom)) }} to {{ date('F d, Y', strtotime($dateTo)) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            color: #7c3aed;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #666;
        }

        .summary-section {
            margin-bottom: 30px;
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
        }

        .summary-section h2 {
            font-size: 16px;
            color: #7c3aed;
            margin-bottom: 15px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
        }

        .summary-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border-left: 3px solid #7c3aed;
        }

        .summary-item .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #7c3aed;
        }

        .customer-section {
            page-break-inside: avoid;
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .customer-header {
            background-color: #ede9fe;
            padding: 12px 15px;
            border-bottom: 2px solid #7c3aed;
        }

        .customer-name {
            font-size: 16px;
            font-weight: bold;
            color: #7c3aed;
            margin-bottom: 5px;
        }

        .customer-info {
            font-size: 11px;
            color: #666;
        }

        .customer-totals {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            padding: 10px 15px;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .customer-totals .total-item {
            text-align: center;
        }

        .customer-totals .total-label {
            font-size: 10px;
            color: #666;
        }

        .customer-totals .total-value {
            font-size: 13px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background-color: #f3f4f6;
        }

        table th {
            padding: 8px;
            text-align: left;
            font-size: 11px;
            color: #374151;
            font-weight: 600;
            border-bottom: 2px solid #d1d5db;
        }

        table th.text-right {
            text-align: right;
        }

        table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        table td.text-right {
            text-align: right;
        }

        table tbody tr:hover {
            background-color: #f9fafb;
        }

        .cost { color: #2563eb; }
        .sales { color: #16a34a; }
        .profit { color: #7c3aed; font-weight: 600; }

        .grand-total {
            margin-top: 30px;
            padding: 15px;
            background-color: #ede9fe;
            border-radius: 8px;
            border: 2px solid #7c3aed;
        }

        .grand-total h3 {
            font-size: 16px;
            color: #7c3aed;
            margin-bottom: 10px;
            text-align: center;
        }

        .grand-total-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
        }

        .grand-total-item {
            text-align: center;
        }

        .grand-total-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }

        .grand-total-value {
            font-size: 20px;
            font-weight: bold;
            color: #7c3aed;
        }

        @media print {
            body {
                padding: 10px;
            }

            .customer-section {
                page-break-inside: avoid;
            }

            @page {
                size: landscape;
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CUSTOMER ORDERS MONITORING REPORT</h1>
        <p>Date Range: {{ date('F d, Y', strtotime($dateFrom)) }} - {{ date('F d, Y', strtotime($dateTo)) }}</p>
        <p>Generated on: {{ date('F d, Y h:i A') }}</p>
    </div>

    <div class="summary-section">
        <h2>Summary</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total Cost (Kita)</div>
                <div class="value">₱{{ number_format($summary['total_kita'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Sales (Benta)</div>
                <div class="value">₱{{ number_format($summary['total_benta'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Profit</div>
                <div class="value">₱{{ number_format($summary['total_profit'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Orders</div>
                <div class="value">{{ $summary['total_orders'] }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Total Items</div>
                <div class="value">{{ $summary['total_quantity'] }}</div>
            </div>
        </div>
    </div>

    @foreach($ordersByCustomer as $customer)
    <div class="customer-section">
        <div class="customer-header">
            <div class="customer-name">{{ $customer['customer_name'] }}</div>
            <div class="customer-info">
                Phone: {{ $customer['customer_phone'] }} |
                Address: {{ $customer['customer_address'] }}
            </div>
        </div>

        <div class="customer-totals">
            <div class="total-item">
                <div class="total-label">Orders</div>
                <div class="total-value">{{ $customer['order_count'] }}</div>
            </div>
            <div class="total-item">
                <div class="total-label">Items</div>
                <div class="total-value">{{ $customer['total_quantity'] }}</div>
            </div>
            <div class="total-item">
                <div class="total-label">Total Cost</div>
                <div class="total-value cost">₱{{ number_format($customer['total_cost'], 2) }}</div>
            </div>
            <div class="total-item">
                <div class="total-label">Total Sales</div>
                <div class="total-value sales">₱{{ number_format($customer['total_sales'], 2) }}</div>
            </div>
            <div class="total-item">
                <div class="total-label">Total Profit</div>
                <div class="total-value profit">₱{{ number_format($customer['profit'], 2) }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Payment</th>
                    <th class="text-right">Items</th>
                    <th class="text-right">Cost</th>
                    <th class="text-right">Sales</th>
                    <th class="text-right">Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customer['orders'] as $order)
                <tr>
                    <td>#{{ $order['order_id'] }}</td>
                    <td>{{ date('M d, Y h:i A', strtotime($order['order_date'])) }}</td>
                    <td>{{ $order['payment_method'] }}</td>
                    <td class="text-right">{{ $order['total_quantity'] }}</td>
                    <td class="text-right cost">₱{{ number_format($order['total_cost'], 2) }}</td>
                    <td class="text-right sales">₱{{ number_format($order['total_sales'], 2) }}</td>
                    <td class="text-right profit">₱{{ number_format($order['profit'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    <div class="grand-total">
        <h3>GRAND TOTAL</h3>
        <div class="grand-total-grid">
            <div class="grand-total-item">
                <div class="grand-total-label">Total Customers</div>
                <div class="grand-total-value">{{ count($ordersByCustomer) }}</div>
            </div>
            <div class="grand-total-item">
                <div class="grand-total-label">Total Orders</div>
                <div class="grand-total-value">{{ $summary['total_orders'] }}</div>
            </div>
            <div class="grand-total-item">
                <div class="grand-total-label">Total Cost</div>
                <div class="grand-total-value">₱{{ number_format($summary['total_kita'], 2) }}</div>
            </div>
            <div class="grand-total-item">
                <div class="grand-total-label">Total Sales</div>
                <div class="grand-total-value">₱{{ number_format($summary['total_benta'], 2) }}</div>
            </div>
            <div class="grand-total-item">
                <div class="grand-total-label">Total Profit</div>
                <div class="grand-total-value">₱{{ number_format($summary['total_profit'], 2) }}</div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
