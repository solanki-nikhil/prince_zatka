<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
     // List all cart items for logged in user
    public function index()
    {
        $carts = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $carts
        ]);
    }

    // Add product to cart
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        $cart = Cart::updateOrCreate(
            [
                'user_id'    => Auth::id(),
                'product_id' => $request->product_id
            ],
            [
                'quantity'   => $request->quantity
            ]
        );

        return response()->json([
            'status'  => true,
            'message' => 'Product added to cart',
            'data'    => $cart
        ], 201);
    }

    // Update cart quantity
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        $cart->quantity = $request->quantity;
        $cart->save();

        return response()->json([
            'status'  => true,
            'message' => 'Cart updated',
            'data'    => $cart
        ]);
    }

    // Remove product from cart
    public function destroy($id)
    {
        $cart = Cart::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        $cart->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Cart item removed'
        ]);
    }
}
