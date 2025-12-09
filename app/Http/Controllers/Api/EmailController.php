<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Supplier;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function getEmailList()
    {
        $distributorEmails = Distributor::whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('email')
            ->toArray();

        $supplierEmails = Supplier::whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('email')
            ->toArray();

        $allEmails = array_merge($distributorEmails, $supplierEmails);
        $uniqueEmails = array_values(array_unique($allEmails));

        return response()->json([
            'success' => true,
            'emails' => $uniqueEmails
        ]);
    }

    public function sendEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        try {
            // Process base64 images - remove them or convert to text
            $processedMessage = $this->processBase64Images($validated['message']);

            $result = send_email(
                to: $validated['email'],
                subject: $validated['subject'],
                view: 'emails.custom',
                data: ['emailContent' => $processedMessage]
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'error' => $result['error'] ?? null
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process base64 encoded images in the message
     * Removes base64 images and replaces with a placeholder or removes them entirely
     */
    private function processBase64Images($message)
    {
        // Remove base64 encoded images from img tags
        $pattern = '/<img[^>]+src="data:image\/[^;]+;base64[^"]*"[^>]*>/i';

        // Replace with a notice that images are not supported
        $replacement = '<div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 12px; border-radius: 6px; margin: 12px 0; color: #92400e; font-size: 14px;">
            <strong>Note:</strong> Inline images are not supported in emails. Please use image URLs instead.
        </div>';

        $processedMessage = preg_replace($pattern, $replacement, $message);

        return $processedMessage;
    }
}
