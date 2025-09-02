<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MediaMaster;

class MediaController extends Controller
{
    // Media listing (public, no auth)
    public function index()
    {
        $media = MediaMaster::select('id', 'title', 'description', 'image', 'link', 'created_at')
            ->orderBy('id', 'desc')
            ->get();

        

        return response()->json([
            'status'  => true,
            'message' => 'Media List Retrieved Successfully',
            'data'    => $media
        ], 200);
    }
}
