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
            $result = send_email(
                to: $validated['email'],
                subject: $validated['subject'],
                view: 'emails.custom',
                data: ['emailContent' => $validated['message']]
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
}
