<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SerialNo;
use App\Models\WarrantyHistory;

class WarrantyController extends Controller
{
    public function checkWarranty(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string'
        ]);

        // Find serial number
        $serial = Serialno::with(['product.category'])->where('sn', $request->serial_number)->first();

        if (!$serial) {
            return response()->json([
                'status' => false,
                'message' => 'Serial number not found'
            ], 404);
        }

        // Get warranty history
        $warrantyHistories = WarrantyHistory::where('serial_no_id', $serial->id)->get();

        return response()->json([
            'status' => true,
            'message' => 'Warranty details found',
            'data' => [
                'serial' => [
                    'id' => $serial->id,
                    'sn' => $serial->sn,
                    'valid_from' => $serial->valid_from,
                    'valid_to' => $serial->valid_to,
                    'status' => $serial->status,
                    'is_replace' => $serial->is_replace,
                    'is_reject' => $serial->is_reject,
                ],
                'product' => [
                    'id' => $serial->product->id ?? null,
                    'name' => $serial->product->name ?? null,
                    'category' => $serial->product->category->name ?? null,
                ],
                'warranty_history' => $warrantyHistories
            ]
        ], 200);
    }
}
