<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Notification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
            padding: 40px 20px;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background-color: #ffffff;
            padding: 40px 40px 20px 40px;
            text-align: center;
        }
        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
        }
        .logo svg {
            width: 100%;
            height: 100%;
        }
        .email-title {
            color: #1f2937;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 30px;
        }
        .email-body {
            padding: 0 40px 40px 40px;
        }
        .info-section {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
        }
        .info-label {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 6px;
        }
        .info-value {
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .status-badge {
            display: inline-block;
            background-color: #fef3c7;
            color: #92400e;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            margin-top: 4px;
        }
        .amount-section {
            margin: 20px 0;
        }
        .amount-value {
            color: #1f2937;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .due-date {
            color: #6b7280;
            font-size: 14px;
        }
        .invoice-number {
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
        }
        .notes-section {
            margin: 20px 0;
        }
        .notes-label {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .notes-content {
            color: #4b5563;
            font-size: 14px;
            line-height: 1.6;
        }
        .cta-section {
            text-align: left;
            margin: 30px 0 20px 0;
        }
        .cta-text {
            color: #4b5563;
            font-size: 14px;
            margin-bottom: 16px;
        }
        .btn-view-invoice {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        .btn-view-invoice:hover {
            background-color: #1d4ed8;
        }
        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 20px 0;
        }
        .url-section {
            margin-top: 20px;
        }
        .url-label {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 8px;
        }
        .url-link {
            color: #2563eb;
            font-size: 13px;
            word-break: break-all;
            text-decoration: none;
        }
        .url-link:hover {
            text-decoration: underline;
        }
        .email-footer {
            padding: 20px 40px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            color: #6b7280;
            font-size: 12px;
        }
        .powered-by {
            color: #9ca3af;
            font-size: 11px;
            margin-top: 8px;
        }
        @media only screen and (max-width: 600px) {
            .email-header,
            .email-body,
            .email-footer {
                padding-left: 24px;
                padding-right: 24px;
            }
            .amount-value {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">
                <!-- Company Logo or Icon -->
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <rect x="10" y="30" width="80" height="50" rx="5" fill="#2563eb" opacity="0.8"/>
                    <rect x="20" y="20" width="60" height="50" rx="5" fill="#2563eb"/>
                    <path d="M 35 35 L 45 45 L 65 30" stroke="white" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="email-title">{{ $sender_name ?? company_name() }} sent you an invoice with the following details:</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Invoice Status -->
            <div class="info-section">
                <div class="info-label">Invoice Status</div>
                <span class="status-badge">{{ $status ?? 'Under Review' }}</span>
            </div>

            <!-- Total Amount Due -->
            <div class="info-section amount-section">
                <div class="info-label">Total Amount Due</div>
                <div class="amount-value">{{ $total_amount ?? '0.00 PHP' }}</div>
                <div class="due-date">Due on {{ $due_date ?? 'N/A' }}</div>
            </div>

            <!-- Invoice Number -->
            <div class="info-section">
                <div class="info-label">Invoice Number</div>
                <div class="invoice-number">{{ $invoice_number ?? 'N/A' }}</div>
            </div>

            <!-- Notes/Instructions -->
            @if(isset($notes) && $notes)
            <div class="info-section notes-section">
                <div class="notes-label">Notes/Instructions</div>
                <div class="notes-content">{{ $notes }}</div>
            </div>
            @endif

            <!-- CTA Section -->
            <div class="cta-section">
                <p class="cta-text">Click on the button below to view your invoice.</p>
                <a href="{{ $invoice_url ?? '#' }}" class="btn-view-invoice">View Invoice</a>
            </div>

            <!-- Divider -->
            <hr class="divider">

            <!-- URL Section -->
            <div class="url-section">
                <p class="url-label">Or copy this URL to your browser:</p>
                <a href="{{ $invoice_url ?? '#' }}" class="url-link">{{ $invoice_url ?? 'N/A' }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p class="footer-text">{{ company_name() }}</p>
            @if(company_address())
            <p class="footer-text">{{ company_address() }}</p>
            @endif
            @if(company_email() || company_phone())
            <p class="footer-text">
                @if(company_email()){{ company_email() }}@endif
                @if(company_email() && company_phone()) | @endif
                @if(company_phone()){{ company_phone() }}@endif
            </p>
            @endif
            <p class="powered-by">Powered by {{ config('app.name', 'Laravel') }}</p>
        </div>
    </div>
</body>
</html>
