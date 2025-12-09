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
            line-height: 1.4;
        }
        .email-body {
            padding: 0 40px 40px 40px;
        }
        .message-section {
            background-color: #faf5ff;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
            border-left: 4px solid #7c3aed;
        }
        .message-label {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 12px;
            font-weight: 500;
        }
        .message-content {
            color: #1f2937;
            font-size: 15px;
            line-height: 1.7;
        }
        .message-content p {
            margin-bottom: 12px;
        }
        .message-content p:last-child {
            margin-bottom: 0;
        }
        .message-content h1, .message-content h2, .message-content h3 {
            margin-top: 16px;
            margin-bottom: 12px;
            font-weight: 600;
        }
        .message-content h1 {
            font-size: 24px;
        }
        .message-content h2 {
            font-size: 20px;
        }
        .message-content h3 {
            font-size: 18px;
        }
        .message-content ul, .message-content ol {
            margin-left: 20px;
            margin-bottom: 12px;
        }
        .message-content li {
            margin-bottom: 6px;
        }
        .message-content strong {
            font-weight: 600;
        }
        .message-content em {
            font-style: italic;
        }
        .message-content a {
            color: #7c3aed;
            text-decoration: underline;
        }
        .message-content blockquote {
            border-left: 3px solid #7c3aed;
            padding-left: 16px;
            margin: 12px 0;
            color: #4b5563;
            font-style: italic;
        }
        .message-content code {
            background-color: #f3f4f6;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 14px;
        }
        .message-content pre {
            background-color: #f3f4f6;
            padding: 12px;
            border-radius: 6px;
            overflow-x: auto;
            margin: 12px 0;
        }
        .message-content img {
            max-width: 100%;
            height: auto;
            border-radius: 6px;
            margin: 12px 0;
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
                    <defs>
                        <linearGradient id="purpleGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#7c3aed;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#a855f7;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <path d="M30 25 L30 75 L45 75 C55 75 62 68 62 57.5 C62 50 57 45 50 45 L30 45 M30 45 L50 45 C57 45 62 40 62 32.5 C62 25 55 25 45 25 L30 25"
                          stroke="url(#purpleGradient)" stroke-width="8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M65 35 L75 35 L70 50 L80 50" stroke="url(#purpleGradient)" stroke-width="6" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="email-title">{{ company_name() }} sent you a message with the following details:</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            <!-- Message Content -->
            <div class="message-section">
                <div class="message-label">Message Content</div>
                <div class="message-content">{!! $emailContent ?? 'No message content' !!}</div>
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
        </div>
    </div>
</body>
</html>
