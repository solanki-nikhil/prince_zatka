<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        // ✅ Ensure user is logged in
        if (!$request->user()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }

        // ✅ Validation
        $validator = Validator::make($request->all(), [
            'category_id'     => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'product_name'    => 'required|string|max:255',
            'product_code'    => 'required|string|max:100|unique:products,product_code',
            'quantity'        => 'required|integer|min:1',
            'box'             => 'required|integer|min:1',
            'price'           => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        // ✅ Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // ✅ Create Product
        $product = Product::create([
            'category_id'     => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'product_name'    => $request->product_name,
            'product_code'    => $request->product_code,
            'quantity'        => $request->quantity,
            'box'             => $request->box,
            'price'           => $request->price,
            'description'     => $request->description,
            'image'           => $imagePath
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Product created successfully',
            'data'    => $product
        ], 201);
    }
}
