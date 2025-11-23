<?php

namespace App\Http\Controllers;

use App\Models\InventoryHistory;
use App\Models\InventoryHistoryGroup;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    //
    public function printPdf($id)
    {
        $order = InventoryHistory::where('inventory_group_id', $id)
            ->get();

        $inv_group = InventoryHistoryGroup::find($id);
        $pdf = Pdf::loadView('receipt.pdf', compact('order','inv_group'));

        // Optional: set paper size for thermal printer (80mm width)
        $pdf->setPaper([0, 0, 226.77, 1000]); // 80mm width × height, in points

        return $pdf->stream('receipt_'.$id.'.pdf'); // use $id instead of $order->id because $order is a collection
    }
}
