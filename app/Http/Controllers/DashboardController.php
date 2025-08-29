<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $categories = Category::all(); // Fetch all categories
        $products = Product::all();

        // Fetch cart items for the logged-in user
        $cartItems = Cart::where('user_id', $user->id)->get()->keyBy('product_id');

        // Attach cart info to each product
        foreach ($products as $product) {
            $cartItem = $cartItems->get($product->id);
            $product->cart_quantity = $cartItem ? $cartItem->quantity : 0;
        }

        return view('customer.dashboard', compact('categories', 'products'));
    }
}
