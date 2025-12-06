<?php

namespace App\Http\Controllers;

use App\Models\InventoryHistory;
use App\Models\InventoryHistoryGroup;
use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    //
    public function printPdf($id)
    {
        // Increase execution time for PDF generation
        set_time_limit(120);

        $order = InventoryHistory::where('inventory_group_id', $id)
            ->get();

        $inv_group = InventoryHistoryGroup::find($id);

        if (!$inv_group) {
            abort(404, 'Receipt not found');
        }

        // Get company settings
        $company = CompanySetting::getSettings();

        // Additional receipt data
        $data = [
            'order' => $order,
            'inv_group' => $inv_group,

            // Company Information from database
            'company_name' => $company->company_name,
            'company_logo' => $company->company_logo,
            'company_address' => $company->company_address,
            'company_phone' => $company->company_phone,
            'company_email' => $company->company_email,
            'company_tax_id' => $company->company_tax_id,

            // Customer Information (you can pass these from form or database)
            'customer_name' => $inv_group->customer_name ?? null,
            'customer_address' => $inv_group->customer_address ?? null,
            'customer_phone' => $inv_group->customer_phone ?? null,

            // Optional fields
            'payment_method' => $inv_group->payment_method ?? 'Cashless',
            'amount_paid' => $inv_group->amount_paid ?? null,
            'discount' => $inv_group->discount ?? 0,
            'tax' => $inv_group->tax ?? 0,

            // Optional features
            'show_qr' => false, // Set to true to enable QR code
            'footer_message' => null, // Add custom footer message if needed
        ];

        $pdf = Pdf::loadView('receipt.pdf', $data);

        // Optional: set paper size for thermal printer (80mm width)
        $pdf->setPaper([0, 0, 226.77, 1000]); // 80mm width × height, in points

        return $pdf->stream('receipt_'.$id.'.pdf'); // use $id instead of $order->id because $order is a collection
    }
}
