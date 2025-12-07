<?php

use App\Models\CompanySetting;

if (!function_exists('company_settings')) {
    /**
     * Get company settings from database
     * Returns the company settings object with all configuration
     *
     * @return \App\Models\CompanySetting|null
     */
    function company_settings()
    {
        return CompanySetting::getSettings();
    }
}

if (!function_exists('company_name')) {
    /**
     * Get the company name
     *
     * @return string
     */
    function company_name()
    {
        $settings = company_settings();
        return $settings ? $settings->company_name : config('app.name', 'Laravel');
    }
}

if (!function_exists('company_logo')) {
    /**
     * Get the company logo path
     *
     * @param bool $fullUrl Whether to return full URL or just path
     * @return string
     */
    function company_logo($fullUrl = false)
    {
        $settings = company_settings();
        $logoPath = $settings && $settings->company_logo
            ? $settings->company_logo
            : 'assets/images/calva.jpg';

        return $fullUrl ? asset($logoPath) : $logoPath;
    }
}

if (!function_exists('company_address')) {
    /**
     * Get the company address
     *
     * @return string
     */
    function company_address()
    {
        $settings = company_settings();
        return $settings ? $settings->company_address : '';
    }
}

if (!function_exists('company_phone')) {
    /**
     * Get the company phone number
     *
     * @return string|null
     */
    function company_phone()
    {
        $settings = company_settings();
        return $settings ? $settings->company_phone : null;
    }
}

if (!function_exists('company_email')) {
    /**
     * Get the company email
     *
     * @return string|null
     */
    function company_email()
    {
        $settings = company_settings();
        return $settings ? $settings->company_email : null;
    }
}

if (!function_exists('company_tax_id')) {
    /**
     * Get the company tax ID
     *
     * @return string|null
     */
    function company_tax_id()
    {
        $settings = company_settings();
        return $settings ? $settings->company_tax_id : null;
    }
}

if (!function_exists('send_email')) {
    /**
     * Send email using SMTP configuration
     *
     * How to call this function:
     *
     * Example 1: Simple email
     * send_email(
     *     to: 'recipient@example.com',
     *     subject: 'Test Email',
     *     view: 'emails.test',
     *     data: ['name' => 'John Doe']
     * );
     *
     * Example 2: Invoice email
     * send_email(
     *     to: 'customer@example.com',
     *     subject: 'Invoice #INV-001',
     *     view: 'emails.invoice',
     *     data: [
     *         'invoice_number' => 'INV-001',
     *         'total_amount' => '500.00 PHP',
     *         'due_date' => 'Nov 30, 2025',
     *         'status' => 'Under Review',
     *         'invoice_url' => 'https://example.com/invoice/123',
     *         'customer_name' => 'John Doe'
     *     ]
     * );
     *
     * Example 3: Email with CC and BCC
     * send_email(
     *     to: 'recipient@example.com',
     *     subject: 'Important Notice',
     *     view: 'emails.notice',
     *     data: ['message' => 'Hello'],
     *     cc: ['cc@example.com'],
     *     bcc: ['bcc@example.com'],
     *     fromEmail: 'noreply@yourcompany.com',
     *     fromName: 'Your Company'
     * );
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $view Blade view path (e.g., 'emails.invoice')
     * @param array $data Data to pass to the email view
     * @param array|null $cc CC email addresses
     * @param array|null $bcc BCC email addresses
     * @param string|null $fromEmail From email address (uses config if null)
     * @param string|null $fromName From name (uses company name if null)
     * @return bool True if email sent successfully, false otherwise
     */
    function send_email(
        string $to,
        string $subject,
        string $view,
        array $data = [],
        ?array $cc = null,
        ?array $bcc = null,
        ?string $fromEmail = null,
        ?string $fromName = null
    ): bool {
        try {
            // Set from email and name
            $fromEmail = $fromEmail ?? config('mail.from.address');
            $fromName = $fromName ?? company_name();

            // Send the email
            \Illuminate\Support\Facades\Mail::send($view, $data, function ($message) use (
                $to,
                $subject,
                $cc,
                $bcc,
                $fromEmail,
                $fromName
            ) {
                $message->to($to)
                    ->subject($subject)
                    ->from($fromEmail, $fromName);

                // Add CC if provided
                if ($cc && is_array($cc) && count($cc) > 0) {
                    $message->cc($cc);
                }

                // Add BCC if provided
                if ($bcc && is_array($bcc) && count($bcc) > 0) {
                    $message->bcc($bcc);
                }
            });

            return true;
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Email sending failed: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'view' => $view
            ]);

            return false;
        }
    }
}
