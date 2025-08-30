<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SerialNo;

class WarrantyController extends Controller
{
    public function checkWarranty(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string'
        ]);

        $serialNumber = $request->serial_number;

        $warranty = SerialNo::with(['product.category'])
            ->where('sn', $serialNumber)
            ->first();

        if (!$warranty) {
            return response()->json([
                'status'  => false,
                'message' => 'Serial Number not found'
            ], 404);
        }

        if ($warranty->valid_to) {
            return response()->json([
                'status'  => true,
                'message' => 'Serial Number is assigned',
                'data'    => [
                    'serial_id' => $warranty->id,
                    'serial_no' => $warranty->sn,
                    'product'   => $warranty->product->product_name ?? null,
                    'category'  => $warranty->product->category->category_name ?? null,
                    'customer'  => [
                        'name'     => $warranty->cus_name,
                        'village'  => $warranty->cus_village,
                        'mobile'   => $warranty->cus_mobile,
                    ],
                    'warranty_date' => $warranty->valid_to
                ]
            ], 200);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Serial Number is available',
            'data'    => [
                'serial_id' => $warranty->id,
                'serial_no' => $warranty->sn,
                'product'   => $warranty->product->product_name ?? null,
                'category'  => $warranty->product->category->category_name ?? null
            ]
        ], 200);
    }
}
