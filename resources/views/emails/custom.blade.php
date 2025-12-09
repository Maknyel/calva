<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Message</title>
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
        .message-section {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
        }
        .message-content {
            color: #1f2937;
            font-size: 15px;
            line-height: 1.7;
            white-space: pre-wrap;
        }
        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 20px 0;
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
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <rect x="10" y="30" width="80" height="50" rx="5" fill="#2563eb" opacity="0.8"/>
                    <rect x="20" y="20" width="60" height="50" rx="5" fill="#2563eb"/>
                    <path d="M 30 45 L 40 55 L 60 35" stroke="white" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="email-title">Message from {{ company_name() }}</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Message Content -->
            <div class="message-section">
                <div class="message-content">{{ $emailContent ?? 'No message content' }}</div>
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
