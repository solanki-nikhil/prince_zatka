<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // List all products
    public function index()
    {
        $products = Product::with(['category', 'subCategory'])->get();

        return response()->json([
            'status' => true,
            'message' => 'Product List',
            'data' => $products
        ], 200);
    }
}
