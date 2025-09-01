<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SerialNo;
use Illuminate\Support\Facades\Auth;

class SerialNoController extends Controller
{
    public function rejectedList(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $rejectedSerials = SerialNo::where('user_id', $user->id)
            ->where('is_reject', 1) // 1 = rejected
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Rejected Serial Numbers fetched successfully',
            'data'    => $rejectedSerials
        ], 200);
    }
}
