<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $carts = Cart::all(); // Fetch all categories
        $categories = Category::all(); // Fetch all categories
        $products = Product::all();

        return view('carts', compact('carts', 'categories', 'products'));
    }

    public function addToCart(Request $request)
    {
        $userId = Auth::id();
        $productId = $request->product_id;

        // Get product's box value
        $product = Product::findOrFail($productId);
        $boxQuantity = $product->box; // Get the quantity per box

        // Check if product is already in cart
        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            // Increase quantity by box quantity if already in cart
            $cartItem->increment('quantity', $boxQuantity);
        } else {
            // Add new product to cart with box quantity
            Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $boxQuantity
            ]);
        }

        return response()->json(['message' => 'Added to cart successfully']);
    }

    public function updateCart(Request $request, $id)
    {
        $cartItem = Cart::find($id);

        if (!$cartItem) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        // Validate the quantity to ensure it's a positive integer
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Update the quantity
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'cartItem' => $cartItem
        ]);
    }

    public function removeItem(Request $request)
    {
        $cartItem = Cart::where('id', $request->id)->where('user_id', Auth::id())->first();

        if ($cartItem) {
            $cartItem->delete();
            return response()->json(['message' => 'Item removed successfully.']);
        }

        return response()->json(['message' => 'Item not found.'], 404);
    }


    public function totalCart(Request $request)
    {
        $total = Cart::where('user_id', Auth::id())
            ->get()
            ->sum(fn($item) => $item->product->price * $item->quantity);

        return response()->json(['total' => $total]);
    }

    public function viewCart()
    {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return view('customer.cart_sidebar', compact('cartItems', 'total'));
    }

    public function manageCart(Request $request)
    {
        $userId = Auth::id();
        $productId = $request->product_id;
        $quantity = $request->quantity; // Get quantity from UI

        // Validate input
        // if ($quantity < 1) {
        //     return response()->json(['error' => 'Invalid quantity'], 400);
        // }

        // Get product details
        $product = Product::findOrFail($productId);
        $boxQuantity = $product->box; // Get default box quantity

        // Check if product exists in cart
        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            // If quantity is 0, remove item
            if ($quantity == 0) {
                $cartItem->delete();
                return response()->json(['message' => 'Item removed successfully.']);
            }

            // Update cart quantity
            $cartItem->update(['quantity' => $quantity]);
        } else {
            // Add new item with default box quantity
            Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity ?: $boxQuantity
            ]);
        }

        return response()->json([
            'message' => 'Cart updated successfully',
            'cartItem' => $cartItem ?? null
        ]);
    }
}
